# Egypt Payment Options for imeihub — Fawry, Vodafone Cash, InstaPay (PR-FAWRY-01)

**Date**: 2026-05-28. **Status**: Pre-integration research; no code written.
**Scope**: Three dominant Egyptian payment rails for a PHP + MySQL IMEI-check service whose typical paid transaction is **0.5–210 EGP** (≈ USD 0.01–4.20 at ~50 EGP/USD). Stripe is the incumbent and works poorly in Egypt (low card penetration, punitive FX markup, high friction). The deciding question for imeihub — **a non-Egyptian entity** — is which rails are realistically integrable and which are blocked by policy.

**Headline finding**: all three providers' direct merchant rails require Egyptian commercial registration and tax ID; imeihub has neither. Recommendation steers to (a) routing Fawry via an international acquirer that already holds Egyptian licensing, or (b) operating via a Merchant-of-Record. Direct onboarding with Fawry, Paymob, or an IPN bank is structurally blocked without an Egyptian legal entity.

---

## Fawry

### Overview & market position

Fawry is Egypt's dominant offline-to-online payments network. 2025: **~53.1M active customers**, **FY2025 throughput EGP 943.6B (+57% YoY)**, **>372,000 agent locations + 36 partner banks**, **>6M transactions/day** [Fawry FY2025, 2025-09](https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k); [Fawry corporate, 2025-03](https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/). The niche: **cash-on-bills / kiosk vouchers** — customer initiates online, gets a 6-digit reference, pays at any kiosk, ATM, or bank app. Captures the card-light segment hitting imeihub's funnel after the January 21, 2026 customs-exemption end. FawryPay layers cards and mobile wallets on the same reference-number rail.

### API documentation URL + integration model

