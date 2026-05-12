import { initTRPC, TRPCError } from "@trpc/server";
import type { CreateExpressContextOptions } from "@trpc/server/adapters/express";
import superjson from "superjson";
import { readSessionFromReq, type SessionUser } from "./auth.js";

export type Context = {
  user: SessionUser | null;
  req: CreateExpressContextOptions["req"];
  res: CreateExpressContextOptions["res"];
};

export function createContext({ req, res }: CreateExpressContextOptions): Context {
  return { user: readSessionFromReq(req), req, res };
}

const t = initTRPC.context<Context>().create({ transformer: superjson });

export const router = t.router;
export const publicProcedure = t.procedure;

const isAdmin = t.middleware(({ ctx, next }) => {
  if (!ctx.user || ctx.user.role !== "admin") {
    throw new TRPCError({ code: "UNAUTHORIZED" });
  }
  return next({ ctx: { ...ctx, user: ctx.user } });
});

export const adminProcedure = t.procedure.use(isAdmin);
