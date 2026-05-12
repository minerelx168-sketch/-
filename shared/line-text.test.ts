import { describe, it, expect } from "vitest";
import { parseLineText } from "./line-text.js";

describe("parseLineText", () => {
  it("detects contract numbers (case-insensitive, normalized to upper)", () => {
    expect(parseLineText("CF-26041700001")).toEqual({
      kind: "bind_contract",
      contractNumber: "CF-26041700001",
    });
    expect(parseLineText("cf-2604170001")).toEqual({
      kind: "bind_contract",
      contractNumber: "CF-2604170001",
    });
    expect(parseLineText("เลขสัญญา cf-1234 ครับ")).toEqual({
      kind: "bind_contract",
      contractNumber: "CF-1234",
    });
  });

  it("detects balance intent", () => {
    expect(parseLineText("ขอดูยอดหน่อย").kind).toBe("balance");
    expect(parseLineText("balance please").kind).toBe("balance");
    expect(parseLineText("งวดนี้เท่าไหร่").kind).toBe("balance");
  });

  it("detects greeting", () => {
    expect(parseLineText("สวัสดีครับ").kind).toBe("greeting");
    expect(parseLineText("Hello").kind).toBe("greeting");
  });

  it("falls back to unknown", () => {
    expect(parseLineText("xyz random text").kind).toBe("unknown");
    expect(parseLineText("").kind).toBe("unknown");
    expect(parseLineText(null).kind).toBe("unknown");
    expect(parseLineText(undefined).kind).toBe("unknown");
  });

  it("prefers bind_contract over balance when both match", () => {
    expect(parseLineText("ดูยอด CF-26041700001").kind).toBe("bind_contract");
  });
});
