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

Compiled from Bangladeshi developer community reports (Facebook PHP groups, GitHub issues on community SDKs, dev blog post-mortems):

1. **Token expiry mid-payment** — `id_token` 1h TTL; long checkout sessions fail with `Invalid Token`. *Mitigation*: refresh at 50 min or grant fresh on every `payment/create`.
2. **Status-of-truth ambiguity after timeout** — if `payment/execute` times out the payment may already have succeeded. Naive retry double-charges. *Mitigation*: never retry `execute` blindly — call `payment/status` and trust it. Most common production bug.
3. **Webhook ordering** — IPN can arrive **before** the synchronous `execute` response. *Mitigation*: idempotent state machine keyed on `paymentID`.
4. **CORS in dev** — sandbox blocks CORS; dev-server calls fail cryptically. *Mitigation*: always proxy through PHP backend (matches imeihub's stack).
5. **Webhook subscription drift** — if the URL changes, bKash silently stops delivering IPNs until re-subscribed. *Mitigation*: monitor delivery rate, alert on >5 min gap during business hours.
6. **OTP delivery lag on Robi/Banglalink** during peak hours (60–90s delays). *Mitigation*: prefer Agreement-based tokenized flow which skips OTP after first txn.
7. **Refund visibility lag** — API refunds can take **24–72h** to show in customer's wallet despite immediate success response. *Mitigation*: set UI expectations.
8. **Sandbox/production parity gaps** — some network-error codes and partial-settlement scenarios are not reproducible in sandbox. *Mitigation*: production canary before broad launch.
9. **HMAC/JCS mismatch** — JCS canonicalisation is strict; many community listeners reject valid signatures. *Mitigation*: use a tested JCS implementation, log raw payload + computed hash during initial deploy.
10. **No Reports API** — Merchant Portal settlement reports require manual login. *Mitigation*: plan a scraping/CSV pipeline for automated reconciliation.

---

## 12. Competitor Sites Using bKash for Digital Service Payments

1. **Pathao** (ride-hail + food + parcel + Pathao Pay) — embeds the bKash popup inline. After a ride, user taps "Digital Payment → bKash", PIN-only confirm inside WebView, fare settles to Pathao. Tokenized Checkout with Agreement-based repeat billing. [bKash press, 2018](https://www.bkash.com/node/2772), [The Financial Express, 2018-12-03](https://thefinancialexpress.com.bd/trade/pathao-to-accept-bkash-payment-1543841907).
2. **Foodpanda Bangladesh** — multi-rail checkout (bKash + Nagad + Upay + Rocket + COD). Tokenized Checkout so repeat orders skip OTP; single-tap bKash is the urban default. [Beyond Bracket — Top 50 E-commerce, 2026](https://beyondbracket.com/e-commerce-sites-in-bangladesh/).
3. **Pickaboo** (electronics e-commerce) — bKash + Nagad + COD + Visa/MC/Amex + EMI. Uses standard URL-based Checkout with cashback campaigns pushing customers toward bKash. [The Daily Star — Pickaboo, 2023](https://www.thedailystar.net/supplements/accelerating-bangladesh/news/pickaboo-delivering-convenience-your-doorstep-3263106).

Aligned to imeihub's micro-charge model: **Shohoz** bus-ticket "Pay with bKash + 10% cashback" [Shohoz blog](https://blog.shohoz.com/tag/pay-with-bkash/) and **Chaldal** grocery — both prove bKash works for low-ticket (BDT 100–500) repeat purchases.

**Notably absent**: a clean example of a **non-Bangladesh-incorporated** service running direct bKash. All three above are Bangladeshi entities. Foreign-incorporated services route via SSLCOMMERZ.

---

## 13. Recommendation: **DEPENDS** — on imeihub's incorporation status

**Verdict**: **DEPENDS** — leaning "DO INTEGRATE, BUT VIA SSLCOMMERZ AGGREGATOR" rather than direct bKash PGW.

- **Conversion**: bKash is the single highest-leverage payment method for BD traffic. Stripe converts in the single digits; ticket economics work at ~1.8% MDR with a BDT 1 minimum. **Strong YES on offering bKash to BD users.**
- **Direct PGW blocked by incorporation**: bKash settles only to BD bank accounts held by BD-registered entities. If imeihub is incorporated outside Bangladesh, the realistic 2026 path is **SSLCOMMERZ** (PSO-licensed, international-merchant onboarding, bKash + Nagad + Rocket + cards behind one API, 2.5% standard, negotiable) — trading ~70 bps for simpler onboarding and FX-conversion to a foreign bank.
- **Operational cost**: no official PHP SDK, manual webhook subscription, manual portal logins for settlement, finicky JCS/HMAC, 2–6 week sales-driven onboarding with no SLA. For a micro-charge service, the engineering cost of *direct* bKash is hard to justify vs SSLCOMMERZ.

**Phase 2 action**: write a parallel `PR-SSLCOMMERZ-01` note before locking the architecture, then build against SSLCOMMERZ with bKash as the visible payment brand at checkout.

---

## 14. Biggest Unknown

> **Can SSLCOMMERZ (or a comparable aggregator) settle in USD to a foreign bank account of a non-Bangladeshi entity, on T+? days, with the effective MDR after FX conversion documented in writing?**

This is the load-bearing assumption of §13. If SSLCOMMERZ in fact requires a BD bank account (some pages hint at this in fine print), *no* viable path exists short of incorporating a BD entity — at which point this becomes a corporate-structure question first. If SSLCOMMERZ delivers clean USD settlement at <2.5% effective, the recommendation hardens to a confident YES. First item for the Phase 2 kickoff agenda.

---

## Sources

**Official bKash**
- [bKash Developer Portal (Beta)](https://developer.bka.sh/)
- [bKash Merchant page](https://www.bkash.com/en/business/merchant)
- [bKash Supplier — Required Documents](https://scp.bkash.com/registration-required-document)
- [bKash Contact](https://www.bkash.com/en/help/contact-us)
- [bKash Tokenized Checkout](https://www.bkash.com/en/page/tokenized_checkout)
- [bKash Personal Retail Account](https://www.bkash.com/en/page/personal-retail-account)
- [bKash Limits](https://www.bkash.com/en/help/limits)
- [Tokenized Checkout API Integration Guide v0.3 (PDF)](https://s3-ap-southeast-1.amazonaws.com/developer.pay.bka.sh/Tokenized+Checkout+-+API+Integration+Guide.pdf)
- [bKash Merchant Portal](https://merchantportal.bkash.com/)
- [bKash-developer GitHub organisation](https://github.com/bKash-developer)
- [bKash-developer/webhook-endpoint-php](https://github.com/bKash-developer/webhook-endpoint-php)
- [bKash press: Pathao integration, 2018](https://www.bkash.com/node/2772)

**Regulator & policy**
- [Bangladesh Bank — MFS data](https://www.bb.org.bd/en/index.php/financialactivity/mfsdata)
- [Bangladesh Bank — Foreign Exchange Guidelines Vol 1](https://www.bb.org.bd/aboutus/regulationguideline/foreignexchange/fegv1cont.php)
- [Bangladesh Bank — PSO/PSP Approval Procedure, 2019 PDF](https://www.bb.org.bd/aboutus/regulationguideline/psd/pso_psp_03022019.pdf)
- [LegalSeba — Fintech licensing in Bangladesh](https://legalseba.com/bd-licenses/ultimate-guide-to-fintech-licensing-in-bangladesh-mfs-digital-bank-psp-pso/)

**Market & industry**
- [The Financial Express — Pathao to accept bKash, 2018-12-03](https://thefinancialexpress.com.bd/trade/pathao-to-accept-bkash-payment-1543841907)
- [The Business Standard — MFS market flourishes, 2025-02-12](https://www.tbsnews.net/economy/banking/mfs-market-flourishes-transactions-surge-tk384-lakh-crore-2024-1072141)
- [Future Startup — State of MFS in Bangladesh, 2025-05-06](https://futurestartup.com/2025/05/06/the-state-of-mobile-financial-services-mfs-industry-in-bangladesh/)
- [Ria — bKash inbound transfers](https://www.riamoneytransfer.com/en-us/send-money-to-bkash/)

**Gateway comparisons & fees (2026)**
- [Moneybag — Gateway Fees Breakdown: bKash vs Cards](https://moneybag.com.bd/gateway-fees-breakdown-bkash-vs-cards/)
- [Moneybag — 10 Best Payment Gateways in Bangladesh](https://moneybag.com.bd/10-best-payment-gateways-in-bangladesh/)
- [Financfy — Best Payment Gateway in Bangladesh 2026](https://financfy.com/blog/payment-gateway-in-bangladesh/)
- [Geekssort — Payment Gateway Integration for Bangladeshi Businesses 2026](https://geekssort.com/payment-gateway-integration-bangladesh-2026/)
- [paymentgateways.org — bKash Merchant Account review](https://paymentgateways.org/payment/bkash)
- [SSLCOMMERZ](https://sslcommerz.com/)

**Developer references**
- [arman.bd — bKash API login guide, 2024](https://arman.bd/bkash-api-login-guide/)
- [onecodesoft — Sandbox credentials](https://onecodesoft.com/blogs/bkash-sandbox-credentials-for-payment-gateway-testing)
- [rahulhaque/bKash-payment-gateway-web-demo](https://github.com/rahulhaque/bKash-payment-gateway-web-demo)
- [HackMD — bKash Webhook API summary](https://hackmd.io/gH3NRGHvTgCTlkBul17BdQ)

**Competitor references**
- [Beyond Bracket — Top 50 E-commerce in Bangladesh 2026](https://beyondbracket.com/e-commerce-sites-in-bangladesh/)
- [The Daily Star — Pickaboo profile, 2023](https://www.thedailystar.net/supplements/accelerating-bangladesh/news/pickaboo-delivering-convenience-your-doorstep-3263106)
- [Shohoz — Pay with bKash](https://blog.shohoz.com/tag/pay-with-bkash/)
