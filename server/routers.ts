import { COOKIE_NAME } from "@shared/const";
import { getSessionCookieOptions } from "./_core/cookies";
import { systemRouter } from "./_core/systemRouter";
import { publicProcedure, protectedProcedure, router } from "./_core/trpc";
import { getUserBalance, getUserTransactions } from "./db";

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
  }),
});

export type AppRouter = typeof appRouter;
