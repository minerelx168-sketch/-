import { Request, Response } from "express";
import crypto from "crypto";
import {
  getDb,
  getUserByOpenId,
  getUserById,
  issueCredits,
  logWebhookEvent,
  markWebhookProcessed,
  getTopupOrderByPublicId,
  updateTopupOrderStatus,
} from "./db";
import { users } from "../drizzle/schema";
import { eq } from "drizzle-orm";
import { LEMON_VARIANT_MAP } from "../shared/const";

/**
 * Verify Lemon Squeezy webhook signature (HMAC-SHA256).
 */
function verifyWebhookSignature(rawBody: string, signature: string, secret: string): boolean {
  if (!secret || !signature) return true; // Skip verification if no secret configured
  const hmac = crypto.createHmac("sha256", secret);
  const digest = hmac.update(rawBody).digest("hex");
  try {
    return crypto.timingSafeEqual(Buffer.from(signature), Buffer.from(digest));
  } catch {
    return false;
  }
}

/**
 * Production-grade Lemon Squeezy webhook handler.
 * Pattern: logWebhookEvent → alreadyProcessed check → process → markWebhookProcessed → return 5xx on failure
 */
export async function handleLemonWebhook(req: Request, res: Response) {
  const signature = (req.headers["x-signature"] as string) || "";
  const eventName = (req.headers["x-event-name"] as string) || "";
  const rawBody = (req as any).rawBody || JSON.stringify(req.body);

  // Generate a unique event ID from the payload
  const data = req.body?.data;
  const eventId = data?.id ? `${eventName}_${data.id}` : `${eventName}_${Date.now()}`;

  // Verify signature
  const webhookSecret = process.env.LEMON_SQUEEZY_WEBHOOK_SECRET || "";
  const signatureOk = verifyWebhookSignature(rawBody, signature, webhookSecret);

  // Step 1: Audit log - INSERT IGNORE (always log, even if invalid signature)
  let webhookLog: { id: number; alreadyProcessed: boolean };
  try {
    webhookLog = await logWebhookEvent({
      provider: "lemonsqueezy",
      eventId,
      eventType: eventName,
      signatureOk,
      rawBody,
    });
  } catch (err) {
    console.error("[Webhook] Failed to log event:", err);
    return res.status(500).json({ error: "Failed to log webhook event" });
  }

  // Reject invalid signature after logging
  if (!signatureOk) {
    console.error(`[Webhook] Invalid signature for event ${eventId}`);
    return res.status(401).json({ error: "Invalid signature" });
  }

  // Step 2: Idempotency check
  if (webhookLog.alreadyProcessed) {
    console.log(`[Webhook] Event ${eventId} already processed (idempotent)`);
    return res.status(200).json({ received: true, already_processed: true });
  }

  // Only process order_created events (payment completed)
  if (eventName !== "order_created") {
    // Mark as processed (non-actionable event)
    await markWebhookProcessed("lemonsqueezy", eventId);
    return res.status(200).json({ received: true, skipped: true });
  }

  // Step 3: Process the order
  try {
    const attributes = data?.attributes;
    const meta = req.body?.meta;

    if (!attributes) {
      await markWebhookProcessed("lemonsqueezy", eventId, "Missing order attributes");
      return res.status(400).json({ error: "Missing order attributes" });
    }

    const orderId = String(data.id || "");
    const orderStatus = attributes.status;
    const userEmail = attributes.user_email;

    // Get custom data (passed during checkout)
    const customData = meta?.custom_data || {};
    const customUserId = customData.user_id;
    const customOrderId = customData.order_id; // our topup_orders.publicId
    const customAmount = customData.amount; // credit amount in USD

    console.log(`[Webhook] Processing order ${orderId}: email=${userEmail}, status=${orderStatus}, customOrderId=${customOrderId}`);

    // Only process paid orders
    if (orderStatus !== "paid") {
      await markWebhookProcessed("lemonsqueezy", eventId);
      console.log(`[Webhook] Order ${orderId} status=${orderStatus}, skipping`);
      return res.status(200).json({ received: true, skipped: true });
    }

    // Determine credit amount
    let creditAmount: number;
    if (customAmount) {
      creditAmount = parseFloat(customAmount);
    } else {
      // Fallback: derive from order total (cents → dollars)
      const totalCents = attributes.total || 0;
      creditAmount = totalCents / 100;
    }

    if (!creditAmount || creditAmount <= 0) {
      await markWebhookProcessed("lemonsqueezy", eventId, "Invalid credit amount");
      return res.status(200).json({ received: true, error: "Invalid credit amount" });
    }

    // Validate: if we have a linked order, verify the credit amount matches
    if (customOrderId) {
      const linkedOrder = await getTopupOrderByPublicId(customOrderId);
      if (linkedOrder) {
        const orderAmount = parseFloat(linkedOrder.amount);
        if (Math.abs(orderAmount - creditAmount) > 0.01) {
          const errMsg = `Amount mismatch: order=${orderAmount}, webhook=${creditAmount}`;
          console.error(`[Webhook] ${errMsg}`);
          await markWebhookProcessed("lemonsqueezy", eventId, errMsg);
          return res.status(200).json({ received: true, error: "Amount mismatch" });
        }
      }
    }

    // Find user
    let userId: number | null = null;
    const db = await getDb();
    if (!db) {
      console.error("[Webhook] Database not available");
      return res.status(500).json({ error: "Database unavailable" });
    }

    if (customUserId) {
      userId = parseInt(customUserId, 10);
      // Verify user exists
      const user = await getUserById(userId);
      if (!user) userId = null;
    }

    if (!userId && userEmail) {
      // Fallback: find user by email
      const userResult = await db.select().from(users).where(eq(users.email, userEmail)).limit(1);
      if (userResult.length > 0) {
        userId = userResult[0].id;
      }
    }

    if (!userId) {
      const errMsg = `User not found for order ${orderId} (email: ${userEmail}, customUserId: ${customUserId})`;
      console.error(`[Webhook] ${errMsg}`);
      await markWebhookProcessed("lemonsqueezy", eventId, errMsg);
      // Return 200 to prevent retries - user genuinely not found
      return res.status(200).json({ received: true, error: "User not found" });
    }

    // Update topup_orders status if we have a linked order
    if (customOrderId) {
      await updateTopupOrderStatus(customOrderId, "PAID", orderId);
    }

    // Issue credits
    const { newBalance } = await issueCredits({
      userId,
      amount: creditAmount.toFixed(2),
      type: "TOPUP",
      referenceType: "TopUpOrder",
      referenceId: customOrderId || orderId,
      description: `Top-up $${creditAmount.toFixed(2)} via Lemon Squeezy (Order #${orderId})`,
    });

    // Update topup_orders status to CREDITED
    if (customOrderId) {
      await updateTopupOrderStatus(customOrderId, "CREDITED", orderId);
    }

    // Step 4: Mark webhook as processed
    await markWebhookProcessed("lemonsqueezy", eventId);

    console.log(`[Webhook] ✓ Credited $${creditAmount.toFixed(2)} to user ${userId}, new balance: $${newBalance}`);
    return res.status(200).json({ received: true, credits_added: creditAmount, new_balance: newBalance });
  } catch (error: any) {
    // Step 5: On failure, mark error but return 5xx to trigger retry
    console.error("[Webhook] Error processing:", error);
    await markWebhookProcessed("lemonsqueezy", eventId, error?.message || "Unknown error");
    return res.status(500).json({ error: "Internal server error" });
  }
}
