import type { FlexMessage } from "./lineApi.js";
import type { SlipStatus } from "./thunderApi.js";
import { formatThaiDateTime } from "../shared/dates.js";
import { thb } from "../shared/money.js";

const COLORS: Record<SlipStatus | "reminder", string> = {
  valid: "#27AE60",
  duplicate: "#F39C12",
  invalid: "#E74C3C",
  error: "#95A5A6",
  reminder: "#2196F3",
};

const HEADER_TEXT: Record<SlipStatus, string> = {
  valid: "✅ ตรวจสลิปสำเร็จ",
  duplicate: "⚠️ สลิปซ้ำ",
  invalid: "❌ สลิปไม่ถูกต้อง",
  error: "ระบบขัดข้อง",
};

function row(label: string, value: string, weight: "regular" | "bold" = "regular") {
  return {
    type: "box",
    layout: "horizontal",
    contents: [
      { type: "text", text: label, size: "xs", color: "#888888", flex: 3 },
      { type: "text", text: value, size: "xs", weight, flex: 5, wrap: true },
    ],
  };
}

export type SlipFlexInput = {
  status: SlipStatus;
  amount?: number;
  contractNumber?: string;
  matchResult?: "exact_installment" | "exact_full" | "overpaid" | "underpaid" | "unmatched";
  expectedAmount?: number;
  paymentUrl?: string;
  remainingAmount?: number;
  reason?: string;
  paidAt?: Date;
};

export function buildSlipFlexMessage(input: SlipFlexInput): FlexMessage {
  const color = COLORS[input.status];
  const bodyContents: unknown[] = [];

  if (input.contractNumber) bodyContents.push(row("เลขสัญญา", input.contractNumber, "bold"));
  if (input.amount !== undefined) bodyContents.push(row("ยอดโอน", `${thb(input.amount)} บาท`));
  if (input.matchResult) bodyContents.push(row("ผล", labelMatch(input.matchResult)));
  if (input.expectedAmount !== undefined)
    bodyContents.push(row("ที่ต้องชำระ", `${thb(input.expectedAmount)} บาท`));
  if (input.remainingAmount !== undefined && input.remainingAmount > 0)
    bodyContents.push(row("ยอดคงเหลือ", `${thb(input.remainingAmount)} บาท`));
  if (input.paidAt) bodyContents.push(row("เวลา", formatThaiDateTime(input.paidAt)));
  if (input.reason) bodyContents.push(row("หมายเหตุ", input.reason));

  const footerContents: unknown[] = [];
  if (input.paymentUrl) {
    footerContents.push({
      type: "button",
      style: "primary",
      color,
      action: { type: "uri", label: "ชำระยอดคงเหลือ", uri: input.paymentUrl },
    });
  }

  const bubble = {
    type: "bubble",
    size: "nano",
    header: {
      type: "box",
      layout: "vertical",
      backgroundColor: color,
      paddingAll: "12px",
      contents: [
        {
          type: "text",
          text: HEADER_TEXT[input.status],
          color: "#FFFFFF",
          weight: "bold",
          size: "sm",
        },
      ],
    },
    body: {
      type: "box",
      layout: "vertical",
      spacing: "sm",
      paddingAll: "12px",
      contents: bodyContents,
    },
    ...(footerContents.length
      ? {
          footer: {
            type: "box",
            layout: "vertical",
            spacing: "sm",
            paddingAll: "8px",
            contents: footerContents,
          },
        }
      : {}),
  };

  return {
    type: "flex",
    altText: HEADER_TEXT[input.status],
    contents: bubble,
  };
}

function labelMatch(m: SlipFlexInput["matchResult"]): string {
  switch (m) {
    case "exact_installment":
      return "ตรงค่างวด";
    case "exact_full":
      return "ปิดยอดเต็มจำนวน";
    case "overpaid":
      return "เกินยอด";
    case "underpaid":
      return "ขาด";
    case "unmatched":
      return "ไม่ตรงยอด";
    default:
      return "-";
  }
}

export type ReminderFlexInput = {
  contractNumber: string;
  customerName?: string;
  dueDate: Date;
  dueAmount: number;
  penaltyAmount?: number;
  overdueDays?: number;
  paymentUrl?: string;
  variant?: "soft" | "L1" | "L3" | "L5" | "L7" | "L10";
};

const ESCALATION_COLORS: Record<NonNullable<ReminderFlexInput["variant"]>, string> = {
  soft: "#F1C40F",
  L1: "#F39C12",
  L3: "#E67E22",
  L5: "#D35400",
  L7: "#C0392B",
  L10: "#922B21",
};

const ESCALATION_TITLES: Record<NonNullable<ReminderFlexInput["variant"]>, string> = {
  soft: "📅 พรุ่งนี้ครบกำหนดชำระ",
  L1: "⚠️ เกินกำหนดชำระ 1 วัน",
  L3: "⚠️ เกินกำหนด 3 วัน มีค่าปรับ",
  L5: "⚠️ เกินกำหนด 5 วัน",
  L7: "🚨 เกินกำหนด 7 วัน — แจ้งล็อคเครื่อง",
  L10: "🚨 เกินกำหนด 10 วัน — ดำเนินการตามกฎหมาย",
};

export function buildReminderFlexMessage(input: ReminderFlexInput): FlexMessage {
  const variant = input.variant ?? "L1";
  const color = ESCALATION_COLORS[variant];
  const title = ESCALATION_TITLES[variant];

  const total =
    Math.max(0, Number(input.dueAmount) || 0) + Math.max(0, Number(input.penaltyAmount) || 0);

  const bodyContents: unknown[] = [
    row("เลขสัญญา", input.contractNumber, "bold"),
    row("ครบกำหนด", formatThaiDateTime(input.dueDate)),
    row("ค่างวด", `${thb(input.dueAmount)} บาท`),
  ];
  if (input.penaltyAmount && input.penaltyAmount > 0)
    bodyContents.push(row("ค่าปรับ", `${thb(input.penaltyAmount)} บาท`));
  if (input.overdueDays && input.overdueDays > 0)
    bodyContents.push(row("เกินกำหนด", `${input.overdueDays} วัน`));
  bodyContents.push(row("รวมที่ต้องชำระ", `${thb(total)} บาท`, "bold"));

  const footerContents: unknown[] = [];
  if (input.paymentUrl) {
    footerContents.push({
      type: "button",
      style: "primary",
      color,
      action: { type: "uri", label: "ชำระเงิน", uri: input.paymentUrl },
    });
  }

  return {
    type: "flex",
    altText: title,
    contents: {
      type: "bubble",
      size: "nano",
      header: {
        type: "box",
        layout: "vertical",
        backgroundColor: color,
        paddingAll: "12px",
        contents: [{ type: "text", text: title, color: "#FFFFFF", weight: "bold", size: "sm" }],
      },
      body: {
        type: "box",
        layout: "vertical",
        spacing: "sm",
        paddingAll: "12px",
        contents: bodyContents,
      },
      ...(footerContents.length
        ? {
            footer: {
              type: "box",
              layout: "vertical",
              spacing: "sm",
              paddingAll: "8px",
              contents: footerContents,
            },
          }
        : {}),
    },
  };
}
