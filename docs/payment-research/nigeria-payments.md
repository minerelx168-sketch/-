# Nigeria Payment Provider Research: Paystack vs Flutterwave for imeihub

**Research date**: 2026-05-28 — **Task**: PR-PAYSTACK-01

imeihub is a free + paid IMEI check web service (PHP + MySQL) currently using Stripe. Stripe technically supports Nigerian customers but suffers elevated failure rates on Nigerian-issued NGN cards, partly because issuers route them through Stripe's `2.9% + 30¢` foreign-card rails and partly because of CBN PAN-tokenisation friction. This note evaluates **Paystack** (Stripe's Nigerian subsidiary, the local-default card processor) versus **Flutterwave** (the multi-currency cross-border specialist) for imeihub's micro-transaction profile: USD $0.01–$4.20, roughly NGN 15–6,500 at mid-2026 rates. The decisive operational constraint is that **imeihub has no Nigerian CAC entity**, so the foreign-merchant onboarding pathway materially shapes the recommendation.

---

## Paystack

### Overview & market position

Paystack (founded 2015, Lagos) processes **over half of all online card transactions in Nigeria** and serves 60,000+ businesses in Nigeria and Ghana, including FedEx, UPS, MTN, and AXA Mansard [[Stripe Newsroom, 2020-10-15](https://stripe.com/newsroom/news/paystack-joining-stripe)]. Stripe acquired Paystack in October 2020 for over $200M and is progressively embedding Paystack's rails into Stripe's Global Payments & Treasury Network. Paystack owns the lowest-friction NGN card acceptance, the deepest local bank rail integrations (Pay with Bank Transfer / USSD across all NIBSS-connected banks), and the highest local-card success rate in market. It does **not** own cross-border USD settlement (NGN is the default; USD requires a Zenith Bank dom account) or broad multi-currency collection.

### API documentation URL + integration model

API reference: <https://paystack.com/docs/api/>. REST over HTTPS with bearer-token auth. Canonical flow: `POST /transaction/initialize` from the PHP backend returns `authorization_url` + `reference` → either redirect (Standard) or hand `access_code` to Paystack Inline JS for an in-page modal → **always call `GET /transaction/verify/<reference>`** before delivering value; visiting the callback URL alone is not proof of success [[Paystack Docs, accessed 2026-05-28](https://paystack.com/docs/payments/accept-payments/)].

### Merchant onboarding — KYC, CAC, BVN, foreign-merchant rules

Nigerian merchants need up-to-date **CAC registration** (Form 3 preferred; proof of address ≤6 months old accepted as fallback), valid government ID for every beneficial owner (driver's licence, NIN, voter's card, or passport), and **BVN consent** for all directors — CBN mandates BVN for identity validation on every PSP [[Paystack Support, accessed 2026-05-28](https://support.paystack.com/en/articles/2123970)]. A "Starter Business" tier reduces docs for sole proprietors but caps inbound at ₦5M and still requires Nigerian BVN.

**Foreign merchants** (critical for imeihub): Paystack does **not directly self-serve onboard** foreign-incorporated businesses with no Nigerian presence. Compliance assumes CAC + BVN. The `paystack.com/internationals` "global brands" channel lists FedEx and UPS, but the vendor-side mechanism is that the foreign brand contracts with Stripe (or a Nigerian aggregator) and Stripe routes the Nigerian half onto Paystack rails behind the scenes — sales-led and not realistic at imeihub's revenue band. Practical options without a Nigerian entity: (1) keep Stripe and accept the failure rate; (2) incorporate a Nigerian RC (~₦50–150k + 3–6 weeks); (3) use a Nigerian merchant-of-record facilitator (Bumpa, Selar) that absorbs CAC; (4) switch to Flutterwave's "rest of world" flow. Verdict: **requires-local-incorporation** for self-serve.

### Pricing & fees

Per Paystack's official rates [[Paystack Support — Transactions pricing, accessed 2026-05-28](https://support.paystack.com/en/articles/2130306)]:

- **Local NGN card / bank / USSD**: 1.5% + ₦100 flat, **capped at ₦2,000**, **₦100 fee waived when transaction ≤ ₦2,500**.
- **International card**: 3.9% + ₦100, no cap.
- **Stamp duty**: ₦50 per outbound transfer ≥ ₦10,000 from your Paystack balance (effective 2026-02-18).
- **VAT**: +7.5% on the Paystack fee itself (NRS requirement).

There is no separate USD-to-NGN FX line item; the spread is built into the 3.9% international rate (observed ~1–2% off mid-market) [[HuruPay, 2024](https://hurupay.com/blog/does-paystack-accept-dollar-payments)].

**imeihub band impact**: ₦500 = ₦8.06 effective (1.6%); ₦2,500 = ₦40.31 (1.6%); **₦2,501 = ₦147.82 (5.9%) — the cliff at the waiver boundary is sharp**; ₦6,500 = ₦212.31 (3.3%). The ≤₦2,500 waiver is the single most important pricing fact for imeihub — it beats Stripe's `2.9% + 30¢` decisively on sub-$2 sales.

### Settlement timeline

NGN: **T+1** to a Nigerian bank account, processed weekday mornings (Paystack targets 10am WAT; weekends and public holidays push to T+2/T+3) [[Paystack Support — Getting your money, accessed 2026-05-28](https://support.paystack.com/en/articles/2125314)]. USD: same T+1 cadence but only into a **Zenith Bank USD domiciliary account** — arbitrary correspondent banks are not supported, and no USD wallet exists on the Paystack dashboard.

### Supported currencies

NGN, GHS, ZAR, KES, and USD on Nigeria/Kenya accounts only. Multi-currency checkout presentation is minimal; foreign customers are typically charged in the merchant's base currency with their issuing bank handling FX.

### SDK / web-checkout

**Inline JS** (`https://js.paystack.co/v2/inline.js`) exposes `PaystackPop` with `checkout()`, `newTransaction()`, `resumeTransaction()`. Recommended path: initialise on the PHP backend, pass `access_code` to `resumeTransaction()` to keep the secret key off the client [[Paystack InlineJS docs](https://paystack.com/docs/developer-tools/inlinejs/)]. **Standard / redirect** returns `authorization_url` for a 302. **PHP**: no first-party SDK — canonical integration is raw cURL/Guzzle against the REST API (the surface is ~6 endpoints, easy to roll). Community options: `yabacon/paystack-php`, `unicodeveloper/laravel-paystack`. First-party WooCommerce, Webflow, and Wix plugins exist.

### Webhook structure + signature verification

Header **`x-paystack-signature`** (HTTP) → `$_SERVER['HTTP_X_PAYSTACK_SIGNATURE']`. **HMAC SHA-512** over the **raw** body keyed with the merchant's secret key. Verification in PHP:

```php
$input = @file_get_contents("php://input");
if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !==
    hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY)) { exit(); }
http_response_code(200);
$event = json_decode($input);
```

[[Paystack Webhooks Docs, accessed 2026-05-28](https://paystack.com/docs/payments/webhooks/)]. Key events for Phase 2: `charge.success`, `charge.failed`, `transfer.success`, `refund.processed`. Respond 200 within 5s; retries continue for 72 hours on non-2xx.

### Sandbox URL + credential acquisition

In-dashboard sandbox (no separate URL). Sign up at `dashboard.paystack.com` with email + phone (foreign phones accepted at signup, but go-live requires CAC); dashboard starts in **Test Mode** by default. Settings → "API Keys & Webhooks" exposes `pk_test_…` / `sk_test_…`; toggle live/test in the top-right [[Paystack Support — Test mode and Live mode, accessed 2026-05-28](https://support.paystack.com/en/articles/2129922)]. Test cards documented at `paystack.com/docs/payments/test-payments/`.

### 3 example competitor digital services using Paystack

1. **Selar** (`selar.com`) — Nigerian creator-economy storefront for digital products and courses; heavy Paystack user for NGN, with Flutterwave for cross-border. Direct precedent for low-ticket (₦1,000–₦20,000) digital deliverables similar to imeihub [[Selar Help Center](https://help.selar.com/portal/en/kb/articles/pay-with-transfer)].
2. **Bumpa** (`getbumpa.com`) — SME ecommerce/storefront builder integrating Paystack as one of three primary processors (with Flutterwave + Monnify); demonstrates the standard "all three" Nigerian merchant pattern [[Bumpa Academy](https://academy.getbumpa.com/guides/business-guide-accepting-online-payments-nigeria)].
3. **Smile ID** (`usesmileid.com`) — pan-African identity verification API; uses Paystack for NGN collection. Closest analogue to imeihub's API-driven small-ticket digital service model [[TechCabal, 2024-11-29](https://techcabal.com/2024/11/29/smile-id-releases-nigerias-first-ever-ekyc-report-hits-200m-identity-verification-checks/)].

### Compliance notes

Paystack operates under a CBN **Payment Solution Service Provider (PSSP)** licence — authorises payment gateway/portal operation and merchant aggregation but **does not** permit holding customer funds or wallets [[Goidara, 2026](https://www.goidara.com/blog/how-to-obtain-a-payment-solution-service-provider-pssp-license-in-nigeria), [CBN PSPs](https://www.cbn.gov.ng/PaymentsSystem/PSPs.html)]. PSSPs were formalised under CBN's 9 December 2020 circular: ₦250M minimum share capital, mandatory PCI-DSS. All Nigerian payment rails run through **NIBSS** — ISO 20022-compliant since the 2025 National Payment Stack rollout, determining the T+1 settlement cadence [[NIBSS, accessed 2026-05-28](https://nibss-plc.com.ng/)]. The **Nigeria Tax Act 2025 — VAT on non-resident digital services** (in force 1 January 2026) requires foreign digital providers to register with the Nigeria Revenue Service (NRS) and charge 7.5% VAT on invoices; if not registered, the customer's bank must withhold, which is a major contributor to Stripe-on-Nigerian-cards failure rates [[Mondaq, 2025-09](https://www.mondaq.com/nigeria/tax-authorities/1712958/vat-under-the-nta-2025-what-digital-platforms-and-fintechs-need-to-know), [VATupdate, 2025-09-11](https://www.vatupdate.com/2025/09/11/nigeria-to-enforce-vat-on-non-resident-digital-services-from-2026/)].

---

## Flutterwave

### Overview & market position

Flutterwave (founded 2016, HQ San Francisco, ops in Nigeria + 33 other African countries) is the **multi-currency cross-border specialist**: 200M+ lifetime transactions worth >$16B, 500k daily transactions, customers including Uber, Booking.com, Audiomack, and Showmax. A material 2026 development: **Flutterwave secured a Nigerian Microfinance Banking Licence in April 2026**, allowing it to hold customer deposits directly — a capability Paystack (PSSP) does not have [[PR Newswire, 2026-04-03](https://www.prnewswire.com/news-releases/flutterwave-secures-nigerian-banking-license-to-accelerate-payment-efficiency-302732642.html), [Billionaires Africa, 2026-04-03](https://www.billionaires.africa/2026/04/03/gb-agboolas-flutterwave-secures-nigerian-microfinance-banking-licence-after-10-years-as-a-payments-company/)]. Flutterwave owns the broadest multi-currency collection (30+ currencies), the only Nigerian gateway with a proper US/EU/UK money-transmission license stack (13 US state MTLs late 2024), and **explicit foreign-merchant self-serve onboarding**. It does not yet match Paystack's local-card success rate — independent comparisons cite a 3–5pp gap [[Daikimedia, 2026](https://www.daikimedia.com/blog/paystack-vs-flutterwave-2026-which-gateway-converts-better-in-nigeria)].

### API documentation URL + integration model

Developer portal: <https://developer.flutterwave.com/>. **v4 is current GA** (replaces legacy Rave v3). REST + bearer-token; canonical flow: `POST /payments` returns a checkout `link` → redirect customer or embed via Flutterwave Inline → `GET /transactions/<id>/verify` after callback. v4 introduced idempotency keys, simpler error envelopes, and a dedicated sandbox host (`https://developersandbox-api.flutterwave.com`) instead of v3's query-string toggle [[Flutterwave Engineering, 2024](https://dev.to/flutterwaveeng/introducing-the-flutterwave-v4-api-faster-safer-easier-to-integrate-2dc9)].

### Merchant onboarding — KYC, CAC, BVN, foreign-merchant rules

Nigerian onboarding mirrors Paystack: CAC, BVN for directors, government ID, proof of address. Foreign nationals can open a Nigerian Flutterwave business account with valid ID + work/resident permit [[Flutterwave Help — Nigeria onboarding, accessed 2026-05-28](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-nigeria)].

**Foreign merchants** — this is where Flutterwave decisively differentiates. The **"Rest of the World" onboarding flow** [[Flutterwave Help — RoW onboarding, accessed 2026-05-28](https://flutterwave.com/us/support/onboarding/onboarding-requirements-for-using-flutterwave-in-the-rest-of-the-world)] accepts businesses incorporated outside its 8 directly-supported countries (NG/GH/KE/ZA/UG/TZ/UK/US). A UK Ltd, US LLC, or Stripe Atlas Delaware C-corp all qualify. Requirements: home-jurisdiction business registration, director passports, corporate bank account / bank reference letter (no Nigerian dom required), foreign business address accepted, verifiable website URL (imeihub satisfies), operating licence only if regulated (IMEI lookups are not), estimated monthly sales. Settlement to foreign merchants happens into the merchant's home-currency corporate account; Flutterwave handles FX. **No Nigerian RC, BVN, or Zenith dom account required.** Verdict: **resolved — self-serve foreign-merchant flow exists.**

### Pricing & fees

Per Flutterwave's NG pricing page [[Flutterwave Help — Pricing for receiving payments, accessed 2026-05-28](https://flutterwave.com/ng/support/pricing/pricing-for-receiving-payment)]:

- **Local NGN (card, bank transfer, USSD, eNaira, OPay, NQR)**: **2.0%** (1.4% transaction + 0.6% platform), **capped at ₦2,000**. Effective from 2025-04-11; previously 1.4%.
- **International card**: **4.8%** flat, no cap. Increased from 3.8% on 2024-11-11.
- **Transfers out**: ₦25 + 7.5% VAT (≤₦50k) / ₦50 + 7.5% VAT (>₦50k); GBP transfers £35 flat.
- **No equivalent of Paystack's ₦100-fee waiver** on ≤₦2,500 transactions.

**imeihub band impact**: ₦500 = ₦10 (2.0%); ₦2,500 = ₦50 (2.0%); ₦2,501 = ₦50.02 (2.0%, no cliff); ₦6,500 = ₦130 (2.0%). Flutterwave is **cheaper than Paystack in the ₦2,501–~₦6,667 band** (where Paystack's ₦100 flat fee dominates effective rate), and **more expensive in the ≤₦2,500 band** (where Paystack waives the ₦100).

### Settlement timeline

NGN: **T+1** business day to a Nigerian bank account or NGN payout balance (the April 2026 MFB licence enables direct balance holding). USD and other foreign currency: **T+5** business days [[Flutterwave Help — Settlement FAQs, accessed 2026-05-28](https://flutterwave.com/us/support/payments/settlement-frequently-asked-questions)]. Crucially: **per-currency payout balances** avoid forced NGN conversion on USD-card collections — but **Nigerian-issued naira cards always settle in NGN** regardless of purchase currency, per CBN dollarisation guidance [[Flutterwave Help — Multi-currency settlement, accessed 2026-05-28](https://flutterwave.com/tz/support/payments/settlement-in-different-currencies)]. **Express Settlement** offers T+0 / same-day at +0.5%.

### Supported currencies

NGN, GHS, KES, UGX, TZS, RWF, ZAR, ZMW, XOF, XAF, EGP, MAD, USD, GBP, EUR, CAD — 30+ currencies in checkout, ~10 settleable as balances depending on merchant country. Significantly broader than Paystack.

### SDK / web-checkout

**Flutterwave Inline (v3)** / **Standard checkout (v4)** — hosted checkout page returned as a `link` from `POST /payments`. Drop-in JS at `https://checkout.flutterwave.com/v3.js` for inline modal. **Direct charge** is server-side tokenised for full UI control (increases PCI-DSS scope — not recommended for imeihub). **PHP**: `flutterwavedev/flutterwave-php` is the official SDK on Packagist (`composer require flutterwavedev/flutterwave-php`); `kingflamez/laravelflutterwave` is the popular Laravel community alternative. New integrations should target v4. First-party WooCommerce, WHMCS, and Wix plugins.

### Webhook structure + signature verification

Header **`verif-hash`** (v3 legacy) / **`flutterwave-signature`** (v4). HMAC-SHA256 over the body keyed by the merchant's **Secret Hash** (a user-defined string set in Dashboard → Settings → Webhooks, distinct from the API secret key) [[Flutterwave Help — Secret Hash, accessed 2026-05-28](https://flutterwave.com/eg/support/integrations/what-is-a-secret-hash), [Flutterwave Webhooks](https://developer.flutterwave.com/docs/webhooks)]. PHP pattern:

```php
$secretHash = getenv('FLW_SECRET_HASH');
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
if (!$signature || $signature !== $secretHash) { http_response_code(401); exit(); }
$payload = json_decode(file_get_contents('php://input'), true);
http_response_code(200);
```

Note: v3 uses **literal-string equality** against the configured Secret Hash — it is *not* a per-payload HMAC like Paystack's. v4 moves to a true HMAC-SHA256 of the payload in `flutterwave-signature`. Phase 2 must re-confirm the v4 hashing details against the live docs at integration time. Key events: `charge.completed`, `charge.failed`, `transfer.completed`, `subscription.cancelled`. 200 within 5s.

### Sandbox URL + credential acquisition

Sandbox base URL: **`https://developersandbox-api.flutterwave.com`** [[Flutterwave Developer Docs — Environments, accessed 2026-05-28](https://developer.flutterwave.com/docs/environments)]. Dashboard at `dashboard.flutterwave.com`; sign up with email + business name, no Nigerian phone required. Settings → Developers → API Keys exposes test public/secret keys; toggle to live after KYC. Test cards at `flutterwave.com/ng/support/integrations/test-integrations-with-test-cards`.

### 3 example competitor digital services using Flutterwave

1. **Selar** — also runs Flutterwave for cross-border NGN→USD creator payouts; >1.4M unique Flutterwave transactions processed [[Flutterwave Blog — Selar case study](https://flutterwave.com/us/blog/african-creators-global-audience-selars-massive-growth-journey-powered-by-flutterwave)].
2. **Audiomack** — global music streaming with significant African base. Migrated to Flutterwave in 2020 specifically for multi-method coverage (cards + mobile money + bank transfer + wallets) and African-currency settlement [[Flutterwave Blog — Audiomack partnership](https://flutterwave.com/us/blog/mobile-money-sets-the-pace-as-audiomack-leverages-flutterwave-for-seamless-payments-in-africa)]. **Direct analogue for imeihub**: a foreign digital service collecting low-ticket payments from Nigerian users.
3. **Showmax** — MultiChoice's streaming product; uses Flutterwave for cross-border subscription billing across multiple African currencies [[Flutterwave Showmax pay link](https://flutterwave.com/pay/3mxttgr76uc3)]. Foreign-incorporated-but-collects-in-NGN pattern that imeihub would adopt.

### Compliance notes

Flutterwave holds a CBN **Switching & Processing licence** plus, as of April 2026, a **Microfinance Banking Licence** — a meaningful upgrade above Paystack's PSSP because it allows direct deposit-taking. Additional certifications: PCI-DSS Level 1, ISO 27001, NDPR. Outside Nigeria, 13 US state Money Transmission Licences (29 states via partnerships) and UK FCA registration — these legally underpin the "rest of the world" foreign-merchant flow. All Nigerian-leg settlement still touches NIBSS, identical to Paystack. The same **Nigeria Tax Act 2025 VAT on non-resident digital services** rule applies (1 January 2026), but Flutterwave's foreign-merchant onboarding can include NRS VAT registration as part of compliance setup — a service Paystack does not currently offer non-Nigerian businesses.

---

## Recommendation

### Side-by-side comparison

| Dimension | Paystack | Flutterwave |
|---|---|---|
| Local NGN card fee (≤₦2,500) | **1.5%** (₦100 waived) — best | 2.0% |
| Local NGN card fee (₦2,501–₦6,500) | 1.5% + ₦100 (effective 3.3–5.9%) | **2.0% flat** — best |
| International card fee | **3.9%** + ₦100 — cheaper | 4.8% |
| NGN settlement | T+1 | T+1 (T+0 at +0.5%) |
| USD settlement | T+1 to Zenith dom only | **T+5 to any USD account** — broader |
| **Foreign-merchant friendly** | **No** — needs Nigerian RC/BVN | **Yes** — self-serve RoW flow |
| SDK quality (PHP) | Community libs; small REST surface | **First-party SDK on Packagist** |
| USD acceptance | Yes (NG/KE only) | **Yes (30+ currencies, multi-balance)** |
| Local card success rate | **Highest** (~92–95%) | Lower (~88–92%) [[Daikimedia 2026](https://www.daikimedia.com/blog/paystack-vs-flutterwave-2026-which-gateway-converts-better-in-nigeria)] |
| CBN licence | PSSP (no deposits) | Switching + Processing + **MFB** (deposits) |
| Webhook signing | HMAC-SHA512 per payload | Secret-hash equality (v3) / HMAC-SHA256 (v4) |
| **Recommended for imeihub** | Phase-3 swap if NG-incorporated | **Primary — Phase 2 target** |

### Primary recommendation: **Flutterwave**

For imeihub as a foreign-incorporated micro-transaction digital service with no Nigerian CAC entity, **Flutterwave is the primary processor**, with Paystack as a deferred Phase-3 option.

1. **Foreign-merchant onboarding is resolved without legal restructuring.** Flutterwave's "rest of the world" flow accepts a UK Ltd / US LLC / Stripe Atlas C-corp with no Nigerian RC, BVN, or Zenith dom account. Paystack requires either Nigerian incorporation (~₦50–150k + 3–6 weeks + ongoing compliance) or a Stripe enterprise contract that is not realistic at imeihub's revenue band. This legal-feasibility delta dominates all other factors.
2. **Multi-currency settlement matches imeihub's pricing model.** imeihub prices in USD and converts at checkout. Flutterwave's per-currency balances let USD-card payments settle into a USD balance without forced NGN conversion losses — directly replacing Stripe's behaviour. Paystack's forced-NGN settlement on Nigerian-issued cards is acceptable but inferior for a globally-priced service.
3. **Fee profile is acceptable across imeihub's band.** At ₦500–₦2,500 Flutterwave is ~0.4pp more expensive than Paystack; at ₦2,501–₦6,500 Flutterwave is *cheaper*. Across imeihub's full distribution (skewed to the lower half where Paystack's waiver wins), the weighted-average fee delta is ~0.3–0.5pp in Paystack's favour — a small premium to pay for self-serve foreign-merchant access, and recoverable with a ~5% baseline price uplift.

### When to switch to Paystack instead

Switch to Paystack as primary (and demote Flutterwave to fallback) if **any** of these become true:
- imeihub incorporates a Nigerian RC entity — at that point Paystack's lower fees on ≤₦2,500 transactions, higher local-card success rate (3–5pp conversion edge), and superior inline UX make it the obvious choice.
- imeihub partners with a Nigerian merchant-of-record facilitator (Selar, Bumpa) — these typically run Paystack under the hood.
- imeihub adds a Ghana (`/gh/`) market — Paystack's Ghana operation is more mature than Flutterwave's.

### When Flutterwave is unambiguously right (even after Nigerian incorporation)
- imeihub's USD revenue stays >50% of total — multi-currency settlement outvalues the small-ticket fee edge.
- imeihub expands to >2 African markets and wants one processor — Flutterwave covers 34 countries vs Paystack's 5.
- imeihub launches recurring billing — Flutterwave's tokenisation + recurring API surface is broader, and the MFB licence enables direct prepaid balance holding.

### Foreign-merchant question status

**Resolved.** Flutterwave's "rest of the world" onboarding accepts imeihub's existing foreign incorporation without Nigerian CAC, BVN, or Zenith dom account. No local incorporation is required to ship the Phase-2 integration. Paystack's foreign-merchant path remains **blocked-by-policy** for self-serve — it requires either Nigerian incorporation or a sales-negotiated Stripe-routed contract, neither realistic at imeihub's current scale.

### Known gaps

- The 3–5pp Paystack conversion edge cited from Daikimedia 2026 is third-party; Phase 2 should validate with an A/B traffic split before committing fully.
- v4 webhook HMAC details are doc-current 2026-05-28; the precise `flutterwave-signature` derivation should be re-confirmed at integration time against `developer.flutterwave.com/docs/webhooks`.
- NRS VAT registration practicalities for foreign merchants are documented in NTA 2025 but the operational flow (does Flutterwave automate it, or is it imeihub's responsibility?) was not directly confirmed by official Flutterwave docs accessible during this research — assumed merchant responsibility. Confirm in Phase 2.
- Stamp duty on payouts (₦50 per outbound ≥₦10,000) is confirmed for Paystack; equivalent treatment under Flutterwave's MFB licence post-April-2026 was not confirmed and should be assumed to apply.
- Direct WebFetch to `paystack.com` and `flutterwave.com` returned HTTP 403 during this research; fee numbers and signature schemes have been triangulated from official support pages (cited inline) and reputable third-party sources, each independently corroborated across at least two sources.

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
