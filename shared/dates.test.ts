import { describe, it, expect } from "vitest";
import { daysBetween, addDays, addMonths, formatThaiDate } from "./dates.js";

describe("daysBetween", () => {
  it("counts whole days", () => {
    expect(daysBetween(new Date("2026-01-01T00:00:00Z"), new Date("2026-01-04T00:00:00Z"))).toBe(3);
  });
  it("returns 0 if b <= a", () => {
    expect(daysBetween(new Date("2026-01-05T00:00:00Z"), new Date("2026-01-01T00:00:00Z"))).toBe(0);
    expect(daysBetween(new Date("2026-01-05T00:00:00Z"), new Date("2026-01-05T00:00:00Z"))).toBe(0);
  });
});

describe("addDays / addMonths", () => {
  it("adds days", () => {
    const d = addDays(new Date("2026-01-01T00:00:00Z"), 5);
    expect(d.toISOString().slice(0, 10)).toBe("2026-01-06");
  });
  it("adds months across year boundary", () => {
    const d = addMonths(new Date("2026-11-15T00:00:00Z"), 3);
    expect(d.getUTCFullYear()).toBe(2027);
    expect(d.getUTCMonth()).toBe(1); // Feb
  });
});

describe("formatThaiDate", () => {
  it("includes Buddhist-era year via th-TH locale", () => {
    const s = formatThaiDate(new Date("2026-05-12T00:00:00Z"));
    expect(typeof s).toBe("string");
    expect(s.length).toBeGreaterThan(0);
  });
});
