import { describe, it, expect } from "vitest";
import { safeNum, round2, thb } from "./money.js";

describe("safeNum", () => {
  it("returns number unchanged for finite numbers", () => {
    expect(safeNum(123.45)).toBe(123.45);
    expect(safeNum(0)).toBe(0);
    expect(safeNum(-50)).toBe(-50);
  });

  it("parses decimal strings (Drizzle decimal columns)", () => {
    expect(safeNum("100.50")).toBe(100.5);
    expect(safeNum("0.00")).toBe(0);
  });

  it("returns fallback for NaN / Infinity / null / undefined / garbage", () => {
    expect(safeNum(NaN)).toBe(0);
    expect(safeNum(Infinity)).toBe(0);
    expect(safeNum(-Infinity)).toBe(0);
    expect(safeNum(null)).toBe(0);
    expect(safeNum(undefined)).toBe(0);
    expect(safeNum("abc")).toBe(0);
    expect(safeNum("")).toBe(0);
    expect(safeNum({})).toBe(0);
  });

  it("respects custom fallback", () => {
    expect(safeNum(null, 999)).toBe(999);
    expect(safeNum("abc", -1)).toBe(-1);
  });
});

describe("round2", () => {
  it("rounds to 2 decimal places", () => {
    expect(round2(1.234)).toBe(1.23);
    expect(round2(1.235)).toBe(1.24);
    expect(round2(0.1 + 0.2)).toBe(0.3);
  });
  it("coerces strings safely", () => {
    expect(round2("100.999" as unknown as number)).toBe(101);
  });
});

describe("thb formatter", () => {
  it("formats with 2 decimal places and th-TH grouping", () => {
    expect(thb(1234567.5)).toContain("1,234,567.50");
  });
});
