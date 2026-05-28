---
task_id: AR-NG-03
title: MTN, Glo, Airtel, 9mobile — IMEI Check Per Carrier in Nigeria
target_keyword: mtn imei check nigeria
lang: en-NG
slug: mtn-glo-airtel-9mobile-imei-check-nigeria
word_count_target: 1000
---

# MTN, Glo, Airtel, 9mobile — IMEI Check Per Carrier in Nigeria

Each Nigerian network keeps a record of the IMEI it has seen attached to your SIM. With the NCC-DMS (Device Management System) live since October 2025, those records now sync into a central register run by the Nigerian Communications Commission (NCC). But each network — MTN Nigeria, Glo (Globacom), Airtel Nigeria and 9mobile — still has its own short-code, customer-care channel and unlock process you can use directly from the phone.

This guide gives you the working USSD codes, the official channels, and the carrier-lock checks for each of the four major MNOs, with one section per network so you can jump to the one you use.

## The Universal Code Every Nigerian Should Know

Before you go to your network, run the universal IMEI display. Dial `*#06#` on any phone, on any network in the world. The 15-digit IMEI appears immediately. On dual-SIM devices you will see two IMEIs — both are tied to your device and both are tracked in NCC-DMS.

Copy or screenshot the IMEI before you continue. You will need it for every network-specific check below, for the NCC-DMS portal at `ncc.gov.ng`, and for our [Free IMEI Check](/ng/) which gives an instant cross-network status.

## MTN Nigeria IMEI Check

MTN is the largest MNO in Nigeria with the deepest NCC-DMS integration. There are three ways to verify and manage an IMEI on MTN.

- **USSD self-service**: Dial `*123#` from your MTN line, choose "My Tools" → "Device" → "Verify IMEI". Enter the 15-digit IMEI. MTN returns a status SMS within minutes.
- **MyMTN app**: Open the MyMTN app (available on Play Store and App Store), go to Account → Devices. The app shows every IMEI currently bound to your MSISDN.
- **Customer care**: Dial 180 from your MTN SIM (free) or 0803 100 0180 from another network. Ask the agent to confirm your IMEI status on the NCC-DMS register.

For IMEI binding: MTN allows multiple IMEIs per MSISDN. If you switch phones, the new IMEI is registered automatically when you insert the SIM, but it must already be on the NCC-DMS register. MTN's network rejects unregistered IMEIs the second time the device tries to attach.

Carrier lock on MTN: MTN sells subsidised devices (mostly low-end Android) that are locked to its network for 12 months. To check whether your device is locked, dial `*123*7#` and look at the "Device status" field. To request an unlock after the 12-month period, dial 180 with proof of purchase.

## Glo (Globacom) IMEI Check

Globacom's verification flow is slightly less automated than MTN's but the channels work reliably.

- **USSD self-service**: Dial `*100#` from your Glo line, choose "Account" → "Device Status". The current IMEI is shown along with its NCC-DMS verification flag.
- **Glo Cafe**: The official Glo Cafe app (Android and iOS) shows your registered devices under Profile → My Devices.
- **Customer care**: Dial 121 from your Glo SIM (free) or 0805 002 0121 from another network.

Glo's IMEI binding behaves the same way as MTN — multiple IMEIs are allowed per SIM, but the IMEI must be on the NCC-DMS register before the network will let it complete a call. If you get a "service blocked" message on a known-good IMEI, contact Glo customer care first; it is often a sync delay rather than a real block.

Carrier lock on Glo: most Glo handsets sold from Glo World stores are unlocked, but the entry-level "Glo Bounce" device range is network-locked for 6 months. Confirm by dialling `*100*4#`.

## Airtel Nigeria IMEI Check

Airtel runs its IMEI verification through its self-service codes and the Airtel Thanks app.

- **USSD self-service**: Dial `*121#` from your Airtel line, choose "My Account" → "Manage Devices" → "Check IMEI". Enter the 15-digit number. The status SMS lands within a minute.
- **Airtel Thanks app**: Available on Play Store and App Store. Open the app, go to Profile → My Devices.
- **Customer care**: Dial 111 from your Airtel SIM (free) or 0802 150 0111 from another network.
- **Email**: customerservice.ng@airtel.com for written records of a device dispute.

Airtel's IMEI binding rule is the strictest of the four MNOs. Airtel typically allows up to 3 IMEIs per MSISDN per month. If you swap phones more than three times in a calendar month, the fourth swap is flagged and a customer care call is required to lift the flag. This rule was tightened in late 2025 alongside the NCC-DMS rollout to reduce SIM-swap fraud.

Carrier lock on Airtel: Airtel sells some Itel, Tecno and Infinix models locked to Airtel for the first 6 months. Dial `*121*4#` to confirm whether your device is locked.

## 9mobile IMEI Check

9mobile (formerly Etisalat Nigeria) has the smallest subscriber base of the four MNOs but a clean, simple verification flow.

