import type { Request, Response } from "express";
import { and, asc, desc, eq, or, sql, type SQL } from "drizzle-orm";
import { getDb, schema, getCurrentInstallment } from "./db.js";
import { pushMessage } from "./lineApi.js";
import { buildReminderFlexMessage } from "./slipFlexMessages.js";
import {
  determineEscalationLevel,
  shouldSendEscalation,
  type EscalationLevel,
} from "../shared/escalation.js";
import { daysBetween, formatThaiDate } from "../shared/dates.js";
import { round2, safeNum } from "../shared/money.js";

type Contract = typeof schema.contracts.$inferSelect;
type Installment = typeof schema.installmentSchedule.$inferSelect;

export type DailySyncSummary = {
  scannedContracts: number;
  updatedOverdue: number;
  remindersSent: number;
  errors: number;
  ownerNotified: number;
};

const RATE_LIMIT_MS = Number(process.env.SCHEDULED_RATE_LIMIT_MS ?? 500);
const sleep = (ms: number) => new Promise((r) => setTimeout(r, ms));

/**
 * Phase 1: walk active/overdue contracts, refresh installment_schedule.penaltyAmount,
 * contracts.overdueDays and contracts.status accordingly.
 */
export async function syncOverdueAndPenalty(now: Date = new Date()): Promise<{
  scanned: number;
  updated: number;
}> {
  const db = getDb();
  const contracts = await db
    .select()
    .from(schema.contracts)
    .where(
      or(eq(schema.contracts.status, "active"), eq(schema.contracts.status, "overdue")),
    );
  let updated = 0;
  for (const c of contracts) {
    const current = await getCurrentInstallment(c.id);
    if (!current) continue;
    const due = current.dueDate instanceof Date ? current.dueDate : new Date(current.dueDate);
    const overdueDays = daysBetween(due, now);
    if (overdueDays > 0) {
      const penalty = round2(overdueDays * safeNum(c.penaltyRate));
      await db
        .update(schema.installmentSchedule)
        .set({
          penaltyAmount: penalty.toFixed(2),
          status: current.status === "paid" ? current.status : "overdue",
        })
        .where(eq(schema.installmentSchedule.id, current.id));
      await db
        .update(schema.contracts)
        .set({ overdueDays, status: "overdue" })
        .where(eq(schema.contracts.id, c.id));
      updated++;
    } else if (c.overdueDays !== 0 || c.status === "overdue") {
      // recovered (e.g. after a payment caught it up)
      await db
        .update(schema.contracts)
        .set({ overdueDays: 0, status: "active" })
        .where(eq(schema.contracts.id, c.id));
      updated++;
    }
  }
  return { scanned: contracts.length, updated };
}

type Candidate = {
  contract: Contract;
  customer: typeof schema.customers.$inferSelect;
  installment: Installment;
  overdueDays: number;
  daysUntilDue: number;
};

async function loadCandidates(now: Date): Promise<Candidate[]> {
  const db = getDb();
  const rows = await db
    .select({ contract: schema.contracts, customer: schema.customers })
    .from(schema.contracts)
    .innerJoin(schema.customers, eq(schema.contracts.customerId, schema.customers.id))
    .where(
      and(
        eq(schema.customers.botActive, 1),
        sql`${schema.customers.lineUserId} IS NOT NULL`,
        sql`${schema.contracts.status} NOT IN ('paid','closed','defaulted')`,
        sql`${schema.contracts.loanStatus} <> 'CLOSED'`,
      ),
    );

  const out: Candidate[] = [];
  for (const r of rows) {
    const inst = await getCurrentInstallment(r.contract.id);
    if (!inst) continue;
    const due = inst.dueDate instanceof Date ? inst.dueDate : new Date(inst.dueDate);
    const od = daysBetween(due, now);
    const upcoming = od > 0 ? 0 : daysBetween(now, due);
    if (od === 0 && upcoming > 1) continue; // no event for this contract today
    out.push({ contract: r.contract, customer: r.customer, installment: inst, overdueDays: od, daysUntilDue: upcoming });
  }
  return out;
}

async function pushReminderTo(c: Candidate, level: EscalationLevel) {
  if (!level) return;
  const variant = level === "REMIND_TOMORROW" ? "soft"
    : level === "OVERDUE_1" ? "L1"
    : level === "OVERDUE_3" ? "L3"
    : level === "OVERDUE_5" ? "L5"
    : level === "OVERDUE_7" ? "L7"
    : "L10";
  const flex = buildReminderFlexMessage({
    contractNumber: c.contract.contractNumber,
    customerName: c.customer.fullName,
    dueDate: c.installment.dueDate as Date,
    dueAmount: safeNum(c.installment.remainingAmount),
    penaltyAmount: safeNum(c.installment.penaltyAmount),
    overdueDays: c.overdueDays,
    paymentUrl: process.env.PUBLIC_BASE_URL
      ? `${process.env.PUBLIC_BASE_URL}/pay/${encodeURIComponent(c.contract.contractNumber)}/installment`
      : undefined,
    variant,
  });
  await pushMessage(c.customer.lineUserId!, [flex]);
  await logOutgoing(c.customer.id, c.customer.lineUserId!, "flex", `${level}:${c.contract.contractNumber}`);
}

