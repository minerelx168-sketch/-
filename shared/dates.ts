export const BANGKOK_TZ = "Asia/Bangkok";
const MS_PER_DAY = 24 * 60 * 60 * 1000;

/** Number of whole days between two dates (b - a), clamped at 0. */
export function daysBetween(a: Date, b: Date): number {
  const diff = b.getTime() - a.getTime();
  if (!Number.isFinite(diff) || diff <= 0) return 0;
  return Math.floor(diff / MS_PER_DAY);
}

/** Add N days to a date (returns new Date). */
export function addDays(d: Date, days: number): Date {
  const out = new Date(d.getTime());
  out.setUTCDate(out.getUTCDate() + days);
  return out;
}

/** Add N months to a date (returns new Date) — handles month-end correctly. */
export function addMonths(d: Date, months: number): Date {
  const out = new Date(d.getTime());
  const targetMonth = out.getUTCMonth() + months;
  out.setUTCMonth(targetMonth);
  return out;
}

/** Format a Date in Bangkok timezone using th-TH locale. */
export function formatThaiDate(d: Date | string | number): string {
  const date = d instanceof Date ? d : new Date(d);
  return date.toLocaleString("th-TH", {
    timeZone: BANGKOK_TZ,
    year: "numeric",
    month: "short",
    day: "2-digit",
  });
}

export function formatThaiDateTime(d: Date | string | number): string {
  const date = d instanceof Date ? d : new Date(d);
  return date.toLocaleString("th-TH", {
    timeZone: BANGKOK_TZ,
    year: "numeric",
    month: "short",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}
