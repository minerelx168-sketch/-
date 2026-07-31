---
slug: apple-sim-lock
service_name: Apple SIM-Lock Check
price_usd: 0.15
target_keyword: "iPhone SIM lock check"
secondary_keywords:
  - "iPhone carrier lock check IMEI"
  - "iPhone network lock check"
  - "iPhone unlock status check"
  - "US iPhone locked to carrier"
  - "iPhone original carrier lookup"
---

# Apple SIM-Lock Check — Confirm Carrier Lock Status Before You Import or Buy

An iPhone that looks "unlocked" until the moment you insert a local SIM card is a well-known heartbreak for importers, resellers, and cross-border buyers. Grey-market US iPhones — especially bundled AT&T, T-Mobile, and Verizon units — are shipped worldwide with the assumption that carrier locks have been cleared. Often, they have not. The buyer in Dhaka, Cairo, Lagos, or Bangkok inserts a Grameenphone / Orange / MTN / AIS SIM, sees "SIM Not Supported", and now owns an expensive brick until a carrier IMEI unlock is arranged — which for a device the local buyer never had a contract on can be very difficult.

The Apple SIM-Lock check by IMEI is the pre-purchase, pre-import sanity check that catches this. It returns whether the iPhone is currently locked to a specific carrier or is fully unlocked (worldwide-usable). If locked, the report also usually returns the original carrier — the piece of information you need to pursue an unlock request. All of that for approximately $0.15 per check (see the service page for current pricing).

Unlike iCloud or MDM issues, a carrier-locked iPhone is often recoverable — most major carriers now provide free IMEI unlocks for contract-completed or fully-paid units. But you need to know which carrier and you need to know before you commit to the purchase. This service gives you both.

## What This Check Reveals

The report returns two key fields:

- **Lock status: Locked or Unlocked.** Unlocked means the iPhone will accept any GSM SIM from any carrier worldwide (subject to hardware compatibility with local bands). Locked means the phone will only accept SIMs from a specific carrier or bundle of carriers.
- **Original carrier.** Where available, the carrier the iPhone was originally sold on or locked to (e.g. AT&T, T-Mobile, Verizon, Sprint, Vodafone UK, Rogers, Bell, Telstra, EE, and many more). This is the party you need to contact to request an IMEI unlock.

Some tiers of the data return additional context such as the original SIM lock policy code, the SKU-level lock indicator (for example, whether the unit is a "T-Mobile Prepaid" variant vs a postpaid contract SKU), and any known unlock eligibility notes.

The report does **not** perform an unlock. imeihub is a verification service only. If your iPhone is locked, you still need to pursue an unlock through the original carrier's normal channel — but at least now you know where to start.

## How It Works

You submit the IMEI in your imeihub dashboard, select Apple SIM-Lock Check, and confirm. The IMEI is forwarded to certified IMEI API partners that hold authorised access to Apple's carrier lock lookup and, where applicable, to individual carrier eligibility databases. Response time is typically under 30 seconds, occasionally longer for less common carriers where the lookup traverses multiple databases.

The check is read-only. It does not modify the SIM lock state, does not attempt to unlock, and does not contact the carrier. It reads Apple's current record and returns the answer.

## Common Use Cases

**Cross-border iPhone import.** You are buying a batch of US-market iPhone 13s from a wholesaler in Miami, planning to ship them to a reseller in Dhaka or Cairo. Before the container ships, run SIM-Lock checks on every IMEI. Any that come back Locked need either wholesaler-arranged unlocks first or must be priced accordingly for the local market. Shipping locked phones to a market that cannot use them is the single most expensive mistake in this trade.

**Second-hand purchase where seller claims "unlocked".** A used iPhone on Facebook Marketplace is advertised as "factory unlocked". Ask the seller for the IMEI, run the SIM-Lock check for 15 cents, and confirm. About one in five "factory unlocked" listings in high-import markets is actually a carrier-locked unit whose seller has never tested it with a foreign SIM.

**Pre-travel check for your own iPhone.** You are travelling from a country where your iPhone was on-contract to a country where you want to use a local SIM. Run the check before you leave. If Locked, either request an unlock from your carrier ahead of time (usually free if the contract is completed) or plan to use eSIM / roaming instead.

**Resale price calibration.** You are pricing a used iPhone for sale. Unlocked units command a 10–15% premium over locked units in most markets. Confirming Unlocked status with a check lets you list at the higher price with proof.

**Trade-in intake for repair shops.** A customer brings in an iPhone for trade-in credit and swears it is unlocked. The check verifies quickly. If Locked, you either offer a lower trade-in value or refuse the intake — because reselling a Locked unit as Unlocked is a chargeback waiting to happen.

## What to Do If Results Are Negative

- **SIM-Locked, pre-purchase or pre-import.** Renegotiate the price to reflect the lock, arrange an unlock in advance with the original carrier, or pass on the deal. Do not pay unlocked-unit prices for a locked unit.
- **Locked to a US postpaid carrier (AT&T, Verizon, T-Mobile postpaid).** Contact the carrier's unlock portal with the IMEI. If the contract is complete and the device is fully paid, unlock is usually free and processed within 48 hours. If the device is still on-contract or unpaid, unlock will be refused until the balance is cleared.
- **Locked to a US prepaid carrier (T-Mobile Prepaid, Cricket, Metro, Straight Talk).** Unlock policies vary; usually a minimum service period (60–365 days) on the carrier's network is required. Older prepaid SKUs may not be eligible at all.
- **Locked to a foreign carrier you have no relationship with.** Difficult but not impossible. Some carriers (Vodafone UK, Rogers, Bell) will unlock any IMEI for a fee even without an account. Others will not respond to requests from non-customers. If the unlock cannot be arranged, treat the device as usable only on that carrier's network.
- **Third-party "unlock services".** Be extremely cautious. Some legitimate GSM unlocks are performed via authorised whitelisting of the IMEI at the carrier; many advertised services are scams that take payment and disappear, or apply fraudulent unlock requests that later re-lock the device. imeihub does not offer unlock services and cannot recommend any specific provider.

