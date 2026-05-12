import { drizzle, type MySql2Database } from "drizzle-orm/mysql2";
import mysql from "mysql2/promise";
import { and, asc, desc, eq, like, or, sql } from "drizzle-orm";
import * as schema from "../drizzle/schema.js";

function getInsertId(result: unknown): number {
  if (Array.isArray(result)) {
    const first = result[0] as { insertId?: number } | undefined;
    return Number(first?.insertId ?? 0);
  }
  const r = result as { insertId?: number };
  return Number(r?.insertId ?? 0);
}

import { safeNum, round2 } from "../shared/money.js";
import {
  generateContractNumber,
  generateSchedule,
  calculatePaymentAmount,
  type ContractLike,
  type InstallmentLike,
  type PaymentType,
} from "../shared/payment-math.js";

let _db: MySql2Database<typeof schema> | null = null;
let _pool: mysql.Pool | null = null;

export function getDb(): MySql2Database<typeof schema> {
  if (_db) return _db;
  const url = process.env.DATABASE_URL;
  if (!url) {
    throw new Error("DATABASE_URL is not set. Configure it in your .env file.");
  }
  _pool = mysql.createPool({ uri: url, connectionLimit: 10, decimalNumbers: false });
  _db = drizzle(_pool, { schema, mode: "default" });
  return _db;
}

export async function closeDb(): Promise<void> {
  if (_pool) {
    await _pool.end();
    _pool = null;
    _db = null;
  }
}

// =========================
// Products
// =========================

export async function listProducts(opts: { search?: string; activeOnly?: boolean } = {}) {
  const db = getDb();
  const conds = [];
  if (opts.activeOnly) conds.push(eq(schema.products.isActive, true));
  if (opts.search) {
    conds.push(
      or(
        like(schema.products.name, `%${opts.search}%`),
        like(schema.products.sku, `%${opts.search}%`),
      ),
    );
  }
  const q = db.select().from(schema.products).orderBy(desc(schema.products.createdAt));
  const rows = conds.length ? await q.where(and(...conds)) : await q;
  return rows;
}

export async function getProductById(id: number) {
  const db = getDb();
  const [row] = await db.select().from(schema.products).where(eq(schema.products.id, id)).limit(1);
  return row ?? null;
}

export async function createProduct(data: schema.NewProduct) {
  const db = getDb();
  const result = await db.insert(schema.products).values(data);
  const insertId = getInsertId(result);
  return getProductById(insertId);
}

export async function updateProduct(id: number, patch: Partial<schema.NewProduct>) {
  const db = getDb();
  await db.update(schema.products).set(patch).where(eq(schema.products.id, id));
  return getProductById(id);
}

export async function deleteProduct(id: number) {
  const db = getDb();
  await db.update(schema.products).set({ isActive: false }).where(eq(schema.products.id, id));
}

// =========================
// Customers
// =========================

export async function listCustomers(opts: { search?: string } = {}) {
  const db = getDb();
  const q = db.select().from(schema.customers).orderBy(desc(schema.customers.createdAt));
  if (opts.search) {
    return await q.where(
      or(
        like(schema.customers.fullName, `%${opts.search}%`),
        like(schema.customers.idCardNumber, `%${opts.search}%`),
        like(schema.customers.phoneNumber, `%${opts.search}%`),
      ),
    );
  }
  return await q;
}

export async function getCustomerById(id: number) {
  const db = getDb();
  const [row] = await db.select().from(schema.customers).where(eq(schema.customers.id, id)).limit(1);
  return row ?? null;
}

export async function getCustomerByIdCard(idCardNumber: string) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.customers)
    .where(eq(schema.customers.idCardNumber, idCardNumber))
    .limit(1);
  return row ?? null;
}

export async function getCustomerByLineUserId(lineUserId: string) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.customers)
    .where(eq(schema.customers.lineUserId, lineUserId))
    .limit(1);
  return row ?? null;
}

export async function createCustomer(data: schema.NewCustomer) {
  const db = getDb();
  const existing = await getCustomerByIdCard(data.idCardNumber);
  if (existing) return existing;
  const result = await db.insert(schema.customers).values(data);
  const insertId = getInsertId(result);
  return getCustomerById(insertId);
}

export async function updateCustomer(id: number, patch: Partial<schema.NewCustomer>) {
  const db = getDb();
  await db.update(schema.customers).set(patch).where(eq(schema.customers.id, id));
  return getCustomerById(id);
}

// =========================
// Contracts
// =========================

async function nextDailyContractSequence(date: Date): Promise<number> {
  const db = getDb();
  const yy = String(date.getUTCFullYear()).slice(-2);
  const mm = String(date.getUTCMonth() + 1).padStart(2, "0");
  const dd = String(date.getUTCDate()).padStart(2, "0");
  const prefix = `CF-${yy}${mm}${dd}`;
  const rows = await db
    .select({ contractNumber: schema.contracts.contractNumber })
    .from(schema.contracts)
    .where(like(schema.contracts.contractNumber, `${prefix}%`));
  return rows.length + 1;
}

