export const COOKIE_NAME = "session";
export const AXIOS_TIMEOUT_MS = 30000;
export const ONE_YEAR_MS = 365 * 24 * 60 * 60 * 1000;
export const NOT_ADMIN_ERR_MSG = "You must be an admin to perform this action";
export const UNAUTHED_ERR_MSG = "You must be logged in to perform this action";

// Lemon Squeezy Configuration
export const LEMON_SQUEEZY_STORE = "imeihub";
export const LEMON_SQUEEZY_STORE_URL = "https://imeihub.lemonsqueezy.com";

// Top-up presets (matching production)
export const TOPUP_PRESETS = [5, 10, 25, 50, 100, 250] as const;

// Payment methods configuration (matching production data/payment_methods.php)
export const PAYMENT_METHODS = [
  {
    id: "card",
    label: "Card",
    provider: "lemonsqueezy",
    currency: "USD",
    feePct: 5,
    minUsd: 5,
    maxUsd: 500,
    bonusPct: 0,
    badges: [] as string[],
    enabled: true,
  },
] as const;

/**
 * Lemon Squeezy variant IDs.
 * 
 * NOTE: Lemon Squeezy uses fixed-price products/variants.
 * For this demo, we use a single "Credit Top-Up" product with the following variants:
 * - variant 1210408 = $25 Credit Top-Up (published)
 * - variant 1210419 = $50 Credit Top-Up (published)
 * 
 * For amounts that don't have a dedicated variant ($5, $10, $100, $250),
 * we use the $25 variant as a placeholder. In production, either:
 * 1. Create separate variants for each amount, OR
 * 2. Use Lemon Squeezy's "pay what you want" feature with custom pricing
 * 
 * The webhook handler uses custom_data.amount to determine the actual credit amount,
 * NOT the variant price. This is validated against the topup_order record.
 */
export const LEMON_VARIANT_MAP: Record<string, string> = {
  "5": "1210408",   // Uses $25 variant (placeholder for demo)
  "10": "1210408",  // Uses $25 variant (placeholder for demo)
  "25": "1210408",  // $25 Credit Top-Up variant (real)
  "50": "1210419",  // $50 Credit Top-Up variant (real)
  "100": "1210408", // Placeholder - needs real variant in production
  "250": "1210408", // Placeholder - needs real variant in production
};

export function getLemonCheckoutUrl(variantId: string, params: {
  userId: number;
  email?: string;
  amount: number;
  orderId: string;
  successUrl: string;
}): string {
  const base = `${LEMON_SQUEEZY_STORE_URL}/buy/${variantId}`;
  const searchParams = new URLSearchParams({
    "checkout[custom][user_id]": String(params.userId),
    "checkout[custom][order_id]": params.orderId,
    "checkout[custom][amount]": String(params.amount),
    "checkout[success_url]": params.successUrl,
  });
  if (params.email) {
    searchParams.set("checkout[email]", params.email);
  }
  return `${base}?${searchParams.toString()}`;
}
