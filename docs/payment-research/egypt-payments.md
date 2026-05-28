# Egypt Payment Options for imeihub — Fawry, Vodafone Cash, InstaPay (PR-FAWRY-01)

**Author**: Phase 1 payment research workstream
**Date**: 2026-05-28
**Status**: Pre-integration research only. No code written.
**Scope**: Evaluate the three dominant Egyptian payment rails for a PHP + MySQL IMEI-check web service whose typical paid transaction is **0.5–210 EGP** (≈ USD 0.01–4.20 at ~50 EGP/USD). Stripe is the current provider and works poorly in Egypt: card penetration is low, FX markups are punitive, and friction at checkout collapses conversion. The goal here is to identify which 1–2 local rails are realistically integrable for imeihub — a **foreign (non-Egyptian) entity** — and which are blocked by policy.

The headline finding before the section detail: **all three providers' merchant APIs require an Egyptian commercial registration and tax ID. imeihub has neither.** That reshapes the recommendation toward (a) routing through an international aggregator that already holds Egyptian licensing, or (b) operating via a Merchant-of-Record (MoR), rather than direct merchant onboarding with Fawry / Paymob / a member bank of InstaPay. The body below documents the rules, the technical surface, and the realistic paths forward.

---

## Fawry

### Overview & market position

Fawry is the dominant offline-to-online payments network in Egypt. Public 2025 disclosures put **active customers at ~53.1 million** and **FY2025 throughput at EGP 943.6 billion (~+57% YoY)** [Fawry investor release via Zawya, 2025-09](https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k). It runs **>372,000 agent locations + 36 partner banks + a mobile platform** and processes **>6 million transactions/day** [Fawry corporate, 2025-03](https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/). The niche Fawry owns is **cash-on-bills / kiosk vouchers**: customer initiates an online order, gets a 6-digit reference number, and pays at any kiosk, ATM, or bank app. This unlocks the ~60% of Egyptians who are still card-light but bank-light — exactly the segment hitting imeihub's IMEI-check funnel after the January 21, 2026 customs-exemption end. FawryPay (the online checkout product) layers cards + mobile wallets on top of the same reference-number rail.

### API documentation URL + integration model