export type CreateContractInput = {
  customerId: number;
  productId: number;
  devicePrice: number;
  downPayment: number;
  totalPeriods: number;
  installmentAmount?: number;
  penaltyRate?: number;
  unlockFee?: number;
  startDate: Date;
  notes?: string;
  performedBy?: string;
};

export async function createContract(input: CreateContractInput) {
  const db = getDb();
  const device = round2(safeNum(input.devicePrice));
  const down = round2(safeNum(input.downPayment));
  const total = round2(Math.max(0, device - down));
  const periods = Math.max(1, Math.floor(safeNum(input.totalPeriods)));
  const installment = round2(
    safeNum(input.installmentAmount ?? total / periods),
  );
  const penaltyRate = round2(safeNum(input.penaltyRate ?? 100));
  const unlockFee = round2(safeNum(input.unlockFee ?? 500));

  const seq = await nextDailyContractSequence(input.startDate);
  const contractNumber = generateContractNumber(input.startDate, seq);

  const insert = await db.insert(schema.contracts).values({
    contractNumber,
    customerId: input.customerId,
    productId: input.productId,
    devicePrice: device.toFixed(2),
    downPayment: down.toFixed(2),
    totalAmount: total.toFixed(2),
    installmentAmount: installment.toFixed(2),
    totalPeriods: periods,
    penaltyRate: penaltyRate.toFixed(2),
    unlockFee: unlockFee.toFixed(2),
    startDate: input.startDate,
    notes: input.notes,
  });
  const insertId = getInsertId(insert);

  // generate schedule
  const schedule = generateSchedule({
    startDate: input.startDate,
    totalPeriods: periods,
    installmentAmount: installment,
    totalAmount: total,
  });
  if (schedule.length > 0) {
    await db.insert(schema.installmentSchedule).values(
      schedule.map((r) => ({
        contractId: insertId,
        period: r.period,
        dueDate: r.dueDate,
        dueAmount: r.dueAmount.toFixed(2),
        remainingAmount: r.remainingAmount.toFixed(2),
      })),
    );
  }

  // decrement stock
  const product = await getProductById(input.productId);
  if (product && product.stockQuantity > 0) {
    await db
      .update(schema.products)
      .set({ stockQuantity: product.stockQuantity - 1 })
      .where(eq(schema.products.id, input.productId));
  }

  // audit
  await db.insert(schema.auditLogs).values({
    contractId: insertId,
    customerId: input.customerId,
    contractNumber,
    actionType: "contract_created",
    description: `สร้างสัญญา ${contractNumber}`,
    newValue: JSON.stringify({ device, down, total, periods, installment }),
    performedBy: input.performedBy ?? "system",
  });
  await db.insert(schema.auditLogs).values({
    contractId: insertId,
    customerId: input.customerId,
    contractNumber,
    actionType: "installment_generated",
    description: `สร้างตารางงวด ${periods} งวด`,
    performedBy: input.performedBy ?? "system",
  });

  return getContractById(insertId);
}

export async function getContractById(id: number) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.contracts)
    .where(eq(schema.contracts.id, id))
    .limit(1);
  return row ?? null;
}

export async function getContractByNumber(contractNumber: string) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.contracts)
    .where(eq(schema.contracts.contractNumber, contractNumber))
    .limit(1);
  return row ?? null;
}

export type ContractFilter = {
  status?: schema.Contract["status"];
  customerId?: number;
  search?: string;
};

export async function listContracts(opts: ContractFilter = {}) {
  const db = getDb();
  const conds = [];
  if (opts.status) conds.push(eq(schema.contracts.status, opts.status));
  if (opts.customerId) conds.push(eq(schema.contracts.customerId, opts.customerId));
  if (opts.search) conds.push(like(schema.contracts.contractNumber, `%${opts.search}%`));
  const q = db.select().from(schema.contracts).orderBy(desc(schema.contracts.createdAt));
  return conds.length ? await q.where(and(...conds)) : await q;
}

export async function contractStats() {
  const db = getDb();
  const [stats] = await db
    .select({
      total: sql<number>`COUNT(*)`,
      active: sql<number>`SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END)`,
      overdue: sql<number>`SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END)`,
      paid: sql<number>`SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END)`,
      defaulted: sql<number>`SUM(CASE WHEN status = 'defaulted' THEN 1 ELSE 0 END)`,
    })
    .from(schema.contracts);
  return stats;
}

// =========================
// Installments
// =========================

