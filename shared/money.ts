/**
 * Safe number coercion — returns 0 for NaN / Infinity / null / undefined / non-numeric.
 * Accepts decimal-as-string (Drizzle decimal columns return string) or number.
 */
export function safeNum(v: unknown, fallback = 0): number {
  if (v === null || v === undefined) return fallback;
  const n = typeof v === "number" ? v : Number(v);
  if (!Number.isFinite(n)) return fallback;
  return n;
}

/** Round to 2 decimal places (THB cents) to avoid float drift. */
export function round2(n: number): number {
  return Math.round(safeNum(n) * 100) / 100;
}

/** Whole baht, no decimals (for display in some flex messages). */
export function thb(n: number): string {
  return round2(n).toLocaleString("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
