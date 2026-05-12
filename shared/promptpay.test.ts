import { describe, it, expect } from "vitest";
import { crc16ccitt, generatePromptPayPayload } from "./promptpay.js";

describe("crc16ccitt", () => {
  it("matches known CRC-16/CCITT-FALSE for '123456789'", () => {
    // canonical reference value
    expect(crc16ccitt("123456789")).toBe("29B1");
  });
  it("empty string → FFFF", () => {
    expect(crc16ccitt("")).toBe("FFFF");
  });
});

describe("generatePromptPayPayload", () => {
  it("static MSISDN QR has correct structure", () => {
    const p = generatePromptPayPayload({ payee: { kind: "msisdn", value: "0812345678" } });
    // Payload format indicator
    expect(p).toMatch(/^000201/);
    // Static (no amount) → POI method 11
    expect(p.slice(6, 12)).toBe("010211");
    // Merchant account info tag 29 should contain the AID
    expect(p).toContain("A000000677010111");
    // Country TH
    expect(p).toContain("5802TH");
    // No amount tag (54) should appear
    expect(p).not.toMatch(/54\d{2}/);
    // Ends with CRC (tag 63, length 04, 4 hex chars)
    expect(p).toMatch(/6304[0-9A-F]{4}$/);
  });

  it("dynamic QR includes amount", () => {
    const p = generatePromptPayPayload({
      payee: { kind: "msisdn", value: "0812345678" },
      amount: 1234.5,
    });
    // POI 12 + amount tag 54 with "1234.50"
    expect(p.slice(6, 12)).toBe("010212");
    expect(p).toContain("5407" + "1234.50");
  });

  it("MSISDN normalizes to 0066... regardless of input format", () => {
    const a = generatePromptPayPayload({ payee: { kind: "msisdn", value: "0812345678" } });
    const b = generatePromptPayPayload({ payee: { kind: "msisdn", value: "812345678" } });
    const c = generatePromptPayPayload({ payee: { kind: "msisdn", value: "66812345678" } });
    expect(a).toContain("0066812345678");
    expect(b).toContain("0066812345678");
    expect(c).toContain("0066812345678");
  });

  it("NID payload uses sub-tag 02", () => {
    const p = generatePromptPayPayload({ payee: { kind: "nid", value: "1234567890123" } });
    expect(p).toContain("0213" + "1234567890123");
  });

  it("rejects bad inputs", () => {
    expect(() =>
      generatePromptPayPayload({ payee: { kind: "msisdn", value: "123" } }),
    ).toThrow();
    expect(() =>
      generatePromptPayPayload({ payee: { kind: "nid", value: "123" } }),
    ).toThrow();
  });

  it("CRC is valid for the produced payload", () => {
    const p = generatePromptPayPayload({
      payee: { kind: "msisdn", value: "0812345678" },
      amount: 100,
    });
    const base = p.slice(0, -4);
    const crc = p.slice(-4);
    expect(crc16ccitt(base)).toBe(crc);
  });
});
