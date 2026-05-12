import { describe, it, expect } from "vitest";
import { buildSlipFlexMessage, buildReminderFlexMessage } from "./slipFlexMessages.js";

function asBubble(m: ReturnType<typeof buildSlipFlexMessage>) {
  return m.contents as { type: string; size: string; header: { backgroundColor: string } };
}

describe("buildSlipFlexMessage", () => {
  it("valid slip → green nano bubble", () => {
    const m = buildSlipFlexMessage({
      status: "valid",
      amount: 1000,
      contractNumber: "CF-26011500001",
      matchResult: "exact_installment",
    });
    expect(m.type).toBe("flex");
    const b = asBubble(m);
    expect(b.size).toBe("nano");
    expect(b.header.backgroundColor).toBe("#27AE60");
    expect(m.altText).toContain("สำเร็จ");
  });

  it("duplicate slip → orange", () => {
    const m = buildSlipFlexMessage({ status: "duplicate", contractNumber: "X" });
    expect(asBubble(m).header.backgroundColor).toBe("#F39C12");
  });

  it("invalid → red", () => {
    expect(asBubble(buildSlipFlexMessage({ status: "invalid" })).header.backgroundColor).toBe(
      "#E74C3C",
    );
  });

  it("error → gray", () => {
    expect(asBubble(buildSlipFlexMessage({ status: "error" })).header.backgroundColor).toBe(
      "#95A5A6",
    );
  });

  it("underpaid includes paymentUrl in footer button", () => {
    const m = buildSlipFlexMessage({
      status: "valid",
      matchResult: "underpaid",
      amount: 700,
      expectedAmount: 1000,
      remainingAmount: 300,
      paymentUrl: "https://x.test/pay/CF-1/installment?remaining=300",
    });
    const b = m.contents as {
      footer?: { contents: Array<{ action?: { uri?: string } }> };
    };
    expect(b.footer?.contents[0]?.action?.uri).toContain("remaining=300");
  });
});

describe("buildReminderFlexMessage", () => {
  it("default L1 variant is orange", () => {
    const m = buildReminderFlexMessage({
      contractNumber: "CF-1",
      dueDate: new Date("2026-05-12T00:00:00Z"),
      dueAmount: 1000,
    });
    expect(asBubble(m).header.backgroundColor).toBe("#F39C12");
  });
  it("L7 is red", () => {
    const m = buildReminderFlexMessage({
      contractNumber: "CF-1",
      dueDate: new Date(),
      dueAmount: 1000,
      variant: "L7",
    });
    expect(asBubble(m).header.backgroundColor).toBe("#C0392B");
  });
  it("L10 is darkest red", () => {
    const m = buildReminderFlexMessage({
      contractNumber: "CF-1",
      dueDate: new Date(),
      dueAmount: 1000,
      variant: "L10",
    });
    expect(asBubble(m).header.backgroundColor).toBe("#922B21");
  });
});
