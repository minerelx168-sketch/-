---
slug: apple-basic
service_name: Apple Basic Info
price_usd: 0.10
target_keyword: "Apple Basic Info check"
secondary_keywords:
  - "iPhone activation status check"
  - "Apple IMEI lookup"
  - "iPhone warranty check by IMEI"
  - "iPhone model check IMEI"
  - "iPhone purchase country lookup"
word_count_target: 1400
lang: en
---

# Apple Basic Info — Instant iPhone Model, Activation & Warranty Snapshot

Buying a used iPhone from a marketplace or verifying a stock unit before resale? An Apple Basic Info check by IMEI is the fastest, cheapest way to confirm the seller's claims match the device in front of you. In under a minute, you get the model name Apple's own systems register for that IMEI, whether the phone has ever been activated, whether it is still under warranty, and the country where it was originally sold — all pulled from authoritative Apple data, not a generic TAC guess.

The pain is real. Every week, buyers in Bangladesh, Egypt, Nigeria, and beyond hand over $300–$800 for a "sealed iPhone 13 128GB, US model, unopened" only to discover the box holds a 64GB Chinese unit, a refurbished swap, or an already-activated demo. Apple's basic lookup shuts that door: if the IMEI on the box, the SIM tray, and `*#06#` on-screen don't all match the Basic Info report, you have proof before the money leaves your wallet. For sellers, running the same check and screenshotting the result builds trust and speeds up sales.

This service is the entry-level tier of imeihub's Apple portfolio. It is aimed at the 90% of buyers and small resellers who don't need the full GSX printout — they just need to know the model is real, the phone is unlocked from the demo state, and the warranty status won't blow up their resale price. If you later need deeper data (service history, SIM lock, MDM, or iCloud), imeihub offers dedicated checks for each.

## What This Check Reveals

An Apple Basic Info report returns the core identity and status fields that Apple's activation servers hold for the IMEI you submit:

- **Model name** — the exact SKU as Apple registers it (e.g. "iPhone 14 Pro Max 256GB Deep Purple"), including storage and color where available.
- **Serial number** — cross-reference with the serial engraved on the SIM tray or shown in Settings > General > About.
- **Activation status** — whether the device has ever been activated on Apple's servers. A never-activated unit is closer to "new sealed" than an activated one, and this affects resale value.
- **Warranty coverage** — whether the standard one-year limited warranty is still active, and the estimated expiration date derived from the activation record.
- **Telephone technology** — GSM, CDMA, or dual, useful when importing a US iPhone into a GSM-only market.
- **Purchase country** — the country of first sale, which matters for FaceTime restrictions (Saudi Arabia and UAE units), physical SIM vs eSIM availability (US iPhone 14+), and warranty portability.
- **Initial activation date** (when returned) — helpful for confirming a "brand new" claim.

If any field contradicts what the seller claims — for example, they advertise a US-sold model but Basic Info reports Hong Kong — treat that as a hard red flag and re-negotiate or walk away.

## How It Works

You paste the 15-digit IMEI (dial `*#06#` on the iPhone, or read it from the SIM tray or the retail box barcode), select the Apple Basic Info service, and pay from your imeihub credit balance. The IMEI is forwarded to certified IMEI API partners that hold direct, contract-based access to Apple's GSX-tier data. The response comes back in seconds — occasionally up to a minute during peak load — and is displayed in your dashboard with a permanent link you can share or save as PDF.

imeihub does not scrape Apple's public pages, does not use consumer-facing tools, and does not spoof credentials. The lookup relies on authorised commercial pipelines that operate under Apple's rules for approved service providers. That authorisation is what keeps the data accurate and the service stable — free scraping tools break the moment Apple rotates a token, but a proper API partnership does not.

## Common Use Cases

**Second-hand marketplace purchase.** You found an iPhone 13 on Facebook Marketplace for $420. The seller shows the box, but the shrink-wrap is missing. A $0.10 Basic Info check confirms the model matches the box, tells you whether the device was ever activated, and shows the warranty window. If the report says "Activated, out of warranty" and the seller advertised "sealed unused", you have hard evidence to renegotiate or refuse.

**Small reseller inventory intake.** You buy 20 used iPhones a week from wholesalers. Running Basic Info on every unit before it hits your shelves takes seconds per phone and catches misgraded stock, wrong storage tiers, or units that were quietly swapped for a lower-model unit during shipping.

**Insurance and warranty claim prep.** Before filing a warranty claim with Apple or a third-party insurer, run Basic Info to confirm coverage is still active and to timestamp the state of the device.

**Cross-border import validation.** You are shipping an iPhone from the US to Egypt or Nigeria. Basic Info tells you the model's original purchase country, which affects whether Apple's local walk-in support will service it and whether your buyer will encounter FaceTime or eSIM limitations.

## What to Do If Results Are Negative

