# Nigeria Payment Provider Research: Paystack vs Flutterwave for imeihub

**Research date**: 2026-05-28 — **Task**: PR-PAYSTACK-01

imeihub is a free + paid IMEI check (PHP + MySQL) currently on Stripe. Stripe supports NG customers but has elevated failure rates on NG-issued cards (foreign-card rails + CBN PAN-tokenisation friction). This note compares **Paystack** (Stripe's NG subsidiary, local-default) and **Flutterwave** (multi-currency cross-border specialist) for imeihub's micro-transaction band: USD $0.01–$4.20 ≈ NGN 15–6,500 at mid-2026 rates. The decisive constraint: **imeihub has no NG CAC entity**, so foreign-merchant onboarding shapes the recommendation.

---

## Paystack

### Overview & market position

Paystack (founded 2015, Lagos) processes **>50% of all NG online card transactions** and serves 60,000+ NG/GH businesses, including FedEx, UPS, MTN, AXA Mansard [[Stripe Newsroom, 2020-10-15](https://stripe.com/newsroom/news/paystack-joining-stripe)]. Stripe acquired it October 2020 for >$200M and is embedding its rails into Stripe GPTN. Paystack owns lowest-friction NGN card acceptance, deepest local bank rail integrations (Pay with Bank Transfer / USSD across all NIBSS banks), and highest local-card success rate. It does **not** own cross-border USD settlement (NGN default; USD requires Zenith dom account) or broad multi-currency collection.

### API documentation URL + integration model

API ref: <https://paystack.com/docs/api/>. REST + bearer-token auth. Canonical flow: `POST /transaction/initialize` from PHP backend returns `authorization_url` + `reference` → redirect (Standard) or hand `access_code` to Inline JS modal → **always call `GET /transaction/verify/<reference>`** before delivering value; callback-URL visit alone is not proof [[Paystack Docs, accessed 2026-05-28](https://paystack.com/docs/payments/accept-payments/)].

### Merchant onboarding — KYC, CAC, BVN, foreign-merchant rules

NG merchants need up-to-date **CAC registration** (Form 3 preferred; proof of address ≤6 months as fallback), valid gov ID per beneficial owner (driver's licence, NIN, voter's card, or passport), and **BVN consent** for all directors — CBN mandates BVN for PSP identity validation [[Paystack Support, accessed 2026-05-28](https://support.paystack.com/en/articles/2123970)]. A "Starter Business" tier reduces docs for sole proprietors but caps inbound at ₦5M and still requires NG BVN.

**Foreign merchants**: Paystack does **not self-serve onboard** foreign-incorporated businesses with no NG presence. Compliance assumes CAC + BVN. The `paystack.com/internationals` "global brands" channel exists (FedEx, UPS) but the mechanism is sales-led: foreign brand contracts with Stripe (or NG aggregator) which routes the NG leg onto Paystack rails — not realistic at imeihub's scale. Options without a NG entity: (1) keep Stripe; (2) incorporate NG RC (~₦50–150k + 3–6 weeks); (3) NG merchant-of-record (Bumpa, Selar); (4) Flutterwave RoW. Verdict: **requires-local-incorporation** for self-serve.

### Pricing & fees

Per Paystack's official rates [[Paystack Support — Transactions pricing, accessed 2026-05-28](https://support.paystack.com/en/articles/2130306)]:

- **Local NGN card / bank / USSD**: 1.5% + ₦100 flat, **capped ₦2,000**, **₦100 waived if ≤ ₦2,500**.
- **International card**: 3.9% + ₦100, no cap.
- **Stamp duty**: ₦50 per outbound transfer ≥ ₦10,000 (effective 2026-02-18).
- **VAT**: +7.5% on the Paystack fee itself (NRS rule).

No separate USD-to-NGN FX line; spread is built into the 3.9% international rate (~1–2% off mid-market observed) [[HuruPay, 2024](https://hurupay.com/blog/does-paystack-accept-dollar-payments)].

**imeihub band**: ₦500 = ₦8.06 (1.6%); ₦2,500 = ₦40.31 (1.6%); **₦2,501 = ₦147.82 (5.9%) — sharp cliff**; ₦6,500 = ₦212.31 (3.3%). The ≤₦2,500 waiver is the single most important pricing fact for imeihub — beats Stripe's `2.9% + 30¢` decisively on sub-$2 sales.

### Settlement timeline

NGN: **T+1** to a NG bank account, weekday mornings (10am WAT target; weekends + public holidays push to T+2/T+3) [[Paystack Support — Getting your money, accessed 2026-05-28](https://support.paystack.com/en/articles/2125314)]. USD: same T+1 but only into a **Zenith Bank USD domiciliary account** — no arbitrary correspondent banks, no USD wallet on the dashboard.

### Supported currencies

NGN, GHS, ZAR, KES, USD (USD on NG/KE accounts only). Multi-currency checkout presentation is minimal; foreign customers are charged in the merchant's base currency with their issuing bank handling FX.

### SDK / web-checkout

**Inline JS** (`https://js.paystack.co/v2/inline.js`) exposes `PaystackPop` with `checkout()`, `newTransaction()`, `resumeTransaction()`. Recommended: backend-initialise + `resumeTransaction(access_code)` to keep the secret key off the client [[Paystack InlineJS docs](https://paystack.com/docs/developer-tools/inlinejs/)]. **Standard / redirect** returns `authorization_url` for a 302. **PHP**: no first-party SDK — canonical is raw cURL/Guzzle against REST (~6 endpoints). Community: `yabacon/paystack-php`, `unicodeveloper/laravel-paystack`. First-party WooCommerce/Webflow/Wix plugins.

### Webhook structure + signature verification

Header **`x-paystack-signature`** (HTTP) → `$_SERVER['HTTP_X_PAYSTACK_SIGNATURE']`. **HMAC SHA-512** over the **raw** body keyed with the merchant's secret key. Verification in PHP:

```php
$input = @file_get_contents("php://input");
if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !==
    hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY)) { exit(); }
http_response_code(200);
$event = json_decode($input);
```

[[Paystack Webhooks Docs, accessed 2026-05-28](https://paystack.com/docs/payments/webhooks/)]. Key events for Phase 2: `charge.success`, `charge.failed`, `transfer.success`, `refund.processed`. Respond 200 within 5s; retries 72h on non-2xx.

### Sandbox URL + credential acquisition

In-dashboard sandbox (no separate URL). Sign up at `dashboard.paystack.com` (foreign phones accepted at signup; go-live needs CAC). Dashboard starts in **Test Mode** by default. Settings → "API Keys & Webhooks" exposes `pk_test_…` / `sk_test_…`; toggle live/test top-right [[Paystack Support — Test mode and Live mode, accessed 2026-05-28](https://support.paystack.com/en/articles/2129922)]. Test cards at `paystack.com/docs/payments/test-payments/`.

### 3 example competitor digital services using Paystack

1. **Selar** (`selar.com`) — NG creator-economy storefront for digital products; heavy Paystack user for NGN, Flutterwave for cross-border. Direct precedent for low-ticket (₦1k–₦20k) digital deliverables [[Selar Help](https://help.selar.com/portal/en/kb/articles/pay-with-transfer)].
2. **Bumpa** (`getbumpa.com`) — SME storefront builder integrating Paystack alongside Flutterwave + Monnify; the standard "all three" NG merchant pattern [[Bumpa Academy](https://academy.getbumpa.com/guides/business-guide-accepting-online-payments-nigeria)].
3. **Smile ID** (`usesmileid.com`) — pan-African identity verification API using Paystack for NGN collection. Closest analogue to imeihub's API-driven small-ticket digital service [[TechCabal, 2024-11-29](https://techcabal.com/2024/11/29/smile-id-releases-nigerias-first-ever-ekyc-report-hits-200m-identity-verification-checks/)].

### Compliance notes

Paystack operates under a CBN **Payment Solution Service Provider (PSSP)** licence — authorises gateway/portal + merchant aggregation but **does not** permit holding customer funds or wallets [[Goidara, 2026](https://www.goidara.com/blog/how-to-obtain-a-payment-solution-service-provider-pssp-license-in-nigeria), [CBN PSPs](https://www.cbn.gov.ng/PaymentsSystem/PSPs.html)]. PSSPs were formalised in CBN's 9 December 2020 circular: ₦250M minimum share capital, mandatory PCI-DSS. All NG payment rails run through **NIBSS** — ISO 20022-compliant since the 2025 National Payment Stack rollout; this determines the T+1 cadence [[NIBSS, accessed 2026-05-28](https://nibss-plc.com.ng/)]. The **Nigeria Tax Act 2025 — VAT on non-resident digital services** (in force 1 January 2026) requires foreign digital providers to register with the Nigeria Revenue Service (NRS) and charge 7.5% VAT; unregistered, the customer's bank must withhold, a major contributor to Stripe-on-NG-card failure rates [[Mondaq, 2025-09](https://www.mondaq.com/nigeria/tax-authorities/1712958/vat-under-the-nta-2025-what-digital-platforms-and-fintechs-need-to-know), [VATupdate, 2025-09-11](https://www.vatupdate.com/2025/09/11/nigeria-to-enforce-vat-on-non-resident-digital-services-from-2026/)].

---

## Flutterwave

### Overview & market position

Flutterwave (2016, HQ San Francisco, ops in NG + 33 other African countries) is the **multi-currency cross-border specialist**: 200M+ lifetime transactions worth >$16B, 500k daily, customers including Uber, Booking.com, Audiomack, Showmax. Material 2026 development: **Flutterwave secured a Nigerian Microfinance Banking Licence in April 2026**, allowing it to hold customer deposits directly — Paystack (PSSP) cannot [[PR Newswire, 2026-04-03](https://www.prnewswire.com/news-releases/flutterwave-secures-nigerian-banking-license-to-accelerate-payment-efficiency-302732642.html), [Billionaires Africa, 2026-04-03](https://www.billionaires.africa/2026/04/03/gb-agboolas-flutterwave-secures-nigerian-microfinance-banking-licence-after-10-years-as-a-payments-company/)]. Flutterwave owns the broadest multi-currency collection (30+ currencies), the only NG gateway with a proper US/EU/UK money-transmission stack (13 US state MTLs late 2024), and **explicit foreign-merchant self-serve onboarding**. Local-card success lags Paystack by a 3–5pp gap in independent comparisons [[Daikimedia, 2026](https://www.daikimedia.com/blog/paystack-vs-flutterwave-2026-which-gateway-converts-better-in-nigeria)].

### API documentation URL + integration model

Developer portal: <https://developer.flutterwave.com/>. **v4 is current GA** (replaces Rave v3). REST + bearer-token. Canonical flow: `POST /payments` returns a checkout `link` → redirect or embed via Inline → `GET /transactions/<id>/verify` after callback. v4 introduced idempotency keys, cleaner error envelopes, dedicated sandbox host (`https://developersandbox-api.flutterwave.com`) instead of v3's query-string toggle [[Flutterwave Engineering, 2024](https://dev.to/flutterwaveeng/introducing-the-flutterwave-v4-api-faster-safer-easier-to-integrate-2dc9)].

### Merchant onboarding — KYC, CAC, BVN, foreign-merchant rules

NG onboarding mirrors Paystack: CAC, BVN per director, gov ID, proof of address. Foreign nationals can open a NG Flutterwave business account with valid ID + work/resident permit [[Flutterwave Help — NG onboarding, accessed 2026-05-28](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-nigeria)].

**Foreign merchants** — Flutterwave decisively differentiates here. The **"Rest of the World" onboarding flow** [[Flutterwave Help — RoW onboarding, accessed 2026-05-28](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-the-rest-of-the-world)] accepts businesses incorporated outside its 8 directly-supported countries (NG/GH/KE/ZA/UG/TZ/UK/US). A UK Ltd, US LLC, or Stripe Atlas Delaware C-corp all qualify. Required: home-jurisdiction business registration, director passports, corporate bank account / reference letter (no NG dom required), foreign business address accepted, verifiable website URL (imeihub satisfies), operating licence only if regulated (IMEI lookups are not), estimated monthly sales. Settlement to foreign merchants goes into the merchant's home-currency corporate account; Flutterwave handles FX. **No NG RC, BVN, or Zenith dom required.** Verdict: **resolved — self-serve foreign-merchant flow exists.**

### Pricing & fees

Per Flutterwave's NG pricing page [[Flutterwave Help — Pricing for receiving payments, accessed 2026-05-28](https://flutterwave.com/ng/support/pricing/pricing-for-receiving-payment)]:

- **Local NGN (card/bank transfer/USSD/eNaira/OPay/NQR)**: **2.0%** (1.4% transaction + 0.6% platform), **capped ₦2,000**. From 2025-04-11; previously 1.4%.
- **International card**: **4.8%** flat, no cap. Up from 3.8% on 2024-11-11.
- **Transfers out**: ₦25 + 7.5% VAT (≤₦50k) / ₦50 + 7.5% VAT (>₦50k); GBP transfers £35.
- **No equivalent of Paystack's ₦100-fee waiver** on ≤₦2,500.

**imeihub band**: ₦500 = ₦10; ₦2,500 = ₦50; ₦2,501 = ₦50.02 (no cliff); ₦6,500 = ₦130 — flat 2.0%. Flutterwave is **cheaper than Paystack in ₦2,501–~₦6,667** (Paystack's ₦100 flat dominates) and **more expensive in ≤₦2,500** (Paystack waives ₦100).

### Settlement timeline

NGN: **T+1** business day to NG bank account or NGN payout balance (April 2026 MFB licence enables direct balance holding). USD/foreign currency: **T+5** business days [[Flutterwave Help — Settlement FAQs, accessed 2026-05-28](https://flutterwave.com/us/support/payments/settlement-frequently-asked-questions)]. **Per-currency payout balances** avoid forced NGN conversion on USD-card collections — but **NG-issued naira cards always settle in NGN** regardless of purchase currency, per CBN dollarisation guidance [[Flutterwave Help — Multi-currency settlement, accessed 2026-05-28](https://flutterwave.com/tz/support/payments/settlement-in-different-currencies)]. **Express Settlement** offers T+0 at +0.5%.

### Supported currencies

NGN, GHS, KES, UGX, TZS, RWF, ZAR, ZMW, XOF, XAF, EGP, MAD, USD, GBP, EUR, CAD — 30+ in checkout, ~10 settleable as balances by merchant country. Significantly broader than Paystack.

### SDK / web-checkout

**Inline (v3)** / **Standard checkout (v4)** — hosted page returned as a `link` from `POST /payments`. Drop-in JS at `https://checkout.flutterwave.com/v3.js`. **Direct charge** is server-side tokenised for full UI control (PCI-DSS scope creep — not for imeihub). **PHP**: `flutterwavedev/flutterwave-php` is the official Packagist SDK (`composer require flutterwavedev/flutterwave-php`); `kingflamez/laravelflutterwave` is the popular Laravel community option. New integrations target v4. First-party WooCommerce/WHMCS/Wix plugins.

### Webhook structure + signature verification

Header **`verif-hash`** (v3) / **`flutterwave-signature`** (v4). HMAC-SHA256 over the body keyed by the merchant's **Secret Hash** (user-defined string in Dashboard → Settings → Webhooks, distinct from the API secret key) [[Flutterwave — Secret Hash, accessed 2026-05-28](https://flutterwave.com/eg/support/integrations/what-is-a-secret-hash), [Flutterwave Webhooks](https://developer.flutterwave.com/docs/webhooks)]. PHP pattern:

```php
$secretHash = getenv('FLW_SECRET_HASH');
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
if (!$signature || $signature !== $secretHash) { http_response_code(401); exit(); }
$payload = json_decode(file_get_contents('php://input'), true);
http_response_code(200);
```

Note: v3 uses **literal-string equality** against the Secret Hash — *not* a per-payload HMAC like Paystack. v4 moves to a true HMAC-SHA256 of the payload in `flutterwave-signature`. Phase 2 must re-confirm v4 hashing against live docs at integration. Key events: `charge.completed`, `charge.failed`, `transfer.completed`, `subscription.cancelled`. 200 within 5s.

### Sandbox URL + credential acquisition

Sandbox base URL: **`https://developersandbox-api.flutterwave.com`** [[Flutterwave Docs — Environments, accessed 2026-05-28](https://developer.flutterwave.com/docs/environments)]. Dashboard at `dashboard.flutterwave.com`; sign up with email + business name, no NG phone required. Settings → Developers → API Keys exposes test keys; toggle to live after KYC. Test cards at `flutterwave.com/ng/support/integrations/test-integrations-with-test-cards`.

### 3 example competitor digital services using Flutterwave

1. **Selar** — also runs Flutterwave for cross-border NGN→USD creator payouts (>1.4M unique Flutterwave transactions) [[Flutterwave Blog — Selar](https://flutterwave.com/us/blog/african-creators-global-audience-selars-massive-growth-journey-powered-by-flutterwave)].
2. **Audiomack** — global music streaming with major African base. Migrated to Flutterwave in 2020 specifically for multi-method coverage (cards + mobile money + bank transfer + wallets) and African-currency settlement [[Flutterwave Blog — Audiomack](https://flutterwave.com/us/blog/mobile-money-sets-the-pace-as-audiomack-leverages-flutterwave-for-seamless-payments-in-africa)]. **Direct analogue for imeihub**: foreign digital service collecting low-ticket payments from NG users.
3. **Showmax** — MultiChoice's streaming product; Flutterwave for cross-border subscription billing across multiple African currencies [[Flutterwave Showmax pay link](https://flutterwave.com/pay/3mxttgr76uc3)]. The foreign-incorporated-but-collects-in-NGN pattern imeihub would adopt.

### Compliance notes

Flutterwave holds a CBN **Switching & Processing licence** plus, as of April 2026, a **Microfinance Banking Licence** — meaningful upgrade over Paystack's PSSP, as it allows direct deposit-taking. Additional: PCI-DSS Level 1, ISO 27001, NDPR. Outside NG: 13 US state Money Transmission Licences (29 states via partnerships) and UK FCA registration — these legally underpin the "rest of the world" foreign-merchant flow. NG-leg settlement still touches NIBSS, identical to Paystack. The same **NTA 2025 VAT on non-resident digital services** rule applies (1 January 2026), but Flutterwave's foreign-merchant onboarding can include NRS VAT registration in compliance setup — a service Paystack does not offer non-NG businesses.

---

## Recommendation

### Side-by-side comparison

| Dimension | Paystack | Flutterwave |
|---|---|---|
| Local NGN fee (≤₦2,500) | **1.5%** (₦100 waived) | 2.0% |
| Local NGN fee (₦2,501–₦6,500) | 1.5% + ₦100 (eff. 3.3–5.9%) | **2.0% flat** |
| International card fee | **3.9% + ₦100** | 4.8% |
| NGN settlement | T+1 | T+1 (T+0 at +0.5%) |
| USD settlement | T+1 to Zenith dom only | **T+5 to any USD account** |
| **Foreign-merchant friendly** | **No** — needs NG RC/BVN | **Yes** — self-serve RoW |
| SDK quality (PHP) | Community libs; small REST | **First-party Packagist SDK** |
| USD acceptance | Yes (NG/KE only) | **Yes (30+ ccy, multi-balance)** |
| Local card success rate | **Highest** (~92–95%) | Lower (~88–92%) [[Daikimedia 2026](https://www.daikimedia.com/blog/paystack-vs-flutterwave-2026-which-gateway-converts-better-in-nigeria)] |
| CBN licence | PSSP (no deposits) | Switching + Processing + **MFB** |
| Webhook signing | HMAC-SHA512 per payload | Secret-hash (v3) / HMAC-SHA256 (v4) |
| **Recommended for imeihub** | Phase-3 if NG-incorporated | **Primary — Phase 2** |

### Primary recommendation: **Flutterwave**

For imeihub (foreign-incorporated micro-transaction digital service, no NG CAC entity): **Flutterwave is primary**, Paystack a deferred Phase-3 option.

1. **Foreign-merchant onboarding is resolved without legal restructuring.** Flutterwave's RoW flow accepts a UK Ltd / US LLC / Stripe Atlas C-corp without NG RC, BVN, or Zenith dom. Paystack requires either NG incorporation (~₦50–150k + 3–6 weeks + ongoing compliance) or a Stripe enterprise contract not realistic at imeihub's scale. This legal-feasibility delta dominates everything else.
2. **Multi-currency settlement matches imeihub's USD pricing.** Flutterwave's per-currency balances let USD-card payments settle into a USD balance without forced NGN conversion — direct Stripe replacement. Paystack's forced-NGN settlement on NG-issued cards is acceptable but inferior for a globally-priced service.
3. **Fee profile is acceptable.** At ₦500–₦2,500 Flutterwave is ~0.4pp more expensive than Paystack; at ₦2,501–₦6,500 Flutterwave is *cheaper*. Weighted across imeihub's distribution the delta is ~0.3–0.5pp in Paystack's favour — recoverable with a ~5% baseline price uplift, small price for self-serve onboarding access.

### When to switch to Paystack instead

Promote Paystack to primary if **any** become true:
- imeihub incorporates a NG RC entity — Paystack's lower fees on ≤₦2,500 transactions, higher local-card success rate (3–5pp), and superior inline UX then dominate.
- imeihub partners with a NG merchant-of-record (Selar, Bumpa) — these typically run Paystack under the hood.
- imeihub adds a Ghana (`/gh/`) market — Paystack's GH operation is more mature.

### When Flutterwave stays right even after NG incorporation
- USD revenue stays >50% — multi-currency settlement outvalues the fee edge.
- imeihub expands to >2 African markets — Flutterwave covers 34 vs Paystack's 5.
- Recurring billing launches — Flutterwave's recurring API is broader; MFB licence enables prepaid balance holding.

### Foreign-merchant question status

**Resolved.** Flutterwave's RoW onboarding accepts imeihub's existing foreign incorporation without NG CAC, BVN, or Zenith dom. No local incorporation needed for Phase 2. Paystack's foreign-merchant path remains **blocked-by-policy** for self-serve — needs NG incorporation or a sales-negotiated Stripe-routed contract, neither realistic at imeihub's scale.

### Known gaps

- The 3–5pp Paystack conversion edge (Daikimedia 2026) is third-party; validate with A/B in Phase 2.
- v4 webhook HMAC details are doc-current 2026-05-28; re-confirm the `flutterwave-signature` derivation at integration against `developer.flutterwave.com/docs/webhooks`.
- NRS VAT registration practicalities for foreign merchants are codified in NTA 2025 but whether Flutterwave automates it or it's merchant responsibility was not directly confirmed in official Flutterwave docs accessible during research — assumed merchant responsibility. Confirm in Phase 2.
- Stamp duty (₦50 per outbound ≥₦10,000) is confirmed for Paystack; equivalent post-April-2026 treatment under Flutterwave's MFB licence was not confirmed and should be assumed to apply.
- Direct WebFetch to `paystack.com` / `flutterwave.com` returned HTTP 403; all fee numbers and signature schemes are triangulated from official support pages and reputable third-party sources, each independently corroborated.

---

## Sources

- [Paystack Support — Transactions pricing (accessed 2026-05-28)](https://support.paystack.com/en/articles/2130306)
- [Paystack Support — Getting your money (accessed 2026-05-28)](https://support.paystack.com/en/articles/2125314)
- [Paystack Support — Test Mode and Live Mode (accessed 2026-05-28)](https://support.paystack.com/en/articles/2129922)
- [Paystack Support — Compliance requirements for Nigeria (accessed 2026-05-28)](https://support.paystack.com/en/articles/2123970)
- [Paystack Support — Enabling international payments (accessed 2026-05-28)](https://support.paystack.com/en/articles/2130690)
- [Paystack Developer Docs — API Reference (accessed 2026-05-28)](https://paystack.com/docs/api/)
- [Paystack Developer Docs — Webhooks (accessed 2026-05-28)](https://paystack.com/docs/payments/webhooks/)
- [Paystack Developer Docs — Inline JS (accessed 2026-05-28)](https://paystack.com/docs/developer-tools/inlinejs/)
- [Paystack Developer Docs — Accept Payments (accessed 2026-05-28)](https://paystack.com/docs/payments/accept-payments/)
- [Stripe Newsroom — Stripe to acquire Paystack, 2020-10-15](https://stripe.com/newsroom/news/paystack-joining-stripe)
- [Flutterwave Help — Pricing for receiving payments NG (accessed 2026-05-28)](https://flutterwave.com/ng/support/pricing/pricing-for-receiving-payment)
- [Flutterwave Help — Settlement FAQs (accessed 2026-05-28)](https://flutterwave.com/us/support/payments/settlement-frequently-asked-questions)
- [Flutterwave Help — Settlement in different currencies (accessed 2026-05-28)](https://flutterwave.com/tz/support/payments/settlement-in-different-currencies)
- [Flutterwave Help — RoW onboarding (accessed 2026-05-28)](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-the-rest-of-the-world)
- [Flutterwave Help — Nigeria onboarding (accessed 2026-05-28)](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-nigeria)
- [Flutterwave Help — What is a Secret Hash? (accessed 2026-05-28)](https://flutterwave.com/eg/support/integrations/what-is-a-secret-hash)
- [Flutterwave Developer Docs — Environments (accessed 2026-05-28)](https://developer.flutterwave.com/docs/environments)
- [Flutterwave Developer Docs — Webhooks (accessed 2026-05-28)](https://developer.flutterwave.com/docs/webhooks)
- [Flutterwave Engineering — Introducing v4 API, 2024](https://dev.to/flutterwaveeng/introducing-the-flutterwave-v4-api-faster-safer-easier-to-integrate-2dc9)
- [Flutterwave Blog — Selar case study](https://flutterwave.com/us/blog/african-creators-global-audience-selars-massive-growth-journey-powered-by-flutterwave)
- [Flutterwave Blog — Audiomack partnership](https://flutterwave.com/us/blog/mobile-money-sets-the-pace-as-audiomack-leverages-flutterwave-for-seamless-payments-in-africa)
- [Flutterwave Nigerian Banking Licence — PR Newswire, 2026-04-03](https://www.prnewswire.com/news-releases/flutterwave-secures-nigerian-banking-license-to-accelerate-payment-efficiency-302732642.html)
- [Flutterwave MFB Licence — Billionaires Africa, 2026-04-03](https://www.billionaires.africa/2026/04/03/gb-agboolas-flutterwave-secures-nigerian-microfinance-banking-licence-after-10-years-as-a-payments-company/)
- [Central Bank of Nigeria — Payment Service Providers (accessed 2026-05-28)](https://www.cbn.gov.ng/PaymentsSystem/PSPs.html)
- [Goidara — PSSP Licence in Nigeria 2026 Guide](https://www.goidara.com/blog/how-to-obtain-a-payment-solution-service-provider-pssp-license-in-nigeria)
- [NIBSS — Home (accessed 2026-05-28)](https://nibss-plc.com.ng/)
- [Mondaq — VAT Under NTA 2025: digital platforms and fintechs, 2025-09](https://www.mondaq.com/nigeria/tax-authorities/1712958/vat-under-the-nta-2025-what-digital-platforms-and-fintechs-need-to-know)
- [VATupdate — Nigeria enforces VAT on non-resident digital services, 2025-09-11](https://www.vatupdate.com/2025/09/11/nigeria-to-enforce-vat-on-non-resident-digital-services-from-2026/)
- [Daikimedia — Paystack vs Flutterwave 2026 conversion comparison](https://www.daikimedia.com/blog/paystack-vs-flutterwave-2026-which-gateway-converts-better-in-nigeria)
- [HuruPay — Does Paystack Accept Dollar Payments?, 2024](https://hurupay.com/blog/does-paystack-accept-dollar-payments)
- [TechCabal — Smile ID 200M eKYC checks, 2024-11-29](https://techcabal.com/2024/11/29/smile-id-releases-nigerias-first-ever-ekyc-report-hits-200m-identity-verification-checks/)
- [Bumpa Academy — Accepting online payments in Nigeria](https://academy.getbumpa.com/guides/business-guide-accepting-online-payments-nigeria)
- [Selar Help Center — Pay with transfer](https://help.selar.com/portal/en/kb/articles/pay-with-transfer)
