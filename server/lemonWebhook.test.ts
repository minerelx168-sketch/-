import { describe, expect, it, vi, beforeEach } from "vitest";
import { handleLemonWebhook } from "./lemonWebhook";
import type { Request, Response } from "express";

// Mock db module with new function signatures
vi.mock("./db", () => ({
  getDb: vi.fn().mockResolvedValue({
    select: vi.fn().mockReturnValue({
      from: vi.fn().mockReturnValue({
        where: vi.fn().mockReturnValue({
          limit: vi.fn().mockResolvedValue([{ id: 1 }]),
        }),
      }),
    }),
  }),
  getUserByOpenId: vi.fn(),
  getUserById: vi.fn().mockResolvedValue({ id: 1, openId: "test", cachedBalance: "100.00" }),
  issueCredits: vi.fn().mockResolvedValue({ newBalance: "125.00" }),
  logWebhookEvent: vi.fn().mockResolvedValue({ id: 1, alreadyProcessed: false }),
  markWebhookProcessed: vi.fn().mockResolvedValue(undefined),
  getTopupOrderByPublicId: vi.fn().mockResolvedValue(null),
  updateTopupOrderStatus: vi.fn().mockResolvedValue(undefined),
  getUserBalance: vi.fn(),
  getUserTransactions: vi.fn(),
  getUserTopupOrders: vi.fn(),
  createTopupOrder: vi.fn(),
  upsertUser: vi.fn(),
}));

function createMockReq(body: any, headers: Record<string, string> = {}): Request {
  return {
    body,
    headers: {
      "x-event-name": "order_created",
      "x-signature": "",
      ...headers,
    },
  } as unknown as Request;
}

function createMockRes() {
  const res = {
    status: vi.fn().mockReturnThis(),
    json: vi.fn().mockReturnThis(),
  } as unknown as Response;
  return res;
}

describe("handleLemonWebhook", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("returns 200 and skips non-order_created events", async () => {
    const { logWebhookEvent, markWebhookProcessed } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: false });

    const req = createMockReq({}, { "x-event-name": "subscription_created" });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(res.json).toHaveBeenCalledWith({ received: true, skipped: true });
    expect(markWebhookProcessed).toHaveBeenCalled();
  });

  it("returns 400 when order attributes are missing", async () => {
    const { logWebhookEvent } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: false });

    const req = createMockReq({ data: { id: "order_123" } });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(400);
    expect(res.json).toHaveBeenCalledWith({ error: "Missing order attributes" });
  });

  it("skips unpaid orders", async () => {
    const { logWebhookEvent, markWebhookProcessed } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: false });

    const req = createMockReq({
      data: {
        id: "order_123",
        attributes: {
          total: 2500,
          user_email: "test@example.com",
          status: "pending",
          first_order_item: { product_id: "1210408", variant_id: "741029" },
        },
      },
      meta: { custom_data: { user_id: "1" } },
    });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(res.json).toHaveBeenCalledWith({ received: true, skipped: true });
    expect(markWebhookProcessed).toHaveBeenCalled();
  });

  it("processes paid order and issues credits", async () => {
    const { logWebhookEvent, issueCredits, markWebhookProcessed, updateTopupOrderStatus } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: false });
    (issueCredits as any).mockResolvedValue({ newBalance: "125.00" });

    const req = createMockReq({
      data: {
        id: "order_789",
        attributes: {
          total: 2500,
          user_email: "test@example.com",
          status: "paid",
          first_order_item: { product_id: "1210408", variant_id: "741029" },
        },
      },
      meta: { custom_data: { user_id: "1", order_id: "txn_abc123", amount: "25" } },
    });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(issueCredits).toHaveBeenCalledWith(expect.objectContaining({
      userId: 1,
      amount: "25.00",
      type: "TOPUP",
      referenceType: "TopUpOrder",
      referenceId: "txn_abc123",
    }));
    expect(updateTopupOrderStatus).toHaveBeenCalledWith("txn_abc123", "PAID", "order_789");
    expect(updateTopupOrderStatus).toHaveBeenCalledWith("txn_abc123", "CREDITED", "order_789");
    expect(markWebhookProcessed).toHaveBeenCalledWith("lemonsqueezy", expect.stringContaining("order_created_order_789"));
  });

  it("returns 401 for invalid signature when secret is configured", async () => {
    const { logWebhookEvent } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: false });

    process.env.LEMON_SQUEEZY_WEBHOOK_SECRET = "test-secret";

    const req = createMockReq(
      { data: { id: "1", attributes: { status: "paid", total: 100 } }, meta: {} },
      { "x-signature": "invalid-signature" }
    );
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(401);
    expect(res.json).toHaveBeenCalledWith({ error: "Invalid signature" });

    delete process.env.LEMON_SQUEEZY_WEBHOOK_SECRET;
  });

  it("returns 200 with already_processed when event was already handled", async () => {
    const { logWebhookEvent } = await import("./db");
    (logWebhookEvent as any).mockResolvedValue({ id: 1, alreadyProcessed: true });

    const req = createMockReq({
      data: { id: "order_duplicate", attributes: { status: "paid", total: 2500 } },
      meta: { custom_data: { user_id: "1" } },
    });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(res.json).toHaveBeenCalledWith({ received: true, already_processed: true });
  });
});
