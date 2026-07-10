import { describe, expect, it, vi, beforeEach } from "vitest";
import { appRouter } from "./routers";
import type { TrpcContext } from "./_core/context";

// Mock db functions
vi.mock("./db", () => ({
  getUserBalance: vi.fn().mockResolvedValue(5000),
  getUserTransactions: vi.fn().mockResolvedValue([
    {
      id: 1,
      userId: 1,
      amount: 2500,
      type: "topup",
      status: "completed",
      description: "Top-up via Lemon Squeezy (Order #123)",
      lemonOrderId: "123",
      lemonProductId: "1210408",
      createdAt: new Date("2025-01-15"),
    },
    {
      id: 2,
      userId: 1,
      amount: 5000,
      type: "topup",
      status: "completed",
      description: "Top-up via Lemon Squeezy (Order #456)",
      lemonOrderId: "456",
      lemonProductId: "1210419",
      createdAt: new Date("2025-01-16"),
    },
  ]),
  getDb: vi.fn(),
  upsertUser: vi.fn(),
  getUserByOpenId: vi.fn(),
  addCredits: vi.fn(),
}));

function createAuthContext(): { ctx: TrpcContext } {
  const user = {
    id: 1,
    openId: "test-user-123",
    email: "test@example.com",
    name: "Test User",
    loginMethod: "manus",
    role: "user" as const,
    createdAt: new Date(),
    updatedAt: new Date(),
    lastSignedIn: new Date(),
  };

  const ctx: TrpcContext = {
    user,
    req: {
      protocol: "https",
      headers: {},
    } as TrpcContext["req"],
    res: {
      clearCookie: vi.fn(),
    } as unknown as TrpcContext["res"],
  };

  return { ctx };
}

function createUnauthContext(): { ctx: TrpcContext } {
  const ctx: TrpcContext = {
    user: null,
    req: {
      protocol: "https",
      headers: {},
    } as TrpcContext["req"],
    res: {
      clearCookie: vi.fn(),
    } as unknown as TrpcContext["res"],
  };

  return { ctx };
}

describe("credits.getBalance", () => {
  it("returns the user balance when authenticated", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    const result = await caller.credits.getBalance();

    expect(result).toEqual({ balance: 5000 });
  });

  it("throws UNAUTHORIZED when not authenticated", async () => {
    const { ctx } = createUnauthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(caller.credits.getBalance()).rejects.toThrow();
  });
});

describe("credits.getTransactions", () => {
  it("returns transaction history when authenticated", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    const result = await caller.credits.getTransactions();

    expect(result.transactions).toHaveLength(2);
    expect(result.transactions[0]).toMatchObject({
      id: 1,
      userId: 1,
      amount: 2500,
      type: "topup",
      status: "completed",
    });
  });

  it("throws UNAUTHORIZED when not authenticated", async () => {
    const { ctx } = createUnauthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(caller.credits.getTransactions()).rejects.toThrow();
  });
});
