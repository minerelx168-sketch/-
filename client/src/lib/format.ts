export function formatTHB(n: number | string | null | undefined): string {
  const num = typeof n === "number" ? n : Number(n);
  if (!Number.isFinite(num)) return "0.00";
  return num.toLocaleString("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export function formatThaiDate(d: Date | string | null | undefined): string {
  if (!d) return "-";
  const date = d instanceof Date ? d : new Date(d);
  if (Number.isNaN(date.getTime())) return "-";
  return date.toLocaleString("th-TH", {
    timeZone: "Asia/Bangkok",
    year: "numeric",
    month: "short",
    day: "2-digit",
  });
}

export function formatThaiDateTime(d: Date | string | null | undefined): string {
  if (!d) return "-";
  const date = d instanceof Date ? d : new Date(d);
  if (Number.isNaN(date.getTime())) return "-";
  return date.toLocaleString("th-TH", {
    timeZone: "Asia/Bangkok",
    year: "numeric",
    month: "short",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export const STATUS_LABELS: Record<string, { label: string; color: string }> = {
  active: { label: "ปกติ", color: "bg-emerald-700 text-emerald-100" },
  overdue: { label: "ค้างชำระ", color: "bg-red-700 text-red-100" },
  paid: { label: "ปิดสัญญา", color: "bg-blue-700 text-blue-100" },
  defaulted: { label: "หนี้สูญ", color: "bg-zinc-700 text-zinc-100" },
  closed: { label: "ปิดแล้ว", color: "bg-slate-700 text-slate-100" },
  pending: { label: "รอชำระ", color: "bg-yellow-700 text-yellow-100" },
  partial: { label: "บางส่วน", color: "bg-orange-700 text-orange-100" },
};
