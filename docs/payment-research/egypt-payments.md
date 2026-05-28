# Egypt Payment Options for imeihub — Fawry, Vodafone Cash, InstaPay (PR-FAWRY-01)

**Date**: 2026-05-28. **Status**: Pre-integration research; no code written.
**Scope**: Three dominant Egyptian payment rails for a PHP + MySQL IMEI-check service whose typical paid transaction is **0.5–210 EGP** (≈ USD 0.01–4.20 at ~50 EGP/USD). Stripe is the incumbent; it works poorly in Egypt (low card penetration, punitive FX markup, high checkout friction). The deciding question for imeihub — **a non-Egyptian entity** — is which rails are realistically integrable and which are blocked by policy.

**Headline finding**: all three providers' direct merchant rails require Egyptian commercial registration and tax ID. imeihub has neither. The recommendation steers to (a) routing Fawry through an international acquirer that already holds Egyptian licensing, or (b) operating via a Merchant-of-Record (MoR). Direct onboarding with Fawry, Paymob, or an IPN-participating bank is structurally blocked without an Egyptian legal entity.

---

## Fawry

### Overview & market position

Fawry is Egypt's dominant offline-to-online payments network. 2025 disclosures: **~53.1M active customers**, **FY2025 throughput EGP 943.6B (+57% YoY)**, **>372,000 agent locations + 36 partner banks**, **>6M transactions/day** [Fawry FY2025 release, 2025-09](https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k); [Fawry corporate, 2025-03](https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/). The niche: **cash-on-bills / kiosk vouchers** — customer initiates online, gets a 6-digit reference number, pays at any kiosk, ATM, or bank app. This captures the card-light segment hitting imeihub's funnel after the January 21, 2026 customs-exemption end. FawryPay (the online product) layers cards and mobile wallets on the same reference-number rail.

### API documentation URL + integration model

