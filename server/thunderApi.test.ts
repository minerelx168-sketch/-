import { describe, it, expect } from "vitest";
import { determineSlipStatus, type ThunderVerifyResult } from "./thunderApi.js";

function res(data: ThunderVerifyResult["data"]): ThunderVerifyResult {
  return { data, raw: {} };
}

describe("determineSlipStatus", () => {
  it("valid", () => {
    expect(determineSlipStatus(res({ valid: true, amount: 1000 }))).toBe("valid");
  });
  it("duplicate beats valid", () => {
    expect(determineSlipStatus(res({ valid: true, isDuplicate: true }))).toBe("duplicate");
  });
  it("invalid", () => {
    expect(determineSlipStatus(res({ valid: false }))).toBe("invalid");
  });
  it("missing data → error", () => {
    expect(determineSlipStatus(res(null))).toBe("error");
    expect(determineSlipStatus({ data: null, raw: null })).toBe("error");
  });
  it("ambiguous → error", () => {
    expect(determineSlipStatus(res({ valid: undefined as unknown as boolean }))).toBe("error");
  });
});
