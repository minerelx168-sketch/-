import type { Request, Response } from "express";
import { and, desc, eq } from "drizzle-orm";
import { verifyLineSignature } from "../shared/line-signature.js";
import { TtlSet } from "../shared/dedup.js";
import { parseLineText } from "../shared/line-text.js";
import { thb } from "../shared/money.js";
import {
  calculatePaymentAmount,
  matchAmount,
  type ContractLike,
  type InstallmentLike,
} from "../shared/payment-math.js";
import { formatThaiDateTime } from "../shared/dates.js";
import { getDb, schema, getCurrentInstallment } from "./db.js";
import {
  downloadMessageContent,
  getUserProfile,
  pushMessage,
  replyMessage,
  type FlexMessage,
  type LineMessage,
} from "./lineApi.js";
import { determineSlipStatus, verifySlip } from "./thunderApi.js";
import { buildSlipFlexMessage } from "./slipFlexMessages.js";
import { bindLineUserToContract } from "./lineBinding.js";

// shared dedup across requests
const dedup = new TtlSet(5 * 60 * 1000);

type LineEvent = {
  type: string;
  replyToken?: string;
  source?: { userId?: string; type?: string };
  message?: {
    id?: string;
    type?: string;
    text?: string;
  };
  timestamp?: number;
  webhookEventId?: string;
};

export async function handleLineWebhook(req: Request, res: Response): Promise<void> {
  const rawBody: string =
    (req as Request & { rawBody?: string }).rawBody ?? JSON.stringify(req.body);
  const signature = req.header("x-line-signature");
  const secret = process.env.LINE_CHANNEL_SECRET ?? "";

  // Verify signature unless explicitly disabled (e.g. for local dev).
  if (process.env.LINE_VERIFY_SIGNATURE !== "false") {
    if (!verifyLineSignature(rawBody, signature, secret)) {
      res.status(401).json({ error: "Invalid signature" });
      return;
    }
  }

  const body = req.body as { events?: LineEvent[] };
  const events = Array.isArray(body?.events) ? body.events : [];

  // Respond 200 IMMEDIATELY to prevent LINE retry — spec §4.1
  res.status(200).json({ ok: true });

  // Log the raw payload (fire-and-forget).
  void logWebhook(events, rawBody);

  // Process events asynchronously.
  for (const event of events) {
    void processEvent(event).catch((err) => {
      // eslint-disable-next-line no-console
      console.error("[line-webhook] processEvent failed", err);
    });
  }
}

async function logWebhook(events: LineEvent[], raw: string): Promise<void> {
  try {
    const db = getDb();
    for (const ev of events) {
      await db.insert(schema.webhookLogs).values({
        eventType: ev.type ?? "unknown",
        lineUserId: ev.source?.userId,
        rawPayload: raw.length > 10_000 ? raw.slice(0, 10_000) : raw,
        processed: false,
      });
    }
  } catch {
    // best-effort
  }
}

export async function processEvent(event: LineEvent): Promise<void> {
  const id = event.webhookEventId ?? event.message?.id ?? `${event.type}-${event.timestamp ?? ""}`;
  if (id && dedup.seen(id)) return;

  switch (event.type) {
    case "message":
      await handleMessageEvent(event);
      break;
    case "follow":
      await handleFollow(event);
      break;
    default:
      // ignore postback / unfollow / join / leave for phase 3
      break;
  }
}

async function handleMessageEvent(event: LineEvent): Promise<void> {
  const userId = event.source?.userId;
  const replyToken = event.replyToken;
  if (!userId || !event.message) return;

  // Log incoming message
  await logIncoming(userId, event.message.type ?? "unknown", event.message.text ?? "");

  if (event.message.type === "text") {
    await handleTextMessage({ userId, replyToken, text: event.message.text ?? "" });
    return;
  }
  if (event.message.type === "image" && event.message.id) {
    await handleSlipImage({ userId, replyToken, messageId: event.message.id });
    return;
  }
}

