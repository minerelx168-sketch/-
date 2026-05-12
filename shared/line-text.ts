/**
 * Parses incoming LINE text for known intents.
 * Phase 3 supports:
 *   - Contract binding: `^(CF-\d+)` (case-insensitive)
 *   - Balance query: keywords like "ยอด" / "ยอดค้าง" / "ดูยอด" / "balance"
 *   - Greeting: keywords like "สวัสดี" / "hi" / "hello"
 */
export type LineTextIntent =
  | { kind: "bind_contract"; contractNumber: string }
  | { kind: "balance" }
  | { kind: "greeting" }
  | { kind: "unknown"; raw: string };

const CONTRACT_RE = /\bCF-\d{4,}\b/i;
const BALANCE_RE = /ยอด|ดูยอด|balance|งวด/i;
const GREETING_RE = /สวัสดี|hi|hello|hey|ทักทาย/i;

export function parseLineText(text: string | null | undefined): LineTextIntent {
  const raw = (text ?? "").trim();
  if (!raw) return { kind: "unknown", raw: "" };

  const m = raw.match(CONTRACT_RE);
  if (m) {
    return { kind: "bind_contract", contractNumber: m[0].toUpperCase() };
  }
  if (BALANCE_RE.test(raw)) return { kind: "balance" };
  if (GREETING_RE.test(raw)) return { kind: "greeting" };
  return { kind: "unknown", raw };
}
