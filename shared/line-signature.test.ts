import { describe, it, expect } from "vitest";
import { computeLineSignature, verifyLineSignature } from "./line-signature.js";

const SECRET = "test-channel-secret";
const BODY = JSON.stringify({ events: [{ type: "message", id: "x" }] });

describe("LINE signature", () => {
  it("verifies a signature computed with the same secret + body", () => {
    const sig = computeLineSignature(BODY, SECRET);
    expect(verifyLineSignature(BODY, sig, SECRET)).toBe(true);
  });

  it("rejects when body changes by a single byte", () => {
    const sig = computeLineSignature(BODY, SECRET);
    expect(verifyLineSignature(BODY + " ", sig, SECRET)).toBe(false);
  });

  it("rejects when secret differs", () => {
    const sig = computeLineSignature(BODY, SECRET);
    expect(verifyLineSignature(BODY, sig, "other-secret")).toBe(false);
  });

  it("rejects when signature header is missing or empty", () => {
    expect(verifyLineSignature(BODY, undefined, SECRET)).toBe(false);
    expect(verifyLineSignature(BODY, "", SECRET)).toBe(false);
  });

  it("rejects when channel secret is missing", () => {
    const sig = computeLineSignature(BODY, SECRET);
    expect(verifyLineSignature(BODY, sig, "")).toBe(false);
  });

  it("accepts Buffer body identical to string body", () => {
    const sig = computeLineSignature(BODY, SECRET);
    expect(verifyLineSignature(Buffer.from(BODY, "utf-8"), sig, SECRET)).toBe(true);
  });
});