Developer portal: [`developer.fawrystaging.com`](https://developer.fawrystaging.com/) (covers both staging and production). Postman public workspace also available. Integration models: **Checkout Link** (Fawry-hosted, lightest), **Checkout Button** (self-hosted JS embed), **Server-to-server REST** for card-on-file/refunds/status, **Mobile SDKs** (Android/iOS; `fawry_sdk` on pub.dev for Flutter [pub.dev](https://pub.dev/packages/fawry_sdk)). Methods: cards (Visa/Mastercard/Meeza), e-wallet pass-through, PAYATFAWRY reference number, partner-bank installments.

### Merchant onboarding — KYC docs, foreign-merchant rules

**Critical for imeihub.** Third-party documentation is explicit: *"A Fawry merchant account can only be opened by a company based in Egypt"* — requires Commercial Register + Tax ID plus CBE KYC packet [WHMCS Fawry plugin, 2024](https://marketplace.whmcs.com/product/5540-fawry-for-whmcs); corroborated by Nafezly PHP guide and `fawry.com/sme-registration-form/`. Required: Egyptian commercial registration (سجل تجاري), Tax ID (بطاقة ضريبية), bank account at a Fawry-partnered Egyptian bank for EGP settlement, AML/KYC officer, VAT certificate if revenue >EGP 500K/yr. **No public foreign-merchant tier or non-resident MID.** CBE June 2025 rules allow foreign payment institutions to apply for CBE licensing, but that's for PSPs (not merchants) and requires home-jurisdiction equivalence — irrelevant for imeihub [CBE rules, 2025-06](https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules); [Shehata Law, 2025](https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/).

**Foreign-merchant verdict: BLOCKED for direct merchant accounts.** Workarounds: (a) incorporate an Egyptian LLC (~USD 1,500–3,000, 30–45 days), (b) sign with an international acquirer routing to Fawry (EBANX, Nuvei, Checkout.com, PaymentWall all expose Fawry to non-Egyptian merchants of record), or (c) use an MoR like Dodo Payments [Dodo MoR guide, 2026](https://dodopayments.com/blogs/merchant-of-record-in-egypt).

### Pricing & fees

Fawry does not publish a public MDR card; pricing is commercially negotiated by channel, volume, merchant profile. Public 2025 signals: via international aggregators (EBANX/Nuvei/Checkout.com) **3.0–4.5% MDR** plus FX margin if settling in USD — EBANX passes EGP and settles weekly in USD [EBANX, 2025](https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/); direct merchants pay **~2.75% + ~2.5 EGP** for card; PAYATFAWRY kiosk is a flat ~EGP 5–15 [Akurateco, 2024](https://akurateco.com/payment-methods/fawry). Refunds via `/refund`; kiosk refunds slow (T+5 to T+14) — reverse through the agent network. imeihub's ~EGP 50 ticket: flat EGP 5 kiosk = ~10% effective, card = ~7%. Implication: enable Fawry only above ~EGP 30, bundle small services into credit packs.

### Settlement timeline

Settlements **process within 5 business days** [Flutterwave Fawry FAQ, 2025](https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt). Direct merchants negotiate T+2 to T+3 for card, T+5 to T+7 for kiosk (agent reconciliation lag). EGP into an Egyptian IBAN. International acquirers deliver weekly USD payouts.

### Supported currencies

**EGP only at the rail.** Customer pays EGP; merchant of record absorbs FX. CBE FX controls and the 2024 EGP float make USD-displayed pricing impossible at the consumer rail — USD displays get converted at reference rate plus 1–3% markup [Grey FX, 2026](https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments).

### SDK / web-checkout availability — PHP and JS

- **PHP**: `fawry-api/fawry` (charge, refund, status, card payment, callback v2) [GitHub](https://github.com/fawry-api/fawry); also `Nafezly/payments` which wraps Fawry + 18 other gateways under a uniform `setUserId()->setAmount()->pay()` [GitHub](https://github.com/Nafezly/payments).
- **JS**: Fawry Checkout Button drop-in `<script>`. No first-party Node SDK; community wrappers are thin REST clients.

For PHP + MySQL: composer install `fawry-api/fawry`, set `FAWRY_URL=https://atfawry.fawrystaging.com/` (sandbox) or `https://www.atfawry.com/` (prod), plus `FAWRY_SECRET` and `FAWRY_MERCHANT`.

### Webhook structure + signature verification

Server Callback v2 — HTTPS POST with `requestId`, `fawryRefNumber`, `merchantRefNumber`, `customerMobile`, `paymentAmount`, `orderAmount`, `fawryFees`, `orderStatus`, `paymentMethod`, `messageSignature`, `orderExpiryDate`, `orderItems`. Signature: **SHA-256** of `merchantCode + merchantRefNumber + paymentAmount + orderAmount + orderStatus + paymentMethod + secureKey` vs `messageSignature`. Retries on non-2xx with backoff. No replay nonce — dedupe on `fawryRefNumber`.

### Sandbox URL + credential acquisition

Sandbox: `https://atfawry.fawrystaging.com/`. Production: `https://www.atfawry.com/`. **No self-service signup** — fill `fawry.com/sme-registration-form/`, a rep emails back, `merchantCode` + `secureKey` arrive in 1–5 business days. Hard gate for foreign teams without an Egyptian sales contact. International aggregators (EBANX, PaymentWall, Tap) expose Fawry in their own sandboxes without this gate.

### 3 example competitor digital services using Fawry

1. **Jumia Egypt** — Fawry at checkout for card and kiosk-pay-later [Jumia, 2026](https://www.jumia.com.eg/).
2. **Noon Egypt** — Fawry alongside Noon Pay [Noon, 2026](https://www.noon.com/egypt-en/).
3. **inDrive Egypt** — driver wallet top-ups via Fawry kiosk codes [inDrive, 2026](https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account).

All three are Egyptian-incorporated; the Fawry-via-EBANX precedent is the strongest foreign-merchant analog.

### Compliance notes

- **CBE**: Fawry is a licensed PSP. June 2025 PSO/PSP rules apply with transition through June 2026 [Daily News Egypt, 2025-06-21](https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/). Merchants' own AML/KYC obligation continues.
- **ITIDA**: Not involved in payment licensing; supervises E-Signature Law 15/2004 [ITIDA](https://itida.gov.eg/).
- **NTRA**: imeihub is telecom-adjacent but not gated by NTRA as long as it does not provide carrier billing. NTRA's Jan 2025 IMEI regime uses its own Telefoni app for fees [NTRA, 2025](https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/). No third-party-billing license needed.
- **Tax**: Egypt's simplified VAT registration for nonresident digital providers applies at 14% on B2C sales once Egyptian revenue exceeds EGP 500K/yr [EY, 2023](https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem).

---

## Vodafone Cash (via Paymob aggregator)

### Overview & market position

Vodafone Cash is Egypt's dominant mobile wallet. Q2 2025: **~25.5M users (55% of 46.3M wallet base)**, **78% of wallet transactions by count**, **81% by value** [Daily News Egypt, 2025-09-13](https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/); [Statista, 2025](https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/). Total Q2 wallet value EGP 943.4B (~USD 19.6B), +72% YoY [IBS Intelligence, 2025-09](https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/). The niche: **carrier-backed mobile balance** — any Vodafone Egypt subscriber spends balance funded by airtime top-up, bank transfer, or salary; no card needed. For imeihub, the biggest reach lever for users who failed Stripe.

### API documentation URL + integration model

**No public direct Vodafone Cash merchant API.** Vodafone Egypt has no developer self-service. Vodafone Cash is **exposed exclusively through CBE-licensed aggregators** — chiefly **Paymob**, plus MyFawry, Tap, Kashier, Geidea [Paymob digital wallet](https://paymob.com/en/digital-wallet). Reference path via Paymob: portal [`developers.paymob.com`](https://developers.paymob.com/); mobile-wallets docs at `docs.paymob.com/docs/mobile-wallets`. Flow: (1) `POST /api/auth/tokens` → auth token; (2) `POST /api/ecommerce/orders` → `order_id`; (3) `POST /api/acceptance/payment_keys` with `integration_id` for Vodafone Cash → `payment_token`; (4) `POST /api/acceptance/payments/pay` with `source.identifier=<phone>`, `source.subtype=WALLET`. Customer receives OTP on Vodafone line, enters on the Paymob-hosted page, payment completes; returns via webhook.

### Merchant onboarding — KYC docs, foreign-merchant rules

Paymob requires the same baseline as Fawry: **Egyptian commercial registration, Tax ID, Egyptian bank account for EGP settlement**. Paymob holds a UAE Central Bank RPS license (Jan 2025) and operates in KSA and Oman [Disrupt Africa, 2025-01-31](https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/), so a foreign entity could onboard via Paymob UAE for *card* — but **Egyptian wallet rails (Vodafone Cash, Orange Money, e& Money) are gated to Egyptian-licensed MIDs only**. PayAtlas notes Egypt is "more conservative" than UAE on non-resident MIDs [PayAtlas, 2026](https://payatlas.com/countries/egypt-eg).

**Foreign-merchant verdict: BLOCKED for direct Vodafone Cash MID.** Workarounds: (a) Egyptian subsidiary, (b) aggregator fronting (Tap and Checkout.com list it cross-border but require GCC incorporation), (c) MoR.

### Pricing & fees

Paymob public pricing: **2.75% + EGP 3 per successful transaction**, zero monthly fee, local SMB/SME card and wallet [Paymob](https://www.paymob.com/en/pricing); [Bilixe, 2025](https://bilixe.com/listing/paymob-payment-gateway/). Enterprise and international on inquiry. Vodafone Cash MDR same 2.75% + EGP 3.

**EGP 3 flat fee is the killer at imeihub's band.** EGP 50: **8.75% effective**. EGP 100: 5.75%. EGP 200: 4.25%. Above ~EGP 150 rate flattens. Refunds via `/api/acceptance/void_refund/refund`; processing fee generally not refunded, costing original fee + aggregator refund fee (EGP 2–5).

### Settlement timeline

Paymob: **T+1 for card and wallets**; weekly bank deposits by default, daily on enterprise. EGP into an Egyptian IBAN. Vodafone Cash: same-day Vodafone → Paymob, T+1 Paymob → merchant.

### Supported currencies

**EGP only.** No USD pass-through on a carrier-billed wallet. Multi-currency display works upstream (imeihub shows USD, Paymob converts to EGP at API call) but settlement is unavoidably EGP.

### SDK / web-checkout availability — PHP and JS

- **PHP**: First-party `PaymobAccept/paymob-php` [GitHub](https://github.com/PaymobAccept/paymob-php); `skrskr/paymob` for Laravel; Nafezly wraps both.
- **JS**: Paymob iframe checkout is the documented web path. No first-party Node SDK; REST is trivial to call from any HTTP client.
- **Plugins**: WooCommerce, Magento, Shopify, OpenCart, Wix [Paymob plugins](https://developers.paymob.com/egypt/e-commerce-plugins).

### Webhook structure + signature verification

**HMAC-SHA512.** Callback as HTTPS POST (processed) or GET-redirect (response). Parameters sorted lexicographically by key, values concatenated in order, HMAC-SHA512 with merchant secret, compared against the `hmac` query parameter [Paymob HMAC docs](https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac). Payload includes `amount_cents`, `currency`, `id`, `integration_id`, `is_3d_secure`, `is_refunded`, `order.id`, `source_data.type`, `success`.

### Sandbox URL + credential acquisition

Self-service signup at `accept.paymob.com/portal2/en/register`. Test creds at `developers.paymob.com/paymob-docs/need-help/faq/test-credentials.md`. Vodafone Cash sandbox: **phone `01010101010`, PIN/OTP `123456`** [Nafezly](https://github.com/Nafezly/payments). Self-service sandbox (no sales gate, unlike Fawry); production requires the same Egyptian KYC packet.

### 3 example competitor digital services using Vodafone Cash via Paymob

1. **Swvl** — Egyptian mass-transit booking; Paymob for card and Vodafone Cash.
2. **MaxAB** — B2B grocery serving 150K+ Egyptian retailers; Paymob underpins mobile-wallet settlements.
3. **elmenus** — restaurant discovery and food delivery; Vodafone Cash via Paymob checkout.

All three are Egyptian-incorporated. No clean foreign-merchant precedent at the consumer-checkout layer.

### Compliance notes

- **CBE**: Paymob is a CBE-licensed PSP under the 2025 framework. June 2026 transition applies to Paymob, not merchants.
- **NTRA**: Vodafone Cash is a Vodafone-Egypt service; NTRA regulates Vodafone as a carrier but does not gate merchants accepting Vodafone Cash via a CBE-licensed PSP. No NTRA third-party-billing license needed.
- **Data residency**: Paymob processes within Egypt; CBE-mandated 7-year retention on PSP-side records.
- **PCI DSS**: Paymob is PCI-DSS Level 1; iframe-integrated merchants inherit SAQ-A scope.

---

## InstaPay (Egypt IPN)

### Overview & market position

InstaPay is the consumer app for Egypt's **Instant Payment Network (IPN)** — domestic real-time rail launched **March 22, 2022**, operated by the Egyptian Banks Company (EBC) under CBE [EBC IPN](https://www.egyptianbanks.com/instant-payment-network/); [CBE IPN](https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network). Closest Egyptian analog to UPI (India) or Pix (Brazil). End of 2024: **1.5B transactions, EGP 2.9T value, 10M+ app downloads** [Lightspark, 2026](https://www.lightspark.com/knowledge/egypt-instant-payments); [CBPN, 2025-05](https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay). The niche: **bank-to-bank real-time transfers across ~36 banks** at near-zero customer-side fees. 2025 growth explosive [Egyptian Streets, 2026-01-04](https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/).

### API documentation URL + integration model

**No public InstaPay merchant API for third-party developers.** The IPN rail is consumed via:

- **Bank-side APIs**: Each participating bank (CIB, Banque Misr, NBE, ABC, etc.) exposes IPN APIs to its corporate customers behind that bank's developer portal — none standardized, none open [Bank ABC InstaPay, 2026](https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx).
- **CBE merchant QR**: Since 2024, CBE extended InstaPay with a merchant-acceptance QR (customer scans, payment auto-routes via IPN) [EgyptToday, 2024](https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments). Rolled out via member banks, not as a standalone API.
- **Aggregators**: Some PSPs (Paymob, MNT-Halan, Khazna) have begun fronting IPN pull, but coverage is limited and conditional on the customer's bank.

`instapay.eg` is consumer-only; no self-service developer surface [InstaPay Q&A](https://www.instapay.eg/?page_id=348&lang=en). The unrelated US processor at `secure.instapaygateway.com` is often confused in search results.

### Merchant onboarding — KYC docs, foreign-merchant rules

Direct bank IPN APIs: full Egyptian corporate banking relationship (commercial registration, Tax ID, board resolution, beneficial-ownership disclosure, AML interview). Aggregator-fronted: terms vary, but Paymob's IPN pull is Egyptian-MID-only today.

**Foreign-merchant verdict: BLOCKED-BY-POLICY at the rail level.** No public path for a non-Egyptian entity to consume the IPN merchant rail without (a) an Egyptian corporate bank account or (b) an Egyptian-licensed PSP fronting. CBE 2025 rules allow foreign payment institutions to apply for CBE PSP licensing — multi-year regulatory project, not a payment-integration option.

### Pricing & fees

Customer-side: **zero fee P2P up to EGP 70,000/month**. Merchants accepting IPN pull pay **<1% MDR** in current pilots — cheaper than card. Customer-push (initiated from banking app): zero customer-side, ~EGP 1–3 flat on merchant.

For imeihub's band this would be the cheapest rail — if access could be unlocked. EGP 50 at 0.5% + EGP 1 = **2.5%**. EGP 200: ~1.0%. Structurally 3–6× better than Fawry or Vodafone Cash.

### Settlement timeline

**T+0 / real-time.** IPN settles between participating banks in seconds, 24/7. Merchant-side to the corporate account is real-time — the headline advantage and the reason CBE has prioritized IPN growth.

### Supported currencies

**EGP only.** IPN is a domestic ACH-style rail — no FX, no cross-border.

### SDK / web-checkout availability — PHP and JS

**None publicly.** Bank-side APIs are issued under NDA to corporate banking customers, not standardized across the 36 member banks. No PHP SDK, no JS SDK, no documented webhook surface for the open developer ecosystem.

### Webhook structure + signature verification

Not publicly documented. Bank-issued APIs use bank-specific auth (typically mTLS + JWT). No standardized webhook contract across IPN.

### Sandbox URL + credential acquisition

**No third-party sandbox.** Bank-side sandboxes are issued case-by-case after KYC and account opening at the bank.

### 3 example competitor digital services using InstaPay

InstaPay is not yet meaningfully used for online checkout the way Fawry and Vodafone Cash are. Current uses:

1. **Peer-to-peer transfers** (~80%+ of volume).
2. **Bill-pay and government collections** routed through CBE-approved aggregators (Misr Digital Innovation, e-Finance).
3. **Bank-app-initiated transfers to merchants** — common for high-value (rent, freelance invoicing), not consumer checkout.

The QR merchant feature is the early e-commerce path, but as of May 2026 no Tier-1 Egyptian e-commerce site lists an InstaPay-branded checkout button alongside Fawry/card/wallet options.

### Compliance notes

- **CBE**: InstaPay is CBE-operated infrastructure; merchant access gated by participating banks under CBE's PSO/PSP licensing (2025).
- **Data residency**: All IPN data within Egypt by EBC.
- **NTRA / ITIDA**: Not involved at the payment layer.

---

## Recommendation

### Comparison table

| Dimension | Fawry | Vodafone Cash (Paymob) | InstaPay |
|---|---|---|---|
| Fee on EGP 50 ticket | ~10% kiosk / ~7% card | ~8.75% (2.75% + EGP 3) | ~2.5% (~0.5% + EGP 1) |
| Settlement | T+2 to T+5 EGP | T+1 EGP | T+0 real-time |
| Foreign-merchant friendly | **No** — workaround via EBANX/Nuvei MoR | **No** — workaround via MoR | **No** — no third-party path |
| SDK quality (PHP/JS) | Good — community PHP, JS embed, Postman | Excellent — official PHP SDK, iframe, HMAC docs | None |
| Sandbox | Gated (sales, 1–5 days) | Self-service | None |
| Reach | ~53M customers, kiosk-heavy | ~25.5M wallet users | ~46M bank accounts, no merchant maturity |
| Recommendation | **Prioritize** via Checkout.com/EBANX | Parallel — defer until EG entity/MoR | Defer — revisit late 2026 |

### Top-1 prioritized provider: **Fawry — routed via Checkout.com or EBANX as the merchant of record**

Three-bullet rationale:

- **Reach beats cost.** Fawry's 53M customers — particularly the kiosk channel — capture the segment imeihub fails on today: Egyptians with no card, no Vodafone line, no Stripe-friendly wallet. At 7–10% effective fee, the conversion lift vs Stripe's near-zero EG conversion is the larger lever; case studies report ~10× uplift adding Fawry to card-only checkout.
- **Foreign-merchant constraint is solvable via an international acquirer**, not Egyptian incorporation. Checkout.com and EBANX list Fawry for non-Egyptian merchants of record, settling weekly in USD. imeihub accepts ~4–5% all-in MDR vs direct-Fawry's lower rate — the right Phase 2 tradeoff vs 30–45 days and USD 2K–3K on an Egyptian LLC.
- **Lowest implementation effort.** `fawry-api/fawry` PHP and JS embed are documented, webhook signature is plain SHA-256, sandbox works via the acquirer. InstaPay has no path; Vodafone Cash via Paymob has the same workaround AND lower reach.

**Top-2 (parallel track): Vodafone Cash via Paymob — only after an Egyptian entity or MoR is in place.** Adds ~25.5M wallet users at similar fees. Captures smartphone-first millennials, a different cohort from Fawry's mass-market kiosk base.

**Deprioritize InstaPay for Phase 2.** Revisit late 2026 if (a) CBE publishes a foreign-merchant pull-API spec, (b) an aggregator launches a foreign-merchant IPN tier, or (c) imeihub incorporates in Egypt. 2.5% fee + T+0 settlement = strategically correct long-term, but the consumption surface does not exist today for a non-Egyptian entity.

### Price-band caveat

Both Fawry card and Paymob/Vodafone Cash carry a ~EGP 2.5–3 flat fee. Below ~EGP 30 the flat fee alone is >8% before percentage MDR. The integration should: (1) set a minimum charge floor (~EGP 30) for Fawry/Paymob; (2) bundle small services into prepaid credit packs (EGP 50 / 100 / 250, deducted per check internally) — amortizes the flat fee across 5–50 checks; (3) keep Stripe or PayPal live for the small expat/diaspora slice already converting on international rails.

---

## Sources

- Fawry FY2025 results — Zawya, 2025-09 — https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k
- Fawry × Contact JV — Fawry, 2025-03-19 — https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/
- FawryPay Documentation — Fawry, 2026-05 — https://developer.fawrystaging.com/
- Fawry public Postman — Fawry, 2026-05 — https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/
- fawry-api/fawry PHP library — GitHub, 2026-05 — https://github.com/fawry-api/fawry
- Nafezly/payments PHP library — GitHub, 2026-05 — https://github.com/Nafezly/payments
- fawry_sdk Flutter — pub.dev, 2026-05 — https://pub.dev/packages/fawry_sdk
- Fawry Pay FAQ — Flutterwave, 2025 — https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt
- Akurateco Fawry analysis — 2024 — https://akurateco.com/payment-methods/fawry
- EBANX Fawry integration — 2025 — https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/
- WHMCS Fawry plugin (Egyptian-only merchant note) — 2024 — https://marketplace.whmcs.com/product/5540-fawry-for-whmcs
- Paymob Developer Portal — 2026-05 — https://developers.paymob.com/
- Paymob webhook/HMAC docs — 2026-05 — https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac
- Paymob mobile-wallets docs — 2026-05 — https://docs.paymob.com/docs/mobile-wallets
- Paymob pricing — 2026-05 — https://www.paymob.com/en/pricing
- Paymob e-commerce plugins — 2026-05 — https://developers.paymob.com/egypt/e-commerce-plugins
- PaymobAccept/paymob-php — GitHub, 2026-05 — https://github.com/PaymobAccept/paymob-php
- Bilixe Paymob review — 2025 — https://bilixe.com/listing/paymob-payment-gateway/
- Paymob UAE RPS license — Disrupt Africa, 2025-01-31 — https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/
- Vodafone Cash market share Q2 2025 — Daily News Egypt, 2025-09-13 — https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/
- Mobile wallet market share — Statista, 2025 — https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/
- Egypt digital payments surge — IBS Intelligence, 2025-09 — https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/
- EBC IPN page — 2026 — https://www.egyptianbanks.com/instant-payment-network/
- CBE Instant Payment Network — 2026 — https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network
- Evolution of IPN — CBPN, 2025-05-23 — https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay
- Egypt Instant Payments — Lightspark, 2026 — https://www.lightspark.com/knowledge/egypt-instant-payments
- InstaPay Q&A — 2026 — https://www.instapay.eg/?page_id=348&lang=en
- Inside Egypt's InstaPay Economy — Egyptian Streets, 2026-01-04 — https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/
- CBE QR feature — EgyptToday, 2024 — https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments
- Bank ABC InstaPay — 2026 — https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx
- CBE PSO/PSP licensing rules — 2025-06-19 — https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules
- Shehata Law on CBE licensing — 2025 — https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/
- CBE new licensing framework — Daily News Egypt, 2025-06-21 — https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/
- Merchant of Record in Egypt — Dodo Payments, 2026 — https://dodopayments.com/blogs/merchant-of-record-in-egypt
- Accepting Payments in Egypt — PayAtlas, 2026 — https://payatlas.com/countries/egypt-eg
- Egypt VAT for nonresident digital services — EY, 2023 — https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem
- Egypt FX controls — Grey, 2026-05 — https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments
- ITIDA — 2026 — https://itida.gov.eg/
- NTRA Telefoni — 2025 — https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/
- Jumia Egypt — 2026 — https://www.jumia.com.eg/
- Noon Egypt — 2026 — https://www.noon.com/egypt-en/
- inDrive Egypt Fawry top-up — 2026 — https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account
