<?php
/**
 * SEO Article Data — IMEIHub.net
 * Each element maps directly to a blog/guide post.
 * Body field uses Markdown (rendered client-side or via Parsedown).
 */
return [

    [
        'slug'    => 'kenya-imei-check-kra-registration-status',
        'title'   => 'Kenya IMEI Check: Verify KRA Registration Status (2025)',
        'excerpt' => 'Check if your phone is registered with KRA in Kenya. Use the CA portal, Safaricom SMS, or free IMEI tools — avoid network blocks in 2025.',
        'date'    => '2026-06-12',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Is Your Phone Registered with KRA in Kenya? Here's How to Check (2025)

If you recently bought a phone in Kenya — new or second-hand — you must verify its IMEI registration status with the **Kenya Revenue Authority (KRA)**. Since January 2025, unregistered devices are being progressively blocked on all Kenyan networks: **Safaricom**, **Airtel Kenya**, and **Telkom Kenya**.

This guide walks you through every official and free method to check your phone's IMEI status, understand what the results mean, and fix issues before your device goes silent.

---

## What Is IMEI Registration in Kenya?

Kenya's **Communications Authority (CA)** and KRA jointly operate a **Device Management System (DMS)** — Kenya's national version of the international CEIR (Central Equipment Identity Register). Every mobile device using a SIM card in Kenya must have its unique 15-digit IMEI number entered in this database.

Phones not registered within the permitted window are flagged and eventually **blocked at the network level**: no calls, no SMS, no mobile data — even with a valid SIM inserted.

**Key facts you need to know:**
- Mandatory registration came into force **January 2025**
- Applies to smartphones, tablets, and mobile routers (MiFi/pocket Wi-Fi)
- Both **IMEI 1** and **IMEI 2** on dual-SIM phones must be registered
- Penalties include device seizure at border crossings and airports
- Tourists receive a **90-day grace period** before enforcement kicks in

---

## Step 1: Find Your IMEI Number

Before checking registration status, locate your phone's IMEI.

**Method A — Universal Dial Code (fastest)**
1. Open the Phone dialer app
2. Dial `*#06#`
3. Your IMEI(s) appear on screen instantly — no call is placed
4. Screenshot or write down every IMEI displayed

**Method B — Phone Settings**
- **Android:** Settings → About Phone → Status → IMEI Information
- **iPhone / iOS:** Settings → General → About → scroll to IMEI

**Method C — Physical Label**
- Back of the original retail box (barcode sticker)
- Inside the battery compartment on older models
- SIM tray slot label on some newer iPhones

> **Dual-SIM tip:** Both IMEI 1 and IMEI 2 need separate checks. Some phones show them in the same `*#06#` screen — note both numbers.

---

## Method 1: Official KRA / Communications Authority Portal

Kenya's Communications Authority maintains an online portal for citizens to check device status.

**Steps:**
1. Open your browser and go to the CA portal at **https://www.ca.go.ke**
2. Navigate to **"Device Registration"** → **"Check Device Status"**
3. Enter your full 15-digit IMEI (digits only — no spaces or dashes)
4. Complete the on-screen CAPTCHA
5. Click **Check Status** and read your result

**Interpreting your result:**

| Status Shown | Meaning | What To Do |
|---|---|---|
| **Registered / Compliant** | IMEI is in the KRA/CA database and approved | Nothing — your device is fully legal |
| **Not Registered** | IMEI is absent from the national database | Register via KRA eCitizen immediately |
| **Blocked** | Flagged as stolen, counterfeit, or non-compliant | Contact CA or your network operator |
| **Pending** | Application submitted, awaiting processing | Wait 24–72 hours, then recheck |
| **Not Found / Invalid** | IMEI not recognized at all | May indicate a counterfeit device — verify with your retailer |

---

## Method 2: Check via SMS on Safaricom, Airtel, or Telkom

All three of Kenya's major carriers let you check IMEI status by SMS — completely free.

**Safaricom:**
- Send your 15-digit IMEI as an SMS to **22233**
- You receive a status reply within minutes
- Cost: **Free** (no airtime deducted)

**Airtel Kenya:**
- Send your IMEI to **12200**
- Instant status notification via return SMS

**Telkom Kenya:**
- Dial `*100#` and navigate to the **"Device Registration"** option
- Alternatively, SMS your IMEI to **61800**

> **Important:** The SMS check reflects each carrier's own copy of the DMS database. There can be a 24–48 hour sync lag between networks. If one carrier shows "registered" and another shows "pending," wait a day and recheck.

---

## Method 3: Free International IMEI Check via IMEIHub

For a deeper check — including global blacklist status, stolen device databases spanning 50+ countries, and carrier lock information — use IMEIHub's free check tool.

**How to use it:**
1. Visit the [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) tool
2. Enter your 15-digit IMEI
3. Receive an instant report covering:
   - Global blacklist and stolen-device status
   - Carrier / SIM lock status (locked vs. unlocked)
   - Device model and specifications verification
   - Warranty status for Apple, Samsung, and other major brands

**When is this essential?**
- Buying a used phone on **Jiji.co.ke**, **OLX Kenya**, or street markets in Nairobi
- Receiving a device imported as a gift from abroad
- Reselling or purchasing stock from **Computer City**, **Luthuli Avenue**, or **Tom Mboya Street** markets

For the most comprehensive stolen-device check across international law enforcement databases, run the [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist).

---

## Understanding the KRA F88 Customs Declaration Form

If you personally carried a phone into Kenya through **JKIA (Jomo Kenyatta International Airport)**, **Moi International Airport Mombasa**, or any official border crossing, the **F88 Customs Entry Form** is your legal proof of import and the start of the registration process.

**What you need to know about F88:**

| Detail | Information |
|---|---|
| **When required** | Any device valued over USD 500 (approx. KES 65,000) |
| **Duty rates** | ~25% import duty + 16% VAT + 3% IDF levy |
| **Duty-free allowance** | One personal phone per adult passenger |
| **Processing time** | IMEI appears in DMS within 3–5 business days |
| **Where to obtain** | KRA customs desk at point of entry |

If you hold an F88 receipt but your IMEI still shows "Not Registered" after 5 days, bring the receipt to the nearest **KRA service center** (branches in Nairobi CBD, Mombasa, Kisumu, Eldoret, and Nakuru) for manual resolution.

---

## What Happens If Your Phone Gets Blocked?

A blocked phone in Kenya means:

- **No outgoing or incoming calls** on any Kenyan network
- **No mobile data** — device shows "No Service" or "Emergency Calls Only"
- **SIM-swapping does not help** — the block is on the IMEI, not the SIM
- **Wi-Fi still works** — apps like WhatsApp, Gmail, and browsers remain functional over Wi-Fi

**How to get your phone unblocked:**

1. **Registration pending:** Wait 48–72 hours and recheck
2. **Unregistered device:** Pay applicable import duty via **KRA eCitizen** portal or visit a KRA office in person
3. **Stolen / blacklisted:** Provide proof of purchase to **Communications Authority**; contact them at **info@ca.go.ke** or **+254 020 4242000**
4. **Counterfeit (invalid IMEI):** The device cannot be unblocked — the IMEI is fabricated and the device violates CA regulations

---

## Pre-Purchase Checklist: Buying a Used Phone in Kenya

Kenya's second-hand smartphone market is massive. Before handing over cash, run through this checklist:

- [ ] Dial `*#06#` on the phone and confirm the IMEI matches what's on the box or back label
- [ ] Run a [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) to verify it is not globally blacklisted
- [ ] Send the IMEI to Safaricom **22233** to confirm local registration status
- [ ] Ask the seller for the original purchase receipt, warranty card, or F88 form if imported
- [ ] Inspect the IMEI sticker — scratched-off or replaced stickers indicate tampering
- [ ] Test the phone on your own SIM card before paying

> **Non-negotiable rule:** If a seller refuses to let you check the IMEI before purchase, walk away. A legitimate seller has nothing to hide.

---

## Registration Processing Times at a Glance

| Registration Method | Typical Processing Time |
|---|---|
| Airport declaration (F88 form) | 3–5 business days |
| KRA eCitizen online application | 24–48 hours |
| KRA service center walk-in | Same day to 24 hours |
| Authorized retailer (new device) | Immediate — registered at point of sale |

---

## Frequently Asked Questions

**Q: I bought my phone at a Safaricom Shop — is it already registered?**
A: Yes. Phones sold by authorized retailers are registered at the point of sale. You can still verify via the CA portal or Safaricom SMS for peace of mind.

**Q: My phone shows "registered" on Safaricom but "blocked" on Airtel. What does that mean?**
A: Network databases sync with the CA's DMS, but there can be a 24–48 hour lag. Wait one day and recheck. If the mismatch persists, contact the CA directly.

**Q: Does a dual-SIM phone need both IMEIs registered?**
A: Yes. Both IMEI 1 and IMEI 2 must be individually registered. Registering only one IMEI may cause the second SIM slot to stop working.

**Q: I'm a tourist in Kenya for 3 weeks. Do I need to register my phone?**
A: Tourists currently receive a **90-day grace period** before registration is enforced. For stays shorter than 90 days, your device will continue to work normally.

**Q: What if I own a tablet with a SIM card slot?**
A: Any device with an IMEI that accepts a SIM card — including tablets and MiFi routers — must be registered under the same KRA rules.

**Q: Can I check someone else's phone IMEI?**
A: Yes. The CA portal and carrier SMS checks are public-facing tools. You can check any IMEI without owning the device — useful when buying second-hand.

---

## Quick Comparison: All Kenya IMEI Check Methods

| Method | Speed | Cost | Best For |
|---|---|---|---|
| CA / KRA Online Portal | 1–2 minutes | Free | Official registration status |
| Safaricom SMS to 22233 | 1–3 minutes | Free | Quick Safaricom network check |
| Airtel SMS to 12200 | 1–3 minutes | Free | Quick Airtel network check |
| IMEIHub Free Check | Under 1 minute | Free | Global blacklist + carrier lock status |
| IMEIHub Worldwide Blacklist | 2 minutes | Paid | Full international stolen-phone report |

---

## Conclusion

Verifying your phone's IMEI registration status in Kenya takes under two minutes and is completely free — yet it can save you from buying a blocked, stolen, or counterfeit device worth tens of thousands of shillings.

With **KRA's January 2025 enforcement** now fully active, the risk of ending up with an unusable phone has never been higher. Whether you're buying second-hand on Jiji, bringing a device through JKIA, or simply want to confirm your existing phone is complaint — start with a free check.

Run a [Free IMEI Check on IMEIHub](https://imeihub.net/service.php?slug=free-imei-check) right now — it takes less than 30 seconds and gives you full global blacklist and registration intelligence before you commit to any purchase.
MD,
    ],

];