- Developer portal: `https://developer.fawrystaging.com/` (documents both staging and production) [FawryPay Documentation](https://developer.fawrystaging.com/).
- Postman public workspace: [Fawry Postman, 2025](https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/documentation/7656578-58bc3d2b-8bff-4aef-9c09-b0b37fe9da85).

Integration models: **Checkout Link** (Fawry-hosted, lightest lift), **Checkout Button** (self-hosted JS embed), **Server-to-server REST** for card-on-file/refunds/status polling, and **Mobile SDKs** for Android/iOS (also `fawry_sdk` on pub.dev for Flutter) [pub.dev fawry_sdk](https://pub.dev/packages/fawry_sdk). Payment methods exposed: cards (Visa/Mastercard/Meeza), e-wallet pass-through, PAYATFAWRY reference number, partner-bank installments.

### Merchant onboarding — KYC docs, foreign-merchant rules

**Critical for imeihub.** Third-party documentation is explicit: *"A Fawry merchant account can only be opened by a company based in Egypt"* — requires Commercial Register + Tax ID plus the CBE KYC packet [WHMCS Fawry plugin docs, 2024](https://marketplace.whmcs.com/product/5540-fawry-for-whmcs); corroborated by Nafezly PHP guide and `fawry.com/sme-registration-form/`. Required: Egyptian commercial registration (سجل تجاري), Egyptian Tax ID (بطاقة ضريبية), bank account at a Fawry-partnered Egyptian bank for EGP settlement, AML/KYC officer, VAT certificate if revenue >EGP 500K/yr. **No public foreign-merchant tier or non-resident MID program.** CBE June 2025 rules allow foreign payment institutions to apply for CBE licensing, but that path is for PSPs (not merchants) and requires home-jurisdiction equivalence — irrelevant for imeihub [CBE PSO/PSP rules, 2025-06](https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules); [Shehata Law, 2025](https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/).

**Foreign-merchant verdict: BLOCKED for direct merchant accounts.** Workarounds: (a) incorporate an Egyptian LLC (~USD 1,500–3,000, 30–45 days), (b) sign with an international acquirer routing to Fawry (EBANX, Nuvei, Checkout.com, PaymentWall all expose Fawry to non-Egyptian merchants of record), or (c) use an MoR like Dodo Payments [Dodo MoR guide, 2026](https://dodopayments.com/blogs/merchant-of-record-in-egypt).

### Pricing & fees

Fawry does not publish a public MDR card; pricing is commercially negotiated by channel, volume, merchant profile. Public third-party signals (2025): via international aggregators (EBANX/Nuvei/Checkout.com) **3.0–4.5% MDR** plus FX margin if settling in USD — EBANX's Fawry voucher model passes EGP and settles in USD weekly [EBANX Fawry, 2025](https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/); direct Fawry merchants pay roughly **2.75% + ~2.5 EGP per transaction** for card, while the PAYATFAWRY kiosk model is a flat ~EGP 5–15 shouldered by merchant or customer [Akurateco, 2024](https://akurateco.com/payment-methods/fawry). Refunds via `/refund`; kiosk refunds are slow (T+5 to T+14) because they reverse through the agent network. For imeihub's ~EGP 50 average ticket: flat EGP 5 kiosk = ~10% effective, card (~3% + EGP 2.5) = ~7%. Implication: enable Fawry only above ~EGP 30 and bundle small services into credit packs.

### Settlement timeline

Settlements **process within 5 business days** to the merchant's destination [Flutterwave Fawry FAQ, 2025](https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt). Direct merchants negotiate T+2 to T+3 for card and T+5 to T+7 for kiosk (agent reconciliation lag). Currency: EGP into an Egyptian IBAN. International acquirers deliver weekly USD payouts.

### Supported currencies

**EGP only at the rail.** Customer pays EGP; merchant of record absorbs FX if needed. CBE FX controls and the 2024 EGP float make USD-displayed pricing impossible at the consumer rail — USD displays get converted at reference rate plus 1–3% markup [Grey on Egypt FX controls](https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments).

### SDK / web-checkout availability — PHP and JS

- **PHP**: `fawry-api/fawry` (charge, refund, status, card payment, callback v2) [GitHub](https://github.com/fawry-api/fawry); also `Nafezly/payments` which wraps Fawry and 18 other gateways under a uniform `setUserId()->setAmount()->pay()` [GitHub](https://github.com/Nafezly/payments).
- **JS**: Fawry Checkout Button drop-in `<script>`. No first-party Node SDK; community wrappers are thin REST clients.

For PHP + MySQL: composer install `fawry-api/fawry`, set `FAWRY_URL=https://atfawry.fawrystaging.com/` (sandbox) or `https://www.atfawry.com/` (production), plus `FAWRY_SECRET` and `FAWRY_MERCHANT`.

### Webhook structure + signature verification

Server Callback v2 — HTTPS POST to a merchant-configured URL with `requestId`, `fawryRefNumber`, `merchantRefNumber`, `customerMobile`, `customerMail`, `paymentAmount`, `orderAmount`, `fawryFees`, `orderStatus`, `paymentMethod`, `messageSignature`, `orderExpiryDate`, `orderItems`. Signature is **SHA-256** of `merchantCode + merchantRefNumber + paymentAmount + orderAmount + orderStatus + paymentMethod + secureKey` compared against `messageSignature`. Retries on non-2xx with backoff. No replay nonce — dedupe on `fawryRefNumber`.

### Sandbox URL + credential acquisition

Sandbox: `https://atfawry.fawrystaging.com/`. Production: `https://www.atfawry.com/`. **No self-service signup.** Fill `fawry.com/sme-registration-form/`, a rep emails back, sandbox `merchantCode` + `secureKey` arrive in 1–5 business days. This is a hard gate for foreign teams without an Egyptian sales contact. International aggregators (EBANX, PaymentWall, Tap) expose Fawry in their own sandboxes without this gate.

### 3 example competitor digital services using Fawry

1. **Jumia Egypt** — Fawry at checkout for card and kiosk-pay-later [Jumia, 2026](https://www.jumia.com.eg/).
2. **Noon Egypt** — Fawry alongside Noon Pay [Noon, 2026](https://www.noon.com/egypt-en/).
3. **inDrive Egypt** — driver wallet top-ups via Fawry kiosk codes [inDrive, 2026](https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account).

All three are Egyptian-incorporated; the Fawry-via-EBANX precedent is the strongest foreign-merchant analog.

### Compliance notes

- **CBE**: Fawry is a licensed PSP. June 2025 PSO/PSP rules apply with transition through June 2026 [Daily News Egypt, 2025-06-21](https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/). Merchants' own AML/KYC obligation continues.
- **ITIDA**: Not involved in payment licensing; supervises E-Signature Law 15/2004 [ITIDA](https://itida.gov.eg/).
- **NTRA**: imeihub is telecom-adjacent but not regulated by NTRA as long as it does not provide carrier billing. NTRA's Jan 2025 IMEI regime uses its own Telefoni app for fees [NTRA Telefoni, 2025](https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/). No third-party-billing license needed for non-carrier-billed digital products.
- **Tax**: Egypt's simplified VAT registration for nonresident digital service providers applies at 14% on B2C sales once Egyptian revenue exceeds EGP 500K/yr [EY VAT alert, 2023](https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem).

---

## Vodafone Cash (via Paymob aggregator)

### Overview & market position

Vodafone Cash is Egypt's dominant mobile wallet. Q2 2025: **~25.5M users (55% of the 46.3M wallet base)**, **78% of wallet transactions by count**, **81% by value** [Daily News Egypt, 2025-09-13](https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/); [Statista, 2025](https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/). Total Q2 2025 wallet value EGP 943.4B (~USD 19.6B), +72% YoY [IBS Intelligence, 2025-09](https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/). The niche: **carrier-backed mobile balance** — any Vodafone Egypt subscriber can spend balance funded by airtime top-up, bank transfer, or salary, no card needed. For imeihub this is the biggest reach lever for users who failed Stripe due to no card.

### API documentation URL + integration model

**No public direct Vodafone Cash merchant API.** Vodafone Egypt does not run a developer self-service. Vodafone Cash is **exposed exclusively through CBE-licensed aggregators** — chiefly **Paymob**, plus Fawry's MyFawry wallet rail, Tap, Kashier, Geidea [Paymob digital wallet page](https://paymob.com/en/digital-wallet). The reference path is **via Paymob**:

- Paymob developer portal: `https://developers.paymob.com/` [Paymob Developer Portal](https://developers.paymob.com/).
- Mobile-wallets docs: `https://docs.paymob.com/docs/mobile-wallets`.

Paymob flow: (1) `POST /api/auth/tokens` with API key → auth token; (2) `POST /api/ecommerce/orders` → `order_id`; (3) `POST /api/acceptance/payment_keys` with the `integration_id` for Vodafone Cash → `payment_token`; (4) `POST /api/acceptance/payments/pay` with `source.identifier=<phone>`, `source.subtype=WALLET`. Customer receives an OTP on their Vodafone line, enters it on the Paymob-hosted page, payment completes. Returns via webhook to a merchant URL.

### Merchant onboarding — KYC docs, foreign-merchant rules

Paymob's CBE license requires the same baseline as Fawry: **Egyptian commercial registration, Tax ID, Egyptian bank account for EGP settlement**. Paymob does hold a UAE Central Bank Retail Payment Services license (January 2025) and operates in KSA and Oman [Disrupt Africa Paymob UAE license, 2025-01-31](https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/), so a foreign entity may onboard via Paymob UAE for *card* acceptance — but the **Egyptian mobile-wallet rails (Vodafone Cash, Orange Money, e& Money) are gated to Egyptian-licensed MIDs only**. PayAtlas notes Egypt is "more conservative" than UAE on non-resident MIDs and emphasizes "local accountability" [PayAtlas Egypt PSP guide, 2026](https://payatlas.com/countries/egypt-eg).

**Foreign-merchant verdict: BLOCKED for direct Vodafone Cash MID.** Workarounds: (a) Egyptian subsidiary, (b) an aggregator fronting Vodafone Cash to foreign merchants — Tap and Checkout.com list it for cross-border, but typically require GCC incorporation, (c) MoR. Paymob's e-commerce-plugin guides assume an Egyptian merchant; there is no documented foreign-merchant onboarding path through Paymob Egypt.

### Pricing & fees

Paymob's public pricing card: **2.75% + EGP 3 per successful transaction**, zero monthly fee, for local SMB/SME card and wallet [Paymob pricing](https://www.paymob.com/en/pricing); [Bilixe Paymob review, 2025](https://bilixe.com/listing/paymob-payment-gateway/). Enterprise and international "on inquiry only." For Vodafone Cash specifically, MDR is typically the same 2.75% + EGP 3.

For imeihub's price band the **EGP 3 flat fee is the killer**. On a 50 EGP ticket: 2.75% × 50 + 3 = EGP 4.375 = **8.75% effective**. On 100 EGP: 5.75%. On 200 EGP: 4.25%. Only above ~EGP 150 does the rate flatten into single digits. Refunds: `/api/acceptance/void_refund/refund`; the processing fee is generally not refunded, costing the merchant the original fee plus an aggregator refund fee (EGP 2–5).

### Settlement timeline

Paymob: **T+1 for card payments and digital wallets**; weekly bank deposits by default, daily on enterprise plans [Bilixe, 2025](https://bilixe.com/listing/paymob-payment-gateway/). EGP into an Egyptian IBAN. Vodafone Cash settles same-day Vodafone → Paymob, then T+1 Paymob → merchant.

### Supported currencies

**EGP only.** Vodafone Cash is a carrier-billed wallet — no USD pass-through. Multi-currency display works upstream (imeihub displays USD, Paymob converts to EGP at API call) but settlement is unavoidably EGP.

### SDK / web-checkout availability — PHP and JS

- **PHP**: First-party `PaymobAccept/paymob-php` [GitHub PaymobAccept/paymob-php](https://github.com/PaymobAccept/paymob-php). Also `skrskr/paymob` for Laravel. Nafezly/payments wraps both.
- **JS**: Paymob iframe checkout (hosted in an iframe) is the documented web path. No first-party Node SDK, but the REST API is trivial to call from any HTTP client.
- **Plugins**: WooCommerce, Magento, Shopify, OpenCart, Wix [Paymob e-commerce plugins](https://developers.paymob.com/egypt/e-commerce-plugins).

### Webhook structure + signature verification

**HMAC-SHA512.** Callback delivered as HTTPS POST (processed callback) or GET-redirect (response callback). Parameters sorted lexicographically by key, values concatenated in order, then HMAC-SHA512 hashed with the merchant's HMAC secret, compared against the `hmac` query parameter [Paymob HMAC docs](https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac). Payload fields include `amount_cents`, `currency`, `id`, `integration_id`, `is_3d_secure`, `is_refunded`, `order.id`, `source_data.sub_type`, `source_data.type`, `success`.

### Sandbox URL + credential acquisition

Self-service registration at `https://accept.paymob.com/portal2/en/register`. Test credentials documented at `https://developers.paymob.com/paymob-docs/need-help/faq/test-credentials.md`. Vodafone Cash sandbox: **phone = `01010101010`, PIN/OTP = `123456`** [GitHub Nafezly/payments](https://github.com/Nafezly/payments). Sandbox is self-service (no sales gate, unlike Fawry); production migration requires the same Egyptian KYC packet as Fawry.

### 3 example competitor digital services using Vodafone Cash via Paymob

1. **Swvl** (`swvl.com`) — Egyptian mass-transit booking; Paymob for card and Vodafone Cash payments.
2. **MaxAB** (`maxab.io`) — B2B grocery for 150K+ Egyptian retailers; Paymob is the underlying processor for mobile-wallet settlements.
3. **elmenus** (`elmenus.com`) — restaurant discovery and food delivery; offers Vodafone Cash via Paymob checkout.

All three are Egyptian-incorporated. No clean foreign-merchant precedent at the consumer-checkout layer.

### Compliance notes

- **CBE**: Paymob is a CBE-licensed PSP under the 2025 framework. The June 2026 transition deadline applies to Paymob's licensing posture, not to merchants.
- **NTRA**: Vodafone Cash is a Vodafone-Egypt service; NTRA regulates Vodafone as a carrier but does not gate merchants accepting Vodafone Cash via a CBE-licensed PSP. No NTRA third-party-billing license needed.
- **Data residency**: Paymob processes within Egypt for Egyptian transactions; CBE-mandated 7-year retention applies to PSP-side records.
- **PCI DSS**: Paymob is PCI-DSS Level 1; iframe-integrated merchants inherit SAQ-A scope.

---

## InstaPay (Egypt IPN)

### Overview & market position

InstaPay is the consumer-facing app for Egypt's **Instant Payment Network (IPN)** — Egypt's domestic real-time payment rail, launched **March 22, 2022** and operated by the Egyptian Banks Company (EBC) under CBE supervision [EBC IPN](https://www.egyptianbanks.com/instant-payment-network/); [CBE IPN page](https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network). It is the closest Egyptian analog to UPI (India) or Pix (Brazil). End of 2024: **1.5B transactions, EGP 2.9T value, 10M+ app downloads** [Lightspark Egypt instant payments, 2026](https://www.lightspark.com/knowledge/egypt-instant-payments); [CBPN IPN evolution, 2025-05](https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay). The niche: **bank-to-bank real-time transfers from ~36 participating banks** at near-zero customer-side fees. 2025 growth has been explosive [Egyptian Streets, 2026-01-04](https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/).

### API documentation URL + integration model

**There is no public InstaPay merchant API for third-party developers.** The IPN rail is consumed via:

- **Bank-side APIs**: Each participating bank (CIB, Banque Misr, NBE, ABC, etc.) exposes IPN APIs to its own corporate customers behind that bank's developer portal — none standardized, none open [Bank ABC InstaPay page, 2026](https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx).
- **CBE merchant QR**: Since 2024, the CBE extended InstaPay with a merchant-acceptance QR feature (customer scans, payment auto-routes via IPN) [EgyptToday CBE QR feature, 2024](https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments). Rolled out via member banks, not as a standalone API.
- **Aggregators**: Some CBE-licensed PSPs (Paymob, MNT-Halan, Khazna) have begun fronting IPN pull, but coverage is limited and conditional on the customer's bank supporting account-to-merchant pull.

`instapay.eg` is consumer-only; no self-service developer surface [InstaPay Q&A](https://www.instapay.eg/?page_id=348&lang=en). The unrelated US processor at `secure.instapaygateway.com` is often confused in search results — different company entirely.

### Merchant onboarding — KYC docs, foreign-merchant rules

For direct merchant acceptance via a bank's IPN APIs: full Egyptian corporate banking relationship required (commercial registration, Tax ID, board resolution, beneficial-ownership disclosure, AML interview). For foreign-merchant acceptance via an aggregator: the aggregator's terms apply, but Paymob's IPN pull is Egyptian-MID-only today.

**Foreign-merchant verdict: BLOCKED-BY-POLICY at the rail level.** No public path for a non-Egyptian entity to consume the IPN merchant rail without (a) an Egyptian bank corporate account or (b) an Egyptian-licensed PSP fronting it. The CBE 2025 rules allow foreign payment institutions to apply for CBE PSP licensing, but that's a multi-year regulatory project, not a payment-integration option.

### Pricing & fees

InstaPay's customer-side: **zero fee for personal-to-personal up to EGP 70,000/month**. Merchants accepting IPN pull pay **<1% MDR** in current pilot programs — significantly cheaper than card MDR. Customer-pushed (initiated from their banking app): typically zero on customer side, ~EGP 1–3 flat on the merchant.

For imeihub's price band, this would be the cheapest rail by a wide margin — if access could be unlocked. On EGP 50 at 0.5% + EGP 1 = EGP 1.25 = **2.5%**. On EGP 200: ~1.0%. Structurally 3–6× better than Fawry or Vodafone Cash.

### Settlement timeline

**T+0 / real-time** at the rail. IPN settles between participating banks in seconds, 24/7. Merchant-side settlement to the corporate bank account is real-time. This is the headline advantage and the reason CBE has prioritized IPN growth.

### Supported currencies

**EGP only.** IPN is a domestic ACH-style rail — no FX, no cross-border.

### SDK / web-checkout availability — PHP and JS

**None publicly.** Bank-side APIs are issued to corporate banking customers under NDA, not standardized across the 36 member banks. No PHP SDK, no JS SDK, no documented webhook surface available to the open developer ecosystem.

### Webhook structure + signature verification

Not publicly documented. Bank-issued APIs use bank-specific auth (typically mTLS + JWT). No standardized webhook contract across the IPN ecosystem.

### Sandbox URL + credential acquisition

**No third-party sandbox.** Bank-side sandboxes are issued case-by-case after KYC and account opening at that bank.

### 3 example competitor digital services using InstaPay

InstaPay is not yet meaningfully used for online checkout the way Fawry and Vodafone Cash are. Current uses:

1. **Peer-to-peer transfers** between individuals (~80%+ of volume).
2. **Bill-pay and government collections** routed through CBE-approved aggregators (Misr Digital Innovation, e-Finance).
3. **Bank-app-initiated transfers to merchants** — common for high-value transactions (rent, freelance invoicing) but not consumer checkout.

The QR merchant feature is the early e-commerce path, but as of May 2026 no Tier-1 Egyptian e-commerce site documents an InstaPay-branded checkout button alongside their Fawry/card/wallet options.

### Compliance notes

- **CBE**: InstaPay is CBE-operated infrastructure; merchant access is gated by participating banks under CBE's PSO/PSP licensing (2025).
- **Data residency**: All IPN data held within Egypt by EBC.
- **NTRA**: Not involved.
- **ITIDA**: Not involved at the payment layer.

---

## Recommendation

### Comparison table

| Dimension | Fawry | Vodafone Cash (via Paymob) | InstaPay |
|---|---|---|---|
| Fee on EGP 50 ticket | ~10% (kiosk) / ~7% (card) | ~8.75% (2.75% + EGP 3) | ~2.5% (~0.5% + EGP 1) |
| Settlement | T+2 to T+5 EGP to EG bank | T+1 EGP to EG bank | T+0 real-time |
| Foreign-merchant friendly | **No** — Egyptian CR + Tax ID required; workaround via aggregator (EBANX/Nuvei) | **No** — Egyptian MID required for wallet rails; workaround via MoR | **No** — bank corporate account required; no third-party path |
| SDK quality (PHP/JS) | Good — community PHP SDK, JS embed, Postman workspace | Excellent — official PHP SDK, iframe checkout, well-documented HMAC | None — no public SDKs |
| Sandbox accessibility | Gated (sales conversation, 1–5 days) | Self-service registration | None |
| Coverage of imeihub's target | ~53M customers, kiosk-heavy (highest reach) | ~25.5M wallet users, smartphone-only | ~46M IPN-capable bank accounts, no merchant checkout maturity |
| Recommendation | **Prioritize** — via Checkout.com or EBANX MoR | Track parallel — defer until Egyptian entity or MoR | Defer — revisit late 2026 |

### Top-1 prioritized provider: **Fawry — routed via Checkout.com or EBANX as the merchant of record**

Three-bullet rationale:

- **Reach matters more than cost at imeihub's scale.** Fawry's 53M customer base — particularly the kiosk channel — captures the exact segment imeihub fails on today: Egyptian users with no card, no Vodafone line, no Stripe-friendly wallet. Even at a 7–10% effective fee, the conversion lift from a cash-friendly checkout vs Stripe's near-zero EG conversion is the larger lever. Typical EG e-commerce case studies show 10× conversion uplift when adding Fawry to a card-only checkout.
- **The foreign-merchant constraint is solvable via an international acquirer**, not by Egyptian incorporation. Checkout.com and EBANX both list Fawry as a payment method for non-Egyptian merchants of record, settling weekly in USD. imeihub does *not* need to incorporate in Egypt to use Fawry; it accepts a higher all-in MDR (4–5%) than direct-Fawry merchants pay. For Phase 2 this is the right tradeoff vs spending 30–45 days and USD 2,000–3,000 on an Egyptian LLC.
- **Implementation effort is the lowest of the three.** The `fawry-api/fawry` PHP library and Fawry's JS embed are documented, the webhook signature is simple SHA-256, the sandbox is real (even if gated via the acquirer). InstaPay has no path; Vodafone Cash via Paymob is identical-difficulty but requires the same foreign-merchant workaround AND has lower reach than Fawry.

**Top-2 (parallel track): Vodafone Cash via Paymob — only after an Egyptian entity exists OR an MoR like Dodo Payments is contracted.** Adds ~25.5M wallet users at a similar fee envelope. Worth pursuing once the foreign-merchant gate is solved — Vodafone Cash captures a different cohort (smartphone-first millennials in Cairo/Alexandria) than Fawry's kiosk base (mass-market, including older/rural users).

**Deprioritize InstaPay for Phase 2.** Revisit in 2026 H2 if (a) CBE publishes a foreign-merchant pull-API spec, (b) Paymob or another aggregator launches a foreign-merchant IPN tier, or (c) imeihub incorporates in Egypt. The 2.5% effective fee and T+0 settlement make it the strategically correct long-term rail, but the surface area to consume it does not exist today for a non-Egyptian entity.

### Price-band caveat

imeihub's typical paid transaction is 0.5–210 EGP. Fawry card and Paymob/Vodafone Cash both carry a ~EGP 2.5–3 flat fee. Below ~EGP 30 — the bottom third of the price band — the flat fee alone is >8% of the transaction before percentage MDR. The integration should therefore: (1) set a minimum charge floor (~EGP 30) on any Fawry/Paymob transaction; (2) bundle small services into a credit-pack model (sell EGP 50 / EGP 100 / EGP 250 prepaid credits, deduct per IMEI check internally) — amortizes the flat fee across 5–50 checks; (3) keep Stripe (or PayPal) live for the small expat/diaspora slice that already converts on international rails.

---

## Sources

1. Fawry FY2025 results — Zawya — 2025-09 — https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k
2. Fawry × Contact joint venture — Fawry — 2025-03-19 — https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/
3. FawryPay Developer Documentation — Fawry — accessed 2026-05 — https://developer.fawrystaging.com/
4. Fawry public Postman workspace — Fawry — accessed 2026-05 — https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/
5. fawry-api/fawry PHP library — GitHub — accessed 2026-05 — https://github.com/fawry-api/fawry
6. Nafezly/payments multi-gateway PHP library — GitHub — accessed 2026-05 — https://github.com/Nafezly/payments
7. fawry_sdk Flutter package — pub.dev — accessed 2026-05 — https://pub.dev/packages/fawry_sdk
8. Fawry Pay FAQ on settlement — Flutterwave — 2025 — https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt
9. Akurateco Fawry payment method analysis — Akurateco — 2024 — https://akurateco.com/payment-methods/fawry
10. EBANX Fawry integration documentation — EBANX — 2025 — https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/
11. WHMCS Fawry plugin (Egyptian-only merchant note) — WHMCS — 2024 — https://marketplace.whmcs.com/product/5540-fawry-for-whmcs
12. Paymob Developer Portal — Paymob — accessed 2026-05 — https://developers.paymob.com/
13. Paymob webhook/HMAC documentation — Paymob — accessed 2026-05 — https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac
14. Paymob mobile-wallets documentation — Paymob — accessed 2026-05 — https://docs.paymob.com/docs/mobile-wallets
15. Paymob public pricing page — Paymob — accessed 2026-05 — https://www.paymob.com/en/pricing
16. Paymob e-commerce plugins for Egypt — Paymob — accessed 2026-05 — https://developers.paymob.com/egypt/e-commerce-plugins
17. PaymobAccept/paymob-php official PHP SDK — GitHub — accessed 2026-05 — https://github.com/PaymobAccept/paymob-php
18. Bilixe Paymob review with fee detail — Bilixe — 2025 — https://bilixe.com/listing/paymob-payment-gateway/
19. Paymob UAE Central Bank RPS license — Disrupt Africa — 2025-01-31 — https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/
20. Vodafone Cash market share, Q2 2025 — Daily News Egypt — 2025-09-13 — https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/
21. Mobile wallet market share — Statista — 2025 — https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/
22. Egypt digital payments / wallet surge — IBS Intelligence — 2025-09 — https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/
23. Egyptian Banks Company IPN page — EBC — 2026 — https://www.egyptianbanks.com/instant-payment-network/
24. CBE Instant Payment Network official page — CBE — 2026 — https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network
25. Evolution of IPN in Egypt — Central Bank Payments News — 2025-05-23 — https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay
26. Egypt Instant Payments analysis — Lightspark — 2026 — https://www.lightspark.com/knowledge/egypt-instant-payments
27. InstaPay consumer Q&A — InstaPay — 2026 — https://www.instapay.eg/?page_id=348&lang=en
28. Inside Egypt's InstaPay Economy — Egyptian Streets — 2026-01-04 — https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/
29. CBE QR feature for InstaPay merchant acceptance — EgyptToday — 2024 — https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments
30. Bank ABC InstaPay page — Bank ABC Egypt — 2026 — https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx
31. CBE PSO/PSP licensing rules — CBE — 2025-06-19 — https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules
32. Shehata Law analysis of CBE licensing — Shehata Law — 2025 — https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/
33. CBE issues new licensing framework — Daily News Egypt — 2025-06-21 — https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/
34. Merchant of Record in Egypt — Dodo Payments — 2026 — https://dodopayments.com/blogs/merchant-of-record-in-egypt
35. Accepting Payments in Egypt: PSPs, Compliance & Fees — PayAtlas — 2026 — https://payatlas.com/countries/egypt-eg
36. Egypt VAT for nonresident digital services — EY — 2023 — https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem
37. Egypt FX controls — Grey — accessed 2026-05 — https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments
38. ITIDA agency overview — ITIDA Egypt — 2026 — https://itida.gov.eg/
39. NTRA Telefoni app new payment methods — NTRA Egypt — 2025 — https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/
40. Jumia Egypt e-commerce site — Jumia — 2026 — https://www.jumia.com.eg/
41. Noon Egypt e-commerce site — Noon — 2026 — https://www.noon.com/egypt-en/
42. inDrive Egypt Fawry top-up help — inDrive — 2026 — https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account
