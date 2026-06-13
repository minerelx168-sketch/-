<?php
/**
 * SEO Articles Data
 * Each entry: slug, title, excerpt, date, tag, body (Markdown)
 */
return [

    [
        'slug'    => 'myanmar-imei-ceir-blocked-status-fix-2025',
        'title'   => 'Myanmar IMEI Blocked? Fix Your CEIR Status in 2025',
        'excerpt' => 'Phone blocked in Myanmar? Learn what CEIR status codes mean, why your IMEI is flagged, and exactly how to get it unblocked in 2025.',
        'date'    => '2026-06-13',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Why Is My Phone Not Working in Myanmar? (CEIR Explained)

If your phone suddenly stopped connecting to any Myanmar network — Mytel, MPT, Telenor, or Ooredoo — there is a high chance your IMEI has been **flagged or blocked by Myanmar's CEIR system**.

Since April 2023, Myanmar's Posts and Telecommunications Department (PTD) has operated the **Central Equipment Identity Register (CEIR)** at [ceir.gov.mm](https://ceir.gov.mm). Every mobile device that connects to a Myanmar SIM is cross-checked against this database in real time. If your IMEI is not registered, is duplicated, or is linked to a stolen report, the network will silently drop your device.

This guide explains every CEIR status code, why phones get blocked, and the exact steps to fix the problem — whether you brought your phone from Thailand, China, Singapore, or anywhere else.

---

## What Is the CEIR and Who Controls It?

The CEIR is operated by Myanmar's **Posts and Telecommunications Department (PTD)** under the Ministry of Transport and Communications. It works alongside the four licensed mobile network operators (MNOs):

| Operator | Brand | Frequency Bands |
|---|---|---|
| Myanmar Post & Telecom | MPT | 900/1800 MHz (GSM), LTE B3/B8 |
| Telenor Myanmar | Telenor | 900/2100 MHz, LTE |
| Mytel (Viettel Myanmar) | Mytel | 2100 MHz, LTE B1/B3 |
| Ooredoo Myanmar | Ooredoo | 900/2100 MHz, LTE |

When you insert a SIM, your device sends its IMEI to the network. The MNO queries CEIR. Within seconds, CEIR returns a status that determines whether your call goes through or is silently dropped.

---

## Understanding CEIR Status Codes

The CEIR portal returns one of five possible statuses when you check your IMEI at ceir.gov.mm or through a third-party tool like imeihub.net.

| Status | Meaning | Can You Still Use It? |
|---|---|---|
| **WHITELISTED** | Registered, clean, and approved | Yes — full service |
| **PENDING** | Registration submitted but not yet confirmed | Limited — may work temporarily |
| **BLACKLISTED** | Flagged as stolen, lost, or fraudulent | No — blocked on all Myanmar networks |
| **GREYLISTED** | Unregistered imported device on 60-day grace period | Yes, but a countdown is running |
| **INVALID IMEI** | Checksum fails or format is wrong | No — counterfeit device |

The most critical distinction is between **GREYLISTED** and **BLACKLISTED**. Greylisted phones can still be fixed; blacklisted phones require a police clearance process and are extremely difficult to recover.

---

## Top 5 Reasons Your IMEI Gets Blocked in Myanmar

### 1. Unregistered Imported Device
The most common reason. If you brought a phone from Thailand, China, Singapore, or any country outside Myanmar without registering it through the official CEIR portal, your phone enters the **GREYLIST** automatically. You have **60 days** to complete registration before blocking begins.

### 2. Duplicate IMEI (Counterfeit Device)
Some counterfeit phones — especially budget Android models sold in Yangon's Bo Gyoke Market — share IMEIs with legitimate devices. CEIR detects this conflict and blocks the duplicate. There is no fix for a cloned IMEI; the device itself is invalid.

### 3. Stolen or Lost Report
If the original owner reported the phone stolen to police or through their carrier, the IMEI is added to the blacklist. This is common with second-hand phones purchased on Facebook Marketplace, Telegram groups, or local bazaars. **Always check IMEI before buying a used phone in Myanmar.**

### 4. End of Grace Period Without Registration
Myanmar gave all existing device owners a registration grace period. Devices that were never registered after the deadline moved automatically from greylist to blacklist.

### 5. Incorrect IMEI Entered During Registration
Entering even one wrong digit during CEIR registration creates a mismatch. The SIM-connected IMEI differs from the registered record, causing a soft block.

---

## How to Check Your IMEI Status in Myanmar Right Now

**Step 1 — Find your IMEI**
Dial `*#06#` on your phone. Your 15-digit IMEI appears on screen immediately. On dual-SIM phones, you will see two IMEIs — use **IMEI 1** for registration.

**Step 2 — Run a free check on imeihub.net**
Before visiting any government portal, run a quick international blacklist check:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — confirms brand, model, and basic status
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — checks 50+ global databases including Interpol

This tells you whether your device is clean internationally *before* you spend time on CEIR registration.

**Step 3 — Check the official CEIR portal**
Visit [ceir.gov.mm](https://ceir.gov.mm), enter your 15-digit IMEI in the search box, and note the exact status code returned.

---

## How to Register Your Phone on Myanmar CEIR (Step-by-Step)

If your status is **GREYLISTED** or **PENDING**, follow these steps immediately. Do not wait until the 60-day grace period expires.

**Requirements before you start:**
- Valid Myanmar NRC (National Registration Card) number
- Your phone's 15-digit IMEI (from `*#06#`)
- Phone's original box or purchase invoice (recommended, not always mandatory)
- Active Myanmar SIM card

**Registration Process:**

1. Go to **ceir.gov.mm** and click "Register Device"
2. Select "Individual Registration" (for personal devices)
3. Enter your **NRC number** in the format: `1/MACANA(N)123456`
4. Enter your **IMEI number** — copy it exactly, no spaces or dashes
5. Fill in the device details: brand, model, and purchase date
6. Upload a photo of your NRC card (front and back)
7. Submit and note down your **reference number**
8. Wait 24–72 hours for status to change from PENDING to WHITELISTED

**For foreign nationals and tourists:**
Use your **passport number** instead of NRC. Registration is valid for the duration of your visa. You may register up to 2 devices per visit.

---

## What to Do If Your Phone Is BLACKLISTED

A blacklisted IMEI in Myanmar is significantly harder to resolve. The process requires:

1. **File a police report** at your nearest township police station confirming you are the legitimate owner (not the person who reported it stolen)
2. **Collect documents**: original purchase receipt, box with IMEI sticker, SIM registration records
3. **Visit a PTD office** in Naypyidaw, Yangon, or Mandalay with all documents
4. Submit a formal appeal — processing takes 2–6 weeks
5. If approved, PTD removes the IMEI from the blacklist and adds it to the whitelist

**Reality check:** If you bought the phone second-hand and the original owner or a previous owner reported it stolen, your appeal will almost certainly be denied. The only protection against this outcome is to **check the IMEI before purchasing**, which is why tools like imeihub.net exist.

---

## Buying a Second-Hand Phone in Myanmar: IMEI Checklist

Myanmar's second-hand phone market — particularly in Yangon's Thiri Mingalar Market, Mandalay's Zay Cho Market, and Facebook/Telegram reseller groups — is enormous and largely unregulated. Protect yourself:

- [ ] Dial `*#06#` on the seller's phone and verify the IMEI matches the box sticker
- [ ] Run a [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) on imeihub.net before paying
- [ ] Check CEIR status at ceir.gov.mm — must show WHITELISTED
- [ ] For iPhones, verify iCloud is OFF: [Apple iCloud Status Check](https://imeihub.net/service.php?slug=icloud-on-off)
- [ ] Ask for the original purchase receipt or import declaration
- [ ] Never pay full price for a phone showing PENDING or GREYLISTED status

---

## Frequently Asked Questions

**My phone was working fine last week — why is it suddenly blocked?**
The most likely cause is that the 60-day grace period for your unregistered imported device expired. Log in to ceir.gov.mm immediately and complete registration.

**I am a tourist. Do I need to register my phone?**
If you use a Myanmar SIM card, technically yes. However, tourist registrations using a passport are simple and take less than 5 minutes online. If you only use Wi-Fi, registration is not required.

**Can I use a VPN to bypass CEIR blocking?**
No. CEIR operates at the network hardware level (IMEI is transmitted before the data session begins). A VPN cannot bypass it. Voice calls and SMS will not work regardless.

**How many devices can one person register?**
Myanmar residents can register up to **5 devices** per NRC. Foreign nationals can register up to **2 devices** per visit.

**My IMEI shows WHITELISTED but calls are still dropping. What is wrong?**
This is likely a network configuration issue rather than a CEIR problem. Contact your operator (MPT: 104 / Telenor: 100 / Mytel: 109 / Ooredoo: 125) directly.

---

## Summary: Myanmar CEIR Status Quick Reference

| Your Situation | Action Required | Urgency |
|---|---|---|
| WHITELISTED | Nothing — you are compliant | None |
| GREYLISTED (< 60 days) | Register at ceir.gov.mm now | High |
| GREYLISTED (> 60 days) | Register immediately — blocking imminent | Critical |
| PENDING | Wait 24–72 hours, then recheck | Medium |
| BLACKLISTED | Collect documents, visit PTD office | Urgent |
| INVALID IMEI | Device is counterfeit — replace it | Immediate |

Checking your IMEI takes 30 seconds. Recovering a blocked phone takes weeks. Run a [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) now and confirm your device is clean before Myanmar's networks force you to act under pressure.
MD,
    ],

];
