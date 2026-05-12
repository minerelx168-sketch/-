import express from "express";
import cookieParser from "cookie-parser";
import cors from "cors";
import { createExpressMiddleware } from "@trpc/server/adapters/express";
import { appRouter } from "./routers.js";
import { createContext } from "./trpc.js";
import {
  exportContractsCsv,
  exportCustomersCsv,
  exportProductsCsv,
  importCustomersCsv,
} from "./csv.js";
import { handleLineWebhook } from "./lineWebhook.js";
import { handleDailySync, handleEscalationScan } from "./escalationApi.js";

const PORT = Number(process.env.PORT ?? 3000);
const app = express();

app.use(cors({ origin: true, credentials: true }));
app.use(cookieParser());

// Capture raw body for LINE webhook signature verification.
app.use(
  express.json({
    limit: "5mb",
    verify: (req, _res, buf) => {
      (req as express.Request & { rawBody?: string }).rawBody = buf.toString("utf-8");
    },
  }),
);

app.get("/api/health", (_req, res) => {
  res.json({ ok: true, ts: Date.now() });
});

// LINE webhook — MUST be before tRPC + return 200 immediately.
app.post("/api/line/webhook", handleLineWebhook);

// Scheduled endpoints (cron-friendly; secured with X-SCHEDULED-SECRET).
app.post("/api/scheduled/daily-sync-and-remind", handleDailySync);
app.post("/api/scheduled/escalation", handleEscalationScan);

app.use(
  "/api/trpc",
  createExpressMiddleware({ router: appRouter, createContext }),
);

// CSV import/export
app.get("/api/csv/customers", exportCustomersCsv);
app.get("/api/csv/contracts", exportContractsCsv);
app.get("/api/csv/products", exportProductsCsv);
app.post("/api/csv/customers/import", importCustomersCsv);

// Error handler
app.use(
  (
    err: Error & { status?: number },
    _req: express.Request,
    res: express.Response,
    _next: express.NextFunction,
  ) => {
    const status = err.status ?? 500;
    res.status(status).json({ error: err.message ?? "Internal error" });
  },
);

app.listen(PORT, () => {
  // eslint-disable-next-line no-console
  console.log(`[cfc] server listening on http://localhost:${PORT}`);
});
