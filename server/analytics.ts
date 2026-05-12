import { and, eq, sql } from "drizzle-orm";
import { getDb, schema } from "./db.js";
import { safeNum } from "../shared/money.js";

const MS_PER_DAY = 24 * 60 * 60 * 1000;

/** Daily payment totals over the last N days (default 30). Only counts verified, non-voided. */
export async function dailyPaymentTotals(days = 30): Promise<{ date: string; total: number; count: number }[]> {
  const db = getDb();
  const since = new Date(Date.now() - days * MS_PER_DAY);
  const rows = await db
    .select({
      date: sql<string>`DATE(${schema.payments.paymentDate})`,
      total: sql<string>`COALESCE(SUM(${schema.payments.amount}), 0)`,
      count: sql<number>`COUNT(*)`,
    })
    .from(schema.payments)
    .where(
      and(
        eq(schema.payments.verificationStatus, "verified"),
        eq(schema.payments.voided, 0),
        sql`${schema.payments.paymentDate} >= ${since}`,
      ),
    )
    .groupBy(sql`DATE(${schema.payments.paymentDate})`)
    .orderBy(sql`DATE(${schema.payments.paymentDate})`);
  return rows.map((r) => ({ date: String(r.date), total: safeNum(r.total), count: Number(r.count) }));
}

export async function contractStatusBreakdown() {
  const db = getDb();
  const rows = await db
    .select({
      status: schema.contracts.status,
      count: sql<number>`COUNT(*)`,
    })
    .from(schema.contracts)
    .groupBy(schema.contracts.status);
  const out: Record<string, number> = { active: 0, overdue: 0, paid: 0, defaulted: 0, closed: 0 };
  for (const r of rows) out[r.status as string] = Number(r.count);
  return out;
}

export async function paymentStatsSummary(): Promise<{
  totalCollected: number;
  verifiedCount: number;
  pendingCount: number;
  voidedCount: number;
  collectionRate: number; // verified / (verified + outstanding)
}> {
  const db = getDb();
  const [paid] = await db
    .select({
      total: sql<string>`COALESCE(SUM(${schema.payments.amount}), 0)`,
      count: sql<number>`COUNT(*)`,
    })
    .from(schema.payments)
    .where(and(eq(schema.payments.verificationStatus, "verified"), eq(schema.payments.voided, 0)));

  const [pending] = await db
    .select({ count: sql<number>`COUNT(*)` })
    .from(schema.payments)
    .where(eq(schema.payments.verificationStatus, "pending"));

  const [voided] = await db
    .select({ count: sql<number>`COUNT(*)` })
    .from(schema.payments)
    .where(eq(schema.payments.voided, 1));

  const [outstanding] = await db
    .select({ total: sql<string>`COALESCE(SUM(${schema.installmentSchedule.remainingAmount}), 0)` })
    .from(schema.installmentSchedule)
    .where(sql`${schema.installmentSchedule.status} <> 'paid'`);

  const totalCollected = safeNum(paid?.total ?? 0);
  const remaining = safeNum(outstanding?.total ?? 0);
  const collectionRate =
    totalCollected + remaining === 0 ? 0 : totalCollected / (totalCollected + remaining);

  return {
    totalCollected,
    verifiedCount: Number(paid?.count ?? 0),
    pendingCount: Number(pending?.count ?? 0),
    voidedCount: Number(voided?.count ?? 0),
    collectionRate: Math.round(collectionRate * 1000) / 1000,
  };
}
