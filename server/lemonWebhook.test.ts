import { describe, expect, it, vi, beforeEach } from "vitest";
import { handleLemonWebhook } from "./lemonWebhook";
import type { Request, Response } from "express";

// Mock db module
vi.mock("./db", () => ({
  getDb: vi.fn().mockResolvedValue({
    select: vi.fn().mockReturnValue({
      from: vi.fn().mockReturnValue({
        where: vi.fn().mockReturnValue({
          limit: vi.fn().mockResolvedValue([]),
        }),
      }),
    }),
  }),
  addCredits: vi.fn().mockResolvedValue(undefined),
  getUserBalance: vi.fn(),
  getUserTransactions: vi.fn(),
  upsertUser: vi.fn(),
  getUserByOpenId: vi.fn(),
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
    const req = createMockReq({}, { "x-event-name": "subscription_created" });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(res.json).toHaveBeenCalledWith({ received: true, skipped: true });
  });

  it("returns 400 when order attributes are missing", async () => {
    const req = createMockReq({ data: {} });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(400);
    expect(res.json).toHaveBeenCalledWith({ error: "Missing order attributes" });
  });

  it("skips unpaid orders", async () => {
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
  });

  it("processes paid order and adds credits", async () => {
    const { addCredits } = await import("./db");

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
      meta: { custom_data: { user_id: "1" } },
    });
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(200);
    expect(addCredits).toHaveBeenCalledWith(
      1,
      2500,
      expect.stringContaining("Order #order_789"),
      "order_789",
      "1210408"
    );
  });

  it("returns 401 for invalid signature when secret is configured", async () => {
    process.env.LEMON_SQUEEZY_WEBHOOK_SECRET = "test-secret";

    const req = createMockReq(
      { data: { id: "1", attributes: { status: "paid", total: 100 } }, meta: {} },
      { "x-signature": "invalid-signature" }
    );
    const res = createMockRes();

    await handleLemonWebhook(req, res);

    expect(res.status).toHaveBeenCalledWith(401);

    delete process.env.LEMON_SQUEEZY_WEBHOOK_SECRET;
  });
});
