import { z } from "zod";
import { COOKIE_NAME, PAYMENT_METHODS, LEMON_VARIANT_MAP, getLemonCheckoutUrl } from "@shared/const";
import { getSessionCookieOptions } from "./_core/cookies";
import { systemRouter } from "./_core/systemRouter";
import { publicProcedure, protectedProcedure, router } from "./_core/trpc";
import { getUserBalance, getUserTransactions, getUserTopupOrders, createTopupOrder } from "./db";

export const appRouter = router({
  system: systemRouter,
  auth: router({
    me: publicProcedure.query(opts => opts.ctx.user),
    logout: publicProcedure.mutation(({ ctx }) => {
      const cookieOptions = getSessionCookieOptions(ctx.req);
      ctx.res.clearCookie(COOKIE_NAME, { ...cookieOptions, maxAge: -1 });
      return { success: true } as const;
    }),
  }),

  credits: router({
    getBalance: protectedProcedure.query(async ({ ctx }) => {
      const balance = await getUserBalance(ctx.user.id);
      return { balance };
    }),
    getTransactions: protectedProcedure.query(async ({ ctx }) => {
      const txns = await getUserTransactions(ctx.user.id);
      return { transactions: txns };
    }),
    getOrders: protectedProcedure.query(async ({ ctx }) => {
      const orders = await getUserTopupOrders(ctx.user.id);
      return { orders };
    }),
    createOrder: protectedProcedure
      .input(z.object({
        amount: z.number().min(5).max(500),
        idempotencyKey: z.string().min(8).max(128),
        successUrl: z.string().url(),
      }))
      .mutation(async ({ ctx, input }) => {
        const { amount, idempotencyKey, successUrl } = input;
        const method = PAYMENT_METHODS[0]; // card method
        const feePct = method.feePct;
        const chargeAmount = amount * (1 + feePct / 100);

        // Create order in DB
        const { publicId, isNew, existing } = await createTopupOrder({
          userId: ctx.user.id,
          amount: amount.toFixed(2),
          chargeAmount: chargeAmount.toFixed(2),
          feePct: feePct.toFixed(2),
          provider: "lemonsqueezy",
          idempotencyKey,
        });

        // If order already exists and is not PENDING, return it as-is
        if (!isNew && existing && existing.status !== "PENDING") {
          return {
            publicId: existing.publicId,
            status: existing.status,
            checkoutUrl: null,
          };
        }

        // Build checkout URL
        // For demo, use a single variant (we'll use the closest match)
        const variantId = LEMON_VARIANT_MAP[String(amount)] || LEMON_VARIANT_MAP["25"] || "741029";
        const checkoutUrl = getLemonCheckoutUrl(variantId, {
          userId: ctx.user.id,
          email: ctx.user.email || undefined,
          amount,
          orderId: publicId,
          successUrl,
        });

        return {
          publicId,
          status: "PENDING",
          checkoutUrl,
        };
      }),
  }),
});

export type AppRouter = typeof appRouter;