async function pushOwnerNotice(c: Candidate, level: EscalationLevel) {
  const owner = process.env.OWNER_LINE_USER_ID;
  if (!owner) return;
  const txt = [
    `🚨 ${level === "OVERDUE_10" ? "เกินกำหนด 10 วัน — ดำเนินการตามกฎหมาย" : "เกินกำหนด 7 วัน — พิจารณาล็อคเครื่อง"}`,
    `สัญญา ${c.contract.contractNumber} (${c.customer.fullName})`,
    `ค้าง ${c.overdueDays} วัน • ยอดคงเหลือ ${Number(c.installment.remainingAmount).toLocaleString("th-TH")} บาท`,
    `ครบกำหนด ${formatThaiDate(c.installment.dueDate)}`,
  ].join("\n");
  await pushMessage(owner, [{ type: "text", text: txt }]);
  await logOutgoing(null, owner, "text", `owner_notice:${level}:${c.contract.contractNumber}`);
}

async function logOutgoing(
  customerId: number | null,
  lineUserId: string,
  type: string,
  content: string,
) {
  const db = getDb();
  await db.insert(schema.lineMessages).values({
    customerId: customerId ?? undefined,
    lineUserId,
    messageType: type,
    messageContent: content.slice(0, 1000),
    direction: "outgoing",
    status: "sent",
  });
}

async function alreadyRemindedToday(customerId: number, level: string, today: Date): Promise<boolean> {
  const db = getDb();
  const startOfDay = new Date(Date.UTC(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate()));
  const rows = await db
    .select({ id: schema.lineMessages.id })
    .from(schema.lineMessages)
    .where(
      and(
        eq(schema.lineMessages.customerId, customerId),
        eq(schema.lineMessages.direction, "outgoing"),
        sql`${schema.lineMessages.messageContent} LIKE ${`${level}:%`}`,
        sql`${schema.lineMessages.createdAt} >= ${startOfDay}`,
      ),
    )
    .limit(1);
  return rows.length > 0;
}

/**
 * Daily 08:00 — refresh status + soft reminders + reminders for low-severity overdue.
 */
export async function runDailySyncAndRemind(
  now: Date = new Date(),
  opts: { dryRun?: boolean } = {},
): Promise<DailySyncSummary> {
  const summary: DailySyncSummary = {
    scannedContracts: 0,
    updatedOverdue: 0,
    remindersSent: 0,
    errors: 0,
    ownerNotified: 0,
  };
  const sync = await syncOverdueAndPenalty(now);
  summary.scannedContracts = sync.scanned;
  summary.updatedOverdue = sync.updated;

  const cands = await loadCandidates(now);
  for (const c of cands) {
    const decision = determineEscalationLevel(c.overdueDays, c.daysUntilDue);
    if (!decision.level) continue;
    if (decision.level === "REMIND_TOMORROW") {
      if (await alreadyRemindedToday(c.customer.id, "REMIND_TOMORROW", now)) continue;
    } else if (!shouldSendEscalation(decision.level, c.contract.lastEscalationLevel)) {
      continue;
    }
    if (opts.dryRun) {
      summary.remindersSent++;
      if (decision.notifyOwner) summary.ownerNotified++;
      continue;
    }
    try {
      await pushReminderTo(c, decision.level);
      summary.remindersSent++;
      if (decision.level !== "REMIND_TOMORROW") {
        const db = getDb();
        await db
          .update(schema.contracts)
          .set({ lastEscalationLevel: decision.level, lastEscalationAt: now })
          .where(eq(schema.contracts.id, c.contract.id));
        await db.insert(schema.auditLogs).values({
          contractId: c.contract.id,
          customerId: c.customer.id,
          contractNumber: c.contract.contractNumber,
          actionType: "reminder_sent",
          description: `ส่งทวงหนี้ระดับ ${decision.level}`,
          performedBy: "scheduler",
        });
      }
      if (decision.notifyOwner) {
        await pushOwnerNotice(c, decision.level);
        summary.ownerNotified++;
      }
    } catch (err) {
      summary.errors++;
      // eslint-disable-next-line no-console
      console.error("[daily-sync] reminder failed", c.contract.contractNumber, err);
    }
    await sleep(RATE_LIMIT_MS);
  }
  return summary;
}