## Pricing & Delivery

Confirmed price: **$0.15 per check** (approximate — see the service page for current pricing, as carrier-lookup pricing occasionally shifts with upstream cost changes). Delivery is typically 10–30 seconds, longer for uncommon carriers. Credits are purchased via card or PayPal (international cards supported).

Combine SIM-Lock with the [Apple iCloud (Clean / Lost) check](/service.php?slug=apple-icloud) and the [Apple MDM check](/service.php?slug=apple-mdm) for a complete pre-purchase screen on any used iPhone. High-value transactions warrant the full [Apple Full GSX report](/service.php?slug=apple-full-gsx). All Apple checks live at [/brand-apple](/brand-apple).

## Frequently Asked Questions

**Q: Can imeihub unlock my SIM-locked iPhone?**
A: No. imeihub is a verification service only. We tell you whether the phone is locked and to which carrier; we do not perform unlocks. Unlock requests go through the original carrier's IMEI unlock portal or, for some markets, through authorised third-party unlock providers.

**Q: The phone reports "Unlocked" but a foreign SIM still says "SIM Not Supported". What is going on?**
A: Unlocked means the software carrier lock is off. If a SIM still does not work, the cause is usually hardware band compatibility — a US CDMA-only iPhone will not work on a GSM-only network in many countries, even fully unlocked. Check the model's supported bands against the local carrier's frequencies before importing.

**Q: The seller claims they can unlock the phone "in five minutes for $30". Is that legitimate?**
A: Almost never. Genuine carrier unlocks are performed by the carrier itself, take 24–72 hours in most cases, and either cost nothing (for eligible contract-complete devices) or a fixed fee published by the carrier. "Five-minute unlock" claims usually mean either a jailbreak-based unlock (which breaks on the next iOS update) or an outright scam.

**Q: If my check says Locked to Sprint, but Sprint no longer exists, who do I contact?**
A: Sprint merged into T-Mobile in 2020. Contact T-Mobile's unlock team with the IMEI and any proof-of-purchase documentation. They now handle legacy Sprint unlock requests.

**Q: How long does a carrier unlock take once requested?**
A: Typically 24–72 hours for major US carriers, sometimes up to a week for smaller regional carriers. Once processed, the unlock is instant on the device the next time it activates on a network — no factory reset required, though occasionally a reboot helps propagate the change.

**Q: Will the SIM-Lock check also tell me if the phone is on a carrier blacklist for unpaid bills?**
A: Not directly — this check focuses on carrier lock only. A blacklist check (device barred from a carrier's network for non-payment or reported stolen at the carrier level) is a separate service. If you are worried about a US-market phone, run both a SIM-Lock check and consider a US carrier blacklist check if imeihub offers one for that carrier.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "name": "Apple SIM-Lock Check",
      "serviceType": "IMEI verification",
      "provider": {
        "@type": "Organization",
        "name": "imeihub",
        "url": "https://imeihub.net"
      },
      "areaServed": "Worldwide",
      "description": "Verify whether an iPhone is carrier-locked or fully unlocked by IMEI. Returns Locked/Unlocked status and, where available, the original carrier. Delivered via certified IMEI API partners.",
      "offers": {
        "@type": "Offer",
        "price": "0.15",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock",
        "url": "https://imeihub.net/service.php?slug=apple-sim-lock"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Can imeihub unlock my SIM-locked iPhone?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. imeihub is a verification service only. We tell you whether the phone is locked and to which carrier; we do not perform unlocks. Unlock requests go through the original carrier's IMEI unlock portal or authorised third-party providers."
          }
        },
        {
          "@type": "Question",
          "name": "The phone reports 'Unlocked' but a foreign SIM still says 'SIM Not Supported'. What is going on?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Unlocked means the software carrier lock is off. If a SIM still does not work, the cause is usually hardware band compatibility — a US CDMA-only iPhone will not work on a GSM-only network even when fully unlocked. Check supported bands against the local carrier's frequencies."
          }
        },
        {
          "@type": "Question",
          "name": "The seller claims they can unlock the phone 'in five minutes for $30'. Is that legitimate?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Almost never. Genuine carrier unlocks take 24–72 hours and either cost nothing (for eligible contract-complete devices) or a fixed fee published by the carrier. 'Five-minute unlock' claims usually mean a jailbreak-based unlock or an outright scam."
          }
        },
        {
          "@type": "Question",
          "name": "If my check says Locked to Sprint, but Sprint no longer exists, who do I contact?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sprint merged into T-Mobile in 2020. Contact T-Mobile's unlock team with the IMEI and any proof-of-purchase documentation. They handle legacy Sprint unlock requests."
          }
        },
        {
          "@type": "Question",
          "name": "How long does a carrier unlock take once requested?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Typically 24–72 hours for major US carriers, sometimes up to a week for smaller regional carriers. Once processed, the unlock is instant on the device the next time it activates on a network."
          }
        },
        {
          "@type": "Question",
          "name": "Will the SIM-Lock check also tell me if the phone is on a carrier blacklist for unpaid bills?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Not directly — this check focuses on carrier lock. A blacklist check is a separate service. If you are worried about a US-market phone, run both a SIM-Lock check and a US carrier blacklist check where available."
          }
        }
      ]
    }
  ]
}
```
