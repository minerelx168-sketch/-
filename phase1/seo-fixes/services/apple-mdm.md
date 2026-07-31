---
slug: apple-mdm
service_name: Apple MDM (ON / OFF) Check
price_usd: 0.35
target_keyword: "Apple MDM lock check"
secondary_keywords:
  - "iPhone MDM check IMEI"
  - "Apple DEP enrollment check"
  - "Jamf lock check iPhone"
  - "corporate iPhone MDM"
  - "iPhone enterprise enrollment IMEI"
---

# Apple MDM (ON / OFF) Check — Catch Enterprise-Locked iPhones Before You Buy

Buying an ex-corporate iPhone at a bargain price feels like a win — until you unbox it, start setup, and hit a screen that says "iPhone is managed by [Company Name]" with a padlock icon you cannot remove. That is MDM lock. Mobile Device Management enrollment binds the iPhone to a corporate configuration profile that reasserts itself on every wipe. No factory reset, no DFU restore, and no third-party tool clears it. Only the original enrolling organisation can release the device from their MDM console.

The Apple MDM (ON / OFF) check by IMEI is the pre-purchase tool that catches this problem for $0.35 — before you hand over cash for a device you will not be able to use. It returns a single, clear flag: is this IMEI currently enrolled in an MDM through Apple's Automated Device Enrollment (ADE, formerly DEP), or is it not? A No verdict clears the device for personal use. A Yes verdict tells you to walk away or to require the seller to obtain a formal release from the original organisation before you pay.

The used-iPhone flood in Bangladesh, Egypt, Nigeria, the Middle East, and Southeast Asia is heavily supplied by ex-corporate liquidations — banks, oil companies, hospitals, and government contractors offloading fleets of 300–500 iPhones a year. A large fraction of these units were never formally de-enrolled by their IT departments. They look pristine, run iOS 17, and appear to reset normally — but the setup wizard reveals the MDM lock at the worst possible moment. Do the 35-cent check first.

## What This Check Reveals

The report returns the MDM / DEP enrollment status for the IMEI:

- **MDM: ON.** The IMEI is currently registered to an organisation in Apple Business Manager or Apple School Manager. On next activation, the device will download and enforce that organisation's configuration profile, including forced supervision, app restrictions, VPN configs, and lock-screen banners. You cannot bypass this without the organisation's cooperation.
- **MDM: OFF.** The IMEI is not currently enrolled in any DEP / ADE program. The device is free for personal setup after a normal factory reset.

The report does **not** return the name of the enrolling organisation. Apple deliberately keeps that identifier private. To identify the organisation, either the previous user knows (payroll records, corporate onboarding paperwork) or the seller can provide the purchase receipt or the corporate asset tag. Without the organisation identifier, you cannot request release from an unknown party.

Some reports also include a hint about whether the device is currently *supervised* (a stronger management state) versus merely enrolled, but this is dependent on the tier of data returned and should be treated as a bonus, not a guarantee.

## How It Works

You submit the IMEI in your imeihub dashboard, choose Apple MDM (ON / OFF), and confirm. imeihub forwards the query through certified IMEI API partners that hold authorised access to Apple's Automated Device Enrollment lookup endpoints. The response typically arrives in under 10 seconds, occasionally up to a minute during peak traffic. You see the ON/OFF verdict in your dashboard with a permanent shareable link.

The check is read-only. It does not enroll or de-enroll the device, does not notify the managing organisation, and does not modify anything about the phone. It reads Apple's current DEP registration state for the IMEI and returns the answer.

## Common Use Cases

**Buying an ex-corporate iPhone from a liquidation seller.** Someone is selling ten iPhone 13s from an "ex-office fleet" for $250 each. Before you commit, run the MDM check on every IMEI. If any come back ON, either the corporate seller has failed to release them or the "liquidation" story is dishonest. A single locked iPhone costs you the full purchase price with no recovery path — 35 cents to check is trivial insurance.

**Second-hand marketplace with ex-corporate provenance.** Some sellers on Facebook Marketplace and OLX advertise "office iPhone, boss upgraded, still in box". These are prime MDM candidates. Insist on running the check before payment — a legitimate seller will happily wait 30 seconds.

**Small reseller inventory intake.** Buying batches from wholesalers who source from corporate liquidations? Run MDM on every unit. Sort the OFF units into resale stock and the ON units into a "pending release" pile that you either return to the wholesaler or leave alone until the enrolling organisation confirms release.

**Repair-shop trade-in intake.** Customers bringing in old iPhones for trade credit occasionally hand over ex-employer devices they were allowed to keep but never had properly de-enrolled. A 35-cent check catches this before you resell into a chargeback nightmare.

**Post-purchase self-check.** You just bought a used iPhone from a seller who mentioned it was from an office. Before you reset it, run the MDM check. If it says ON, contact the seller and ask them to route a release request through their former employer's IT department. Do not reset until you have confirmation.

## What to Do If Results Are Negative

