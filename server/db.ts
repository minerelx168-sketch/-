import { eq, desc, sql, and } from "drizzle-orm";
import { drizzle } from "drizzle-orm/mysql2";
import { InsertUser, users, topupOrders, creditTransactions, webhookEvents } from "../drizzle/schema";
import { ENV } from './_core/env';
import { nanoid } from "nanoid";

let _db: ReturnType<typeof drizzle> | null = null;

export async function getDb() {
  if (!_db && process.env.DATABASE_URL) {
    try {
      _db = drizzle(process.env.DATABASE_URL);
    } catch (error) {
      console.warn("[Database] Failed to connect:", error);
      _db = null;
    }
  }
  return _db;
}

export async function upsertUser(user: InsertUser): Promise<void> {
  if (!user.openId) {
    throw new Error("User openId is required for upsert");
  }

  const db = await getDb();
  if (!db) {
    console.warn("[Database] Cannot upsert user: database not available");
    return;
  }

  try {
    const values: InsertUser = {
      openId: user.openId,
    };
    const updateSet: Record<string, unknown> = {};

    const textFields = ["name", "email", "loginMethod"] as const;
    type TextField = (typeof textFields)[number];

    const assignNullable = (field: TextField) => {
      const value = user[field];
      if (value === undefined) return;
      const normalized = value ?? null;
      values[field] = normalized;
      updateSet[field] = normalized;
    };

    textFields.forEach(assignNullable);

    if (user.lastSignedIn !== undefined) {
      values.lastSignedIn = user.lastSignedIn;
      updateSet.lastSignedIn = user.lastSignedIn;
    }
    if (user.role !== undefined) {
      values.role = user.role;
      updateSet.role = user.role;
    } else if (user.openId === ENV.ownerOpenId) {
      values.role = 'admin';
      updateSet.role = 'admin';
    }

    if (!values.lastSignedIn) {
      values.lastSignedIn = new Date();
    }

    if (Object.keys(updateSet).length === 0) {
      updateSet.lastSignedIn = new Date();
    }

    await db.insert(users).values(values).onDuplicateKeyUpdate({
      set: updateSet,
    });
  } catch (error) {
    console.error("[Database] Failed to upsert user:", error);
    throw error;
  }
}