- **No record found / invalid IMEI** — the number is wrong, the device is a clone, or the IMEI has been re-programmed. Ask the seller to re-read `*#06#` on camera. If it still fails, do not buy.
- **Model does not match the seller's description** — the box, the tray, and the report must agree. If they do not, either the seller is dishonest or the phone has been re-boxed. Walk away.
- **Already activated when advertised "sealed"** — the device is not truly new. Renegotiate on price or refuse; a genuinely sealed iPhone will report "Not activated" on Basic Info.
- **Warranty expired** — factor the loss of coverage into your offer, and consider adding AppleCare+ within 60 days of purchase if the model still qualifies.
- **Wrong purchase country** — check that the model's cellular bands match your network and that FaceTime works in your region before committing.

## Pricing & Delivery

Confirmed price: **$0.10 per check**. Delivery is instant — seconds under normal load, up to a minute at peak. Credits are purchased through Stripe (card worldwide, PromptPay in Thailand). There is no monthly minimum and unused credits do not expire. Volume users can top up in bulk.

Need more depth? Compare Basic Info with the full [Apple Full GSX report](/service.php?slug=apple-full-gsx) for hardware config and service history, or run a targeted [iCloud status check](/service.php?slug=apple-icloud) before any second-hand purchase. All Apple services live under the [/brand-apple](/brand-apple) hub.

## Frequently Asked Questions

**Q: What is the difference between Apple Basic Info and the free brand & model check?**
A: The free lookup uses a bundled TAC database and returns only the marketing name inferred from the IMEI prefix — no activation or warranty data. Apple Basic Info queries Apple's own records via authorised partners and returns the exact SKU, activation status, warranty state, and purchase country.

**Q: Can Basic Info tell me if the iPhone is iCloud-locked or SIM-locked?**
A: No. Basic Info is intentionally focused on model, activation, warranty, and country. Use the dedicated [Apple iCloud check](/service.php?slug=apple-icloud) for Clean/Lost status and the [Apple SIM-Lock check](/service.php?slug=apple-sim-lock) for carrier lock. Running all three costs less than 50 cents combined.

**Q: The seller says the IMEI check "resets" the warranty timer — is that true?**
A: No. Running a lookup does not touch the device or Apple's records. It is a read-only query. The warranty timer starts on first activation and is unaffected by any IMEI check.

**Q: The report shows "Not activated" but the seller has clearly used the phone. What is going on?**
A: Occasionally, refurbished units go through a factory de-activation before resale. More commonly, though, "Not activated" on a phone the seller is using means the IMEI in the report does not belong to the physical device — the seller may have handed you a spare box. Re-dial `*#06#` on the actual phone and re-run the check.

**Q: Do I need to be a business or reseller to use this service?**
A: No. imeihub is open to individual buyers, resellers, repair shops, and marketplace sellers alike. Sign up, add credit, and you are ready.

**Q: How accurate is the purchase-country field?**
A: It reflects the country of first sale as recorded by Apple. It is highly accurate for retail units. For units sold to enterprises or through complex distributor chains, the country can occasionally be the distribution hub rather than the end market — cross-check with the model part number (Settings > General > About > Model) if in doubt.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "name": "Apple Basic Info Check",
      "serviceType": "IMEI verification",
      "provider": {
        "@type": "Organization",
        "name": "imeihub",
        "url": "https://imeihub.net"
      },
      "areaServed": "Worldwide",
      "description": "Instant Apple IMEI lookup returning model, activation status, warranty coverage, and purchase country from Apple's authoritative records via certified IMEI API partners.",
      "offers": {
        "@type": "Offer",
        "price": "0.10",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock",
        "url": "https://imeihub.net/service.php?slug=apple-basic"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is the difference between Apple Basic Info and the free brand & model check?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The free lookup uses a bundled TAC database and returns only the marketing name inferred from the IMEI prefix — no activation or warranty data. Apple Basic Info queries Apple's own records via authorised partners and returns the exact SKU, activation status, warranty state, and purchase country."
          }
        },
        {
          "@type": "Question",
          "name": "Can Basic Info tell me if the iPhone is iCloud-locked or SIM-locked?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. Basic Info is intentionally focused on model, activation, warranty, and country. Use the dedicated Apple iCloud check for Clean/Lost status and the Apple SIM-Lock check for carrier lock."
          }
        },
        {
          "@type": "Question",
          "name": "The seller says the IMEI check 'resets' the warranty timer — is that true?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. Running a lookup does not touch the device or Apple's records. It is a read-only query. The warranty timer starts on first activation and is unaffected by any IMEI check."
          }
        },
        {
          "@type": "Question",
          "name": "The report shows 'Not activated' but the seller has clearly used the phone. What is going on?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Occasionally, refurbished units go through a factory de-activation before resale. More commonly, 'Not activated' on a phone the seller is using means the IMEI in the report does not belong to the physical device — re-dial *#06# on the actual phone and re-run the check."
          }
        },
        {
          "@type": "Question",
          "name": "Do I need to be a business or reseller to use this service?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. imeihub is open to individual buyers, resellers, repair shops, and marketplace sellers alike. Sign up, add credit, and you are ready."
          }
        },
        {
          "@type": "Question",
          "name": "How accurate is the purchase-country field?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "It reflects the country of first sale as recorded by Apple. It is highly accurate for retail units. For enterprise or complex distributor chains the country can occasionally be the distribution hub rather than the end market."
          }
        }
      ]
    }
  ]
}
```