export async function listInstallmentsByContract(contractId: number) {
  const db = getDb();
  return await db
    .select()
    .from(schema.installmentSchedule)
    .where(eq(schema.installmentSchedule.contractId, contractId))
    .orderBy(asc(schema.installmentSchedule.period));
}

export async function getCurrentInstallment(contractId: number) {
  const db = getDb();
  const rows = await db
    .select()
    .from(schema.installmentSchedule)
    .where(
      and(
        eq(schema.installmentSchedule.contractId, contractId),
        or(
          eq(schema.installmentSchedule.status, "pending"),
          eq(schema.installmentSchedule.status, "partial"),
          eq(schema.installmentSchedule.status, "overdue"),
        ),
      ),
    )
    .orderBy(asc(schema.installmentSchedule.period))
    .limit(1);
  return rows[0] ?? null;
}

export async function markInstallmentPaid(
  installmentId: number,
  paidAmount: number,
  performedBy?: string,
) {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.installmentSchedule)
    .where(eq(schema.installmentSchedule.id, installmentId))
    .limit(1);
  if (!row) throw new Error("Installment not found");
  const newPaid = round2(safeNum(row.paidAmount) + safeNum(paidAmount));
  const due = safeNum(row.dueAmount);
  const remaining = round2(Math.max(0, due - newPaid));
  const status = remaining === 0 ? "paid" : "partial";
  await db
    .update(schema.installmentSchedule)
    .set({
      paidAmount: newPaid.toFixed(2),
      remainingAmount: remaining.toFixed(2),
      status,
      paidAt: status === "paid" ? new Date() : row.paidAt,
    })
    .where(eq(schema.installmentSchedule.id, installmentId));
  await db.insert(schema.auditLogs).values({
    contractId: row.contractId,
    actionType: "payment_received",
    description: `งวดที่ ${row.period} รับชำระ ${paidAmount}`,
    previousValue: row.paidAmount,
    newValue: newPaid.toFixed(2),
    performedBy: performedBy ?? "system",
  });
  return { newPaid, remaining, status };
}

// =========================
// Pricing facade — pulls contract + current installment and runs the engine
// =========================

export async function quoteContract(
  contractId: number,
  type: PaymentType,
  now: Date = new Date(),
) {
  const contract = await getContractById(contractId);
  if (!contract) throw new Error("Contract not found");
  const current = await getCurrentInstallment(contractId);
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
  return calculatePaymentAmount(contractLike, instLike, type, now);
}

// =========================
// Payments
// =========================

export async function listPaymentsByContract(contractId: number) {
  const db = getDb();
  return await db
    .select()
    .from(schema.payments)
    .where(eq(schema.payments.contractId, contractId))
    .orderBy(desc(schema.payments.paymentDate));
}

export async function listAllPayments(limit = 200) {
  const db = getDb();
  return await db
    .select()
    .from(schema.payments)
    .orderBy(desc(schema.payments.paymentDate))
    .limit(limit);
}

export async function recordManualPayment(args: {
  contractId: number;
  amount: number;
  paymentDate: Date;
  uploadedBy: string;
  notes?: string;
  period?: number;
}) {
  const db = getDb();
  const contract = await getContractById(args.contractId);
  if (!contract) throw new Error("Contract not found");
  const current = await getCurrentInstallment(args.contractId);
  const insert = await db.insert(schema.payments).values({
    contractId: args.contractId,
    customerId: contract.customerId,
    installmentId: current?.id,
    amount: round2(args.amount).toFixed(2),
    paymentDate: args.paymentDate,
    period: args.period ?? current?.period,
    verificationStatus: "verified",
    manualUpload: 1,
    uploadedBy: args.uploadedBy,
    notes: args.notes,
  });
  const insertId = getInsertId(insert);
  if (current) {
    await markInstallmentPaid(current.id, args.amount, args.uploadedBy);
  }
  await db.insert(schema.auditLogs).values({
    contractId: args.contractId,
    customerId: contract.customerId,
    paymentId: insertId,
    contractNumber: contract.contractNumber,
    actionType: "manual_upload",
    description: `บันทึกรับชำระแบบ manual ${args.amount} บาท`,
    performedBy: args.uploadedBy,
  });
  return insertId;
}

// =========================
// Audit log helper
// =========================

export async function recordAudit(entry: schema.NewAuditLog) {
  const db = getDb();
  await db.insert(schema.auditLogs).values(entry);
}

export async function listAuditLogs(opts: { contractId?: number; limit?: number } = {}) {
  const db = getDb();
  const q = db.select().from(schema.auditLogs).orderBy(desc(schema.auditLogs.createdAt));
  if (opts.contractId) {
    return await q.where(eq(schema.auditLogs.contractId, opts.contractId)).limit(opts.limit ?? 200);
  }
  return await q.limit(opts.limit ?? 200);
}

export { schema };
