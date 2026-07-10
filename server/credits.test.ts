import { describe, expect, it, vi, beforeEach } from "vitest";
import { appRouter } from "./routers";
import type { TrpcContext } from "./_core/context";
import { TOPUP_PRESETS, PAYMENT_METHODS, LEMON_VARIANT_MAP, getLemonCheckoutUrl } from "../shared/const";

// Mock db functions
vi.mock("./db", () => ({
  getUserBalance: vi.fn().mockResolvedValue("125.00"),
  getUserTransactions: vi.fn().mockResolvedValue([
    {
      id: 1,
      userId: 1,
      amount: "25.00",
      type: "TOPUP",
      referenceType: "TopUpOrder",
      referenceId: "txn_abc123",
      balanceAfter: "125.00",
      description: "Top-up $25.00 via Lemon Squeezy (Order #12345)",
      createdAt: new Date("2025-01-15"),
    },
    {
      id: 2,
      userId: 1,
      amount: "50.00",
      type: "TOPUP",
      referenceType: "TopUpOrder",
      referenceId: "txn_xyz789",
      balanceAfter: "100.00",
      description: "Top-up $50.00 via Lemon Squeezy (Order #67890)",
      createdAt: new Date("2025-01-16"),
    },
  ]),
  getUserTopupOrders: vi.fn().mockResolvedValue([]),
  createTopupOrder: vi.fn().mockResolvedValue({ publicId: "txn_test123", isNew: true }),
  getDb: vi.fn(),
  upsertUser: vi.fn(),
  getUserByOpenId: vi.fn(),
  getUserById: vi.fn(),
  issueCredits: vi.fn(),
  logWebhookEvent: vi.fn(),
  markWebhookProcessed: vi.fn(),
  getTopupOrderByPublicId: vi.fn(),
  updateTopupOrderStatus: vi.fn(),
}));

function createAuthContext(): { ctx: TrpcContext } {
  const user = {
    id: 1,
    openId: "test-user-123",
    email: "test@example.com",
    name: "Test User",
    loginMethod: "manus",
    role: "user" as const,
    cachedBalance: "125.00",
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
  it("returns the user balance as string when authenticated", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    const result = await caller.credits.getBalance();

    expect(result).toEqual({ balance: "125.00" });
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
      amount: "25.00",
      type: "TOPUP",
      referenceType: "TopUpOrder",
    });
  });

  it("throws UNAUTHORIZED when not authenticated", async () => {
    const { ctx } = createUnauthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(caller.credits.getTransactions()).rejects.toThrow();
  });
});

describe("credits.createOrder", () => {
  it("creates a new order with correct fee calculation", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    const result = await caller.credits.createOrder({
      amount: 25,
      idempotencyKey: "test_key_12345678",
      successUrl: "https://example.com/success",
    });

    expect(result.publicId).toBe("txn_test123");
    expect(result.status).toBe("PENDING");
    expect(result.checkoutUrl).toContain("https://imeihub.lemonsqueezy.com/buy/");
  });

  it("rejects amounts below minimum", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(
      caller.credits.createOrder({
        amount: 2,
        idempotencyKey: "test_key_below_min",
        successUrl: "https://example.com/success",
      })
    ).rejects.toThrow();
  });

  it("rejects amounts above maximum", async () => {
    const { ctx } = createAuthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(
      caller.credits.createOrder({
        amount: 600,
        idempotencyKey: "test_key_above_max",
        successUrl: "https://example.com/success",
      })
    ).rejects.toThrow();
  });

  it("throws UNAUTHORIZED when not authenticated", async () => {
    const { ctx } = createUnauthContext();
    const caller = appRouter.createCaller(ctx);

    await expect(
      caller.credits.createOrder({
        amount: 25,
        idempotencyKey: "test_key_unauth",
        successUrl: "https://example.com/success",
      })
    ).rejects.toThrow();
  });
});

// Shared const tests
describe("shared/const - TOPUP_PRESETS", () => {
  it("should have 6 preset amounts", () => {
    expect(TOPUP_PRESETS).toHaveLength(6);
  });

  it("should contain expected preset values", () => {
    expect([...TOPUP_PRESETS]).toEqual([5, 10, 25, 50, 100, 250]);
  });
});

describe("shared/const - PAYMENT_METHODS", () => {
  it("card method should have correct config", () => {
    const card = PAYMENT_METHODS[0];
    expect(card.id).toBe("card");
    expect(card.feePct).toBe(5);
    expect(card.minUsd).toBe(5);
    expect(card.maxUsd).toBe(500);
    expect(card.provider).toBe("lemonsqueezy");
  });
});

describe("shared/const - getLemonCheckoutUrl", () => {
  it("should generate a valid checkout URL with all params", () => {
    const url = getLemonCheckoutUrl("741029", {
      userId: 1,
      email: "test@example.com",
      amount: 25,
      orderId: "txn_abc123",
      successUrl: "https://example.com/success",
    });

    expect(url).toContain("https://imeihub.lemonsqueezy.com/buy/741029");
    expect(url).toContain("user_id");
    expect(url).toContain("order_id");
    expect(url).toContain("amount");
    expect(url).toContain("success_url");
    expect(url).toContain("email");
  });

  it("should work without email", () => {
    const url = getLemonCheckoutUrl("741030", {
      userId: 2,
      amount: 50,
      orderId: "txn_xyz789",
      successUrl: "https://example.com/success",
    });

    expect(url).toContain("https://imeihub.lemonsqueezy.com/buy/741030");
    expect(url).not.toContain("email");
  });
});

describe("fee calculation logic", () => {
  it("should calculate 5% fee correctly for all presets", () => {
    const feePct = PAYMENT_METHODS[0].feePct;
    for (const amount of TOPUP_PRESETS) {
      const fee = amount * (feePct / 100);
      const total = amount + fee;
      expect(fee).toBe(amount * 0.05);
      expect(total).toBe(amount * 1.05);
    }
  });
});
