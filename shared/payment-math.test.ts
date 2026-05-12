import { describe, it, expect } from "vitest";
import {
  calculatePaymentAmount,
  matchAmount,
  generateSchedule,
  generateContractNumber,
  type ContractLike,
  type InstallmentLike,
} from "./payment-math.js";

const baseContract: ContractLike = {
  devicePrice: 15000,
  installmentAmount: 1000,
  penaltyRate: 100,
  unlockFee: 500,
  lockStatus: "unlocked",
};

function inst(over: Partial<InstallmentLike> = {}): InstallmentLike {
  return {
    period: 1,
    dueDate: new Date("2026-01-15T00:00:00Z"),
    dueAmount: 1000,
    paidAmount: 0,
    remainingAmount: 1000,
    ...over,
  };
}

describe("calculatePaymentAmount — installment", () => {
  it("on-time, no penalty", () => {
    const r = calculatePaymentAmount(
      baseContract,
      inst(),
      "installment",
      new Date("2026-01-15T00:00:00Z"),
    );
    expect(r.installmentDue).toBe(1000);
    expect(r.penalty).toBe(0);
    expect(r.total).toBe(1000);
    expect(r.overdueDays).toBe(0);
  });

  it("5 days late = penalty 500", () => {
    const r = calculatePaymentAmount(
      baseContract,
      inst(),
      "installment",
      new Date("2026-01-20T00:00:00Z"),
    );
    expect(r.overdueDays).toBe(5);
    expect(r.penalty).toBe(500);
    expect(r.total).toBe(1500);
  });

  it("partial payment carries remaining", () => {
    const r = calculatePaymentAmount(
      baseContract,
      inst({ paidAmount: 600, remainingAmount: 400 }),
      "installment",
      new Date("2026-01-15T00:00:00Z"),
    );
    expect(r.installmentDue).toBe(400);
    expect(r.total).toBe(400);
  });

  it("custom penaltyRate per contract", () => {
    const r = calculatePaymentAmount(
      { ...baseContract, penaltyRate: 50 },
      inst(),
      "installment",
      new Date("2026-01-25T00:00:00Z"),
    );
    expect(r.penalty).toBe(500); // 10 days * 50
    expect(r.total).toBe(1500);
  });
});

describe("calculatePaymentAmount — full", () => {
  it("unlocked: device + installment + penalty (no unlock fee)", () => {
    const r = calculatePaymentAmount(
      baseContract,
      inst(),
      "full",
      new Date("2026-01-15T00:00:00Z"),
    );
    expect(r.devicePrice).toBe(15000);
    expect(r.unlockFee).toBe(0);
    expect(r.total).toBe(16000); // 15000 + 1000
  });

  it("locked: includes unlock fee", () => {
    const r = calculatePaymentAmount(
      { ...baseContract, lockStatus: "locked" },
      inst(),
      "full",
      new Date("2026-01-15T00:00:00Z"),
    );
    expect(r.unlockFee).toBe(500);
    expect(r.total).toBe(16500);
  });

  it("locked + 3 days late", () => {
    const r = calculatePaymentAmount(
      { ...baseContract, lockStatus: "locked" },
      inst(),
      "full",
      new Date("2026-01-18T00:00:00Z"),
    );
    expect(r.penalty).toBe(300);
    expect(r.total).toBe(16800); // 15000 + 1000 + 300 + 500
  });
});

describe("calculatePaymentAmount — chaos inputs", () => {
  it("handles null currentInstallment", () => {
    const r = calculatePaymentAmount(baseContract, null, "installment", new Date());
    expect(r.installmentDue).toBe(0);
    expect(r.penalty).toBe(0);
    expect(r.total).toBe(0);
  });

  it("handles NaN / strings", () => {
    const r = calculatePaymentAmount(
      { devicePrice: "NaN", installmentAmount: "x", penaltyRate: "100", unlockFee: "500" },
      inst({ remainingAmount: "abc" as unknown as number }),
      "full",
      new Date("2026-01-15T00:00:00Z"),
    );
    expect(Number.isFinite(r.total)).toBe(true);
    expect(r.total).toBe(0);
  });
});

describe("matchAmount", () => {
  it("exact installment within tolerance", () => {
    const r = matchAmount(1000, 1000, 16000, "CF-26011500001");
    expect(r.matchResult).toBe("exact_installment");
    expect(r.matchedType).toBe("installment");
  });
  it("exact full", () => {
    const r = matchAmount(16000, 1000, 16000, "CF-26011500001");
    expect(r.matchResult).toBe("exact_full");
  });
  it("tolerance ±1 baht counts as exact", () => {
    expect(matchAmount(999, 1000, 16000, "X").matchResult).toBe("exact_installment");
    expect(matchAmount(1001, 1000, 16000, "X").matchResult).toBe("exact_installment");
    expect(matchAmount(15999, 1000, 16000, "X").matchResult).toBe("exact_full");
  });
  it("overpaid relative to nearest", () => {
    const r = matchAmount(1200, 1000, 16000, "X");
    expect(r.matchResult).toBe("overpaid");
    expect(r.difference).toBe(200);
    expect(r.matchedType).toBe("installment");
  });
  it("underpaid emits paymentUrl with remaining", () => {
    const r = matchAmount(700, 1000, 16000, "CF-26011500001", { baseUrl: "https://x.test" });
    expect(r.matchResult).toBe("underpaid");
    expect(r.paymentUrl).toContain("CF-26011500001");
    expect(r.paymentUrl).toContain("remaining=300");
  });
  it("zero / negative transfer is unmatched", () => {
    expect(matchAmount(0, 1000, 16000, "X").matchResult).toBe("unmatched");
  });
});

describe("generateSchedule", () => {
  it("creates N rows with monthly cadence", () => {
    const rows = generateSchedule({
      startDate: new Date("2026-01-15T00:00:00Z"),
      totalPeriods: 6,
      installmentAmount: 1000,
    });
    expect(rows).toHaveLength(6);
    expect(rows[0].period).toBe(1);
    expect(rows[5].period).toBe(6);
    expect(rows[0].dueDate.getUTCMonth()).toBe(1); // Feb
    expect(rows[5].dueDate.getUTCMonth()).toBe(6); // Jul
  });

  it("last period absorbs rounding remainder", () => {
    const rows = generateSchedule({
      startDate: new Date("2026-01-15T00:00:00Z"),
      totalPeriods: 3,
      installmentAmount: 333.33,
      totalAmount: 1000,
    });
    const sum = rows.reduce((s, r) => s + r.dueAmount, 0);
    expect(Math.round(sum * 100) / 100).toBe(1000);
  });

  it("handles zero periods gracefully", () => {
    expect(
      generateSchedule({
        startDate: new Date(),
        totalPeriods: 0,
        installmentAmount: 1000,
      }),
    ).toEqual([]);
  });
});

describe("generateContractNumber", () => {
  it("formats CF-YYMMDDNNNNN", () => {
    expect(generateContractNumber(new Date("2026-04-17T00:00:00Z"), 1)).toBe("CF-26041700001");
    expect(generateContractNumber(new Date("2026-12-01T00:00:00Z"), 42)).toBe("CF-26120100042");
  });
});
