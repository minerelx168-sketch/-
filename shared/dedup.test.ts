import { describe, it, expect } from "vitest";
import { TtlSet } from "./dedup.js";

describe("TtlSet", () => {
  it("seen() returns false then true within TTL", () => {
    const s = new TtlSet(60_000);
    const t = 1_000_000;
    expect(s.seen("msg-1", t)).toBe(false);
    expect(s.seen("msg-1", t + 10)).toBe(true);
  });

  it("entries expire after TTL", () => {
    const s = new TtlSet(1000);
    const t = 1_000_000;
    s.add("msg-1", t);
    expect(s.has("msg-1", t + 500)).toBe(true);
    expect(s.has("msg-1", t + 2000)).toBe(false);
  });

  it("sweep removes expired entries", () => {
    const s = new TtlSet(500);
    s.add("a", 0);
    s.add("b", 0);
    s.add("c", 10_000);
    s.sweep(5000);
    expect(s.has("a", 5000)).toBe(false);
    expect(s.has("c", 5000)).toBe(true);
    expect(s.size).toBe(1);
  });
});