- **USSD self-service**: Dial `*200#` from your 9mobile line, choose "My Account" → "My Device". The IMEI appears with its current NCC-DMS status.
- **9mobile app**: Available on Play Store and App Store. Profile → Linked Devices.
- **Customer care**: Dial 200 from your 9mobile SIM (free) or 0809 999 0200 from another network.

9mobile's IMEI binding is the most permissive: multiple IMEIs are allowed per MSISDN with no monthly cap, but each IMEI must still pass the NCC-DMS check. If you have an older 9mobile SIM (issued before 2023), it may need a refresh at a 9mobile experience centre before the new IMEI binding works.

9mobile does not currently sell network-locked phones; almost all devices on 9mobile are SIM-free.

## When to Use the NCC Channel Instead

The four network channels are the right place for routine checks. Go directly to the NCC, on the `*622#` channel or `ncc.gov.ng` portal, when:

- Two or more networks return different statuses for the same IMEI.
- A network customer-care agent cannot explain why your IMEI is blocked.
- You suspect an IMEI was stolen and want it added to the cross-network register.
- You are about to buy a used phone and want the authoritative answer, not the network cache.

A combined check — your network channel plus the NCC-DMS portal — covers virtually every IMEI question Nigerian users face today.

## Frequently Asked Questions

**Which USSD code shows my IMEI on any phone?**
`*#06#` works on every phone on every network worldwide. It is the fastest way to display the IMEI before running a verification.

**Can I use one network's IMEI check for a phone on a different network?**
Partly. Each network only shows the IMEIs bound to its own SIMs. For a cross-network answer use the NCC-DMS portal at `ncc.gov.ng` or a free third-party tool like our [Free IMEI Check](/ng/).

**What does it mean when MTN says my IMEI is "Pending NCC verification"?**
The IMEI has reached MTN but the NCC-DMS sync has not returned its status yet. This usually clears within 24 hours. If it persists, contact MTN on 180 with your IMEI and NIN.

**Does Airtel really limit me to 3 IMEIs per month?**
Yes, since late 2025. The cap is intended to reduce fraud. If you legitimately need a higher limit (for example, you repair phones), Airtel can register your MSISDN as a service account on request via 111.

**Is there a single Nigerian app that checks my IMEI on all four networks?**
Not officially. The closest thing is the NCC-DMS public portal which queries the central register. Independent tools that aggregate carrier data are useful but should always be cross-checked against the NCC portal.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "MTN, Glo, Airtel, 9mobile — IMEI Check Per Carrier in Nigeria",
      "description": "Working USSD codes, app paths and customer-care channels for IMEI verification on each Nigerian network operator: MTN, Glo, Airtel and 9mobile.",
      "inLanguage": "en-NG",
      "author": {"@type": "Organization", "name": "imeihub"},
      "publisher": {"@type": "Organization", "name": "imeihub"},
      "datePublished": "2026-05-28",
      "dateModified": "2026-05-28",
      "mainEntityOfPage": "https://imeihub.com/ng/articles/mtn-glo-airtel-9mobile-imei-check-nigeria",
      "about": {"@type": "Thing", "name": "Nigerian Mobile Network IMEI Verification"}
    },
    {
      "@type": "FAQPage",
      "inLanguage": "en-NG",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Which USSD code shows my IMEI on any phone?",
          "acceptedAnswer": {"@type": "Answer", "text": "*#06# works on every phone on every network worldwide. It is the fastest way to display the IMEI before running a verification."}
        },
        {
          "@type": "Question",
          "name": "Can I use one network's IMEI check for a phone on a different network?",
          "acceptedAnswer": {"@type": "Answer", "text": "Partly. Each network only shows the IMEIs bound to its own SIMs. For a cross-network answer use the NCC-DMS portal at ncc.gov.ng or a free third-party tool like imeihub's Free IMEI Check."}
        },
        {
          "@type": "Question",
          "name": "What does it mean when MTN says my IMEI is Pending NCC verification?",
          "acceptedAnswer": {"@type": "Answer", "text": "The IMEI has reached MTN but the NCC-DMS sync has not returned its status yet. This usually clears within 24 hours. If it persists, contact MTN on 180 with your IMEI and NIN."}
        },
        {
          "@type": "Question",
          "name": "Does Airtel really limit me to 3 IMEIs per month?",
          "acceptedAnswer": {"@type": "Answer", "text": "Yes, since late 2025. The cap is intended to reduce fraud. If you legitimately need a higher limit (for example, you repair phones), Airtel can register your MSISDN as a service account on request via 111."}
        },
        {
          "@type": "Question",
          "name": "Is there a single Nigerian app that checks my IMEI on all four networks?",
          "acceptedAnswer": {"@type": "Answer", "text": "Not officially. The closest thing is the NCC-DMS public portal which queries the central register. Independent tools that aggregate carrier data are useful but should always be cross-checked against the NCC portal."}
        }
      ]
    }
  ]
}
```
