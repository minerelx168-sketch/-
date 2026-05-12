import { describe, it, expect } from "vitest";
import {
  determineEscalationLevel,
  escalationRank,
  shouldSendEscalation,
} from "./escalation.js";

describe("determineEscalationLevel — overdue", () => {
  it("0 days, no upcoming → null", () => {
    expect(determineEscalationLevel(0).level).toBe(null);
  });
  it("D-1 → REMIND_TOMORROW", () => {
    expect(determineEscalationLevel(0, 1).level).toBe("REMIND_TOMORROW");
  });
  it("D-3 → no action (only D-1 nudge)", () => {
    expect(determineEscalationLevel(0, 3).level).toBe(null);
  });
  it("1-2 days overdue → OVERDUE_1", () => {
    expect(determineEscalationLevel(1).level).toBe("OVERDUE_1");
    expect(determineEscalationLevel(2).level).toBe("OVERDUE_1");
  });
  it("3-4 days → OVERDUE_3", () => {
    expect(determineEscalationLevel(3).level).toBe("OVERDUE_3");
    expect(determineEscalationLevel(4).level).toBe("OVERDUE_3");
  });
  it("5-6 days → OVERDUE_5", () => {
    expect(determineEscalationLevel(5).level).toBe("OVERDUE_5");
    expect(determineEscalationLevel(6).level).toBe("OVERDUE_5");
  });
  it("7-9 days → OVERDUE_7 + notify owner", () => {
    expect(determineEscalationLevel(7)).toMatchObject({ level: "OVERDUE_7", notifyOwner: true });
    expect(determineEscalationLevel(9)).toMatchObject({ level: "OVERDUE_7", notifyOwner: true });
  });
  it("10+ days → OVERDUE_10 + notify owner", () => {
    expect(determineEscalationLevel(10)).toMatchObject({ level: "OVERDUE_10", notifyOwner: true });
    expect(determineEscalationLevel(30)).toMatchObject({ level: "OVERDUE_10", notifyOwner: true });
  });
  it("OVERDUE_1..5 should NOT notify owner", () => {
    for (const d of [1, 3, 5]) {
      expect(determineEscalationLevel(d).notifyOwner).toBe(false);
    }
  });
});

describe("determineEscalationLevel — chaos inputs", () => {
  it("NaN / negative / strings collapse to 0", () => {
    expect(determineEscalationLevel(NaN).level).toBe(null);
    expect(determineEscalationLevel(-5).level).toBe(null);
    expect(determineEscalationLevel("x" as unknown as number).level).toBe(null);
  });
  it("fractional days floored", () => {
    expect(determineEscalationLevel(7.9).level).toBe("OVERDUE_7");
    expect(determineEscalationLevel(0.5).level).toBe(null);
  });
});

describe("shouldSendEscalation", () => {
  it("no current → false", () => {
    expect(shouldSendEscalation(null, null)).toBe(false);
  });
  it("REMIND_TOMORROW always allowed (caller dedups by day)", () => {
    expect(shouldSendEscalation("REMIND_TOMORROW", "REMIND_TOMORROW")).toBe(true);
    expect(shouldSendEscalation("REMIND_TOMORROW", "OVERDUE_3")).toBe(true);
  });
  it("OVERDUE_* only when severity strictly increases", () => {
    expect(shouldSendEscalation("OVERDUE_1", null)).toBe(true);
    expect(shouldSendEscalation("OVERDUE_3", "OVERDUE_1")).toBe(true);
    expect(shouldSendEscalation("OVERDUE_3", "OVERDUE_3")).toBe(false);
    expect(shouldSendEscalation("OVERDUE_3", "OVERDUE_5")).toBe(false);
    expect(shouldSendEscalation("OVERDUE_10", "OVERDUE_7")).toBe(true);
  });
});

describe("escalationRank", () => {
  it("ordered correctly", () => {
    expect(escalationRank(null)).toBe(0);
    expect(escalationRank("REMIND_TOMORROW")).toBeLessThan(escalationRank("OVERDUE_1"));
    expect(escalationRank("OVERDUE_1")).toBeLessThan(escalationRank("OVERDUE_3"));
    expect(escalationRank("OVERDUE_5")).toBeLessThan(escalationRank("OVERDUE_7"));
    expect(escalationRank("OVERDUE_10")).toBeGreaterThan(escalationRank("OVERDUE_7"));
  });
  it("unknown levels rank 0", () => {
    expect(escalationRank("HACK")).toBe(0);
    expect(escalationRank(undefined)).toBe(0);
  });
});