export async function getUserByOpenId(openId: string) {
  const db = await getDb();
  if (!db) {
    console.warn("[Database] Cannot get user: database not available");
    return undefined;
  }

  const result = await db.select().from(users).where(eq(users.openId, openId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function getUserById(userId: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(users).where(eq(users.id, userId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

// ===== Top-Up Order Helpers =====

export async function createTopupOrder(params: {
  userId: number;
  amount: string; // USD credit amount
  chargeAmount: string; // USD charged amount (with fee)
  feePct: string;
  provider: string;
  idempotencyKey: string;
}) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");

  const publicId = `txn_${nanoid(16)}`;

  // Try insert, if idempotency key already exists, return existing order
  try {
    await db.insert(topupOrders).values({
      publicId,
      userId: params.userId,
      amount: params.amount,
      chargeAmount: params.chargeAmount,
      currency: "USD",
      status: "PENDING",
      provider: params.provider,
      idempotencyKey: params.idempotencyKey,
      feePct: params.feePct,
    });
    return { publicId, isNew: true };
  } catch (err: any) {
    // Duplicate idempotency key - return existing order
    if (err?.code === "ER_DUP_ENTRY" || err?.message?.includes("Duplicate")) {
      const existing = await db.select().from(topupOrders)
        .where(eq(topupOrders.idempotencyKey, params.idempotencyKey))
        .limit(1);
      if (existing.length > 0) {
        return { publicId: existing[0].publicId, isNew: false, existing: existing[0] };
      }
    }
    throw err;
  }
}

export async function getTopupOrderByPublicId(publicId: string) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(topupOrders).where(eq(topupOrders.publicId, publicId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function updateTopupOrderStatus(publicId: string, status: string, providerChargeId?: string) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");

  const updateData: Record<string, any> = { status };
  if (providerChargeId) updateData.providerChargeId = providerChargeId;
  if (status === "PAID") updateData.paidAt = new Date();
  if (status === "CREDITED") updateData.creditedAt = new Date();

  await db.update(topupOrders).set(updateData).where(eq(topupOrders.publicId, publicId));
}

// ===== Credit Helpers =====

export async function getUserBalance(userId: number): Promise<string> {
  const db = await getDb();
  if (!db) return "0.00";

  const result = await db.select({ cachedBalance: users.cachedBalance })
    .from(users).where(eq(users.id, userId)).limit(1);
  return result.length > 0 ? (result[0].cachedBalance ?? "0.00") : "0.00";
}

export async function issueCredits(params: {
  userId: number;
  amount: string; // positive decimal string
  type: "TOPUP" | "USAGE" | "REFUND" | "ADJUSTMENT" | "BONUS";
  referenceType?: string;
  referenceId?: string;
  description?: string;
}): Promise<{ newBalance: string }> {
  const db = await getDb();
  if (!db) throw new Error("Database not available");

  // Get current balance
  const userResult = await db.select({ cachedBalance: users.cachedBalance })
    .from(users).where(eq(users.id, params.userId)).limit(1);
  const currentBalance = parseFloat(userResult[0]?.cachedBalance ?? "0");
  const amountNum = parseFloat(params.amount);
  const newBalance = (currentBalance + amountNum).toFixed(2);

  // Insert transaction
  await db.insert(creditTransactions).values({
    userId: params.userId,
    amount: params.amount,
    type: params.type,
    referenceType: params.referenceType || null,
    referenceId: params.referenceId || null,
    balanceAfter: newBalance,
    description: params.description || null,
  });

  // Update cached balance
  await db.update(users).set({ cachedBalance: newBalance }).where(eq(users.id, params.userId));

  return { newBalance };
}

export async function getUserTransactions(userId: number, limit = 50) {
  const db = await getDb();
  if (!db) return [];

  return db.select()
    .from(creditTransactions)
    .where(eq(creditTransactions.userId, userId))
    .orderBy(desc(creditTransactions.createdAt))
    .limit(limit);
}

export async function getUserTopupOrders(userId: number, limit = 50) {
  const db = await getDb();
  if (!db) return [];

  return db.select()
    .from(topupOrders)
    .where(eq(topupOrders.userId, userId))
    .orderBy(desc(topupOrders.createdAt))
    .limit(limit);
}

// ===== Webhook Event Helpers =====

export async function logWebhookEvent(params: {
  provider: string;
  eventId: string;
  eventType?: string;
  signatureOk: boolean;
  rawBody: string;
}): Promise<{ id: number; alreadyProcessed: boolean }> {
  const db = await getDb();
  if (!db) throw new Error("Database not available");

  // INSERT IGNORE equivalent - try insert, catch duplicate
  try {
    const result = await db.insert(webhookEvents).values({
      provider: params.provider,
      eventId: params.eventId,
      eventType: params.eventType || null,
      signatureOk: params.signatureOk,
      rawBody: params.rawBody,
    });
  } catch (err: any) {
    // Duplicate - that's fine, just check if already processed
  }

  // Check if already processed
  const existing = await db.select()
    .from(webhookEvents)
    .where(and(
      eq(webhookEvents.provider, params.provider),
      eq(webhookEvents.eventId, params.eventId)
    ))
    .limit(1);

  if (existing.length > 0) {
    return { id: existing[0].id, alreadyProcessed: existing[0].processed ?? false };
  }

  return { id: 0, alreadyProcessed: false };
}

export async function markWebhookProcessed(provider: string, eventId: string, error?: string) {
  const db = await getDb();
  if (!db) return;

  if (error) {
    await db.update(webhookEvents)
      .set({ processingError: error })
      .where(and(
        eq(webhookEvents.provider, provider),
        eq(webhookEvents.eventId, eventId)
      ));
  } else {
    await db.update(webhookEvents)
      .set({ processed: true, processedAt: new Date() })
      .where(and(
        eq(webhookEvents.provider, provider),
        eq(webhookEvents.eventId, eventId)
      ));
  }
}
