import { eq, desc } from "drizzle-orm";
import { drizzle } from "drizzle-orm/mysql2";
import { InsertUser, users, credits, transactions } from "../drizzle/schema";
import { ENV } from './_core/env';

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

// ===== Credit Helpers =====

export async function getUserBalance(userId: number): Promise<number> {
  const db = await getDb();
  if (!db) return 0;

  const result = await db.select().from(credits).where(eq(credits.userId, userId)).limit(1);
  return result.length > 0 ? result[0].balance : 0;
}

export async function addCredits(userId: number, amount: number, description: string, lemonOrderId?: string, lemonProductId?: string): Promise<void> {
  const db = await getDb();
  if (!db) throw new Error("Database not available");

  // Upsert credits balance
  const existing = await db.select().from(credits).where(eq(credits.userId, userId)).limit(1);
  if (existing.length > 0) {
    await db.update(credits)
      .set({ balance: existing[0].balance + amount })
      .where(eq(credits.userId, userId));
  } else {
    await db.insert(credits).values({ userId, balance: amount });
  }

  // Record transaction
  await db.insert(transactions).values({
    userId,
    amount,
    type: "topup",
    status: "completed",
    description,
    lemonOrderId: lemonOrderId || null,
    lemonProductId: lemonProductId || null,
  });
}

export async function getUserTransactions(userId: number, limit = 50) {
  const db = await getDb();
  if (!db) return [];

  return db.select()
    .from(transactions)
    .where(eq(transactions.userId, userId))
    .orderBy(desc(transactions.createdAt))
    .limit(limit);
}
