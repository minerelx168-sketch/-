import { fetchWithTimeout, type Fetcher } from "./lineApi.js";

/**
 * Thunder API v2 — slip verification.
 * Send multipart/form-data with the raw image; the API replies with parsed slip data
 * plus a duplicate flag (transRef seen before).
 */
export type ThunderVerifyResult = {
  data: {
    valid: boolean;
    isDuplicate?: boolean;
    transRef?: string;
    amount?: number;
    date?: string;
    sender?: { name?: string; account?: string; bank?: string };
    receiver?: { name?: string; account?: string; bank?: string };
  } | null;
  raw: unknown;
};

const THUNDER_URL = "https://api.thunder.in.th/v2/verify";

export async function verifySlip(
  imageBuffer: Buffer,
  fetcher: Fetcher = globalThis.fetch,
): Promise<ThunderVerifyResult> {
  const apiKey = process.env.THUNDER_API_KEY;
  if (!apiKey) throw new Error("THUNDER_API_KEY is not set");

  const form = new FormData();
  // Node's FormData accepts Blob; Buffer can be converted directly.
  form.append("file", new Blob([new Uint8Array(imageBuffer)]), "slip.jpg");

  const res = await fetchWithTimeout(
    THUNDER_URL,
    {
      method: "POST",
      headers: { Authorization: `Bearer ${apiKey}` },
      body: form,
      timeoutMs: 20_000,
    },
    fetcher,
  );

  if (!res.ok) {
    const body = await res.text().catch(() => "");
    return { data: null, raw: { status: res.status, body } };
  }

  const raw = (await res.json().catch(() => null)) as Record<string, unknown> | null;
  return { data: extractV2(raw), raw };
}

/** Normalize Thunder v2 response shape (their v2 wraps under `data`, v1 was flat). */
function extractV2(raw: Record<string, unknown> | null): ThunderVerifyResult["data"] {
  if (!raw || typeof raw !== "object") return null;
  const data = (raw.data as Record<string, unknown>) ?? raw;
  const valid = Boolean(data?.valid ?? data?.success);
  const isDuplicate = Boolean(data?.isDuplicate ?? data?.duplicate);
  const transRef = (data?.transRef ?? data?.transactionRef ?? data?.ref) as string | undefined;
  const amount = data?.amount !== undefined ? Number(data.amount) : undefined;
  const date = (data?.date ?? data?.transactionDate) as string | undefined;
  const sender = (data?.sender ?? {}) as Record<string, unknown>;
  const receiver = (data?.receiver ?? {}) as Record<string, unknown>;
  return {
    valid,
    isDuplicate,
    transRef,
    amount: Number.isFinite(amount) ? amount : undefined,
    date,
    sender: {
      name: sender.name as string | undefined,
      account: sender.account as string | undefined,
      bank: sender.bank as string | undefined,
    },
    receiver: {
      name: receiver.name as string | undefined,
      account: receiver.account as string | undefined,
      bank: receiver.bank as string | undefined,
    },
  };
}

export type SlipStatus = "valid" | "duplicate" | "invalid" | "error";

export function determineSlipStatus(result: ThunderVerifyResult): SlipStatus {
  if (!result?.data) return "error";
  if (result.data.isDuplicate === true) return "duplicate";
  if (result.data.valid === true) return "valid";
  if (result.data.valid === false) return "invalid";
  return "error";
}
