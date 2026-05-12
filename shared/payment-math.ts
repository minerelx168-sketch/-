import { safeNum, round2 } from "./money.js";
import { addMonths, daysBetween } from "./dates.js";

export type ContractLike = {
  devicePrice: number | string;
  installmentAmount: number | string;
  penaltyRate: number | string;
  unlockFee: number | string;
  lockStatus?: "locked" | "unlocked";
};

export type InstallmentLike = {
  period: number;
  dueDate: Date | string;
  dueAmount: number | string;
  paidAmount: number | string;
  remainingAmount: number | string;
};

export type PaymentType = "installment" | "full";

export type PaymentCalculation = {
  total: number;
  installmentDue: number;
  penalty: number;
  unlockFee: number;
  devicePrice: number;
  overdueDays: number;
  breakdown: string;
};

/**
 * Pricing engine.
 * - installment: installmentDue + penalty
 * - full       : devicePrice + installmentDue + penalty + unlockFee (if locked)
 */
export function calculatePaymentAmount(
  contract: ContractLike,
  currentInstallment: InstallmentLike | null,
  type: PaymentType,
  now: Date = new Date(),
): PaymentCalculation {
  const penaltyRate = safeNum(contract.penaltyRate);
  const unlockFee = safeNum(contract.unlockFee);
  const devicePrice = safeNum(contract.devicePrice);
  const isLocked = contract.lockStatus === "locked";

  let installmentDue = 0;
  let overdueDays = 0;
  let penalty = 0;

  if (currentInstallment) {
    installmentDue = Math.max(0, safeNum(currentInstallment.remainingAmount));
    const due = currentInstallment.dueDate instanceof Date
      ? currentInstallment.dueDate
      : new Date(currentInstallment.dueDate);
    overdueDays = daysBetween(due, now);
    penalty = round2(overdueDays * penaltyRate);
  }

  if (type === "installment") {
    const total = round2(installmentDue + penalty);
    return {
      total,
      installmentDue: round2(installmentDue),
      penalty,
      unlockFee: 0,
      devicePrice: 0,
      overdueDays,
      breakdown: `ค่างวด ${round2(installmentDue)} + ค่าปรับ ${penalty} = ${total} บาท`,
    };
  }

  const fee = isLocked ? unlockFee : 0;
  const total = round2(devicePrice + installmentDue + penalty + fee);
  const parts = [
    `ราคาเครื่อง ${devicePrice}`,
    `ค่างวด ${round2(installmentDue)}`,
    `ค่าปรับ ${penalty}`,
  ];
  if (fee > 0) parts.push(`ค่าปลดล็อค ${fee}`);
  return {
    total,
    installmentDue: round2(installmentDue),
    penalty,
    unlockFee: fee,
    devicePrice,
    overdueDays,
    breakdown: `${parts.join(" + ")} = ${total} บาท`,
  };
}

export type MatchResult =
  | "exact_installment"
  | "exact_full"
  | "overpaid"
  | "underpaid"
  | "unmatched";

export type AmountMatchResult = {
  matchResult: MatchResult;
  difference: number;
  matchedType: "installment" | "full" | "none";
  paymentUrl?: string;
};

const DEFAULT_TOLERANCE = 1; // ±1 THB

/**
 * Match a transfer amount against installment / full totals.
 * Priority: exact_installment → exact_full → overpaid → underpaid → unmatched.
 */
export function matchAmount(
  transferAmount: number,
  installmentTotal: number,
  fullTotal: number,
  contractNumber: string,
  opts: { tolerance?: number; baseUrl?: string } = {},
): AmountMatchResult {
  const t = safeNum(transferAmount);
  const inst = safeNum(installmentTotal);
  const full = safeNum(fullTotal);
  const tol = opts.tolerance ?? DEFAULT_TOLERANCE;

  if (Math.abs(t - inst) <= tol) {
    return { matchResult: "exact_installment", difference: 0, matchedType: "installment" };
  }
  if (Math.abs(t - full) <= tol) {
    return { matchResult: "exact_full", difference: 0, matchedType: "full" };
  }
  // Overpaid vs the nearer of the two targets (but only if strictly above)
  const nearest = Math.abs(t - inst) <= Math.abs(t - full) ? inst : full;
  const matchedType: "installment" | "full" =
    Math.abs(t - inst) <= Math.abs(t - full) ? "installment" : "full";
  const diff = round2(t - nearest);

  if (t > nearest + tol) {
    return { matchResult: "overpaid", difference: diff, matchedType };
  }
  if (t < nearest - tol && t > 0) {
    const remaining = round2(nearest - t);
    const url = opts.baseUrl
      ? `${opts.baseUrl}/pay/${encodeURIComponent(contractNumber)}/installment?remaining=${remaining}`
      : undefined;
    return {
      matchResult: "underpaid",
      difference: diff,
      matchedType,
      paymentUrl: url,
    };
  }
  return { matchResult: "unmatched", difference: diff, matchedType: "none" };
}

/**
 * Generate installment schedule for a new contract.
 * Each period is `intervalDays` after the previous due (default monthly via addMonths).
 */
export type ScheduleRow = {
  period: number;
  dueDate: Date;
  dueAmount: number;
  remainingAmount: number;
};

export function generateSchedule(args: {
  startDate: Date;
  totalPeriods: number;
  installmentAmount: number;
  totalAmount?: number;
  mode?: "monthly" | "days";
  intervalDays?: number;
}): ScheduleRow[] {
  const {
    startDate,
    totalPeriods,
    installmentAmount,
    totalAmount,
    mode = "monthly",
    intervalDays = 30,
  } = args;
  const periods = Math.max(0, Math.floor(safeNum(totalPeriods)));
  if (periods === 0) return [];

  const amt = round2(safeNum(installmentAmount));
  const rows: ScheduleRow[] = [];

  // If a totalAmount is provided, last period absorbs rounding remainder.
  let accumulated = 0;
  const total = totalAmount !== undefined ? round2(safeNum(totalAmount)) : round2(amt * periods);

  for (let p = 1; p <= periods; p++) {
    const due =
      mode === "monthly"
        ? addMonths(startDate, p)
        : new Date(startDate.getTime() + p * intervalDays * 24 * 60 * 60 * 1000);
    let due_amount = amt;
    if (p === periods) {
      due_amount = round2(total - accumulated);
      if (due_amount < 0) due_amount = 0;
    }
    accumulated = round2(accumulated + due_amount);
    rows.push({
      period: p,
      dueDate: due,
      dueAmount: due_amount,
      remainingAmount: due_amount,
    });
  }
  return rows;
}

/** Generate a contract number CF-YYMMDDNNNNN given a sequence counter. */
export function generateContractNumber(date: Date, sequence: number): string {
  const yy = String(date.getUTCFullYear()).slice(-2);
  const mm = String(date.getUTCMonth() + 1).padStart(2, "0");
  const dd = String(date.getUTCDate()).padStart(2, "0");
  const seq = String(Math.max(1, sequence)).padStart(5, "0");
  return `CF-${yy}${mm}${dd}${seq}`;
}

export const CONTRACT_NUMBER_RE = /^CF-\d{11}$/i;
