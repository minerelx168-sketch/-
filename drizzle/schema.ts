import { int, mysqlEnum, mysqlTable, text, timestamp, varchar, decimal, bigint } from "drizzle-orm/mysql-core";

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
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
  lastSignedIn: timestamp("lastSignedIn").defaultNow().notNull(),
});

/**
 * Credits table - stores current balance for each user
 */
export const credits = mysqlTable("credits", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  balance: int("balance").notNull().default(0), // balance in cents (e.g., 2500 = $25.00)
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
});

/**
 * Transactions table - stores top-up history
 */
export const transactions = mysqlTable("transactions", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  amount: int("amount").notNull(), // amount in cents
  type: mysqlEnum("type", ["topup", "usage", "refund"]).notNull(),
  status: mysqlEnum("status", ["pending", "completed", "failed"]).notNull().default("completed"),
  description: text("description"),
  lemonOrderId: varchar("lemonOrderId", { length: 128 }),
  lemonProductId: varchar("lemonProductId", { length: 128 }),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

export type User = typeof users.$inferSelect;
export type InsertUser = typeof users.$inferInsert;
export type Credit = typeof credits.$inferSelect;
export type Transaction = typeof transactions.$inferSelect;
