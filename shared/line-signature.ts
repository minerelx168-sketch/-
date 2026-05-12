import crypto from "node:crypto";

/**
 * Verify a LINE webhook signature.
 * LINE signs the raw request body with HMAC-SHA256(channelSecret), base64-encoded.
 * `rawBody` MUST be the byte-exact JSON body — not a re-stringified object.
 */
export function verifyLineSignature(
  rawBody: string | Buffer,
  signatureHeader: string | undefined,
  channelSecret: string,
): boolean {
  if (!signatureHeader || !channelSecret) return false;
  const buf = typeof rawBody === "string" ? Buffer.from(rawBody, "utf-8") : rawBody;
  const expected = crypto.createHmac("sha256", channelSecret).update(buf).digest("base64");
  const a = Buffer.from(expected);
  const b = Buffer.from(signatureHeader);
  if (a.length !== b.length) return false;
  try {
    return crypto.timingSafeEqual(a, b);
  } catch {
    return false;
  }
}

/** Compute the signature for a body — useful in tests and request signing. */
export function computeLineSignature(rawBody: string | Buffer, channelSecret: string): string {
  const buf = typeof rawBody === "string" ? Buffer.from(rawBody, "utf-8") : rawBody;
  return crypto.createHmac("sha256", channelSecret).update(buf).digest("base64");
}
