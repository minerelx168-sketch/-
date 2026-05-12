/**
 * PromptPay EMVCo QR payload generator (Bank of Thailand spec).
 * Produces the TLV string that, when rendered as a QR code, can be scanned
 * by any Thai banking app to initiate a PromptPay transfer.
 *
 * Supports:
 *   - MSISDN  (mobile)  — 9 or 10-digit phone, normalized to 0066…
 *   - NID     (national ID) — 13 digits
 *   - eWallet — 15 digits
 *
 * Reference: https://www.bot.or.th (PromptPay QR Code Standard v0.4)
 */

const TAG_PAYLOAD_FORMAT = "00";
const TAG_POI_METHOD = "01";
const TAG_MERCHANT_ACCOUNT_PROMPTPAY = "29";
const TAG_MCC = "52";
const TAG_CURRENCY = "53";
const TAG_AMOUNT = "54";
const TAG_COUNTRY = "58";
const TAG_CRC = "63";

const SUBTAG_APP_ID = "00";
const SUBTAG_MSISDN = "01";
const SUBTAG_NID = "02";
const SUBTAG_EWALLET = "03";

const APP_ID = "A000000677010111";

type PayeeKind = "msisdn" | "nid" | "ewallet";

export type PromptPayPayeeInput = {
  kind: PayeeKind;
  value: string;
};

/** Format a TLV field: ID (2) + length (2, zero-padded) + value. */
function tlv(id: string, value: string): string {
  const len = value.length.toString().padStart(2, "0");
  return `${id}${len}${value}`;
}

function normalizeMsisdn(input: string): string {
  const digits = input.replace(/\D/g, "");
  if (digits.length === 9) return `0066${digits}`;
  if (digits.length === 10 && digits.startsWith("0")) return `0066${digits.slice(1)}`;
  if (digits.length === 11 && digits.startsWith("66")) return `00${digits}`;
  if (digits.length === 13 && digits.startsWith("0066")) return digits;
  throw new Error(`Invalid MSISDN: ${input}`);
}

function normalizeNid(input: string): string {
  const digits = input.replace(/\D/g, "");
  if (digits.length !== 13) throw new Error(`Invalid NID: ${input}`);
  return digits;
}

function normalizeEwallet(input: string): string {
  const digits = input.replace(/\D/g, "");
  if (digits.length !== 15) throw new Error(`Invalid eWallet ID: ${input}`);
  return digits;
}

function merchantAccountInfo(payee: PromptPayPayeeInput): string {
  let sub: string;
  switch (payee.kind) {
    case "msisdn":
      sub = tlv(SUBTAG_MSISDN, normalizeMsisdn(payee.value));
      break;
    case "nid":
      sub = tlv(SUBTAG_NID, normalizeNid(payee.value));
      break;
    case "ewallet":
      sub = tlv(SUBTAG_EWALLET, normalizeEwallet(payee.value));
      break;
  }
  return tlv(TAG_APP_ID(), APP_ID) + sub;
}

function TAG_APP_ID() {
  return SUBTAG_APP_ID;
}

/**
 * CRC-16/CCITT-FALSE (poly 0x1021, init 0xFFFF, no xor-out, MSB-first).
 * Returns uppercase 4-char hex.
 */
export function crc16ccitt(input: string): string {
  let crc = 0xffff;
  const bytes = Buffer.from(input, "utf-8");
  for (const byte of bytes) {
    crc ^= byte << 8;
    for (let i = 0; i < 8; i++) {
      crc = (crc & 0x8000) ? ((crc << 1) ^ 0x1021) & 0xffff : (crc << 1) & 0xffff;
    }
  }
  return crc.toString(16).toUpperCase().padStart(4, "0");
}

export type GeneratePromptPayInput = {
  payee: PromptPayPayeeInput;
  amount?: number; // omit for "static" QR (user types amount), include for "dynamic"
};

export function generatePromptPayPayload(input: GeneratePromptPayInput): string {
  const hasAmount = typeof input.amount === "number" && input.amount > 0;

  const parts = [
    tlv(TAG_PAYLOAD_FORMAT, "01"),
    tlv(TAG_POI_METHOD, hasAmount ? "12" : "11"),
    tlv(TAG_MERCHANT_ACCOUNT_PROMPTPAY, merchantAccountInfo(input.payee)),
    tlv(TAG_MCC, "0000"),
    tlv(TAG_CURRENCY, "764"),
  ];
  if (hasAmount) {
    parts.push(tlv(TAG_AMOUNT, input.amount!.toFixed(2)));
  }
  parts.push(tlv(TAG_COUNTRY, "TH"));

  // CRC is computed over everything including the tag+length of the CRC field
  // but with the value omitted (i.e. "6304" appended before checksum).
  const base = parts.join("") + TAG_CRC + "04";
  const crc = crc16ccitt(base);
  return base + crc;
}