/**
 * Cron-friendly handler — secured with X-SCHEDULED-SECRET.
 */
export async function handleDailySync(req: Request, res: Response): Promise<void> {
  if (!checkScheduledAuth(req, res)) return;
  try {
    const summary = await runDailySyncAndRemind(new Date(), { dryRun: req.query.dryRun === "1" });
    res.json({ ok: true, summary });
  } catch (err) {
    res.status(500).json({ ok: false, error: (err as Error).message });
  }
}

/** 09:00 / 12:00 / 17:00 — escalation scan only (no penalty recalc). */
export async function handleEscalationScan(req: Request, res: Response): Promise<void> {
  if (!checkScheduledAuth(req, res)) return;
  try {
    const summary = await runDailySyncAndRemind(new Date(), { dryRun: req.query.dryRun === "1" });
    res.json({ ok: true, summary });
  } catch (err) {
    res.status(500).json({ ok: false, error: (err as Error).message });
  }
}

function checkScheduledAuth(req: Request, res: Response): boolean {
  const expected = process.env.SCHEDULED_SECRET;
  if (!expected) {
    res.status(503).json({ error: "SCHEDULED_SECRET not configured" });
    return false;
  }
  const got = req.header("x-scheduled-secret") ?? "";
  if (got !== expected) {
    res.status(401).json({ error: "Unauthorized" });
    return false;
  }
  return true;
}

// ============================
// Broadcast (admin-triggered mass send)
// ============================

export type BroadcastFilter = {
  status?: "active" | "overdue" | "paid" | "defaulted" | "closed";
  minOverdueDays?: number;
  customerIds?: number[];
};

export type BroadcastPreviewRow = {
  contractId: number;
  contractNumber: string;
  customerId: number;
  customerName: string;
  lineUserId: string;
  overdueDays: number;
  remainingAmount: number;
};

export async function previewBroadcast(filter: BroadcastFilter): Promise<BroadcastPreviewRow[]> {
  const db = getDb();
  const conds: SQL[] = [
    eq(schema.customers.botActive, 1),
    sql`${schema.customers.lineUserId} IS NOT NULL`,
  ];
  if (filter.status) conds.push(eq(schema.contracts.status, filter.status));
  if (filter.minOverdueDays && filter.minOverdueDays > 0) {
    conds.push(sql`${schema.contracts.overdueDays} >= ${filter.minOverdueDays}`);
  }
  if (filter.customerIds?.length) {
    conds.push(sql`${schema.contracts.customerId} IN (${sql.join(filter.customerIds, sql`,`)})`);
  }
  const rows = await db
    .select({ contract: schema.contracts, customer: schema.customers })
    .from(schema.contracts)
    .innerJoin(schema.customers, eq(schema.contracts.customerId, schema.customers.id))
    .where(and(...conds))
    .orderBy(desc(schema.contracts.overdueDays));
  const out: BroadcastPreviewRow[] = [];
  for (const r of rows) {
    const inst = await getCurrentInstallment(r.contract.id);
    out.push({
      contractId: r.contract.id,
      contractNumber: r.contract.contractNumber,
      customerId: r.customer.id,
      customerName: r.customer.fullName,
      lineUserId: r.customer.lineUserId!,
      overdueDays: r.contract.overdueDays,
      remainingAmount: inst ? safeNum(inst.remainingAmount) : 0,
    });
  }
  return out;
}

export async function sendBroadcast(args: {
  filter: BroadcastFilter;
  message: string;
  performedBy: string;
  dryRun?: boolean;
}): Promise<{ sent: number; failed: number; total: number }> {
  const rows = await previewBroadcast(args.filter);
  let sent = 0;
  let failed = 0;
  for (const r of rows) {
    if (args.dryRun) {
      sent++;
      continue;
    }
    try {
      const personalized = args.message
        .replace(/{contractNumber}/g, r.contractNumber)
        .replace(/{name}/g, r.customerName)
        .replace(/{remaining}/g, r.remainingAmount.toLocaleString("th-TH"));
      await pushMessage(r.lineUserId, [{ type: "text", text: personalized }]);
      await logOutgoing(r.customerId, r.lineUserId, "text", `broadcast:${r.contractNumber}`);
      sent++;
    } catch (err) {
      failed++;
      // eslint-disable-next-line no-console
      console.error("[broadcast] send failed", r.contractNumber, err);
    }
    await sleep(RATE_LIMIT_MS);
  }
  // Single audit row summarizing the run.
  const db = getDb();
  await db.insert(schema.auditLogs).values({
    actionType: "other",
    description: `broadcast: ${sent}/${rows.length} sent (filter ${JSON.stringify(args.filter)})`,
    performedBy: args.performedBy,
  });
  return { sent, failed, total: rows.length };
}
