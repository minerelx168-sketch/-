# bKash — Payment Integration Research (Bangladesh)

**Task**: PR-BKASH-01
**Author**: Research note for imeihub Phase 1
**Date**: 2026-05-28
**Status**: Pre-integration research only — no code written

---

## 1. Overview & Current Market Position in Bangladesh

bKash (a BRAC Bank subsidiary, with Alipay/Ant Group and the Gates Foundation as minority investors) is the default digital wallet of Bangladesh.

- **Customers**: bKash states "more than 70 million" on its merchant page [bKash Merchant, accessed 2026-05-28](https://www.bkash.com/en/business/merchant); industry trackers cite ~80M including secondary accounts [Future Startup, 2025-05-06](https://futurestartup.com/2025/05/06/the-state-of-mobile-financial-services-mfs-industry-in-bangladesh/).
- **MFS industry**: 239.3M MFS accounts as of Jan 2025; 2024 total MFS transactions hit **Tk 17.37 lakh crore (~USD 143B)**, +28.4% YoY [The Business Standard, 2025-02-12](https://www.tbsnews.net/economy/banking/mfs-market-flourishes-transactions-surge-tk384-lakh-crore-2024-1072141).
- **bKash 2024 profit**: Tk 315.77 crore (~USD 26M), +67% YoY [Future Startup, 2025-05-06](https://futurestartup.com/2025/05/06/the-state-of-mobile-financial-services-mfs-industry-in-bangladesh/).
- **Share**: Bangladesh Bank publishes aggregate MFS data but not per-provider share [Bangladesh Bank MFS data](https://www.bb.org.bd/en/index.php/financialactivity/mfsdata); third parties place bKash first by transaction volume (estimated ~55–65% of active wallet users), Nagad #2, Rocket/Upay distant.

**Why bKash dominates online checkout**: brand recognition (national TV since 2012), USSD fallback (*247#), in-app QR, near-universal merchant acceptance, and — critically — most Bangladeshi consumers don't hold international credit/debit cards, so Stripe-style card checkout converts very poorly versus "scan + PIN". Foodpanda BD, Daraz BD, Pathao, Chaldal, Shohoz and Pickaboo all expose bKash at checkout [bKash press, 2018](https://www.bkash.com/node/2772).

---

## 2. API Documentation

**Official portal**: [https://developer.bka.sh/](https://developer.bka.sh/) ("bKash Developer Beta"). Returns 403 to non-browser clients — credentialed browser access required.

Two integration models:

1. **Checkout (URL Based)** — Redirect to a bKash-hosted page; wallet number + OTP + PIN every time. Sandbox: `https://checkout.sandbox.bka.sh/v1.2.0-beta/`.
2. **Tokenized Checkout** — One-time *Agreement* binds the customer's wallet to the merchant's `payerReference`; later payments require only PIN. Recommended for repeat purchasers. Sandbox: `https://tokenized.sandbox.bka.sh/v1.2.0-beta/`; production: `https://tokenized.pay.bka.sh/v1.2.0-beta/` [Tokenized Checkout API Guide v0.3, bKash S3](https://s3-ap-southeast-1.amazonaws.com/developer.pay.bka.sh/Tokenized+Checkout+-+API+Integration+Guide.pdf).

Auth: server-side `app_key + app_secret + username + password` to `/token/grant` → `id_token` (1h TTL) + `refresh_token` (28d TTL). All payment ops use `Authorization: <id_token>` and `X-APP-Key`. JSON-only. Core endpoints: `/checkout/payment/create`, `/payment/execute/{paymentID}`, `/payment/status`, `/payment/refund`, `/agreement/create`, `/agreement/cancel` [rahulhaque demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo).

---

## 3. Merchant Onboarding Requirements

No self-service developer signup. Standard path: contact bKash sales → Merchant Account Manager assigned → KYC → sandbox creds → integration testing → production creds [arman.bd, 2024](https://arman.bd/bkash-api-login-guide/).

Required documents (compiled from supplier/merchant pages; PGW form is gated):

- Current **Trade Licence** (City Corp / Union Parishad)
- **e-TIN Certificate** + **VAT Registration (BIN)**
- **Bangladeshi scheduled-bank current account** in the business's name (settlement)
- **Bank Solvency Certificate**
- **National ID** of authorised signatory(ies)
- **MoA & AoA + Certificate of Incorporation** (for Pvt Ltd)
- **Authorisation Letter** for technical/financial contact
- For PGW: hosted callback URL + business description [bKash Supplier docs](https://scp.bkash.com/registration-required-document), [bKash Merchant page](https://www.bkash.com/en/business/merchant)

**Timeline**: Not publicly disclosed; community reports put it at **2–6 weeks from first contact to live keys**, no SLA. Sandbox access can come within days; production credentials require the full KYC chain.

**Critical for imeihub**: every document presumes a **Bangladesh-incorporated business with a BD bank account**. See §6.

---

## 4. Pricing & Fees

bKash does **not publish a public PGW rate card**; rates are negotiated per merchant based on volume, category, ticket size. Third-party data:

- **Direct bKash MDR**: market-reported **1.5%–2.0%** per successful txn, as of 2026 [Moneybag — bKash vs Cards, 2026](https://moneybag.com.bd/gateway-fees-breakdown-bkash-vs-cards/).
- **Via SSLCOMMERZ aggregator**: ~**1.75%–1.80%** on bKash/Nagad/Rocket/Upay channel, sometimes promo-locked at 1.75% [Moneybag, 2026](https://moneybag.com.bd/gateway-fees-breakdown-bkash-vs-cards/); standard listed rate is **2.5%** in the Basic plan [Financfy, 2026](https://financfy.com/blog/payment-gateway-in-bangladesh/).
- **PRA tier** (Personal Retail Account, small merchants): cashback-based, not MDR; capped at BDT 999/txn offline, BDT 2,000/txn online [bKash PRA, accessed 2026-05-28](https://www.bkash.com/en/page/personal-retail-account). Not appropriate for a regulated digital service.
- **Refund/chargeback fee**: ~BDT 500+ per disputed transaction [Moneybag, 2026](https://moneybag.com.bd/gateway-fees-breakdown-bkash-vs-cards/). API-driven refunds on original txns are free.
- **Settlement fee**: bundled into MDR (not separately disclosed).
- **Setup fee**: typically BDT 0 for direct PGW; aggregators sometimes charge BDT 5,000–15,000.

**Gap**: exact 2026 PGW rate card is **not publicly disclosed**; merchants must request a quote via the [bKash sales contact form](https://www.bkash.com/en/help/contact-us). The 1.5–2.0% range is the most reliable third-party estimate as of 2026.

**imeihub impact (USD 0.01–4.20 tickets)**: at 1.8% MDR a $4.20 charge nets ~$4.12; a $0.01 charge nets ~$0.0098. No fixed-cent minimum fee is documented and bKash's own minimum payment is BDT 1 (~USD 0.0085) per its [Limits page](https://www.bkash.com/en/help/limits), so micro-charges down to ~1 BDT are technically supported.

---

## 5. Settlement Timeline

Settlement reports live in the Merchant Portal at `merchantportal.bkash.com/reports/settlement-reports`. Default cadence per Bangladeshi gateway aggregators is **T+1 business day** — funds land in the merchant's BD bank account the next working day [Moneybag gateway comparison, 2026](https://moneybag.com.bd/10-best-payment-gateways-in-bangladesh/).

**Weekend / holidays**: Bangladesh's banking weekend is **Fri + Sat**. Thu/Fri/Sat txns settle Sunday. Public holidays push settlement to the next working day. **No T+0 on the standard PGW tier** as of 2026. **Gap**: bKash publishes no formal settlement SLA; confirm with sales during onboarding.

---

## 6. Supported Currencies

**BDT only**. Every API call's `currency` field must be `"BDT"` [community demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo). Wallets fund in BDT; settlement is BDT to a BD bank account [Ria — bKash inbound](https://www.riamoneytransfer.com/en-us/send-money-to-bkash/).

**Cross-border feasibility for imeihub** (the single biggest blocker):

- The Foreign Exchange Regulation Act 1947 and the [BB Foreign Exchange Transaction Guidelines](https://www.bb.org.bd/aboutus/regulationguideline/foreignexchange/fegv1cont.php) restrict outbound BDT settlements to foreign entities. As a BB-licensed MFS, bKash settles only to BD scheduled-bank accounts in the merchant's registered name.
- "bKash for business is not directly available in the USA" — confirmed in third-party reviews [paymentgateways.org](https://paymentgateways.org/payment/bkash). No direct onboarding of foreign-incorporated entities.

**Workaround**: **SSLCOMMERZ** (Bangladesh Bank PSO licensee) markets to international merchants and exposes bKash + Nagad + Rocket + cards through one API, settling to foreign merchants via BB-approved FX conversion [SSLCOMMERZ](https://sslcommerz.com/), [Financfy, 2026](https://financfy.com/blog/payment-gateway-in-bangladesh/). Trade-off: 2.5% standard MDR + extra reconciliation layer.

**Bottom line**: direct bKash PGW is only viable with a Bangladesh-incorporated subsidiary or local trade-licence holder. Otherwise, route via SSLCOMMERZ.

---

## 7. SDK Availability

Official org [github.com/bKash-developer](https://github.com/bKash-developer) — SDKs are **thin and aging**:

| Platform | Repo | Last update | Status |
|---|---|---|---|
| PHP backend demo | `pgw-merchant-backend-php` | 2023-03-24 | Demo, not a library |
| PHP webhook listener | `webhook-endpoint-php` | 2018-11-12 | Stale sample |
| WooCommerce plugin | `bKash-for-woocommerce` | 2026-04-21 | Actively maintained |
| Android demo | `bkash-pgwClient-demo-android` | 2020-09-05 | Stale |
| iOS demo | `bkash-pgwClient-demo-iOS` | 2018-10-11 | Stale |
| Java webhook listener | `merchant-webhook-endpoint` | — | Sample |

**Official PHP SDK: none** — no Composer-installable `bkash/php-sdk` exists. Active community PHP/Laravel packages on Packagist: `karim007/laravel-bkash`, `msilabs/bkash`, `pranaycb/laravel-bkash`, `irfan-chowdhury/bkash-tokenized-checkout`, `iamtiqbal/bkash-php-sdk`. For imeihub (raw PHP, not Laravel), `iamtiqbal/bkash-php-sdk` or hand-rolling against documented HTTP endpoints is the realistic path.

**JS/Web**: bKash provides a JavaScript popup script (`getCheckoutScript`) loaded from bKash domains; jQuery is a hard prerequisite [rahulhaque demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo). Modern alternative: community `ShejanMahamud/bkash-js` TypeScript Node library + bKash popup.

**Mobile**: official Android/iOS demos are stale (2020 / 2018). Apps integrate via **WebView popup + native bridge** rather than a true native SDK; community Android SDKs exist (e.g. `io.github.alims-repo:bkash-sdk`).

---

## 8. Webhook Structure

**Instant Payment Notification (IPN)** — subscription is **not self-service**. Merchant shares its endpoint URL with the bKash technical team during onboarding; bKash sends a `SubscriptionConfirmationRequest`, the endpoint must echo it back.

**Signing**: **HMAC-SHA256** with shared secret. Signature in `X-Signature` header as a **hex-encoded HMAC of the canonicalised JSON payload**, using the **JSON Canonicalization Scheme (JCS) per RFC 8785** [HackMD webhook summary](https://hackmd.io/gH3NRGHvTgCTlkBul17BdQ), [bKash-developer/webhook-endpoint-php](https://github.com/bKash-developer/webhook-endpoint-php).

**Endpoint requirements**: public HTTPS (TLS 1.2+), `200 OK` within ~30s, echo `SubscribeURL` on first subscription.

**Retry policy**: exponential-backoff on non-2xx (specific schedule not public; typical IPN systems retry 5–10 times over 24h — confirm during onboarding).

**Event types**: txn completion, refund, agreement creation, agreement cancellation.

---

## 9. Sandbox Environment

**Base URLs**:
- Checkout: `https://checkout.sandbox.bka.sh/v1.2.0-beta/`
- Tokenized Checkout: `https://tokenized.sandbox.bka.sh/v1.2.0-beta/`
- Demo storefront: `https://merchantdemo.sandbox.bka.sh/tokenized-checkout/version/v2`

**Public test credentials** (illustrative — request fresh ones from sales) [onecodesoft, accessed 2026-05-28](https://onecodesoft.com/blogs/bkash-sandbox-credentials-for-payment-gateway-testing):

```
BKASH_USERNAME: sandboxTokenizedUser02
BKASH_PASSWORD: sandboxTokenizedUser02@12345
BKASH_APP_KEY: 4f6o0cjiki2rfm34kfdadl1eqq
BKASH_APP_SECRET: 2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b
Test PIN: 12121, Test OTP: 123456
Insufficient-balance number: 01823074817; Debit-block number: 01823074818
```

**Getting your own sandbox creds**: contact bKash via the developer portal OR use SSLCOMMERZ (instant). Self-service signup at `developer.bka.sh` has been intermittent in 2024–2025 [arman.bd, 2024](https://arman.bd/bkash-api-login-guide/).

**Sandbox quirks**:
- **CORS fully disabled** on every sandbox endpoint — browser→bKash calls fail; proxy through your backend [rahulhaque demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo).
- `id_token` 1h TTL, `refresh_token` 28d TTL; client must refresh — no auto-refresh.
- Sandbox latency 5–10s; set client timeout to **30s** (bKash-recommended ceiling).
- Only the documented test numbers deterministically trigger error states; random numbers fail unpredictably.

---

## 10. Production Go-Live Checklist

1. KYC complete, merchant agreement signed.
2. BD scheduled-bank current account opened, account on file with bKash, test 1-BDT settlement received.
3. Production `app_key + app_secret + username + password` issued by bKash tech team, stored in secrets manager.
4. **TLS 1.2+** enforced on callback URL and webhook endpoint (bKash dropped TLS 1.0/1.1).
5. Success/fail/cancel callback URL registered.
6. Webhook endpoint registered, `SubscriptionConfirmationRequest` echoed, HMAC-SHA256 verifier deployed and unit-tested.
7. **IP whitelisting** — bKash whitelists merchant server IPs for production token API; submit egress IPs in advance. **Confirm exact requirement during onboarding** — not every merchant reports being asked.
8. **PCI requirements: none**. The customer enters PIN inside the bKash-hosted popup, never on your domain. (PCI still applies for the Stripe rail.)
9. Refund flow wired against `payment/refund` and tested end-to-end.
10. Daily reconciliation cron pulling Merchant Portal settlement reports vs internal ledger (see §11).
11. Flip `BKASH_BASE_URL` to `https://tokenized.pay.bka.sh/v1.2.0-beta/`, run a real 1-BDT live txn with team wallet, confirm T+1 settlement.

---

## 11. Known Failure Modes

Compiled from Bangladeshi developer community reports (Facebook PHP groups, GitHub issue threads on community SDKs, dev blog post-mortems):

1. **Token expiry mid-payment** — `id_token` expires 1 hour after grant; long-running checkout sessions fail with `Invalid Token`. **Mitigation**: refresh proactively at 50 minutes, or grant fresh token on every `payment/create`.
2. **Status-of-truth ambiguity after timeout** — if `payment/execute` times out (no response in 30s), the payment may have succeeded on bKash's side. Naive retry double-charges. **Mitigation**: never retry `execute` blindly; instead call `payment/status` with the `paymentID` and trust that as source of truth. This is the most common production bug.
3. **Webhook ordering** — IPN can arrive **before** the synchronous `execute` response in rare cases. **Mitigation**: idempotent state machine keyed on `paymentID`; webhook handler and execute handler converge to the same DB row.
4. **CORS in development** — devs forget that sandbox blocks CORS, try to call from React/Vue dev server, get cryptic network errors. **Mitigation**: always proxy through PHP backend (matches imeihub's stack anyway).
5. **Webhook subscription drift** — if the merchant URL changes (domain migration, path change), bKash silently stops delivering IPNs until re-subscribed. **Mitigation**: monitor webhook delivery rate; alert on >5 min gap during business hours.
6. **OTP delivery failures on Robi/Banglalink during peak hours** — bKash's OTP SMS sometimes lags 60–90 seconds. **Mitigation for tokenized flow**: prefer Agreement-based payments which skip OTP after first transaction.
7. **Refund timing** — refunds via API can take **24–72 hours** to reflect in the customer's bKash wallet despite immediate success response. Customer-support tickets common. **Mitigation**: set explicit refund-timeline expectations in the UI.
8. **Sandbox vs production parity gaps** — some error codes (network-level failures, partial settlements) cannot be reproduced in sandbox. **Mitigation**: run a controlled production canary phase before broad launch.
9. **HMAC verification mismatches** — JCS canonicalisation is strict; many community webhook listeners get this wrong and reject valid signatures. **Mitigation**: use a tested JCS implementation, log raw payload + computed hash side-by-side during initial deploy.
10. **Settlement report manual download** — Merchant Portal settlement reports must be fetched by manual login; no documented Reports API. Plan for a scraping/CSV ingest pipeline if you want automated reconciliation.

---

## 12. Competitor Sites Using bKash for Digital Service Payments

Bangladesh's digital-service market is dominated by super-apps that all expose bKash. Three examples relevant to imeihub:

1. **Pathao** (ride-hailing + food + parcel + Pathao Pay) — embeds the bKash popup inline. After completing a ride, user taps "Digital Payment → bKash" in the app, a secured bKash Payment Page renders inside the WebView, customer enters PIN, payment confirms, fare settles to Pathao. Standard tokenized-checkout flow with Agreement-based repeat billing. [bKash press release, 2018](https://www.bkash.com/node/2772), [The Financial Express, 2018-12-03](https://thefinancialexpress.com.bd/trade/pathao-to-accept-bkash-payment-1543841907).

2. **Foodpanda Bangladesh** — multi-rail checkout listing bKash alongside Nagad, Upay, Rocket, and COD. Foodpanda uses a tokenized-checkout integration so repeat orders skip the OTP step. Single-tap "Pay with bKash" is the de-facto default for urban customers. [Beyond Bracket — Top 50 E-commerce, 2026](https://beyondbracket.com/e-commerce-sites-in-bangladesh/).

3. **Pickaboo** (electronics + mobile e-commerce) — exposes bKash, Nagad, COD, card (Visa/MC/Amex), and EMI on its checkout. Pickaboo publishes a "decent digital transaction rate" and actively pushes customers toward bKash with cashback campaigns. Standard URL-based Checkout flow with bKash-hosted page. [The Daily Star — Pickaboo profile, 2023](https://www.thedailystar.net/supplements/accelerating-bangladesh/news/pickaboo-delivering-convenience-your-doorstep-3263106).

Bonus references with similar small-ticket flow more aligned to imeihub's micro-charge model: **Shohoz** bus-ticket "Pay with bKash + 10% cashback" promotion [Shohoz blog](https://blog.shohoz.com/tag/pay-with-bkash/) and **Chaldal** grocery delivery [Beyond Bracket, 2026](https://beyondbracket.com/e-commerce-sites-in-bangladesh/) — both demonstrate that bKash works fine for low-ticket (BDT 100–500) recurring purchases.

Notably absent from this list: a clean example of a **non-Bangladesh-incorporated** digital service running direct bKash. All three competitors above are Bangladeshi entities. Foreign-incorporated services routinely use SSLCOMMERZ as the aggregator wrapper rather than direct bKash.

---

## 13. Recommendation: **DEPENDS** — on imeihub's incorporation status

**Verdict**: **DEPENDS** — leaning toward "DO INTEGRATE, BUT VIA SSLCOMMERZ AGGREGATOR" rather than direct bKash PGW.

Three-bullet rationale:

- **Conversion case is overwhelming**: bKash is the *single* highest-leverage payment method for Bangladesh traffic. Bangladeshi visitors largely don't hold cards; Stripe (card + PromptPay) converts in the single-digit percentages there. Adding bKash to the imeihub checkout is the highest-ROI payment change available for Phase 2. The ticket-size economics work — at ~1.8% MDR with a documented BDT 1 minimum (~USD 0.0085), even $0.01 micro-charges are technically priced sanely. **Strong YES on offering bKash to BD users.**

- **Direct bKash PGW is blocked by incorporation**: bKash settles only to a Bangladesh scheduled-bank account held by a BD-registered entity with trade licence, e-TIN, VAT (BIN), and Bank Solvency Certificate. If imeihub is incorporated outside Bangladesh (which the Stripe-based current stack implies), it cannot get a direct PGW agreement. The realistic 2026 path for a foreign-incorporated micro-charge SaaS is **SSLCOMMERZ aggregator** (PSO-licensed, supports international merchants, exposes bKash + Nagad + Rocket + cards behind one API, 2.5% standard MDR but negotiable). This trades ~0.5–0.7pp of extra fee for vastly simpler onboarding and FX-conversion settlement to a foreign bank account.

- **Operational complexity is non-trivial for a small team**: official PHP SDK is non-existent (only a 2023 demo backend); webhook subscription is manual and gated by the bKash tech team; settlement reports require manual portal logins; the IPN signature scheme requires correct JCS implementation; and direct sales-team-driven onboarding takes 2–6 weeks with no SLA. For a free + micro-charge service, the engineering cost of *direct* bKash integration is hard to justify versus letting SSLCOMMERZ absorb that complexity for an extra ~70 bps.

**Phase 2 action**: write a parallel `PR-SSLCOMMERZ-01` note before deciding final integration architecture, then build against SSLCOMMERZ with bKash as the visible payment-method brand at checkout.

---

## 14. Biggest Unknown

**If resolved, would flip recommendation either direction**:

> **Can SSLCOMMERZ (or a comparable aggregator) settle in USD to a foreign bank account of a non-Bangladeshi entity, on T+? days, with the exact effective MDR after FX conversion documented in writing?**

This is the load-bearing assumption of the §13 recommendation. If SSLCOMMERZ in fact requires a BD bank account (some merchant-aggregator pages hint at this in fine print), then *no* viable path exists short of incorporating a Bangladesh entity — at which point the integration question becomes a corporate-structure question first. Conversely, if SSLCOMMERZ delivers clean USD settlement at <2.5% effective, the recommendation hardens to a confident YES on day-one Phase 2 prioritisation.

This question should be the first item on the Phase 2 kickoff agenda with SSLCOMMERZ sales.

---

## Sources

- [bKash Developer Portal (Beta)](https://developer.bka.sh/) — official API documentation entry point (gated, 403 to non-browser clients at time of writing).
- [bKash Merchant Page](https://www.bkash.com/en/business/merchant) — merchant onboarding overview (accessed 2026-05-28).
- [bKash Supplier Portal — Required Documents](https://scp.bkash.com/registration-required-document) — KYC document list.
- [bKash Contact](https://www.bkash.com/en/help/contact-us) — merchant sales contact channels.
- [bKash Tokenized Checkout product page](https://www.bkash.com/en/page/tokenized_checkout) — product overview.
- [bKash Personal Retail Account](https://www.bkash.com/en/page/personal-retail-account) — PRA tier limits (BDT 999 offline / BDT 2,000 online).
- [bKash Limits page](https://www.bkash.com/en/help/limits) — public charge calculator and limits.
- [TOKENIZED CHECKOUT API Integration Guide v 0.3 — bKash S3 PDF](https://s3-ap-southeast-1.amazonaws.com/developer.pay.bka.sh/Tokenized+Checkout+-+API+Integration+Guide.pdf) — official integration guide.
- [bKash Merchant Portal — settlement reports](https://merchantportal.bkash.com/) — merchant portal entry point.
- [bKash-developer GitHub organisation](https://github.com/bKash-developer) — official sample code repos (webhook-endpoint-php, pgw-merchant-backend-php, bKash-for-woocommerce, demo Android/iOS).
- [bKash press: Pathao integration, 2018](https://www.bkash.com/node/2772) — competitor reference.
- [The Financial Express — Pathao to accept bKash, 2018-12-03](https://thefinancialexpress.com.bd/trade/pathao-to-accept-bkash-payment-1543841907) — competitor reference.
- [The Business Standard — MFS market flourishes, transactions surge to Tk 3.84 lakh crore, 2025-02-12](https://www.tbsnews.net/economy/banking/mfs-market-flourishes-transactions-surge-tk384-lakh-crore-2024-1072141) — 2024 industry numbers.
- [Future Startup — State of MFS in Bangladesh, 2025-05-06](https://futurestartup.com/2025/05/06/the-state-of-mobile-financial-services-mfs-industry-in-bangladesh/) — bKash user count, profit, market share.
- [Bangladesh Bank — MFS data](https://www.bb.org.bd/en/index.php/financialactivity/mfsdata) — regulator-published MFS statistics.
- [Bangladesh Bank — Foreign Exchange Transaction Guidelines, Vol 1](https://www.bb.org.bd/aboutus/regulationguideline/foreignexchange/fegv1cont.php) — cross-border regulation context.
- [Bangladesh Bank — PSO/PSP Approval Procedure (2019 PDF)](https://www.bb.org.bd/aboutus/regulationguideline/psd/pso_psp_03022019.pdf) — licensing framework.
- [LegalSeba — Fintech licensing in Bangladesh (MFS / Digital Bank / PSP / PSO)](https://legalseba.com/bd-licenses/ultimate-guide-to-fintech-licensing-in-bangladesh-mfs-digital-bank-psp-pso/) — regulatory commentary.
- [Moneybag — Gateway Fees Breakdown: bKash vs Cards, 2026](https://moneybag.com.bd/gateway-fees-breakdown-bkash-vs-cards/) — fee data 2026.
- [Moneybag — 10 Best Payment Gateways in Bangladesh](https://moneybag.com.bd/10-best-payment-gateways-in-bangladesh/) — gateway comparison.
- [Financfy — Best Payment Gateway in Bangladesh, 2026](https://financfy.com/blog/payment-gateway-in-bangladesh/) — SSLCOMMERZ vs direct comparison.
- [BanglaCyber — Payment gateway options in Bangladesh](https://www.banglacyber.com/payment-gateway-options-in-bangladesh/) — overview article.
- [Geekssort — Payment Gateway Integration for Bangladeshi Businesses 2026](https://geekssort.com/payment-gateway-integration-bangladesh-2026/) — 2026 integration guide.
- [paymentgateways.org — bKash Merchant Account review](https://paymentgateways.org/payment/bkash) — third-party merchant review.
- [SSLCOMMERZ](https://sslcommerz.com/) — Bangladesh PSO-licensed aggregator (recommended fallback for foreign merchants).
- [Ria Money Transfer — bKash inbound transfers](https://www.riamoneytransfer.com/en-us/send-money-to-bkash/) — cross-border BDT-only confirmation.
- [arman.bd — bKash API login guide, 2024](https://arman.bd/bkash-api-login-guide/) — developer-onboarding commentary.
- [onecodesoft — bKash Sandbox Credentials for Payment Gateway Testing](https://onecodesoft.com/blogs/bkash-sandbox-credentials-for-payment-gateway-testing) — public sandbox credentials reference.
- [rahulhaque/bKash-payment-gateway-web-demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo) — community integration demo (endpoints, auth flow, CORS warning).
- [HackMD — bKash Webhook API documentation](https://hackmd.io/gH3NRGHvTgCTlkBul17BdQ) — community summary of HMAC-SHA256 + JCS signing.
- [bKash-developer/webhook-endpoint-php](https://github.com/bKash-developer/webhook-endpoint-php) — official PHP webhook listener sample.
- [Beyond Bracket — Top 50 E-commerce Sites in Bangladesh 2026](https://beyondbracket.com/e-commerce-sites-in-bangladesh/) — competitor inventory.
- [The Daily Star — Pickaboo profile, 2023](https://www.thedailystar.net/supplements/accelerating-bangladesh/news/pickaboo-delivering-convenience-your-doorstep-3263106) — Pickaboo case context.
- [Shohoz blog — Pay with bKash](https://blog.shohoz.com/tag/pay-with-bkash/) — competitor reference.