async function handleTextMessage(args: {
  userId: string;
  replyToken?: string;
  text: string;
}): Promise<void> {
  const intent = parseLineText(args.text);
  const reply = (msgs: LineMessage[]) =>
    args.replyToken ? replyMessage(args.replyToken, msgs) : pushMessage(args.userId, msgs);

  if (intent.kind === "bind_contract") {
    const profile = await getUserProfile(args.userId).catch(
      () => ({}) as Awaited<ReturnType<typeof getUserProfile>>,
    );
    const result = await bindLineUserToContract({
      contractNumber: intent.contractNumber,
      lineUserId: args.userId,
      displayName: profile.displayName,
    });
    if (!result.ok) {
      await reply([
        {
          type: "text",
          text: `ไม่พบเลขสัญญา ${intent.contractNumber} กรุณาตรวจสอบและลองใหม่อีกครั้ง`,
        },
      ]);
      await logOutgoing(args.userId, "text", "contract_not_found");
      return;
    }
    const summary = await summarizeBalance(result.contractId);
    await reply([{ type: "text", text: `ผูกสัญญา ${intent.contractNumber} สำเร็จ\n${summary}` }]);
    await logOutgoing(args.userId, "text", "bind_success");
    return;
  }

  // Other intents only respond when bot is active.
  const customer = await getActiveBotCustomer(args.userId);
  if (!customer) {
    return; // silent — admin handles
  }

  if (intent.kind === "balance") {
    const contract = await getLatestContractForCustomer(customer.id);
    if (!contract) {
      await reply([{ type: "text", text: "ยังไม่พบสัญญาที่ผูกไว้กับบัญชีนี้" }]);
      return;
    }
    const summary = await summarizeBalance(contract.id);
    await reply([{ type: "text", text: summary }]);
    await logOutgoing(args.userId, "text", "balance");
    return;
  }
  if (intent.kind === "greeting") {
    await reply([
      { type: "text", text: "สวัสดีครับ พิมพ์ 'ยอด' เพื่อดูยอดค้างชำระล่าสุด" },
    ]);
  }
}

async function handleSlipImage(args: {
  userId: string;
  replyToken?: string;
  messageId: string;
}): Promise<void> {
  // Acknowledge immediately via replyToken (one-shot — must use it now).
  if (args.replyToken) {
    try {
      await replyMessage(args.replyToken, [
        { type: "text", text: "กำลังตรวจสอบสลิป กรุณารอสักครู่..." },
      ]);
    } catch {
      // ignore — replyToken may have expired
    }
  }

  let flex: FlexMessage;
  try {
    const image = await downloadMessageContent(args.messageId);
    const result = await verifySlip(image);
    const status = determineSlipStatus(result);
    const customer = await getActiveBotCustomer(args.userId);
    const contract = customer ? await getLatestContractForCustomer(customer.id) : null;

    if (status === "valid" && contract && result.data?.amount) {
      const current = await getCurrentInstallment(contract.id);
      const contractLike: ContractLike = {
        devicePrice: contract.devicePrice,
        installmentAmount: contract.installmentAmount,
        penaltyRate: contract.penaltyRate,
        unlockFee: contract.unlockFee,
        lockStatus: contract.lockStatus,
      };
      const instLike: InstallmentLike | null = current
        ? {
            period: current.period,
            dueDate: current.dueDate,
            dueAmount: current.dueAmount,
            paidAmount: current.paidAmount,
            remainingAmount: current.remainingAmount,
          }
        : null;
      const instQuote = calculatePaymentAmount(contractLike, instLike, "installment");
      const fullQuote = calculatePaymentAmount(contractLike, instLike, "full");
      const match = matchAmount(result.data.amount, instQuote.total, fullQuote.total, contract.contractNumber, {
        baseUrl: process.env.PUBLIC_BASE_URL,
      });
      flex = buildSlipFlexMessage({
        status: "valid",
        amount: result.data.amount,
        contractNumber: contract.contractNumber,
        matchResult: match.matchResult,
        expectedAmount:
          match.matchedType === "installment" ? instQuote.total : fullQuote.total,
        paymentUrl: match.paymentUrl,
        remainingAmount: match.matchResult === "underpaid" ? Math.abs(match.difference) : undefined,
        paidAt: new Date(),
      });
      void recordSlipPayment({
        contractId: contract.id,
        customerId: contract.customerId,
        installmentId: current?.id,
        amount: result.data.amount,
        transRef: result.data.transRef ?? undefined,
        senderName: result.data.sender?.name,
        receiverName: result.data.receiver?.name,
        bankName: result.data.sender?.bank,
        thunderRaw: result.raw,
        matchResult: match.matchResult,
      });
    } else {
      flex = buildSlipFlexMessage({
        status,
        amount: result.data?.amount,
        contractNumber: contract?.contractNumber,
        reason: status === "duplicate" ? "สลิปนี้เคยใช้แล้ว" : undefined,
      });
    }
  } catch (err) {
    flex = buildSlipFlexMessage({
      status: "error",
      reason: (err as Error).message?.slice(0, 100),
    });
  }

  try {
    await pushMessage(args.userId, [flex]);
    await logOutgoing(args.userId, "flex", flex.altText);
  } catch (err) {
    // eslint-disable-next-line no-console
    console.error("[line-webhook] push flex failed", err);
  }
}

