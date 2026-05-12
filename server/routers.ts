import { z } from "zod";
import { TRPCError } from "@trpc/server";
import { router, publicProcedure, adminProcedure } from "./trpc.js";
import * as dbApi from "./db.js";
import {
  checkAdminCredentials,
  clearSessionCookie,
  setSessionCookie,
  signSession,
} from "./auth.js";
import { runDailySyncAndRemind, previewBroadcast, sendBroadcast } from "./escalationApi.js";

const moneyInput = z.union([z.number(), z.string()]).transform((v) => {
  const n = typeof v === "number" ? v : Number(v);
  if (!Number.isFinite(n)) throw new TRPCError({ code: "BAD_REQUEST", message: "Invalid amount" });
  return n;
});

const authRouter = router({
  me: publicProcedure.query(({ ctx }) => ctx.user),
  logout: publicProcedure.mutation(({ ctx }) => {
    clearSessionCookie(ctx.res);
    return { ok: true };
  }),
  adminLogin: publicProcedure
    .input(z.object({ username: z.string().min(1), password: z.string().min(1) }))
    .mutation(({ input, ctx }) => {
      if (!checkAdminCredentials(input.username, input.password)) {
        throw new TRPCError({ code: "UNAUTHORIZED", message: "Invalid credentials" });
      }
      const user = { username: input.username, role: "admin" as const };
      setSessionCookie(ctx.res, signSession(user));
      return user;
    }),
});

const productRouter = router({
  list: adminProcedure
    .input(z.object({ search: z.string().optional(), activeOnly: z.boolean().optional() }).optional())
    .query(({ input }) => dbApi.listProducts(input ?? {})),
  getById: adminProcedure.input(z.number().int()).query(({ input }) => dbApi.getProductById(input)),
  create: adminProcedure
    .input(
      z.object({
        sku: z.string().min(1).max(64),
        name: z.string().min(1).max(255),
        category: z.string().default("smartphone"),
        costPrice: moneyInput,
        sellingPrice: moneyInput,
        stockQuantity: z.number().int().nonnegative().default(0),
        imageUrl: z.string().optional(),
      }),
    )
    .mutation(({ input }) =>
      dbApi.createProduct({
        sku: input.sku,
        name: input.name,
        category: input.category,
        costPrice: input.costPrice.toFixed(2),
        sellingPrice: input.sellingPrice.toFixed(2),
        stockQuantity: input.stockQuantity,
        imageUrl: input.imageUrl,
      }),
    ),
  update: adminProcedure
    .input(
      z.object({
        id: z.number().int(),
        patch: z.object({
          sku: z.string().optional(),
          name: z.string().optional(),
          category: z.string().optional(),
          costPrice: moneyInput.optional(),
          sellingPrice: moneyInput.optional(),
          stockQuantity: z.number().int().optional(),
          imageUrl: z.string().optional(),
          isActive: z.boolean().optional(),
        }),
      }),
    )
    .mutation(({ input }) => {
      const p: Record<string, unknown> = { ...input.patch };
      if (typeof input.patch.costPrice === "number") p.costPrice = input.patch.costPrice.toFixed(2);
      if (typeof input.patch.sellingPrice === "number")
        p.sellingPrice = input.patch.sellingPrice.toFixed(2);
      return dbApi.updateProduct(input.id, p);
    }),
  delete: adminProcedure.input(z.number().int()).mutation(({ input }) => dbApi.deleteProduct(input)),
});

const customerRouter = router({
  list: adminProcedure
    .input(z.object({ search: z.string().optional() }).optional())
    .query(({ input }) => dbApi.listCustomers(input ?? {})),
  getById: adminProcedure.input(z.number().int()).query(({ input }) => dbApi.getCustomerById(input)),
  search: adminProcedure
    .input(z.object({ q: z.string().min(1) }))
    .query(({ input }) => dbApi.listCustomers({ search: input.q })),
  create: adminProcedure
    .input(
      z.object({
        fullName: z.string().min(1),
        idCardNumber: z.string().length(13),
        phoneNumber: z.string().optional(),
        address: z.string().optional(),
        notes: z.string().optional(),
      }),
    )
    .mutation(({ input }) => dbApi.createCustomer(input)),
  update: adminProcedure
    .input(
      z.object({
        id: z.number().int(),
        patch: z.object({
          fullName: z.string().optional(),
          phoneNumber: z.string().optional(),
          address: z.string().optional(),
          notes: z.string().optional(),
          botActive: z.number().int().min(0).max(1).optional(),
        }),
      }),
    )
    .mutation(({ input }) => dbApi.updateCustomer(input.id, input.patch)),
});

