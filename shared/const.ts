export const COOKIE_NAME = "session";
export const AXIOS_TIMEOUT_MS = 30000;
export const ONE_YEAR_MS = 365 * 24 * 60 * 60 * 1000;
export const NOT_ADMIN_ERR_MSG = "You must be an admin to perform this action";
export const UNAUTHED_ERR_MSG = "You must be logged in to perform this action";

// Lemon Squeezy Configuration
export const LEMON_SQUEEZY_STORE = "imeihub";
export const LEMON_SQUEEZY_STORE_URL = "https://imeihub.lemonsqueezy.com";

// Credit packages - variant IDs from Lemon Squeezy (test mode)
// NOTE: Replace these with live mode variant IDs before going to production
export const CREDIT_PACKAGES = [
  {
    id: "pkg_25",
    name: "$25 Credit Top-Up",
    amount: 2500, // cents
    price: "$25.00",
    variantId: "741029", // Lemon Squeezy variant ID
    productId: "1210408",
    description: "Add $25 worth of credits",
    popular: false,
  },
  {
    id: "pkg_50",
    name: "$50 Credit Top-Up",
    amount: 5000,
    price: "$50.00",
    variantId: "741030", // Lemon Squeezy variant ID
    productId: "1210419",
    description: "Add $50 worth of credits",
    popular: true,
  },
  {
    id: "pkg_100",
    name: "$100 Credit Top-Up",
    amount: 10000,
    price: "$100.00",
    variantId: "741031", // Placeholder - create product in Lemon Squeezy dashboard
    productId: "pkg_100",
    description: "Add $100 worth of credits",
    popular: false,
  },
  {
    id: "pkg_250",
    name: "$250 Credit Top-Up",
    amount: 25000,
    price: "$250.00",
    variantId: "741032", // Placeholder - create product in Lemon Squeezy dashboard
    productId: "pkg_250",
    description: "Best value! Add $250 worth of credits",
    popular: false,
  },
] as const;
