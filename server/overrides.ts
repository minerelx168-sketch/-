import { and, eq } from "drizzle-orm";
import { getDb, schema, getContractById } from "./db.js";
import { round2, safeNum } from "../shared/money.js";

/**
 * Every override mutates state AND writes an audit_log entry with previous/new values.
 * Caller MUST supply a remark — the tRPC schema enforces this at the boundary.
 */
type AuditAction = typeof schema.auditLogs.$inferInsert.actionType;

async function audit(args: {
  actionType: AuditAction;
  description: string;
  contractId?: number;
  customerId?: number;
  paymentId?: number;
  contractNumber?: string;
  previousValue?: string;
  newValue?: string;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  await db.insert(schema.auditLogs).values({
    contractId: args.contractId,
    customerId: args.customerId,
    paymentId: args.paymentId,
    contractNumber: args.contractNumber,
    actionType: args.actionType,
    description: args.description,
    previousValue: args.previousValue,
    newValue: args.newValue,
    remark: args.remark,
    performedBy: args.performedBy,
  });
}

// =====================================
// Edit payment amount
// =====================================

export async function editPaymentAmount(args: {
  paymentId: number;
  newAmount: number;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const [payment] = await db
    .select()
    .from(schema.payments)
    .where(eq(schema.payments.id, args.paymentId))
    .limit(1);
  if (!payment) throw new Error("Payment not found");
  if (payment.voided) throw new Error("Payment is voided");

  const previous = round2(safeNum(payment.amount));
  const next = round2(args.newAmount);
  const delta = round2(next - previous);

  await db
    .update(schema.payments)
    .set({ amount: next.toFixed(2) })
    .where(eq(schema.payments.id, args.paymentId));

  // Re-apply delta to the installment we touched, if any.
  if (payment.installmentId) {
    const [inst] = await db
      .select()
      .from(schema.installmentSchedule)
      .where(eq(schema.installmentSchedule.id, payment.installmentId))
      .limit(1);
    if (inst) {
      const paidAmount = round2(Math.max(0, safeNum(inst.paidAmount) + delta));
      const due = safeNum(inst.dueAmount);
      const remaining = round2(Math.max(0, due - paidAmount));
      const status = remaining === 0 ? "paid" : paidAmount > 0 ? "partial" : "pending";
      await db
        .update(schema.installmentSchedule)
        .set({
          paidAmount: paidAmount.toFixed(2),
          remainingAmount: remaining.toFixed(2),
          status,
        })
        .where(eq(schema.installmentSchedule.id, payment.installmentId));
    }
  }

  await audit({
    actionType: "edit_amount",
    description: `แก้ไขยอดชำระจาก ${previous} → ${next}`,
    contractId: payment.contractId,
    customerId: payment.customerId,
    paymentId: payment.id,
    previousValue: previous.toFixed(2),
    newValue: next.toFixed(2),
    remark: args.remark,
    performedBy: args.performedBy,
  });
  return { previous, next, delta };
}

// =====================================
// Void payment (reverses the installment progress it caused)
// =====================================

export async function voidPayment(args: {
  paymentId: number;
  reason: string;
  performedBy: string;
}) {
  const db = getDb();
  const [payment] = await db
    .select()
    .from(schema.payments)
    .where(eq(schema.payments.id, args.paymentId))
    .limit(1);
  if (!payment) throw new Error("Payment not found");
  if (payment.voided) throw new Error("Payment already voided");

  await db
    .update(schema.payments)
    .set({
      voided: 1,
      voidedAt: new Date(),
      voidedBy: args.performedBy,
      voidReason: args.reason,
    })
    .where(eq(schema.payments.id, args.paymentId));

  // Reverse installment progress.
  if (payment.installmentId) {
    const [inst] = await db
      .select()
      .from(schema.installmentSchedule)
      .where(eq(schema.installmentSchedule.id, payment.installmentId))
      .limit(1);
    if (inst) {
      const amount = round2(safeNum(payment.amount));
      const paidAmount = round2(Math.max(0, safeNum(inst.paidAmount) - amount));
      const due = safeNum(inst.dueAmount);
      const remaining = round2(Math.max(0, due - paidAmount));
      const status = remaining === 0 ? "paid" : paidAmount > 0 ? "partial" : "pending";
      await db
        .update(schema.installmentSchedule)
        .set({
          paidAmount: paidAmount.toFixed(2),
          remainingAmount: remaining.toFixed(2),
          status,
        })
        .where(eq(schema.installmentSchedule.id, payment.installmentId));
    }
  }

  await audit({
    actionType: "void_transaction",
    description: `ยกเลิกรายการชำระ ${payment.amount} บาท`,
    contractId: payment.contractId,
    customerId: payment.customerId,
    paymentId: payment.id,
    previousValue: payment.amount,
    newValue: "0",
    remark: args.reason,
    performedBy: args.performedBy,
  });
}

// =====================================
// Status / lock overrides
// =====================================

export async function overrideContractStatus(args: {
  contractId: number;
  newStatus: "active" | "overdue" | "paid" | "defaulted" | "closed";
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const c = await getContractById(args.contractId);
  if (!c) throw new Error("Contract not found");
  if (c.status === args.newStatus) return c;

  await db
    .update(schema.contracts)
    .set({ status: args.newStatus })
    .where(eq(schema.contracts.id, args.contractId));

  await audit({
    actionType: "status_override",
    description: `เปลี่ยนสถานะสัญญา ${c.status} → ${args.newStatus}`,
    contractId: c.id,
    customerId: c.customerId,
    contractNumber: c.contractNumber,
    previousValue: c.status,
    newValue: args.newStatus,
    remark: args.remark,
    performedBy: args.performedBy,
  });
  return getContractById(args.contractId);
}

export async function overrideLockStatus(args: {
  contractId: number;
  locked: boolean;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const c = await getContractById(args.contractId);
  if (!c) throw new Error("Contract not found");
  const next = args.locked ? "locked" : "unlocked";
  if (c.lockStatus === next) return c;

  await db
    .update(schema.contracts)
    .set({ lockStatus: next })
    .where(eq(schema.contracts.id, args.contractId));

  await audit({
    actionType: args.locked ? "lock_override" : "unlock_override",
    description: `${args.locked ? "ล็อค" : "ปลดล็อค"}เครื่อง`,
    contractId: c.id,
    customerId: c.customerId,
    contractNumber: c.contractNumber,
    previousValue: c.lockStatus,
    newValue: next,
    remark: args.remark,
    performedBy: args.performedBy,
  });
  return getContractById(args.contractId);
}

// =====================================
// Date + penalty adjustments
// =====================================

export async function adjustInstallmentDate(args: {
  installmentId: number;
  newDueDate: Date;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const [inst] = await db
    .select()
    .from(schema.installmentSchedule)
    .where(eq(schema.installmentSchedule.id, args.installmentId))
    .limit(1);
  if (!inst) throw new Error("Installment not found");

  await db
    .update(schema.installmentSchedule)
    .set({ dueDate: args.newDueDate })
    .where(eq(schema.installmentSchedule.id, args.installmentId));

  await audit({
    actionType: "date_adjustment",
    description: `แก้วันครบกำหนดงวด ${inst.period}`,
    contractId: inst.contractId,
    previousValue: inst.dueDate instanceof Date ? inst.dueDate.toISOString() : String(inst.dueDate),
    newValue: args.newDueDate.toISOString(),
    remark: args.remark,
    performedBy: args.performedBy,
  });
}

export async function adjustPenalty(args: {
  installmentId: number;
  newPenalty: number;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const [inst] = await db
    .select()
    .from(schema.installmentSchedule)
    .where(eq(schema.installmentSchedule.id, args.installmentId))
    .limit(1);
  if (!inst) throw new Error("Installment not found");
  const next = round2(Math.max(0, args.newPenalty));

  await db
    .update(schema.installmentSchedule)
    .set({ penaltyAmount: next.toFixed(2) })
    .where(eq(schema.installmentSchedule.id, args.installmentId));

  await audit({
    actionType: "penalty_adjustment",
    description: `ปรับค่าปรับงวด ${inst.period} → ${next}`,
    contractId: inst.contractId,
    previousValue: inst.penaltyAmount,
    newValue: next.toFixed(2),
    remark: args.remark,
    performedBy: args.performedBy,
  });
}
