import { int, mysqlEnum, mysqlTable, text, timestamp, varchar, decimal, boolean } from "drizzle-orm/mysql-core";

/**
 * Core user table backing auth flow.
 */
export const users = mysqlTable("users", {
  id: int("id").autoincrement().primaryKey(),
  openId: varchar("openId", { length: 64 }).notNull().unique(),
  name: text("name"),
  email: varchar("email", { length: 320 }),
  loginMethod: varchar("loginMethod", { length: 64 }),
  role: mysqlEnum("role", ["user", "admin"]).default("user").notNull(),
  cachedBalance: decimal("cachedBalance", { precision: 10, scale: 2 }).default("0.00").notNull(),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
  lastSignedIn: timestamp("lastSignedIn").defaultNow().notNull(),
});

/**
 * Top-up orders - tracks the lifecycle of each payment attempt.
 * Statuses: PENDING → PAID → CREDITED (or FAILED/EXPIRED/REFUNDED)
 */
export const topupOrders = mysqlTable("topup_orders", {
  id: int("id").autoincrement().primaryKey(),
  publicId: varchar("publicId", { length: 64 }).notNull().unique(),
  userId: int("userId").notNull(),
  amount: decimal("amount", { precision: 10, scale: 2 }).notNull(), // credit amount (USD)
  chargeAmount: decimal("chargeAmount", { precision: 10, scale: 2 }).notNull(), // amount billed to customer (with fee)
  currency: varchar("currency", { length: 8 }).default("USD").notNull(),
  status: mysqlEnum("status", ["PENDING", "PAID", "CREDITED", "FAILED", "EXPIRED", "REFUNDED"]).default("PENDING").notNull(),
  provider: varchar("provider", { length: 32 }).default("lemonsqueezy").notNull(),
  providerChargeId: varchar("providerChargeId", { length: 255 }),
  idempotencyKey: varchar("idempotencyKey", { length: 128 }).notNull().unique(),
  feePct: decimal("feePct", { precision: 5, scale: 2 }).default("0.00").notNull(),
  paidAt: timestamp("paidAt"),
  creditedAt: timestamp("creditedAt"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
});

/**
 * Credit transactions - append-only ledger. NEVER update rows.
 * balance_after is computed at insert time from SUM(amount) for the user.
 */
export const creditTransactions = mysqlTable("credit_transactions", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  amount: decimal("amount", { precision: 10, scale: 2 }).notNull(), // positive = credit, negative = debit
  type: mysqlEnum("type", ["TOPUP", "USAGE", "REFUND", "ADJUSTMENT", "BONUS"]).notNull(),
  referenceType: varchar("referenceType", { length: 64 }), // e.g. "TopUpOrder", "ServiceUsage", "Admin"
  referenceId: varchar("referenceId", { length: 128 }), // e.g. order publicId
  balanceAfter: decimal("balanceAfter", { precision: 10, scale: 2 }).notNull(),
  description: text("description"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

/**
 * Webhook events - audit log + deduplication.
 * Every incoming webhook is logged here BEFORE processing.
 */
export const webhookEvents = mysqlTable("webhook_events", {
  id: int("id").autoincrement().primaryKey(),
  provider: varchar("provider", { length: 32 }).notNull(),
  eventId: varchar("eventId", { length: 128 }).notNull(),
  eventType: varchar("eventType", { length: 128 }),
  signatureOk: boolean("signatureOk").default(true).notNull(),
  processed: boolean("processed").default(false).notNull(),
  processedAt: timestamp("processedAt"),
  processingError: text("processingError"),
  rawBody: text("rawBody"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

export type User = typeof users.$inferSelect;
export type InsertUser = typeof users.$inferInsert;
export type TopupOrder = typeof topupOrders.$inferSelect;
export type CreditTransaction = typeof creditTransactions.$inferSelect;
export type WebhookEvent = typeof webhookEvents.$inferSelect;