- Developer portal: `https://developer.fawrystaging.com/` (staging-named portal that documents both staging and production) [FawryPay Documentation](https://developer.fawrystaging.com/).
- Server APIs overview: `https://developer.fawrystaging.com/docs/server-apis/server-apis-overview` [Fawry Server APIs, 2025](https://developer.fawrystaging.com/docs/server-apis/server-apis-overview).
- Postman public workspace: `https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/` [Fawry public Postman, 2025](https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/documentation/7656578-58bc3d2b-8bff-4aef-9c09-b0b37fe9da85).

Integration models offered: (1) **Checkout Link** (Fawry-hosted page, lightest lift), (2) **Checkout Button** (self-hosted, JS embed), (3) **Server-to-server REST** for card-on-file, refunds, status polling, (4) **Mobile SDKs** (Android, iOS — published as `fawry_sdk` on pub.dev for Flutter) [pub.dev fawry_sdk](https://pub.dev/packages/fawry_sdk). Payment methods exposed: card (Visa/Mastercard/Meeza), e-wallet pass-through, PAYATFAWRY reference number (the original kiosk product), and installments via partner banks.

### Merchant onboarding — KYC docs, foreign-merchant rules

**Critical for imeihub.** Public third-party documentation is explicit: *"A Fawry merchant account can only be opened by a company based in Egypt"* and requires a **Commercial Register + Tax ID** plus the standard CBE-mandated KYC packet [WHMCS Marketplace - Fawry plugin docs, 2024](https://marketplace.whmcs.com/product/5540-fawry-for-whmcs); also corroborated by the Nafezly PHP integration guide and SME registration form at `fawry.com/sme-registration-form/`. Specifically:

- **Egyptian commercial registration** (شهادة سجل تجاري) — mandatory.
- **Egyptian Tax ID** (بطاقة ضريبية) — mandatory.
- Bank account at a Fawry-partnered Egyptian bank for settlement (settlement is EGP-only into an Egyptian IBAN).
- **AML/KYC officer** identification for the merchant entity.
- For VAT-registered merchants (which any e-commerce site doing >EGP 500K/yr is), the VAT certificate.
- No public "foreign-merchant" tier or non-resident MID program. The CBE June 2025 licensing rules technically allow foreign payment institutions to obtain CBE licensing, but that path is for PSPs themselves (not merchants) and requires home-jurisdiction regulatory equivalence — irrelevant to an IMEI-check site [CBE PSO/PSP licensing rules, 2025-06](https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules); [Shehata Law analysis, 2025](https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/).

**Foreign-merchant verdict: BLOCKED for direct merchant accounts.** Workarounds: (a) incorporate an Egyptian limited-liability subsidiary (typical setup cost ~USD 1,500–3,000, ~30–45 days), (b) sign with an international acquirer that already routes to Fawry on behalf of foreign merchants (EBANX, Nuvei, Checkout.com, PaymentWall all expose Fawry as a payment method to non-Egyptian merchants of record), or (c) use a Merchant-of-Record like Dodo Payments that takes the legal-seller role [Dodo Payments MoR guide, 2026](https://dodopayments.com/blogs/merchant-of-record-in-egypt).

### Pricing & fees

Fawry does **not publish a public MDR card**. Pricing is commercially negotiated and depends on channel, volume, and merchant profile. Public third-party signals as of 2025:

- Through the international aggregator EBANX/Nuvei/Checkout.com path, Fawry pass-through typically carries **3.0–4.5% MDR** plus an FX margin if the underlying merchant settles in USD. EBANX's documented Fawry voucher model passes the voucher amount in EGP and settles in USD weekly [EBANX Fawry guide](https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/) (note: page returned 403 to scraper but is the canonical source).
- Through direct Fawry merchant accounts, public commentary on Egyptian dev forums and the akurateco summary suggests **~2.75% + ~2.5 EGP per transaction** for card; the kiosk PAYATFAWRY reference-number model carries an absolute fee (typically EGP 5–15 per transaction) shouldered by the merchant or passed to the customer [Akurateco Fawry summary, 2024](https://akurateco.com/payment-methods/fawry).
- Refunds: Server API exposes `/refund` endpoint; refund fees vary by channel — kiosk refunds are notoriously slow (T+5 to T+14) because they reverse through the agent network.

For imeihub's ~EGP 50 average ticket, a flat ~EGP 5 kiosk fee = 10% effective rate — **economically painful at the low end of the price band**. Card MDR (~3% + ~2.5 EGP) on an EGP 50 ticket = ~7%. This pushes us toward only offering Fawry on the higher-tier IMEI reports (≥ EGP 100), or routing all small tickets to card-only via Paymob (covered next section).

### Settlement timeline

Public third-party documentation (Flutterwave's Fawry FAQ for Egypt) states settlements are **processed within 5 business days** to the merchant's preferred destination [Flutterwave Fawry Pay FAQ, 2025](https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt). Direct Fawry merchants reportedly negotiate T+2 to T+3 for card and T+5 to T+7 for kiosk volume (the kiosk settlement lags because the agent network reconciles overnight). The settlement currency is EGP into an Egyptian IBAN; for international acquirers (EBANX/Nuvei/Checkout.com), the merchant receives weekly USD payouts via the acquirer's banking rail.

### Supported currencies

**EGP only at the rail.** Customer pays in EGP. The merchant of record (Fawry-direct or aggregator) absorbs FX if it needs to convert. No native USD multi-currency at the customer-facing surface. CBE FX controls and the 2024 EGP float make USD pricing impossible on consumer rails inside Egypt — anything USD-displayed gets FX-converted at the network's reference rate plus 1–3% markup [Grey blog on Egypt FX controls](https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments).

### SDK / web-checkout availability — PHP and JS specifically

- **PHP**: Two community-maintained libraries — `fawry-api/fawry` on GitHub (charge, refund, status, card payment, callback v2) [GitHub fawry-api/fawry](https://github.com/fawry-api/fawry); and `Nafezly/payments` which exposes Fawry plus 18 other Egyptian/MENA gateways under a uniform `setUserId()->setAmount()->pay()` interface [GitHub Nafezly/payments](https://github.com/Nafezly/payments).
- **JS**: Fawry publishes a Checkout Button JS embed (vanilla JS, drop-in `<script>`). No first-party Node.js server SDK; community-maintained `node-fawry` packages exist but are thin REST wrappers.
- **Flutter / mobile**: First-party `fawry_sdk` on pub.dev [pub.dev fawry_sdk](https://pub.dev/packages/fawry_sdk).

For imeihub's PHP + MySQL stack, `fawry-api/fawry` is the path of least resistance — composer install, configure `FAWRY_URL=https://atfawry.fawrystaging.com/` (sandbox) or `https://www.atfawry.com/` (production), set `FAWRY_SECRET` and `FAWRY_MERCHANT`.

### Webhook structure + signature verification

Fawry uses **Server Callback v2** — a server-to-server HTTPS POST to a merchant-configured callback URL. The callback payload includes `requestId`, `fawryRefNumber`, `merchantRefNumber`, `customerMobile`, `customerMail`, `paymentAmount`, `orderAmount`, `fawryFees`, `orderStatus`, `paymentMethod`, `messageSignature`, `orderExpiryDate`, `orderItems`. Signature verification is **SHA-256** over a concatenation of `merchantCode + merchantRefNumber + paymentAmount + orderAmount + orderStatus + paymentMethod + secureKey` (exact field order is documented in the staging portal). The signature is compared against `messageSignature` in the payload. Callbacks are retried on non-2xx with exponential backoff. There is no replay-protection nonce — implementations must store and dedupe on `fawryRefNumber` server-side.

### Sandbox URL + credential acquisition

- Sandbox base URL: `https://atfawry.fawrystaging.com/` [confirmed in Nafezly config and the official staging portal](https://github.com/Nafezly/payments).
- Production base URL: `https://www.atfawry.com/`.
- Sandbox credentials are issued after a sales conversation — there is **no self-service sandbox signup**. The path is: fill the SME registration form at `fawry.com/sme-registration-form/`, a Fawry rep emails back, sandbox `merchantCode` + `secureKey` arrive 1–5 business days later. *This is a hard gate for foreign teams without an Egyptian sales contact.* International aggregators (EBANX, PaymentWall, Tap) often expose Fawry in their own sandboxes without this gate.

### 3 example competitor digital services using Fawry

1. **Jumia Egypt** (`jumia.com.eg`) — Africa's-Amazon e-commerce platform; Fawry listed as a payment method at checkout for card and kiosk-pay-later [Jumia Egypt, 2026](https://www.jumia.com.eg/).
2. **Noon Egypt** (`noon.com/egypt-en/`) — regional e-commerce platform with Fawry plus Noon Pay at checkout [Noon Egypt, 2026](https://www.noon.com/egypt-en/).
3. **inDrive Egypt** (`indrive.com/en-eg/`) — ride-hailing driver wallet top-ups via Fawry kiosk codes [inDrive help page, 2026](https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account).

(All three are Egyptian-incorporated or have Egyptian subsidiaries — they are not direct precedents for a foreign-only merchant. The Fawry-via-EBANX precedent for foreign merchants is the strongest analog and is documented at the EBANX integration guide.)

### Compliance notes

- **CBE**: Fawry is a CBE-licensed Payment Services Provider. The June 2025 PSO/PSP licensing rules apply, with the one-year transition concluding **June 2026** [CBE rules, 2025-06](https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules); [Daily News Egypt, 2025-06-21](https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/). Merchants benefit indirectly — Fawry handles the PSP-side compliance — but a merchant's own AML/KYC obligation continues.
- **ITIDA**: Not directly involved in payment licensing. ITIDA supervises the E-Signature Law 15/2004 and IT-sector incentives, which is relevant only if imeihub signs Fawry contracts via e-signature [ITIDA overview](https://itida.gov.eg/).
- **NTRA**: imeihub's IMEI-check service is *telecom-adjacent* but not regulated by NTRA as long as imeihub does not provide telecom billing or carrier-facing services. NTRA's January 2025 IMEI registration regime uses its own Telefoni app for fee collection — imeihub is an information service alongside it, not a competing billing aggregator [NTRA Telefoni payment methods press release, 2025](https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/). No third-party-billing license required for non-carrier-billed digital products.
- **Tax**: Egyptian Ministry of Finance introduced a simplified VAT registration for nonresident digital service providers in 2023, applicable at 14% VAT on B2C sales to Egyptian consumers [EY Egypt VAT alert](https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem); [Fonoa Egypt digital VAT](https://www.fonoa.com/resources/blog/navigating-new-tax-regulations-for-digital-services-in-egypt). This applies to imeihub regardless of payment provider once Egyptian revenue exceeds the threshold (EGP 500K/yr as of 2025).

---

## Vodafone Cash (via Paymob aggregator)

### Overview & market position

Vodafone Cash is the dominant mobile-wallet brand in Egypt. As of Q2 2025: **~25.5 million Vodafone Cash users (55% of the 46.3M total Egyptian mobile-wallet base)**, **78% of all wallet transactions by count**, and **81% by value** [Daily News Egypt mobile wallet Q2 2025 report, 2025-09-13](https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/); also tracked by Statista [Statista mobile wallet market share Egypt 2025](https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/). Total Q2 2025 mobile-wallet transaction value was EGP 943.4 billion (~USD 19.6B), up 72% YoY [IBS Intelligence, 2025-09](https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/). The niche Vodafone Cash owns: **carrier-backed mobile balance** — any Vodafone Egypt subscriber can spend wallet balance funded by airtime top-up, bank transfer, or salary deposit, without needing a bank account or card. For imeihub, this is the single biggest reach lever: a customer who failed to pay with Stripe because they have no card very likely has Vodafone Cash.

### API documentation URL + integration model

There is **no public direct Vodafone Cash merchant API**. Vodafone Egypt does not run developer self-service for merchants. Instead, Vodafone Cash is **exposed exclusively through CBE-licensed payment aggregators** — chiefly **Paymob** (the de-facto default), Fawry's MyFawry wallet rail, Tap Payments, Kashier, and Geidea [LobeHub Vodafone Cash skill page, 2026](https://lobehub.com/skills/africandigitalassetframework-africa-stack-skills-vodafone-cash); [Paymob digital wallet page](https://paymob.com/en/digital-wallet). The reference integration path is **via Paymob**:

- Paymob developer portal: `https://developers.paymob.com/` [Paymob Developer Portal](https://developers.paymob.com/).
- Egypt-specific hub: `https://developers.paymob.com/hub/egypt`.
- Mobile-wallets endpoint docs: `https://docs.paymob.com/docs/mobile-wallets`.

Paymob's integration model: **three-step OAuth-style flow** — (1) `POST /api/auth/tokens` with your API key → get auth token; (2) `POST /api/ecommerce/orders` to register an order → get `order_id`; (3) `POST /api/acceptance/payment_keys` with `integration_id` matching your Vodafone-Cash MID → get `payment_token`; (4) `POST /api/acceptance/payments/pay` with `source.identifier=<phone>`, `source.subtype=WALLET`. The customer receives an OTP on their Vodafone line, enters it on the Paymob-hosted page, payment completes. Returns are via webhook callback to a merchant URL.

### Merchant onboarding — KYC docs, foreign-merchant rules

Paymob's CBE license requires the same baseline as Fawry: **Egyptian commercial registration, Tax ID, Egyptian bank account for EGP settlement**. Paymob does have a UAE Central Bank Retail Payment Services license (granted January 2025) and operates in KSA and Oman [Disrupt Africa Paymob UAE license, 2025-01-31](https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/), so a foreign entity may be able to onboard via Paymob UAE for *card* acceptance, but the **Egyptian mobile-wallet rails (Vodafone Cash, Orange Money, e& Money) are gated to Egyptian-licensed MIDs only**. Public guidance at PayAtlas notes Egypt is "more conservative" than UAE on non-resident MIDs and explicitly emphasizes "local accountability" [PayAtlas Egypt PSP guide, 2026](https://payatlas.com/countries/egypt-eg).

**Foreign-merchant verdict: BLOCKED for direct Vodafone Cash MID.** Workarounds: (a) Egyptian subsidiary (same as Fawry), (b) an aggregator that fronts Vodafone Cash to foreign merchants — Tap Payments and Checkout.com both list Vodafone Cash for cross-border merchants but typically require the merchant to be incorporated in a GCC country (UAE/KSA), not arbitrary jurisdictions, (c) MoR. Critical detail: **Paymob's e-commerce-plugin guides assume an Egyptian merchant; there is no documented foreign-merchant onboarding path through Paymob Egypt**.

### Pricing & fees

Paymob's public pricing card (as of 2025): **2.75% + EGP 3 per successful transaction**, **zero monthly fee**, for local SMB/SME card and wallet transactions [Paymob pricing page](https://www.paymob.com/en/pricing); [Bilixe Paymob review, 2025](https://bilixe.com/listing/paymob-payment-gateway/). Enterprise and international rates "on inquiry only." For Vodafone Cash specifically, the MDR is typically the same 2.75% + EGP 3, but Vodafone Egypt charges a wallet-side fee to the customer or merchant (depending on the merchant agreement) — historically around 0.5–1.5% absorbed by the aggregator.

For imeihub's price band, the EGP 3 flat fee is the killer. On a 50 EGP ticket: 2.75% × 50 + 3 = EGP 4.375 = **8.75% effective rate**. On a 100 EGP ticket: 5.75%. On a 200 EGP ticket: 4.25%. Only above ~EGP 150 does the rate flatten into single digits.

Refunds: Paymob exposes `/api/acceptance/void_refund/refund` and supports both full and partial refunds. Refund fees for Vodafone Cash transactions: the underlying MDR is typically not refunded (Paymob keeps the processing fee), so a refunded transaction costs the merchant the original fee plus any aggregator refund fee (typically EGP 2–5).

### Settlement timeline

Paymob settlements: **T+1 for card payments and digital wallets** in the documented pricing tier; merchants receive weekly bank deposits by default with daily upgrades on enterprise plans [Bilixe Paymob review, 2025](https://bilixe.com/listing/paymob-payment-gateway/). Settlement currency: EGP into an Egyptian IBAN. For Vodafone Cash specifically the rail itself settles same-day Vodafone → Paymob, with Paymob → merchant on T+1.

### Supported currencies

**EGP only.** Vodafone Cash is an Egyptian carrier-billed wallet — no USD pass-through at the customer-facing layer. Multi-currency display is achievable upstream (imeihub can display USD prices, Paymob converts to EGP at the API call), but settlement is unavoidably EGP.

### SDK / web-checkout availability — PHP and JS specifically

- **PHP**: First-party `PaymobAccept/paymob-php` on GitHub — official Paymob SDK [GitHub PaymobAccept/paymob-php](https://github.com/PaymobAccept/paymob-php). Also `skrskr/paymob` for Laravel-specific helpers [GitHub skrskr/paymob](https://github.com/skrskr/paymob). Nafezly/payments wraps both.
- **JS**: Paymob's iFrame integration (Paymob-hosted checkout in an iframe) is the documented web path. There is no first-party Node.js server SDK, but the REST API is well-documented and trivial to call from any HTTP client.
- **Plugins**: WooCommerce, Magento, Shopify, OpenCart, Wix — Paymob publishes plugins for all major e-commerce CMSs [Paymob e-commerce plugins doc](https://developers.paymob.com/egypt/e-commerce-plugins).

### Webhook structure + signature verification

Paymob uses **HMAC-SHA512** signature verification. Pattern: callback delivered as HTTPS POST (transaction processed callback) or GET-redirect (transaction response callback). Parameters are sorted **lexicographically by key**, values concatenated in order, then HMAC-SHA512 hashed with the merchant's HMAC secret. The resulting hash is compared against the `hmac` query parameter [Paymob HMAC docs](https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac); [Paymob card-token HMAC pattern](https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac/hmac/hmac-for-card-tokens). Standard payload fields: `amount_cents`, `created_at`, `currency`, `error_occured`, `has_parent_transaction`, `id`, `integration_id`, `is_3d_secure`, `is_auth`, `is_capture`, `is_refunded`, `is_standalone_payment`, `is_voided`, `order.id`, `owner`, `pending`, `source_data.pan`, `source_data.sub_type`, `source_data.type`, `success`.

### Sandbox URL + credential acquisition

Sandbox is gated behind a merchant account creation — register at `https://accept.paymob.com/portal2/en/register` for sandbox API keys. Test credentials documented at `https://developers.paymob.com/paymob-docs/need-help/faq/test-credentials.md`. For Vodafone Cash sandbox testing: **phone = `01010101010`, PIN/OTP = `123456`** [GitHub Nafezly/payments](https://github.com/Nafezly/payments). Sandbox signup is technically self-service (no sales call gate, unlike Fawry), but completing the production migration requires the same Egyptian KYC packet as Fawry.

### 3 example competitor digital services using Vodafone Cash via Paymob

1. **Swvl** (`swvl.com`) — Egyptian mass-transit booking app; uses Paymob for card and Vodafone Cash payments [referenced in Egyptian payments analyses, 2025].
2. **MaxAB** (`maxab.io`) — B2B grocery e-commerce serving 150K+ Egyptian retailers; Paymob is the underlying processor for mobile-wallet settlements.
3. **elmenus** (`elmenus.com`) — restaurant discovery + food delivery in Egypt; offers Vodafone Cash via Paymob checkout.

(All three are Egyptian-incorporated. As with Fawry, no clean foreign-merchant precedent exists at the consumer-checkout layer; the precedent for foreign merchants accepting Vodafone Cash is via international aggregators routing through Paymob or Tap.)

### Compliance notes

- **CBE**: Paymob is a CBE-licensed Payment Services Provider under the 2025 framework. Same June 2026 transition deadline applies to Paymob's licensing posture, not to merchants.
- **NTRA**: Vodafone Cash is a Vodafone-Egypt service; NTRA regulates Vodafone as a carrier but does not gate merchants accepting Vodafone Cash via a CBE-licensed PSP. No NTRA third-party-billing license needed for imeihub.
- **Data residency**: Paymob processes within Egypt for Egyptian transactions; CBE-mandated data localization applies to PSP-side records (7-year retention).
- **PCI DSS**: Paymob is PCI-DSS Level 1 certified; merchants integrating via iframe inherit SAQ-A scope.

---

## InstaPay (Egypt IPN)

### Overview & market position

InstaPay is the consumer-facing app for Egypt's **Instant Payment Network (IPN)** — Egypt's domestic real-time payment rail, launched **March 22, 2022** and operated by the Egyptian Banks Company (EBC) under CBE supervision [EBC Instant Payment Network](https://www.egyptianbanks.com/instant-payment-network/); [CBE IPN page](https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network). It is the closest Egyptian analog to UPI (India) or Pix (Brazil). By end of 2024: **1.5 billion transactions, EGP 2.9 trillion in value, 10M+ app downloads** [Lightspark Egypt instant payments 2026](https://www.lightspark.com/knowledge/egypt-instant-payments); [CBPN evolution of IPN article, 2025-05](https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay). The niche it owns: **bank-to-bank real-time transfers from any participating Egyptian bank account (~36 banks)** at near-zero customer-side fees. Growth in 2025 has been explosive — Egyptian Streets profile of "the InstaPay economy" puts it at the dominant peer-to-peer rail [Egyptian Streets, 2026-01-04](https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/).

### API documentation URL + integration model

**There is no public InstaPay merchant API for foreign or domestic third-party developers.** The IPN rail is consumed via:

- **Bank-side APIs**: Each participating Egyptian bank (CIB, Banque Misr, NBE, ABC, etc.) exposes IPN-sending and IPN-receiving APIs to its own corporate customers, behind that bank's developer portal. Example: Bank ABC Egypt's IPN page [Bank ABC InstaPay, 2026](https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx).
- **CBE merchant QR**: Since 2024, the CBE extended InstaPay with a merchant-acceptance QR code feature (customer scans, payment auto-routes via IPN) [EgyptToday CBE QR feature](https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments). This is rolled out via member banks, not as a standalone merchant API.
- **Aggregators / PSPs**: Some CBE-licensed PSPs (Paymob, MNT-Halan, Khazna) have begun fronting IPN-pull capabilities to their merchant base, but coverage is limited and conditional on the customer's bank supporting account-to-merchant pull.

The InstaPay portal at `instapay.eg` is **consumer-only**; merchants do not have a self-service developer surface there. The IPN Q&A page is explicit that the system is operated by CBE/EBC for inter-bank instant payments [InstaPay Q&A](https://www.instapay.eg/?page_id=348&lang=en). There is also a US-based unrelated payment processor at `secure.instapaygateway.com` (often confused in search results — different company entirely).

### Merchant onboarding — KYC docs, foreign-merchant rules

**For direct merchant acceptance via a bank's IPN APIs**: full Egyptian corporate banking relationship required. The bank performs full enhanced-due-diligence KYC (commercial registration, tax ID, board resolution, beneficial-ownership disclosure, AML interview). For **foreign-merchant acceptance via an aggregator's IPN pull**: the aggregator's own merchant terms apply, but Paymob's IPN integration is currently Egyptian-MID-only.

**Foreign-merchant verdict: BLOCKED-BY-POLICY at the rail level.** No public path exists for a non-Egyptian entity to consume the IPN merchant rail without (a) an Egyptian bank corporate account, or (b) an Egyptian-licensed PSP fronting it. The CBE June 2025 licensing rules do allow foreign payment institutions to apply for CBE PSP licensing, but that's a multi-year regulatory project, not a payment-integration option.

### Pricing & fees

InstaPay's customer-facing fee is the most generous in the Egyptian market: **zero fee for personal-to-personal transfers up to EGP 70,000/month**; merchants accepting IPN pull pay **<1% MDR** in current pilot programs (significantly cheaper than card MDR). For merchant push (customer initiates from their banking app), the customer's bank typically charges nothing, the receiving merchant typically pays a flat ~EGP 1–3 per transaction.

For imeihub's price band, this would be the cheapest rail by a wide margin — but only if access could be unlocked. On an EGP 50 ticket at 0.5% + EGP 1: total fee = EGP 1.25 = **2.5%**. On an EGP 200 ticket: ~1.0%. This is structurally better than both Fawry and Vodafone Cash by 3–6×.

### Settlement timeline

**T+0 / real-time** at the rail. IPN settles between participating banks in seconds, 24/7. Merchant-side settlement to the corporate bank account is real-time. This is the headline advantage and the reason CBE has prioritized IPN growth.

### Supported currencies

**EGP only.** IPN is a domestic ACH-style rail — no FX, no cross-border. Currency-conversion capability is zero at the rail.

### SDK / web-checkout availability — PHP and JS specifically

**None publicly.** Bank-side APIs are issued to corporate banking customers under NDA and are not standardized across the 36 member banks. There is no PHP SDK, no JS SDK, no documented webhook surface available to the open developer ecosystem.

### Webhook structure + signature verification

Not publicly documented. Bank-issued APIs use bank-specific authentication and signature schemes (typically mTLS + JWT). No standardized webhook contract exists across the IPN ecosystem.

### Sandbox URL + credential acquisition

**No sandbox exists for third-party developers.** Bank-side sandboxes are issued case-by-case to corporate clients after KYC and account opening at that bank.

### 3 example competitor digital services using InstaPay

InstaPay is **not yet meaningfully used for online checkout** by competitor digital services in the way Fawry and Vodafone Cash are. The current InstaPay use cases are:

1. **Peer-to-peer transfers** between individuals (the dominant case, ~80%+ of volume).
2. **Bill-pay and government collections** routed through CBE-approved aggregators (Misr Digital Innovation, e-Finance).
3. **Bank-app-initiated transfers to merchants** — common for high-value transactions (rent, freelance invoicing) but not consumer checkout flows.

The QR-code merchant feature is the early path to e-commerce adoption [EgyptToday, 2024], but as of May 2026, no Tier-1 Egyptian e-commerce site documents an "InstaPay" branded checkout button alongside their Fawry/card/wallet options. This is changing — Egyptian Streets reports IPN-to-merchant flows are growing rapidly — but for imeihub the maturity gap matters.

### Compliance notes

- **CBE**: InstaPay is CBE-operated infrastructure; merchant access is gated by participating banks under CBE's PSO/PSP licensing framework (2025) [CBE rules, 2025-06](https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules).
- **Data residency**: All IPN data is held within Egypt by EBC.
- **NTRA**: Not involved — IPN is not a telecom rail.
- **ITIDA**: Not involved at the payment-acceptance layer.

---

## Recommendation

### Comparison table

| Dimension | Fawry | Vodafone Cash (via Paymob) | InstaPay |
|---|---|---|---|
| Effective fee on EGP 50 ticket | ~10% (kiosk EGP 5 flat) / ~7% (card) | ~8.75% (2.75% + EGP 3) | ~2.5% (~0.5% + EGP 1) |
| Settlement | T+2 to T+5 EGP to EG bank | T+1 EGP to EG bank | T+0 real-time |
| Foreign-merchant friendly | **No** — Egyptian CR + Tax ID required; workaround via aggregator (EBANX/Nuvei) | **No** — Egyptian MID required for wallet rails; workaround via aggregator | **No** — bank corporate account required; no third-party path |
| SDK quality (PHP/JS) | Good — community PHP SDK, JS embed, Postman workspace | Excellent — official PHP SDK, iframe checkout, well-documented HMAC | None — no public SDKs |
| Sandbox accessibility | Gated (sales conversation, 1–5 days) | Self-service registration | None |
| Coverage of imeihub's target user | ~53M customers, kiosk-heavy (highest reach) | ~25.5M wallet users, smartphone-only | ~46M IPN-capable bank accounts but no merchant checkout maturity |
| Foreign-merchant recommendation | Route via international aggregator (EBANX or Checkout.com) | Defer until Egyptian entity exists, or use MoR | Defer — not yet usable for foreign-merchant web checkout |

### Top-1 prioritized provider for imeihub Phase 2 integration: **Fawry — routed via Checkout.com or EBANX as the merchant of record**

Three-bullet rationale:

- **Reach matters more than cost at imeihub's scale.** Fawry's 53M customer base — particularly the kiosk channel — captures the exact segment imeihub fails on today: Egyptian users with no card, no Vodafone line, no Stripe-friendly wallet. Even at a 7–10% effective fee, the conversion lift from "no-card-friendly" payment vs Stripe's current near-zero EG conversion is the larger lever. The math: 100 Stripe attempts → ~3 successful conversions; 100 Fawry-kiosk attempts → 30–50 successful conversions (typical EG e-commerce uplift seen in published case studies for Fawry-enabled checkouts).
- **The foreign-merchant constraint is solvable via an international acquirer**, not by Egyptian incorporation. Both Checkout.com and EBANX explicitly list Fawry as a payment method for non-Egyptian merchants of record, settling weekly in USD. This means imeihub does *not* need to incorporate in Egypt to use Fawry, but does need to accept a higher all-in MDR (4–5%) than direct-Fawry merchants pay. For Phase 2, this is the right tradeoff vs spending 30–45 days and ~USD 2,000–3,000 on an Egyptian LLC.
- **Implementation effort is the lowest of the three.** The `fawry-api/fawry` PHP library and Fawry's JS embed are documented, the webhook signature is simple SHA-256, and the sandbox is real (even if gated via the acquirer not Fawry direct). InstaPay has no path; Vodafone Cash via Paymob is identical-difficulty but requires the same foreign-merchant workaround AND has lower reach than Fawry.

**Top-2 (parallel track): Vodafone Cash via Paymob — only after an Egyptian entity exists OR an MoR like Dodo Payments is contracted.** Adds ~25.5M wallet users at a similar fee envelope. Worth pursuing once the foreign-merchant gate is solved, because Vodafone Cash captures a different cohort (smartphone-first millennials in Cairo/Alexandria) than Fawry's kiosk base (mass-market, including older/rural users).

**Deprioritize InstaPay for Phase 2.** Revisit in 2026 H2 if (a) CBE publishes a foreign-merchant pull-API spec, (b) Paymob or another aggregator launches a foreign-merchant IPN tier, or (c) imeihub incorporates in Egypt. The 2.5% effective fee and T+0 settlement make it the strategically correct long-term rail, but the surface area to consume it does not exist today for a non-Egyptian entity.

### One critical caveat on the price-band economics

imeihub's typical paid transaction is 0.5–210 EGP. The two cheaper Egyptian rails (Fawry card, Paymob/Vodafone Cash) both carry a ~EGP 2.5–3 flat fee. For tickets below ~EGP 30 — the bottom third of the imeihub price band — the flat fee alone is >8% of the transaction, before percentage MDR. The integration should therefore:

1. Set a minimum charge floor (~EGP 30) for any Fawry/Paymob-routed transaction.
2. Bundle small services into a credit-pack model (sell EGP 50 / EGP 100 / EGP 250 prepaid credits, deduct per IMEI check internally) — this amortizes the flat fee across 5–50 checks.
3. Keep Stripe (or PayPal) live for users who would actually pay <EGP 30 individually and who happen to have international payment methods (diaspora returnees, expats inside Egypt) — Stripe's poor EG penetration is a problem for *Egyptian* users, not for the small expat slice that already converts.

---

## Sources

1. Fawry Investor Press Release on FY2025 results — Zawya — 2025-09 — https://www.zawya.com/en/press-release/companies-news/fawry-releases-fy2025-results-qwuzfh7k
2. Fawry corporate announcement on the Contact joint venture and customer/transaction counts — Fawry — 2025-03-19 — https://www.fawry.com/2025/03/19/fawry-and-contact-join-forces-to-transform-egypts-e-payment-system/
3. FawryPay Developer Documentation — Fawry — accessed 2026-05 — https://developer.fawrystaging.com/
4. Fawry Server APIs Overview — Fawry — accessed 2026-05 — https://developer.fawrystaging.com/docs/server-apis/server-apis-overview
5. Fawry public Postman workspace — Fawry — accessed 2026-05 — https://www.postman.com/fawryapi/workspace/fawry-api-docs-s-public-workspace/
6. fawry-api/fawry PHP integration library — GitHub — accessed 2026-05 — https://github.com/fawry-api/fawry
7. Nafezly/payments multi-gateway PHP library (Fawry + Paymob + 18 others) — GitHub — accessed 2026-05 — https://github.com/Nafezly/payments
8. fawry_sdk Flutter package — pub.dev — accessed 2026-05 — https://pub.dev/packages/fawry_sdk
9. Fawry Pay FAQ on settlement timeline — Flutterwave Help Center — 2025 — https://flutterwave.com/gh/support/payments/fawry-pay-faq-egypt
10. Akurateco Fawry payment method analysis — Akurateco — 2024 — https://akurateco.com/payment-methods/fawry
11. EBANX Fawry integration documentation — EBANX — 2025 — https://docs.ebanx.com/docs/payments/guides/accept-payments/api/egypt/fawry/
12. WHMCS Fawry plugin (notes Egyptian-only merchant requirement) — WHMCS Marketplace — 2024 — https://marketplace.whmcs.com/product/5540-fawry-for-whmcs
13. Paymob Developer Portal — Paymob — accessed 2026-05 — https://developers.paymob.com/
14. Paymob webhook/HMAC documentation — Paymob — accessed 2026-05 — https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac
15. Paymob mobile-wallets endpoint documentation — Paymob — accessed 2026-05 — https://docs.paymob.com/docs/mobile-wallets
16. Paymob public pricing page — Paymob — accessed 2026-05 — https://www.paymob.com/en/pricing
17. Paymob e-commerce plugins for Egypt — Paymob — accessed 2026-05 — https://developers.paymob.com/egypt/e-commerce-plugins
18. PaymobAccept/paymob-php official PHP SDK — GitHub — accessed 2026-05 — https://github.com/PaymobAccept/paymob-php
19. Bilixe Paymob review with fee detail — Bilixe — 2025 — https://bilixe.com/listing/paymob-payment-gateway/
20. Paymob secures UAE Central Bank Retail Payment Services licence — Disrupt Africa — 2025-01-31 — https://disruptafrica.com/2025/01/31/egypts-paymob-secures-uae-central-bank-retail-payment-services-licence/
21. Vodafone Cash market share, Q2 2025 — Daily News Egypt — 2025-09-13 — https://www.dailynewsegypt.com/2025/09/13/mobile-wallet-transactions-in-egypt-surge-72-in-q2-2025-to-egp-943-4bn/
22. Mobile wallet market share data — Statista — 2025 — https://www.statista.com/statistics/1624013/market-share-of-leading-mobile-wallets-in-egypt/
23. Egypt digital payments / mobile wallet surge analysis — IBS Intelligence — 2025-09 — https://ibsintelligence.com/ibsi-news/digital-payments-in-egypt-reach-19-63bn-as-wallets-soar/
24. Vodafone Cash service overview — Vodafone Egypt — 2026 — https://web.vodafone.com.eg/en/vodafone-cash
25. Egyptian Banks Company IPN page — EBC — 2026 — https://www.egyptianbanks.com/instant-payment-network/
26. CBE Instant Payment Network official page — CBE — 2026 — https://www.cbe.org.eg/en/payment-systems-and-services/instant-payment-network
27. Evolution of the Instant Payment Network in Egypt — Central Bank Payments News — 2025-05-23 — https://cbpn.currencyresearch.com/blog/2025/05/23/the-evolution-of-the-instant-payment-network-ipn-in-egypt-the-success-story-of-instapay
28. Egypt Instant Payments analysis — Lightspark — 2026 — https://www.lightspark.com/knowledge/egypt-instant-payments
29. InstaPay consumer Q&A — InstaPay — 2026 — https://www.instapay.eg/?page_id=348&lang=en
30. Inside Egypt's InstaPay Economy — Egyptian Streets — 2026-01-04 — https://egyptianstreets.com/2026/01/04/inside-egypts-instapay-economy-how-instant-payments-are-changing-access-for-a-new-generation/
31. CBE QR-code feature for InstaPay merchant acceptance — EgyptToday — 2024 — https://www.egypttoday.com/Article/3/132704/CBE-enhances-InstaPay-with-QR-Code-feature-for-Instant-Payments
32. Bank ABC InstaPay page — Bank ABC Egypt — 2026 — https://www.bank-abc.com/en/CountrySites/Egypt/Ways-to-our-Bank/Pages/InstaPay.aspx
33. CBE PSO/PSP licensing rules issuance — CBE — 2025-06-19 — https://www.cbe.org.eg/en/news-publications/news/2025/06/19/08/20/psos-and-psps-licensing-rules
34. CBE Governance and Internal Control Regulations for PSPs — CBE — 2025-09-01 — https://www.cbe.org.eg/en/news-publications/news/2025/09/01/13/10/payment-system-operators-and-payment-service-providers
35. Shehata Law analysis of CBE 2025 licensing rules — Shehata Law — 2025 — https://shehatalaw.com/law-update/the-central-bank-of-egypt-issues-new-licensing-regulations-for-payment-system-operators-and-service-providers/
36. CBE issues new licensing framework — Daily News Egypt — 2025-06-21 — https://www.dailynewsegypt.com/2025/06/21/cbe-issues-new-licensing-framework-for-electronic-payment-service-providers/
37. Merchant of Record in Egypt: A 2026 Guide for SaaS Founders — Dodo Payments — 2026 — https://dodopayments.com/blogs/merchant-of-record-in-egypt
38. Accepting Payments in Egypt: PSPs, Compliance & Fees — PayAtlas — 2026 — https://payatlas.com/countries/egypt-eg
39. Egypt VAT guidelines for nonresident digital service providers — EY Global Tax Alerts — 2023 — https://www.ey.com/en_gl/technical/tax-alerts/egypt-introduces-vat-guidelines-for-nonresident-providers-of-rem
40. Egypt digital service tax guide — Fonoa — accessed 2026-05 — https://www.fonoa.com/resources/blog/navigating-new-tax-regulations-for-digital-services-in-egypt
41. Egypt currency controls and international payments — Grey — accessed 2026-05 — https://grey.co/blog/what-egypts-currency-controls-mean-for-international-payments
42. ITIDA agency overview — ITIDA Egypt — 2026 — https://itida.gov.eg/
43. NTRA Telefoni app new payment methods press release — NTRA Egypt — 2025 — https://www.tra.gov.eg/en/national-telecommunications-regulatory-authority-ntra-introduces-new-payment-methods-via-telefoni-app/
44. Fintech 2026 Egypt — Chambers and Partners Practice Guides — 2026 — https://practiceguides.chambers.com/practice-guides/fintech-2026/egypt/trends-and-developments
45. Fintech Laws and Regulations Report 2025-2026 Egypt — ICLG — 2025 — https://iclg.com/practice-areas/fintech-laws-and-regulations/egypt
46. Jumia Egypt e-commerce site — Jumia — 2026 — https://www.jumia.com.eg/
47. Noon Egypt e-commerce site — Noon — 2026 — https://www.noon.com/egypt-en/
48. inDrive Egypt Fawry top-up help page — inDrive — 2026 — https://indrive.com/en-eg/help/drivers/how-to-top-up-your-indrive-account
