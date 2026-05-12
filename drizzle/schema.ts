import {
  mysqlTable,
  int,
  varchar,
  text,
  decimal,
  timestamp,
  boolean,
  index,
  uniqueIndex,
  mysqlEnum,
} from "drizzle-orm/mysql-core";

// =========================
// Mini POS — new tables
// =========================

export const products = mysqlTable(
  "products",
  {
    id: int("id").primaryKey().autoincrement(),
    sku: varchar("sku", { length: 64 }).notNull(),
    name: varchar("name", { length: 255 }).notNull(),
    category: varchar("category", { length: 64 }).notNull().default("smartphone"),
    costPrice: decimal("cost_price", { precision: 12, scale: 2 }).notNull().default("0.00"),
    sellingPrice: decimal("selling_price", { precision: 12, scale: 2 }).notNull().default("0.00"),
    stockQuantity: int("stock_quantity").notNull().default(0),
    imageUrl: text("image_url"),
    isActive: boolean("is_active").notNull().default(true),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    skuIdx: uniqueIndex("products_sku_idx").on(t.sku),
    categoryIdx: index("products_category_idx").on(t.category),
  }),
);

export const customers = mysqlTable(
  "customers",
  {
    id: int("id").primaryKey().autoincrement(),
    fullName: varchar("full_name", { length: 255 }).notNull(),
    idCardNumber: varchar("id_card_number", { length: 13 }).notNull(),
    phoneNumber: varchar("phone_number", { length: 20 }),
    address: text("address"),
    lineUserId: varchar("line_user_id", { length: 64 }),
    lineDisplayName: varchar("line_display_name", { length: 255 }),
    botActive: int("bot_active").notNull().default(0),
    notes: text("notes"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    idCardIdx: uniqueIndex("customers_id_card_idx").on(t.idCardNumber),
    lineUserIdx: index("customers_line_user_idx").on(t.lineUserId),
    phoneIdx: index("customers_phone_idx").on(t.phoneNumber),
  }),
);

export const contracts = mysqlTable(
  "contracts",
  {
    id: int("id").primaryKey().autoincrement(),
    contractNumber: varchar("contract_number", { length: 50 }).notNull(),
    customerId: int("customer_id").notNull(),
    productId: int("product_id").notNull(),
    devicePrice: decimal("device_price", { precision: 12, scale: 2 }).notNull(),
    downPayment: decimal("down_payment", { precision: 12, scale: 2 }).notNull().default("0.00"),
    totalAmount: decimal("total_amount", { precision: 12, scale: 2 }).notNull(),
    installmentAmount: decimal("installment_amount", { precision: 12, scale: 2 }).notNull(),
    totalPeriods: int("total_periods").notNull(),
    penaltyRate: decimal("penalty_rate", { precision: 10, scale: 2 }).notNull().default("100.00"),
    unlockFee: decimal("unlock_fee", { precision: 10, scale: 2 }).notNull().default("500.00"),
    startDate: timestamp("start_date").notNull(),
    status: mysqlEnum("status", ["active", "overdue", "paid", "defaulted", "closed"])
      .notNull()
      .default("active"),
    lockStatus: mysqlEnum("lock_status", ["locked", "unlocked"]).notNull().default("unlocked"),
    loanStatus: varchar("loan_status", { length: 50 }).notNull().default("NORMAL"),
    overdueDays: int("overdue_days").notNull().default(0),
    lastEscalationLevel: varchar("last_escalation_level", { length: 30 }),
    lastEscalationAt: timestamp("last_escalation_at"),
    notes: text("notes"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    contractNumberIdx: uniqueIndex("contracts_number_idx").on(t.contractNumber),
    customerIdx: index("contracts_customer_idx").on(t.customerId),
    productIdx: index("contracts_product_idx").on(t.productId),
    statusIdx: index("contracts_status_idx").on(t.status),
    loanStatusIdx: index("contracts_loan_status_idx").on(t.loanStatus),
  }),
);