async function handleFollow(event: LineEvent): Promise<void> {
  const userId = event.source?.userId;
  if (!userId) return;
  // Phase 1: admin manages — just log.
  await logIncoming(userId, "follow", "");
}

// =========================
// helpers
// =========================

async function getActiveBotCustomer(lineUserId: string) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.customers)
    .where(and(eq(schema.customers.lineUserId, lineUserId), eq(schema.customers.botActive, 1)))
    .limit(1);
  return row ?? null;
}

async function getLatestContractForCustomer(customerId: number) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.contracts)
    .where(eq(schema.contracts.customerId, customerId))
    .orderBy(desc(schema.contracts.createdAt))
    .limit(1);
  return row ?? null;
}

async function summarizeBalance(contractId: number): Promise<string> {
  const db = getDb();
  const [contract] = await db
    .select()
    .from(schema.contracts)
    .where(eq(schema.contracts.id, contractId))
    .limit(1);
  if (!contract) return "ไม่พบสัญญา";
  const current = await getCurrentInstallment(contractId);
  if (!current) {
    return `สัญญา ${contract.contractNumber}\nสถานะ: ${contract.status}`;
  }
  return [
    `สัญญา ${contract.contractNumber}`,
    `งวดที่ ${current.period} ครบกำหนด ${formatThaiDateTime(current.dueDate)}`,
    `ยอดต้องชำระ: ${thb(Number(current.remainingAmount))} บาท`,
  ].join("\n");
}

async function logIncoming(lineUserId: string, type: string, content: string) {
  const db = getDb();
  const customer = await getActiveBotCustomer(lineUserId);
  await db.insert(schema.lineMessages).values({
    customerId: customer?.id,
    lineUserId,
    messageType: type,
    messageContent: content.slice(0, 1000),
    direction: "incoming",
    status: "received",
  });
}

async function logOutgoing(lineUserId: string, type: string, content: string) {
  const db = getDb();
  const customer = await getActiveBotCustomer(lineUserId);
  await db.insert(schema.lineMessages).values({
    customerId: customer?.id,
    lineUserId,
    messageType: type,
    messageContent: content.slice(0, 1000),
    direction: "outgoing",
    status: "sent",
  });
}

async function recordSlipPayment(args: {
  contractId: number;
  customerId: number;
  installmentId?: number;
  amount: number;
  transRef?: string;
  senderName?: string;
  receiverName?: string;
  bankName?: string;
  thunderRaw: unknown;
  matchResult: string;
}) {
  try {
    const db = getDb();
    await db.insert(schema.payments).values({
      contractId: args.contractId,
      customerId: args.customerId,
      installmentId: args.installmentId,
      amount: args.amount.toFixed(2),
      paymentDate: new Date(),
      verificationStatus: "verified",
      thunderTransRef: args.transRef,
      thunderResponse: JSON.stringify(args.thunderRaw).slice(0, 10_000),
      senderName: args.senderName,
      receiverName: args.receiverName,
      bankName: args.bankName,
      notes: `slip:${args.matchResult}`,
    });
  } catch (err) {
    // eslint-disable-next-line no-console
    console.error("[line-webhook] recordSlipPayment failed", err);
  }
}

export const _internals = { dedup };