- **MDM: ON, pre-purchase.** Do not buy. There is no consumer-side workaround. Only the enrolling organisation can release the device through their MDM console (Jamf, Intune, Kandji, Mosyle, VMware Workspace ONE, or Apple Business Manager directly). Even a friendly seller cannot help unless they can get their former IT team to act. Political reality: many corporate IT teams will not process a release request from a person who no longer works there.
- **MDM: ON, after payment.** Contact the seller and request they arrange release through the original organisation. Failing that, contact your payment provider or marketplace to open a dispute — describe it as "device is enrolled in enterprise Mobile Device Management and cannot be used". Save your imeihub report as evidence.
- **MDM: OFF, but you know the phone is ex-corporate.** Safe to proceed. The organisation released the device correctly. Reset and set it up normally.
- **Ambiguous or "unable to check" result.** Rare. Contact imeihub support with your check ID; some devices from very old enrollments or non-DEP historic pathways can return inconclusive results. In practice, treat any inconclusive result as "assume ON" and pass on the purchase.

## Pricing & Delivery

Confirmed price: **$0.35 per check**. Delivery is typically 5–30 seconds. Credits are purchased via card or PayPal (international cards supported). At 35 cents, the check pays for itself the first time it catches a locked unit.

Combine MDM with a [Full GSX report](/service.php?slug=apple-full-gsx) for high-value ex-corporate purchases where you want the full picture, or add a quick [Apple iCloud (ON / OFF) check](/service.php?slug=apple-icloud-status) at one cent to also verify Find My status. All Apple checks are listed at [/brand-apple](/brand-apple).

## Frequently Asked Questions

**Q: Does the MDM check show which organisation enrolled the device?**
A: No. Apple deliberately withholds the organisation identifier from IMEI lookups. Only Apple, the enrolling organisation itself, or someone with access to the current user's Apple ID can see the enrolling organisation's name. The check returns an ON/OFF flag only.

**Q: Can I bypass MDM lock with a factory reset or a DFU restore?**
A: No. That is precisely what MDM is designed to prevent. Every activation of the device re-checks Apple's DEP servers, sees the enrollment, and re-installs the configuration profile. Third-party "MDM bypass" tools sold online are unreliable, often bricking the device or installing malware, and do not persist through iOS updates.

**Q: The device passed iCloud checks and looks clean at setup — why did MDM still trigger?**
A: iCloud (Find My) and MDM are separate systems. A device can be Find My OFF (safely resettable from an activation-lock perspective) and still enrolled in DEP. The MDM lock triggers after the language and Wi-Fi steps of setup, once the device contacts Apple's activation servers.

**Q: My employer is willing to release the device — how does that work?**
A: The employer's IT admin logs into Apple Business Manager (or Apple School Manager) and removes the device's serial number from the organisation. Once removed, the DEP record is cleared and the next activation attempt will not enforce MDM. Ask your former employer to confirm removal, then re-run this check to verify OFF before you reset the phone.

**Q: Is the price different for volume checking?**
A: The listed price applies to individual checks. For bulk imports (hundreds of IMEIs a day), contact imeihub support for volume arrangements.

**Q: My check says ON but I bought the device from Apple directly at retail. What happened?**
A: Extremely rare, but possible if the retail unit was mistakenly allocated to a corporate account before being restocked. Contact Apple Support with your proof of purchase and the imeihub report — Apple can remove the erroneous DEP registration.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "name": "Apple MDM (ON / OFF) Check",
      "serviceType": "IMEI verification",
      "provider": {
        "@type": "Organization",
        "name": "imeihub",
        "url": "https://imeihub.net"
      },
      "areaServed": "Worldwide",
      "description": "Check whether an iPhone IMEI is enrolled in Apple's Automated Device Enrollment (DEP) for MDM management. Returns a clear ON/OFF verdict via certified IMEI API partners.",
      "offers": {
        "@type": "Offer",
        "price": "0.35",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock",
        "url": "https://imeihub.net/service.php?slug=apple-mdm"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Does the MDM check show which organisation enrolled the device?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. Apple deliberately withholds the organisation identifier from IMEI lookups. Only Apple, the enrolling organisation, or someone with access to the current user's Apple ID can see the enrolling organisation's name. The check returns an ON/OFF flag only."
          }
        },
        {
          "@type": "Question",
          "name": "Can I bypass MDM lock with a factory reset or a DFU restore?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. That is what MDM is designed to prevent. Every activation re-checks Apple's DEP servers and re-installs the configuration profile. Third-party bypass tools are unreliable, often malicious, and do not persist through iOS updates."
          }
        },
        {
          "@type": "Question",
          "name": "The device passed iCloud checks and looks clean at setup — why did MDM still trigger?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "iCloud (Find My) and MDM are separate systems. A device can be Find My OFF and still enrolled in DEP. The MDM lock triggers after the language and Wi-Fi steps, once the device contacts Apple's activation servers."
          }
        },
        {
          "@type": "Question",
          "name": "My employer is willing to release the device — how does that work?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The employer's IT admin logs into Apple Business Manager and removes the serial number from the organisation. Once removed, the DEP record is cleared and the next activation will not enforce MDM. Re-run this check to verify OFF before resetting."
          }
        },
        {
          "@type": "Question",
          "name": "Is the price different for volume checking?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The listed price applies to individual checks. For bulk imports (hundreds of IMEIs a day), contact imeihub support for volume arrangements."
          }
        },
        {
          "@type": "Question",
          "name": "My check says ON but I bought the device from Apple directly at retail. What happened?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Extremely rare, but possible if the retail unit was mistakenly allocated to a corporate account before being restocked. Contact Apple Support with your proof of purchase and the imeihub report — Apple can remove the erroneous DEP registration."
          }
        }
      ]
    }
  ]
}
```