export const installmentSchedule = mysqlTable(
  "installment_schedule",
  {
    id: int("id").primaryKey().autoincrement(),
    contractId: int("contract_id").notNull(),
    period: int("period").notNull(),
    dueDate: timestamp("due_date").notNull(),
    dueAmount: decimal("due_amount", { precision: 12, scale: 2 }).notNull(),
    paidAmount: decimal("paid_amount", { precision: 12, scale: 2 }).notNull().default("0.00"),
    remainingAmount: decimal("remaining_amount", { precision: 12, scale: 2 }).notNull(),
    penaltyAmount: decimal("penalty_amount", { precision: 12, scale: 2 }).notNull().default("0.00"),
    status: mysqlEnum("status", ["pending", "partial", "paid", "overdue"])
      .notNull()
      .default("pending"),
    paidAt: timestamp("paid_at"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    contractPeriodIdx: uniqueIndex("installment_contract_period_idx").on(t.contractId, t.period),
    contractIdx: index("installment_contract_idx").on(t.contractId),
    dueDateIdx: index("installment_due_date_idx").on(t.dueDate),
    statusIdx: index("installment_status_idx").on(t.status),
  }),
);

// =========================
// Payments + LINE + audit
// =========================

export const payments = mysqlTable(
  "payments",
  {
    id: int("id").primaryKey().autoincrement(),
    contractId: int("contract_id").notNull(),
    customerId: int("customer_id").notNull(),
    installmentId: int("installment_id"),
    amount: decimal("amount", { precision: 12, scale: 2 }).notNull(),
    paymentDate: timestamp("payment_date").notNull(),
    slipImageUrl: text("slip_image_url"),
    slipStorageKey: varchar("slip_storage_key", { length: 255 }),
    verificationStatus: mysqlEnum("verification_status", [
      "pending",
      "verified",
      "rejected",
      "error",
    ])
      .notNull()
      .default("pending"),
    thunderTransRef: varchar("thunder_trans_ref", { length: 100 }),
    thunderResponse: text("thunder_response"),
    senderName: varchar("sender_name", { length: 255 }),
    receiverName: varchar("receiver_name", { length: 255 }),
    bankName: varchar("bank_name", { length: 100 }),
    period: int("period"),
    manualUpload: int("manual_upload").notNull().default(0),
    uploadedBy: varchar("uploaded_by", { length: 100 }),
    voided: int("voided").notNull().default(0),
    voidedAt: timestamp("voided_at"),
    voidedBy: varchar("voided_by", { length: 100 }),
    voidReason: text("void_reason"),
    notes: text("notes"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
  },
  (t) => ({
    contractIdx: index("payments_contract_idx").on(t.contractId),
    customerIdx: index("payments_customer_idx").on(t.customerId),
    installmentIdx: index("payments_installment_idx").on(t.installmentId),
    transRefIdx: index("payments_trans_ref_idx").on(t.thunderTransRef),
  }),
);

export const lineMessages = mysqlTable(
  "line_messages",
  {
    id: int("id").primaryKey().autoincrement(),
    customerId: int("customer_id"),
    lineUserId: varchar("line_user_id", { length: 64 }),
    messageType: varchar("message_type", { length: 32 }).notNull(),
    messageContent: text("message_content"),
    direction: mysqlEnum("direction", ["incoming", "outgoing"]).notNull(),
    status: varchar("status", { length: 32 }).notNull().default("sent"),
    lineMessageId: varchar("line_message_id", { length: 128 }),
    errorMessage: text("error_message"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
  },
  (t) => ({
    customerIdx: index("line_messages_customer_idx").on(t.customerId),
    lineUserIdx: index("line_messages_line_user_idx").on(t.lineUserId),
  }),
);

export const webhookLogs = mysqlTable("webhook_logs", {
  id: int("id").primaryKey().autoincrement(),
  eventType: varchar("event_type", { length: 64 }).notNull(),
  lineUserId: varchar("line_user_id", { length: 64 }),
  rawPayload: text("raw_payload"),
  processed: boolean("processed").notNull().default(false),
  createdAt: timestamp("created_at").notNull().defaultNow(),
});

export const auditLogs = mysqlTable(
  "audit_logs",
  {
    id: int("id").primaryKey().autoincrement(),
    contractId: int("contract_id"),
    customerId: int("customer_id"),
    paymentId: int("payment_id"),
    contractNumber: varchar("contract_number", { length: 50 }),
    actionType: mysqlEnum("action_type", [
      "payment_received",
      "payment_verified",
      "manual_upload",
      "edit_amount",
      "void_transaction",
      "status_override",
      "lock_override",
      "unlock_override",
      "date_adjustment",
      "penalty_adjustment",
      "discount_applied",
      "adjustment_created",
      "reminder_sent",
      "contract_created",
      "product_added",
      "installment_generated",
      "other",
    ]).notNull(),
    description: text("description"),
    previousValue: text("previous_value"),
    newValue: text("new_value"),
    remark: text("remark"),
    performedBy: varchar("performed_by", { length: 100 }),
    performedByUserId: int("performed_by_user_id"),
    ipAddress: varchar("ip_address", { length: 64 }),
    createdAt: timestamp("created_at").notNull().defaultNow(),
  },
  (t) => ({
    contractIdx: index("audit_contract_idx").on(t.contractId),
    customerIdx: index("audit_customer_idx").on(t.customerId),
    paymentIdx: index("audit_payment_idx").on(t.paymentId),
    actionIdx: index("audit_action_idx").on(t.actionType),
  }),
);

export const paymentAdjustments = mysqlTable(
  "payment_adjustments",
  {
    id: int("id").primaryKey().autoincrement(),
    contractId: int("contract_id").notNull(),
    customerId: int("customer_id").notNull(),
    paymentId: int("payment_id"),
    contractNumber: varchar("contract_number", { length: 50 }),
    period: int("period"),
    expectedAmount: decimal("expected_amount", { precision: 12, scale: 2 }).notNull(),
    actualAmount: decimal("actual_amount", { precision: 12, scale: 2 }).notNull(),
    difference: decimal("difference", { precision: 12, scale: 2 }).notNull(),
    carryForward: decimal("carry_forward", { precision: 12, scale: 2 }).notNull().default("0.00"),
    adjustmentType: mysqlEnum("adjustment_type", ["overpaid", "underpaid", "exact"]).notNull(),
    appliedToNextPeriod: boolean("applied_to_next_period").notNull().default(false),
    remark: text("remark"),
    createdBy: varchar("created_by", { length: 100 }),
    createdAt: timestamp("created_at").notNull().defaultNow(),
  },
  (t) => ({
    contractIdx: index("adjustments_contract_idx").on(t.contractId),
  }),
);

export const discounts = mysqlTable(
  "discounts",
  {
    id: int("id").primaryKey().autoincrement(),
    contractId: int("contract_id").notNull(),
    customerId: int("customer_id").notNull(),
    contractNumber: varchar("contract_number", { length: 50 }),
    discountType: mysqlEnum("discount_type", [
      "penalty",
      "closing",
      "installment",
      "other",
    ]).notNull(),
    originalAmount: decimal("original_amount", { precision: 12, scale: 2 }).notNull(),
    discountAmount: decimal("discount_amount", { precision: 12, scale: 2 }).notNull(),
    finalAmount: decimal("final_amount", { precision: 12, scale: 2 }).notNull(),
    reason: text("reason"),
    approvedBy: varchar("approved_by", { length: 100 }),
    status: mysqlEnum("discount_status", ["active", "revoked"]).notNull().default("active"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    contractIdx: index("discounts_contract_idx").on(t.contractId),
  }),
);

export const systemSettings = mysqlTable(
  "system_settings",
  {
    id: int("id").primaryKey().autoincrement(),
    settingKey: varchar("setting_key", { length: 64 }).notNull(),
    settingValue: text("setting_value"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().onUpdateNow(),
  },
  (t) => ({
    keyIdx: uniqueIndex("settings_key_idx").on(t.settingKey),
  }),
);

// =========================
// Inferred types
// =========================

export type Product = typeof products.$inferSelect;
export type NewProduct = typeof products.$inferInsert;
export type Customer = typeof customers.$inferSelect;
export type NewCustomer = typeof customers.$inferInsert;
export type Contract = typeof contracts.$inferSelect;
export type NewContract = typeof contracts.$inferInsert;
export type Installment = typeof installmentSchedule.$inferSelect;
export type NewInstallment = typeof installmentSchedule.$inferInsert;
export type Payment = typeof payments.$inferSelect;
export type NewPayment = typeof payments.$inferInsert;
export type AuditLog = typeof auditLogs.$inferSelect;
export type NewAuditLog = typeof auditLogs.$inferInsert;