const contractRouter = router({
  list: adminProcedure
    .input(
      z
        .object({
          status: z.enum(["active", "overdue", "paid", "defaulted", "closed"]).optional(),
          customerId: z.number().int().optional(),
          search: z.string().optional(),
        })
        .optional(),
    )
    .query(({ input }) => dbApi.listContracts(input ?? {})),
  getById: adminProcedure.input(z.number().int()).query(({ input }) => dbApi.getContractById(input)),
  getByNumber: adminProcedure
    .input(z.string().min(1))
    .query(({ input }) => dbApi.getContractByNumber(input)),
  stats: adminProcedure.query(() => dbApi.contractStats()),
  create: adminProcedure
    .input(
      z.object({
        customerId: z.number().int(),
        productId: z.number().int(),
        devicePrice: moneyInput,
        downPayment: moneyInput.default(0),
        totalPeriods: z.number().int().min(1),
        installmentAmount: moneyInput.optional(),
        penaltyRate: moneyInput.optional(),
        unlockFee: moneyInput.optional(),
        startDate: z.coerce.date(),
        notes: z.string().optional(),
      }),
    )
    .mutation(({ input, ctx }) =>
      dbApi.createContract({
        ...input,
        performedBy: ctx.user.username,
      }),
    ),
  quote: adminProcedure
    .input(
      z.object({
        contractId: z.number().int(),
        type: z.enum(["installment", "full"]),
      }),
    )
    .query(({ input }) => dbApi.quoteContract(input.contractId, input.type)),
});

const installmentRouter = router({
  byContract: adminProcedure
    .input(z.number().int())
    .query(({ input }) => dbApi.listInstallmentsByContract(input)),
  currentDue: adminProcedure
    .input(z.number().int())
    .query(({ input }) => dbApi.getCurrentInstallment(input)),
  markPaid: adminProcedure
    .input(
      z.object({
        installmentId: z.number().int(),
        amount: moneyInput,
      }),
    )
    .mutation(({ input, ctx }) =>
      dbApi.markInstallmentPaid(input.installmentId, input.amount, ctx.user.username),
    ),
});

const paymentRouter = router({
  listByContract: adminProcedure
    .input(z.number().int())
    .query(({ input }) => dbApi.listPaymentsByContract(input)),
  listAll: adminProcedure.query(() => dbApi.listAllPayments()),
  manualRecord: adminProcedure
    .input(
      z.object({
        contractId: z.number().int(),
        amount: moneyInput,
        paymentDate: z.coerce.date(),
        period: z.number().int().optional(),
        notes: z.string().optional(),
      }),
    )
    .mutation(({ input, ctx }) =>
      dbApi.recordManualPayment({ ...input, uploadedBy: ctx.user.username }),
    ),
});

const broadcastFilterSchema = z.object({
  status: z.enum(["active", "overdue", "paid", "defaulted", "closed"]).optional(),
  minOverdueDays: z.number().int().nonnegative().optional(),
  customerIds: z.array(z.number().int()).optional(),
});

const scheduledRouter = router({
  runDailySync: adminProcedure
    .input(z.object({ dryRun: z.boolean().default(false) }).optional())
    .mutation(({ input }) => runDailySyncAndRemind(new Date(), { dryRun: input?.dryRun ?? false })),
});

const broadcastRouter = router({
  preview: adminProcedure
    .input(broadcastFilterSchema)
    .query(({ input }) => previewBroadcast(input)),
  send: adminProcedure
    .input(
      z.object({
        filter: broadcastFilterSchema,
        message: z.string().min(1).max(1000),
        dryRun: z.boolean().default(false),
      }),
    )
    .mutation(({ input, ctx }) =>
      sendBroadcast({
        filter: input.filter,
        message: input.message,
        performedBy: ctx.user.username,
        dryRun: input.dryRun,
      }),
    ),
});

const lineRouter = router({
  messages: adminProcedure
    .input(
      z
        .object({
          customerId: z.number().int().optional(),
          lineUserId: z.string().optional(),
          limit: z.number().int().max(500).optional(),
        })
        .optional(),
    )
    .query(({ input }) => dbApi.listLineMessages(input ?? {})),
  connections: adminProcedure.query(() => dbApi.listCustomersWithLine()),
  toggleBot: adminProcedure
    .input(z.object({ customerId: z.number().int(), botActive: z.number().int().min(0).max(1) }))
    .mutation(({ input }) => dbApi.updateCustomer(input.customerId, { botActive: input.botActive })),
});

const auditRouter = router({
  listByContract: adminProcedure
    .input(z.number().int())
    .query(({ input }) => dbApi.listAuditLogs({ contractId: input })),
  listAll: adminProcedure
    .input(z.object({ limit: z.number().int().max(500).optional() }).optional())
    .query(({ input }) => dbApi.listAuditLogs({ limit: input?.limit })),
});

export const appRouter = router({
  auth: authRouter,
  product: productRouter,
  customer: customerRouter,
  contract: contractRouter,
  installment: installmentRouter,
  payment: paymentRouter,
  audit: auditRouter,
  line: lineRouter,
  scheduled: scheduledRouter,
  broadcast: broadcastRouter,
});

export type AppRouter = typeof appRouter;
