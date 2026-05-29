<?php
declare(strict_types=1);

/**
 * Lightweight article catalog. Each entry has Markdown-like body that
 * we render with a tiny in-house formatter (paragraphs + lists + h2/h3).
 */

return [
    [
        'slug'    => 'what-is-an-imei-number',
        'title'   => 'What is an IMEI number, and what is it used for?',
        'excerpt' => 'A plain-English guide to the 15-digit number that uniquely identifies every mobile phone.',
        'date'    => '2026-05-01',
        'tag'     => 'Basics',
        'body'    => <<<MD
## What does IMEI stand for?

IMEI is short for **International Mobile Equipment Identity**. It is a
15-digit serial number assigned to every GSM-capable mobile phone or
tablet. The number identifies the device itself — not the SIM card,
not the user account.

## Why do networks care?

When a phone connects to a cellular network, it transmits its IMEI as
part of the handshake. Carriers can use this to:

- recognise the device model and apply network-specific settings;
- block stolen devices from making calls or using data;
- enforce manufacturer or carrier-specific lock policies;
- generate usage reports tied to a device rather than a SIM.

## How to find your IMEI

The fastest way is to dial **`*#06#`** on the phone keypad — the IMEI
appears on screen straight away. You can also find it in:

- **Settings &rarr; About phone &rarr; IMEI** (Android & iOS)
- The printed label on the box, or
- The metal SIM tray on most iPhones.

## What can someone do with my IMEI?

Not much, on its own. An IMEI is not a password — it does not unlock
your accounts, your SIM, or your data. Treat it like a serial number:
fine to share with your carrier or insurance company, but no need to
post it publicly.
MD,
    ],
    [
        'slug'    => 'how-to-check-imei',
        'title'   => 'How to check an IMEI before buying a used phone',
        'excerpt' => 'A 5-step checklist that takes 60 seconds and protects you from buying a stolen or blacklisted device.',
        'date'    => '2026-04-18',
        'tag'     => 'Guide',
        'body'    => <<<MD
## The 60-second IMEI test

Whenever you are buying a used phone — in person, online, or from a
re-seller — run this test before you pay.

### 1. Ask the seller to dial `*#06#`

The IMEI must show up on screen. Compare it to the one on the box and
the SIM tray. **All three should match.**

### 2. Confirm the IMEI is 15 digits

If it is shorter, or contains letters, it is not a valid IMEI. Walk
away.

### 3. Run a free IMEI check

Use a lookup tool (like this site) to confirm the brand and model
match what the seller is claiming. A phone advertised as an
"iPhone 15 Pro" should return Apple / iPhone 15 Pro.

### 4. Run a blacklist check

This costs a small fee but is worth it. A blacklisted IMEI means the
device was reported lost or stolen. Most carriers will refuse to
activate it.

### 5. For Apple devices, check iCloud activation lock

If iCloud (Find My iPhone) is still enabled and tied to the seller's
account, you will not be able to re-activate the phone after wiping it.
MD,
    ],
    [
        'slug'    => 'tac-vs-imei',
        'title'   => 'TAC vs IMEI: how the first 8 digits identify your phone model',
        'excerpt' => 'The TAC is a sub-section of every IMEI that tells you who made the phone and what model it is — before any database lookup.',
        'date'    => '2026-04-04',
        'tag'     => 'Technical',
        'body'    => <<<MD
## Breaking down a 15-digit IMEI

An IMEI is actually three numbers stitched together:

| Digits | Section | What it means |
| --- | --- | --- |
| 1&ndash;8  | **TAC** (Type Allocation Code) | Identifies the manufacturer and model. |
| 9&ndash;14 | **Serial** | Unique to this device within that model. |
| 15      | **Check digit** | Luhn checksum to catch typos. |

## What is the TAC?

The Type Allocation Code is assigned by the **GSMA** to manufacturers
when they certify a device. Every iPhone 15 Pro, for example, shares
the same set of TACs — different production batches and regional
variants may each get a different TAC, but they all decode to the
same brand and model.

## Why the TAC is enough for most lookups

When you "check an IMEI" for the model name, the lookup tool is
usually just consulting a TAC-to-model database. The remaining 7
digits are not needed.

The remaining digits **are** needed for blacklist checks, carrier
checks and warranty checks — those tie back to a specific device.

## The check digit, briefly

The 15th digit is a Luhn checksum computed over the first 14. It
catches single-digit typos but nothing more clever — so a Luhn-valid
IMEI is not necessarily a real one.
MD,
    ],
    [
        'slug'    => 'imei-check-by-country',
        'title'   => 'IMEI Check by Country: PTA, CEIR, NTC and the Checks That Matter Most in 2026',
        'excerpt' => 'From Pakistan\'s mandatory PTA compliance to India\'s CEIR system and Philippine stolen-phone checks — what IMEI verification means in the five markets where it matters most.',
        'date'    => '2026-05-29',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Why your country determines what an IMEI check reveals

An IMEI number follows the same 15-digit format in every country — but the databases it connects to are local, national, and often government-mandated. A phone blacklisted through Pakistan's PTA will be blocked there, but the flag does not automatically propagate to India or the Philippines. A device reported stolen in the United States may still work fine on a network in Nigeria.

This patchwork of national systems creates predictable blind spots that fraudsters exploit — and that careful buyers can guard against. Below is a country-by-country breakdown of the checks that actually matter in the five markets where IMEI lookup demand is highest.

## Pakistan: mandatory PTA compliance is not optional

Pakistan operates the **DIRBS** (Device Identification, Registration and Blocking System), managed by the Pakistan Telecommunication Authority (PTA). Every smartphone used in Pakistan must have a PTA-compliant IMEI. Non-compliant devices are blocked from all four major networks — Jazz, Telenor, Ufone, and Zong — within 60 days of first SIM insertion.

### Who is affected

The compliance requirement catches every imported device: phones brought back by overseas Pakistanis, grey-market imports from Dubai or China, and second-hand purchases from international platforms. A phone that passes a visual inspection and shows a valid IMEI can still be PTA non-compliant.

### The three PTA status responses to understand

When you check a device through the DIRBS/PTA system, you get one of three results:

- **Compliant** &mdash; registered and approved; the device will work on Pakistani networks.
- **Valid Non-Compliant** &mdash; in a grace period; the device currently works but will be blocked unless registered within the window.
- **Non-Compliant** &mdash; blocked or pending block; the SIM will not function.

### What Pakistani buyers should check

- **PTA / DIRBS compliance status** &mdash; the single most important check before buying any phone in Pakistan.
- **Registration tax** &mdash; high-end imports (certain iPhone and Samsung Galaxy models) attract registration fees through the FBR. Know the cost before committing to a purchase.
- **IMEI validity** &mdash; a cloned or duplicate IMEI will fail the Luhn check and cannot be legitimately registered.

Start with a [free IMEI check](https://imeihub.net/service.php?slug=free-imei-check) to confirm the brand and model, then verify the DIRBS compliance status before any purchase.

## India: CEIR, clone phones, and a billion-device market

India is the world's second-largest smartphone market, and its second-hand segment is one of the largest anywhere — more than 20 million used units traded annually across platforms like OLX, Cashify, and local repair shops. The Indian government's **CEIR** (Central Equipment Identity Register), operated under the Sanchar Saathi portal, gives citizens a way to verify and block devices at the national level.

### The CEIR difference

CEIR is a national database — a phone reported stolen and blocked in Delhi will not work in Mumbai, Chennai, or Hyderabad. Blocking is carrier-agnostic: the device is rejected by Jio, Airtel, Vi, and BSNL simultaneously. This makes it one of the most comprehensive stolen-device systems in any emerging market.

### The clone problem

India and South Asia have documented problems with counterfeit smartphones — devices that carry forged IMEI numbers or hardware that mimics popular brand designs. An IMEI check against the manufacturer's own database immediately exposes the mismatch: a device claiming to be a Samsung Galaxy S24 Ultra that returns a Galaxy A-series result is a clear red flag.

### What Indian buyers should check

- **Blacklist / CEIR status** &mdash; run this before paying for any second-hand phone on OLX or Cashify.
- **Samsung model and Knox Guard** &mdash; the [Samsung Info + Knox Guard check](https://imeihub.net/service.php?slug=samsung-info) returns the exact registered model, country of purchase, and Knox Guard status. A Knox-locked Samsung cannot be factory-reset without the original Samsung account.
- **Xiaomi / Redmi / POCO Mi Account lock** &mdash; the [Xiaomi Info check](https://imeihub.net/service.php?slug=xiaomi-status) returns the Mi Account / Find Device lock status. A locked Xiaomi device cannot be reset without the original Mi credentials.
- **Apple iCloud status** &mdash; for iPhones, always run an [iCloud Activation Lock check](https://imeihub.net/service.php?slug=apple-icloud) before buying second-hand.
- **Warranty region** &mdash; Samsung and Apple India warranties apply only to devices first sold in India. Grey imports from Dubai or Singapore are common and often carry no local warranty coverage.

## Philippines: NTC blacklisting and the grey import trap

The Philippines has one of Southeast Asia's most active second-hand phone markets — Greenhills mall in Metro Manila, Facebook Marketplace, and Shopee all trade large volumes of devices, many imported from the US, Japan, or South Korea. The National Telecommunications Commission (NTC) maintains a blacklist that carriers can reference, but enforcement varies.

### What "unlocked" often means here

A phone described as "factory unlocked" in Philippine second-hand listings frequently turns out to be one of the following:

- **Carrier-locked to a US network** &mdash; T-Mobile- or AT&T-locked iPhones are imported in large numbers and sold as if fully unlocked.
- **iCloud-locked to a previous owner** &mdash; one of the most common ways Philippine buyers end up with a useless device after a Facebook Marketplace purchase.
- **Missing local LTE bands** &mdash; Japanese or Korean model variants may lack Band 8 (900 MHz), used by Globe and Smart in provincial areas, significantly degrading signal quality outside Metro Manila.

### What Philippine buyers should check

- **Carrier lock status** &mdash; essential for any imported iPhone. An [Apple SIM-Lock check](https://imeihub.net/service.php?slug=apple-sim-lock) confirms the exact carrier lock state.
- **iCloud Activation Lock** &mdash; run an [iCloud Activation Lock check](https://imeihub.net/service.php?slug=apple-icloud) on any second-hand iPhone. A status of "Lost" is a hard stop — the device cannot be reactivated without the previous owner's Apple ID.
- **WorldWide Blacklist** &mdash; a [blacklist check](https://imeihub.net/service.php?slug=blacklist) covers international carrier databases that the NTC coordinates with for flagged devices.
- **Model region** &mdash; the device model suffix (A2111, A2215, etc.) tells you which region the phone was built for and which frequency bands it supports.

The same checks apply to buyers in Indonesia, Vietnam, Malaysia, and Thailand, where cross-border phone trade is similarly active.

## Nigeria: counterfeit detection and the NCC registry

Nigeria is Africa's largest smartphone market, and its urban centres — Lagos, Abuja, Kano — see high volumes of both legitimate and counterfeit devices. The Nigerian Communications Commission (NCC) operates a Device Management System (DMS) that will require all devices on Nigerian networks to carry registered, legitimate IMEIs.

### The counterfeit and clone risk

Clone devices in the Nigerian market fall into several categories:

- **Hardware clones** &mdash; budget Android hardware sold in Samsung or iPhone packaging as the genuine branded product.
- **IMEI duplication** &mdash; a real IMEI from a legitimate device copied onto multiple counterfeit units, particularly common with popular high-end models.
- **Re-boxed refurbished devices** &mdash; phones with replaced screens or logic boards that no longer match the original IMEI.

An IMEI check catches all three: it returns the actual registered manufacturer and model, immediately exposing any mismatch with the seller's claim.

### What Nigerian buyers should check

- **Free IMEI check** &mdash; always start by confirming the brand and model match what the seller is claiming. [Run the check here](https://imeihub.net/service.php?slug=free-imei-check).
- **WorldWide Blacklist** &mdash; a [blacklist check](https://imeihub.net/service.php?slug=blacklist) catches devices reported internationally, which matters for phones imported from the UK, US, or UAE.
- **Samsung Knox Guard** &mdash; Samsung dominates Nigeria's mid-range segment. The [Samsung Info check](https://imeihub.net/service.php?slug=samsung-info) returns Knox Guard status and confirms the device's country of manufacture.

Buyers in Kenya, Ghana, and South Africa face the same dynamics — the counterfeit and grey-import risks are similar, and the same checks apply.

## Mexico and Colombia: unlocking and the cross-border question

Mexico consistently ranks as the second- or third-largest traffic source for every major IMEI lookup service globally, driven by cross-border phone trade with the United States and a large prepaid market where carrier unlocking is a genuine need. Colombia has a dedicated IMEI registry managed by the Communications Regulation Commission (CRC).

### What makes LATAM different

In North America, "IMEI check" primarily means blacklist verification. In Mexico and Colombia, carrier unlocking is an equally common need — large numbers of US-sourced iPhones and Android devices cross the border, and buyers want to know whether the device will work freely on Telcel, Movistar, Claro, or Tigo.

The critical distinction: **carrier unlock and blacklist removal are entirely different processes**. An unlocked phone can still be blacklisted. A clean-listed phone can still be carrier-locked. Both checks are necessary for any imported device.

### What Mexican and Colombian buyers should check

- **Carrier lock status** &mdash; for iPhones, the [Apple SIM-Lock check](https://imeihub.net/service.php?slug=apple-sim-lock) returns the exact lock state and the network the device is locked to.
- **WorldWide Blacklist** &mdash; a [blacklist check](https://imeihub.net/service.php?slug=blacklist) catches US-flagged devices before you pay. Phones sold cheaply near the US border are frequently blacklisted.
- **iCloud status** &mdash; for iPhones, verify that iCloud Activation Lock is OFF before completing any purchase.
- **Model and region compatibility** &mdash; some US models do not support Latin American LTE bands used by Telcel or Claro in regional areas.

## The universal five-step pre-purchase checklist

Wherever you are buying a used phone, these steps apply:

1. **Dial `*#06#` on the device** &mdash; the IMEI appears on screen instantly. Compare it to the box and SIM tray; any mismatch indicates tampering or swapped hardware.
2. **Run a free IMEI check** &mdash; verify the brand and model match what the seller claims before spending anything. [Start here](https://imeihub.net/service.php?slug=free-imei-check).
3. **Run a blacklist check** &mdash; cross-check against international carrier databases. [WorldWide Blacklist check](https://imeihub.net/service.php?slug=blacklist).
4. **Check the carrier lock** &mdash; especially important for any cross-border or imported device.
5. **Check account locks** &mdash; iCloud, Samsung Knox Guard, or Xiaomi Mi Account locks are the most common reason buyers receive a phone they cannot use or reset.

For a step-by-step walkthrough of each check, read the full [how to check an IMEI before buying a used phone](https://imeihub.net/article.php?slug=how-to-check-imei) guide.

## How many checks do you actually need?

| Scenario | Checks to run |
| --- | --- |
| New phone from a local carrier store, sealed box | Free IMEI check only |
| Second-hand phone, private seller, same country | Free IMEI + Blacklist + Brand account lock |
| Imported iPhone from any country | Free IMEI + Blacklist + iCloud status + Carrier lock |
| Any phone in a high-clone market (India, Nigeria, Philippines) | Free IMEI + Blacklist + Brand-specific model check |
| Phone subject to PTA, CEIR, or NCC compliance | Country compliance check + Blacklist + Free IMEI |

Running three to four checks takes under five minutes and costs a fraction of what you would lose on a blacklisted, locked, or counterfeit device. The full list of available checks is at [Imeihub Services](https://imeihub.net/services.php).
MD,
    ],
];
