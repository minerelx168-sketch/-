import type { Request, Response } from "express";
import { parse } from "csv-parse/sync";
import { stringify } from "csv-stringify/sync";
import { readSessionFromReq } from "./auth.js";
import * as dbApi from "./db.js";

function requireAdmin(req: Request, res: Response): boolean {
  if (!readSessionFromReq(req)) {
    res.status(401).json({ error: "Unauthorized" });
    return false;
  }
  return true;
}

export async function exportCustomersCsv(req: Request, res: Response) {
  if (!requireAdmin(req, res)) return;
  const rows = await dbApi.listCustomers();
  const csv = stringify(rows, {
    header: true,
    columns: [
      "id",
      "fullName",
      "idCardNumber",
      "phoneNumber",
      "address",
      "lineUserId",
      "lineDisplayName",
      "botActive",
      "notes",
      "createdAt",
    ],
  });
  res.header("Content-Type", "text/csv; charset=utf-8");
  res.attachment("customers.csv");
  res.send(csv);
}

export async function exportContractsCsv(req: Request, res: Response) {
  if (!requireAdmin(req, res)) return;
  const rows = await dbApi.listContracts();
  const csv = stringify(rows, {
    header: true,
    columns: [
      "id",
      "contractNumber",
      "customerId",
      "productId",
      "devicePrice",
      "downPayment",
      "totalAmount",
      "installmentAmount",
      "totalPeriods",
      "penaltyRate",
      "unlockFee",
      "startDate",
      "status",
      "lockStatus",
      "loanStatus",
      "overdueDays",
      "createdAt",
    ],
  });
  res.header("Content-Type", "text/csv; charset=utf-8");
  res.attachment("contracts.csv");
  res.send(csv);
}

export async function exportProductsCsv(req: Request, res: Response) {
  if (!requireAdmin(req, res)) return;
  const rows = await dbApi.listProducts();
  const csv = stringify(rows, {
    header: true,
    columns: [
      "id",
      "sku",
      "name",
      "category",
      "costPrice",
      "sellingPrice",
      "stockQuantity",
      "isActive",
    ],
  });
  res.header("Content-Type", "text/csv; charset=utf-8");
  res.attachment("products.csv");
  res.send(csv);
}

/**
 * Import customers from CSV. Expects header row with at least:
 *   fullName, idCardNumber, phoneNumber, address, notes
 * Returns { created, skipped, errors }.
 */
export async function importCustomersCsv(req: Request, res: Response) {
  if (!requireAdmin(req, res)) return;
  const buf: Buffer[] = [];
  req.on("data", (chunk) => buf.push(chunk as Buffer));
  await new Promise<void>((resolve) => req.on("end", resolve));
  const raw = Buffer.concat(buf).toString("utf-8");
  let records: Record<string, string>[] = [];
  try {
    records = parse(raw, { columns: true, trim: true, skip_empty_lines: true });
  } catch (err) {
    res.status(400).json({ error: "Invalid CSV", details: (err as Error).message });
    return;
  }
  let created = 0;
  let skipped = 0;
  const errors: { row: number; message: string }[] = [];
  for (let i = 0; i < records.length; i++) {
    const r = records[i];
    if (!r.idCardNumber || r.idCardNumber.length !== 13) {
      errors.push({ row: i + 2, message: "Missing or invalid idCardNumber" });
      continue;
    }
    try {
      const existing = await dbApi.getCustomerByIdCard(r.idCardNumber);
      if (existing) {
        skipped++;
        continue;
      }
      await dbApi.createCustomer({
        fullName: r.fullName || "(no name)",
        idCardNumber: r.idCardNumber,
        phoneNumber: r.phoneNumber || undefined,
        address: r.address || undefined,
        notes: r.notes || undefined,
      });
      created++;
    } catch (err) {
      errors.push({ row: i + 2, message: (err as Error).message });
    }
  }
  res.json({ created, skipped, errors, totalRows: records.length });
}
