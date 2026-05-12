import { and, desc, eq, sql } from "drizzle-orm";
import { getDb, schema, getContractById } from "./db.js";
import { round2, safeNum } from "../shared/money.js";

export async function listDiscountsByContract(contractId: number) {
  const db = getDb();
  return await db
    .select()
    .from(schema.discounts)
    .where(eq(schema.discounts.contractId, contractId))
    .orderBy(desc(schema.discounts.createdAt));
}

export async function activeDiscountTotalByType(
  contractId: number,
  discountType: "penalty" | "closing" | "installment" | "other",
) {
  const db = getDb();
  const [row] = await db
    .select({ total: sql<string>`COALESCE(SUM(${schema.discounts.discountAmount}), 0)` })
    .from(schema.discounts)
    .where(
      and(
        eq(schema.discounts.contractId, contractId),
        eq(schema.discounts.discountType, discountType),
        eq(schema.discounts.status, "active"),
      ),
    );
  return round2(safeNum(row?.total ?? 0));
}

export async function createDiscount(args: {
  contractId: number;
  discountType: "penalty" | "closing" | "installment" | "other";
  originalAmount: number;
  discountAmount: number;
  reason: string;
  approvedBy: string;
}) {
  const db = getDb();
  const c = await getContractById(args.contractId);
  if (!c) throw new Error("Contract not found");
  const original = round2(args.originalAmount);
  const discount = round2(args.discountAmount);
  const final = round2(Math.max(0, original - discount));
  await db.insert(schema.discounts).values({
    contractId: args.contractId,
    customerId: c.customerId,
    contractNumber: c.contractNumber,
    discountType: args.discountType,
    originalAmount: original.toFixed(2),
    discountAmount: discount.toFixed(2),
    finalAmount: final.toFixed(2),
    reason: args.reason,
    approvedBy: args.approvedBy,
    status: "active",
  });
  await db.insert(schema.auditLogs).values({
    contractId: c.id,
    customerId: c.customerId,
    contractNumber: c.contractNumber,
    actionType: "discount_applied",
    description: `ส่วนลด ${args.discountType}: ${discount} (เหลือ ${final})`,
    previousValue: original.toFixed(2),
    newValue: final.toFixed(2),
    remark: args.reason,
    performedBy: args.approvedBy,
  });
}

export async function revokeDiscount(args: {
  discountId: number;
  remark: string;
  performedBy: string;
}) {
  const db = getDb();
  const [d] = await db
    .select()
    .from(schema.discounts)
    .where(eq(schema.discounts.id, args.discountId))
    .limit(1);
  if (!d) throw new Error("Discount not found");
  if (d.status === "revoked") return;

  await db
    .update(schema.discounts)
    .set({ status: "revoked" })
    .where(eq(schema.discounts.id, args.discountId));

  await db.insert(schema.auditLogs).values({
    contractId: d.contractId,
    customerId: d.customerId,
    contractNumber: d.contractNumber,
    actionType: "other",
    description: `เพิกถอนส่วนลด ${d.discountType} ${d.discountAmount}`,
    previousValue: d.status,
    newValue: "revoked",
    remark: args.remark,
    performedBy: args.performedBy,
  });
}
