/**
 * Escalation ladder — pure decision logic.
 *
 * Levels follow the spec §5.1:
 *   REMIND_TOMORROW (D-1)     — soft daily nudge
 *   OVERDUE_1  (D+1..D+2)     — first reminder
 *   OVERDUE_3  (D+3..D+4)     — penalty starts
 *   OVERDUE_5  (D+5..D+6)
 *   OVERDUE_7  (D+7..D+9)     — also notify owner
 *   OVERDUE_10 (D+10+)        — also notify owner + legal action
 */

export type EscalationLevel =
  | "REMIND_TOMORROW"
  | "OVERDUE_1"
  | "OVERDUE_3"
  | "OVERDUE_5"
  | "OVERDUE_7"
  | "OVERDUE_10"
  | null;

/** Match the variants in slipFlexMessages.buildReminderFlexMessage. */
export type FlexVariant = "soft" | "L1" | "L3" | "L5" | "L7" | "L10";

export type EscalationDecision = {
  level: EscalationLevel;
  variant: FlexVariant | null;
  notifyOwner: boolean;
  rank: number; // 0 = no action, higher = more severe
};

const LEVEL_RANK: Record<NonNullable<EscalationLevel>, number> = {
  REMIND_TOMORROW: 1,
  OVERDUE_1: 2,
  OVERDUE_3: 3,
  OVERDUE_5: 4,
  OVERDUE_7: 5,
  OVERDUE_10: 6,
};

const VARIANTS: Record<NonNullable<EscalationLevel>, FlexVariant> = {
  REMIND_TOMORROW: "soft",
  OVERDUE_1: "L1",
  OVERDUE_3: "L3",
  OVERDUE_5: "L5",
  OVERDUE_7: "L7",
  OVERDUE_10: "L10",
};

export function escalationRank(level: EscalationLevel | string | null | undefined): number {
  if (!level) return 0;
  return LEVEL_RANK[level as NonNullable<EscalationLevel>] ?? 0;
}

/**
 * Pick the appropriate level given how many days overdue (or days until due if not yet overdue).
 *
 * @param overdueDays  positive integer if past due, 0 otherwise
 * @param daysUntilDue positive integer if upcoming (>=1), undefined/0 if already past
 */
export function determineEscalationLevel(
  overdueDays: number,
  daysUntilDue?: number,
): EscalationDecision {
  const od = Math.max(0, Math.floor(Number(overdueDays) || 0));
  const upcoming = Math.max(0, Math.floor(Number(daysUntilDue) || 0));

  let level: EscalationLevel = null;
  if (od >= 10) level = "OVERDUE_10";
  else if (od >= 7) level = "OVERDUE_7";
  else if (od >= 5) level = "OVERDUE_5";
  else if (od >= 3) level = "OVERDUE_3";
  else if (od >= 1) level = "OVERDUE_1";
  else if (upcoming === 1) level = "REMIND_TOMORROW";

  if (!level) {
    return { level: null, variant: null, notifyOwner: false, rank: 0 };
  }
  return {
    level,
    variant: VARIANTS[level],
    notifyOwner: level === "OVERDUE_7" || level === "OVERDUE_10",
    rank: LEVEL_RANK[level],
  };
}

/**
 * Should we (re-)send for `current` given the contract's `last` sent level?
 * Re-sends OVERDUE_* only when severity strictly increases.
 * REMIND_TOMORROW is a daily soft nudge — caller dedups by date instead.
 */
export function shouldSendEscalation(
  current: EscalationLevel,
  last: EscalationLevel | string | null | undefined,
): boolean {
  if (!current) return false;
  if (current === "REMIND_TOMORROW") return true;
  return escalationRank(current) > escalationRank(last ?? null);
}
