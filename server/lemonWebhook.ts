import { Request, Response } from "express";
import crypto from "crypto";
import { addCredits } from "./db";
import { users, transactions } from "../drizzle/schema";
import { eq } from "drizzle-orm";
import { getDb } from "./db";
import { CREDIT_PACKAGES } from "../shared/const";

// Build product ID → credit amount mapping from shared config
const PRODUCT_CREDITS: Record<string, number> = {};
for (const pkg of CREDIT_PACKAGES) {
  PRODUCT_CREDITS[pkg.productId] = pkg.amount;
  PRODUCT_CREDITS[pkg.variantId] = pkg.amount;
}

// Fallback: derive credits from order total (1:1 mapping in cents)
function getCreditsFromTotal(totalCents: number): number {
  return totalCents;
}

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

export async function handleLemonWebhook(req: Request, res: Response) {
  try {
    const signature = req.headers["x-signature"] as string || "";
    const eventName = req.headers["x-event-name"] as string;
    const rawBody = JSON.stringify(req.body);

    // Verify signature if webhook secret is configured
    const webhookSecret = process.env.LEMON_SQUEEZY_WEBHOOK_SECRET || "";
    if (webhookSecret && signature) {
      const isValid = verifyWebhookSignature(rawBody, signature, webhookSecret);
      if (!isValid) {
        console.error("[Webhook] Invalid signature");
        return res.status(401).json({ error: "Invalid signature" });
      }
    }

    console.log(`[Webhook] Received event: ${eventName}`);

    // Only process order_created events
    if (eventName !== "order_created") {
      return res.status(200).json({ received: true, skipped: true });
    }

    const data = req.body.data;
    const attributes = data?.attributes;
    const meta = req.body.meta;

    if (!attributes) {
      return res.status(400).json({ error: "Missing order attributes" });
    }

    // Extract order info
    const orderId = String(data.id || "");
    const orderTotal = attributes.total; // in cents
    const userEmail = attributes.user_email;
    const status = attributes.status;

    // Get product/variant info
    const firstOrderItem = attributes.first_order_item;
    const productId = String(firstOrderItem?.product_id || "");
    const variantId = String(firstOrderItem?.variant_id || "");

    // Get custom data (user_id passed during checkout)
    const customData = meta?.custom_data || {};
    const customUserId = customData.user_id;

    console.log(`[Webhook] Order ${orderId}: ${userEmail}, total: ${orderTotal}, product: ${productId}, variant: ${variantId}, status: ${status}`);

    if (status !== "paid") {
      console.log(`[Webhook] Order ${orderId} status is ${status}, skipping credit addition`);
      return res.status(200).json({ received: true, skipped: true });
    }

    // Idempotency check: skip if order already processed
    const db = await getDb();
    if (!db) {
      console.error("[Webhook] Database not available");
      return res.status(500).json({ error: "Database unavailable" });
    }

    const existingTx = await db.select()
      .from(transactions)
      .where(eq(transactions.lemonOrderId, orderId))
      .limit(1);

    if (existingTx.length > 0) {
      console.log(`[Webhook] Order ${orderId} already processed, skipping (idempotent)`);
      return res.status(200).json({ received: true, already_processed: true });
    }

    // Determine credit amount from product/variant mapping or fallback to total
    let creditAmount = PRODUCT_CREDITS[productId] || PRODUCT_CREDITS[variantId];
    if (!creditAmount) {
      creditAmount = getCreditsFromTotal(orderTotal);
    }

    // Find user by custom_data user_id or by email
    let userId: number | null = null;

    if (customUserId) {
      userId = parseInt(customUserId, 10);
    } else if (userEmail) {
      // Fallback: find user by email
      const userResult = await db.select().from(users).where(eq(users.email, userEmail)).limit(1);
      if (userResult.length > 0) {
        userId = userResult[0].id;
      }
    }

    if (!userId) {
      console.error(`[Webhook] Cannot find user for order ${orderId} (email: ${userEmail})`);
      return res.status(200).json({ received: true, error: "User not found" });
    }

    // Add credits
    const description = `Top-up $${(creditAmount / 100).toFixed(2)} via Lemon Squeezy (Order #${orderId})`;
    await addCredits(userId, creditAmount, description, orderId, productId);

    console.log(`[Webhook] Added ${creditAmount} credits to user ${userId} for order ${orderId}`);

    return res.status(200).json({ received: true, credits_added: creditAmount });
  } catch (error) {
    console.error("[Webhook] Error processing webhook:", error);
    return res.status(500).json({ error: "Internal server error" });
  }
}
