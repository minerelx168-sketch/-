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
    [
        'slug'    => 'kra-imei-declaration-kenya-f88-form-guide',
        'title'   => 'KRA IMEI Declaration Kenya: F88 Form Guide 2025',
        'excerpt' => 'Kenya now requires mandatory IMEI declaration at customs. Learn how to complete the F88 form, avoid fines, and stay compliant with KRA rules.',
        'date'    => '2026-05-30',
        'tag'     => 'Regulation',
        'body'    => <<<MD
## KRA IMEI Declaration in Kenya: Everything You Need to Know (2025)

If you are travelling into Kenya — whether you are a returning resident, a tourist, or a business traveller — you must be aware of one critical change that came into effect in **January 2025**: the **Kenya Revenue Authority (KRA)** now requires mandatory IMEI declaration for all mobile phones brought into the country.

Failing to declare can result in your device being seized at **Jomo Kenyatta International Airport (JKIA)**, **Moi International Airport (Mombasa)**, or any other Kenyan port of entry. This guide explains exactly what the rule means, how to complete the **F88 Declaration Form**, and how to check your phone's IMEI status before you land.

---

## What Is KRA IMEI Declaration?

The **KRA IMEI Declaration** is a regulatory requirement under Kenya's **Customs and Excise Act** and the **Kenya Information and Communications Act**. It mandates that anyone arriving in Kenya with a mobile phone — especially a foreign-purchased device — must register the phone's **International Mobile Equipment Identity (IMEI)** number with KRA customs officers.

The policy is jointly enforced by:
- **Kenya Revenue Authority (KRA)** — customs and tax compliance
- **Communications Authority of Kenya (CA)** — IMEI validity and network registration
- **Kenya Ports Authority (KPA)** — for sea-freight arrivals

The primary goal is to:
1. Prevent the importation of **counterfeit and substandard devices**
2. Ensure that **import duties and taxes** are correctly collected
3. Create a national database of registered IMEIs to assist in **stolen phone recovery**

---

## Who Must Declare Their Phone's IMEI?

You are required to declare your phone's IMEI if **any** of the following apply to you:

| Scenario | Declaration Required? |
|---|---|
| Tourist arriving with a foreign-purchased phone | **Yes** |
| Kenyan resident returning from abroad with a new phone | **Yes** |
| Business traveller carrying demo/sample phones | **Yes** |
| Courier or cargo shipment containing mobile devices | **Yes** |
| Phone purchased duty-free at an overseas airport | **Yes** |
| Kenyan resident with a locally purchased phone | No (already registered) |
| Traveller in transit (not clearing customs) | No |

> **Important:** If you are carrying **more than one phone**, each device requires a separate IMEI declaration entry on the F88 form.

---

## What Is the F88 Form?

The **F88 Traveller's Declaration Form** (also called the **Customs Declaration Form**) is the official document used at all Kenyan ports of entry. It is provided by Kenya Revenue Authority for declaring personal effects, including electronic devices.

From January 2025, the F88 form includes a **dedicated IMEI section** where you must:
- Enter the **IMEI number(s)** of each phone you are carrying
- State the **purchase price** in the original currency
- Declare whether the phone is for **personal use** or **commercial sale**

You can obtain the F88 form:
- **On the aircraft** — flight attendants distribute it before landing
- **At the arrivals hall** — available at the customs declaration desk
- **Online (pre-arrival)** — check the KRA official website for the digital version

---

## How to Find Your Phone's IMEI Number

Before you travel to Kenya, locate your phone's IMEI number. There are three easy methods:

### Method 1: Dial a Code
Open your phone's dialler and type:
```
*#06#
```
Your IMEI number (15 digits) will appear on screen immediately. Write it down or take a screenshot.

### Method 2: Check Phone Settings
- **iPhone:** Go to **Settings → General → About → IMEI**
- **Android:** Go to **Settings → About Phone → IMEI Information**
- **Samsung:** Go to **Settings → About Phone → Status → IMEI Information**

### Method 3: Check the Device Box or SIM Tray
Your IMEI is printed on the original retail box and sometimes on the SIM card tray of the device.

---

## Step-by-Step: Completing the KRA F88 Form for IMEI Declaration

Follow these steps when arriving in Kenya with a foreign mobile phone:

**Step 1 — Collect the F88 Form**
Pick up the form on the aircraft or at the customs desk in the arrivals hall.

**Step 2 — Fill in Your Personal Details**
Enter your full name, passport number, nationality, flight number, and country of origin.

**Step 3 — Locate the "Electronic Goods" Section**
The F88 form has a goods declaration section. Under electronic items, you will find fields for mobile phones.

**Step 4 — Enter Each Phone's IMEI Number**
Write the full 15-digit IMEI for every phone you are carrying. If your phone has two SIM slots, it will have two IMEI numbers — declare both.

**Step 5 — State the Purchase Value**
Enter the purchase price as shown on your receipt. If you no longer have the receipt, a bank statement or online order confirmation is acceptable.

**Step 6 — Declare Purpose of Import**
Mark whether the phone is for **personal use** (exempt from duty up to certain limits) or **commercial/resale** (subject to import duty).

**Step 7 — Submit to Customs Officer**
Hand the completed F88 form to the KRA customs officer at the red channel (goods to declare) or the designated IMEI registration desk.

**Step 8 — Receive Your IMEI Registration Stamp**
The officer will verify your IMEI, enter it into the KRA/CA IMEI database, and stamp your declaration. Keep this stamped form — it is proof of lawful importation.

---

## KRA Import Duty on Mobile Phones: What You May Owe

Phones brought into Kenya for **personal use** are typically allowed duty-free up to a value of **KES 50,000** (approximately USD 380). Above this threshold, the following taxes may apply:

| Tax / Levy | Rate |
|---|---|
| Import Duty | 25% of CIF (Cost, Insurance & Freight) value |
| Value Added Tax (VAT) | 16% |
| Import Declaration Fee (IDF) | 3.5% |
| Railway Development Levy (RDL) | 2% |
| **Effective Total Tax** | **~50%+ on dutiable value** |

> **Pro Tip:** If you bought an iPhone 15 or iPhone 16 abroad for USD 1,000, and it exceeds the duty-free allowance, you could face taxes of **USD 500 or more**. Always check your phone's declared value against current KRA thresholds.

Before you travel, verify your phone's IMEI status and estimated import value using the free tool below:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)

---

## Penalties for Not Declaring Your Phone at KRA Customs

Non-declaration or **under-declaration** of a mobile phone at Kenyan customs is a serious offence. Penalties include:

- **Confiscation of the device** — the phone is seized and held pending investigation
- **Financial penalty** — a fine of up to **three times the dutiable value** of the device
- **Criminal prosecution** — in serious cases, smuggling charges under the East African Community Customs Management Act
- **Network blocking** — the Communications Authority of Kenya can instruct local carriers (Safaricom, Airtel Kenya, Telkom Kenya) to **block the SIM card** from registering on any Kenyan network

A blocked phone is completely unusable in Kenya — it cannot make calls, send SMS, or use mobile data on any local network.

---

## How to Check if a Phone Is Already Blacklisted Before Buying

Whether you are buying a second-hand phone in Nairobi's **Tom Mboya Street** market or importing a device from overseas, **always check the IMEI before purchasing**. A stolen or blacklisted phone will be blocked from Kenyan networks — even if it appears to work initially.

Use our free IMEI tools to verify:
- **Blacklist status** — is this phone reported stolen in Kenya or internationally?
- **Network lock status** — is the phone locked to a specific carrier?
- **Model verification** — does the IMEI match the model on the box?

Run a check instantly:
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)
- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)

---

## Frequently Asked Questions

**Q: Do I need to declare a phone I already own if I am just visiting Kenya?**
A: Yes. Even if you have owned the phone for years, if it was purchased outside Kenya, you must declare it on the F88 form when entering the country.

**Q: What if I forget to declare my phone at the airport?**
A: You can approach the KRA customs desk before leaving the arrivals hall. Voluntary declaration after entry is handled on a case-by-case basis, but it is far better than being caught at a random spot-check inside the country.

**Q: Can Safaricom or Airtel block my phone if it is undeclared?**
A: Yes. Under the Communications Authority of Kenya's IMEI management system, unregistered foreign phones can be flagged and blocked by all three major networks within 60 days of arrival.

**Q: Is there a way to check if my phone's IMEI is already in the KRA system?**
A: You can request confirmation from the KRA customs desk at your port of entry. For international blacklist and stolen device checks, use our [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) tool.

**Q: What is the IMEI, and why does it matter for customs?**
A: The IMEI (International Mobile Equipment Identity) is a unique 15-digit number that identifies every mobile phone globally. Customs authorities use it to track whether a device was legally imported, to prevent counterfeit devices from entering circulation, and to assist police in recovering stolen phones.

---

## Check Your Phone's IMEI Right Now

Before you travel to Kenya — or before you buy any second-hand device — run a quick IMEI check to protect yourself:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — Verify brand, model, and basic device info instantly, at no cost
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — Confirm the phone has not been reported stolen in Kenya or any other country

Staying compliant with KRA IMEI declaration rules is straightforward when you are prepared. Know your IMEI number, fill in the F88 form honestly, and keep your stamped receipt — and your arrival in Kenya will be smooth and stress-free.
MD,
    ],

    [
        'slug'    => 'ceir-gov-mm-imei-check-register-myanmar-guide',
        'title'   => 'ceir.gov.mm IMEI Check & Register Myanmar 2025',
        'excerpt' => 'Myanmar CEIR portal lets you check and register your phone IMEI. Avoid network blocking — follow this step-by-step guide to ceir.gov.mm compliance.',
        'date'    => '2026-05-30',
        'tag'     => 'Guide',
        'body'    => <<<MD
## ceir.gov.mm Myanmar IMEI Check & Registration: Complete Guide 2025

Search interest in **ceir.gov.mm** — Myanmar's official Central Equipment Identity Register portal — has surged by more than **3,750%** over the past twelve months. If you own a mobile phone in Myanmar, whether purchased locally or brought from abroad, understanding the CEIR system is no longer optional. Unregistered devices face **network blocking** across all of Myanmar's major operators.

This guide explains what the CEIR is, how to use **ceir.gov.mm** to check your IMEI status, and the step-by-step process to register your device before it gets blocked.

---

## What Is Myanmar's CEIR? (Central Equipment Identity Register)

The **Central Equipment Identity Register (CEIR)** is a national database of mobile phone IMEI numbers maintained by Myanmar's **Ministry of Transport and Communications (MoTC)**. The system is modelled on international GSMA standards and connects directly with all licensed Myanmar network operators.

The CEIR serves three core functions:

1. **Whitelist** — approved, legally imported devices whose IMEIs are active on the network
2. **Blacklist** — stolen, counterfeit, or illegally imported devices that are blocked from all networks
3. **Greylist** — devices flagged as potentially non-compliant and under review

When a phone's IMEI appears on the **blacklist**, all four of Myanmar's major operators are required to prevent that SIM from connecting to their networks. The phone cannot make calls, send texts, or use mobile data — regardless of which operator's SIM you insert.

Myanmar's licensed network operators connected to the CEIR:
- **MPT** (Myanmar Posts and Telecommunications)
- **Mytel** (Myanmar National Telecom Holding Public Co.)
- **Ooredoo Myanmar**
- **ATOM** (formerly Telenor Myanmar)

---

## Why Is Myanmar Implementing IMEI Registration?

Myanmar has one of Southeast Asia's fastest-growing smartphone markets, but a significant portion of devices enter through **unofficial channels** — grey imports from China, Thailand, and Singapore that bypass customs duties and quality checks. The CEIR aims to:

- **Eliminate counterfeit phones** that use cloned or invalid IMEI numbers
- **Reduce mobile phone theft** by making stolen devices unusable on any local network
- **Collect accurate import data** and ensure that customs duties are properly applied to commercial imports
- **Protect consumers** from purchasing phones that will later be blocked

The search spike for `ceir gov mm` reflects a wave of phone owners — especially buyers of imported devices in Yangon, Mandalay, and Naypyidaw — racing to check whether their handset is compliant before the enforcement deadlines.

---

## Who Needs to Register on ceir.gov.mm?

| Device Type | Registration Required? |
|---|---|
| Phone purchased from an authorised Myanmar dealer | Registered by dealer at point of sale |
| Phone brought from abroad (personal use) | **Yes — must self-register** |
| Phone bought from China/Thailand via informal channels | **Yes — high priority** |
| Second-hand phone purchased inside Myanmar | **Verify status first, then register if needed** |
| Phone received as a gift from someone overseas | **Yes — must self-register** |
| Phone used by foreign visitors (short stay) | Grace period applies — check MoTC announcements |

> **Key Rule:** If your phone's IMEI does not appear in the CEIR **whitelist**, it is at risk of being blocked. Even if your phone currently works, it may be blocked during the next enforcement sweep.

---

## How to Check Your IMEI Status on ceir.gov.mm

Before registering, check whether your phone is already compliant. Follow these steps:

**Step 1 — Find Your IMEI**
Dial `*#06#` on your phone. The 15-digit IMEI number will appear on screen. For dual-SIM phones, note both IMEI 1 and IMEI 2.

**Step 2 — Visit the Official CEIR Portal**
Open your browser and go to the official Myanmar CEIR website: **ceir.gov.mm**

**Step 3 — Enter Your IMEI**
On the homepage, find the IMEI check field. Type your 15-digit IMEI number exactly as it appears — no spaces or dashes.

**Step 4 — Read Your Status**
The portal will return one of three results:

| Result | Meaning | Action Required |
|---|---|---|
| **Whitelisted / Valid** | Your phone is registered and compliant | None — you are good |
| **Blacklisted / Blocked** | Your phone is flagged (stolen, counterfeit, or illegal import) | Contact MoTC or your operator |
| **Not Found / Unregistered** | Your phone is not in the database | **Register immediately** |

---

## How to Register Your IMEI on ceir.gov.mm: Step-by-Step

If your IMEI check returns "Not Found" or "Unregistered", follow this registration process:

**Step 1 — Prepare Required Documents**
Gather the following before starting:
- Your **NRC card** (National Registration Card) for Myanmar citizens, or **passport** for foreign nationals
- **Proof of purchase** — receipt, invoice, or online order confirmation
- The phone's **IMEI number(s)** — both IMEI 1 and IMEI 2 if dual-SIM
- The phone's **model name and brand**

**Step 2 — Access the Registration Section**
On **ceir.gov.mm**, navigate to the registration or "Device Registration" section. As of 2025, both online self-registration and in-person registration at operator service centres are available.

**Step 3 — Fill in the Registration Form**
Enter the following details accurately:
- Full name (as on NRC or passport)
- NRC number or passport number
- Phone brand and model
- IMEI 1 (and IMEI 2 if applicable)
- Date of purchase and purchase country
- Purchase price in the original currency

**Step 4 — Upload Supporting Documents**
Attach a clear photo or scan of your purchase receipt and NRC/passport. Files must typically be JPG or PDF format, under 2MB each.

**Step 5 — Submit and Wait for Confirmation**
Submit the form. You will receive an SMS or on-screen confirmation code. Processing time is typically **1–3 business days**.

**Step 6 — Verify Registration**
After the processing period, repeat the IMEI check on ceir.gov.mm. Your device should now show as **Whitelisted**.

> **Alternative — In-Person Registration:** If you encounter issues with the online portal, visit any official service centre of MPT, Mytel, Ooredoo, or ATOM with your documents. Staff can register your IMEI directly at the counter.

---

## In-Person IMEI Registration: Service Centre Locations

All four licensed operators maintain service centres across major cities where you can register your IMEI in person. Key locations include:

| City | Available Operators |
|---|---|
| **Yangon** | MPT, Mytel, Ooredoo, ATOM |
| **Mandalay** | MPT, Mytel, Ooredoo, ATOM |
| **Naypyidaw** | MPT, Mytel |
| **Mawlamyine** | MPT, Ooredoo |
| **Bago** | MPT, Mytel |

Bring your original NRC card (not a photocopy) and original purchase receipt. Processing at service centres is usually completed the same day.

---

## What Happens If You Do Not Register?

The consequences of using an unregistered phone in Myanmar escalate over time:

1. **First Warning** — Your operator sends an SMS warning that your device is unregistered. You have a grace period (typically 30–60 days) to register.
2. **Service Restriction** — Outgoing calls and SMS are disabled while incoming calls remain active.
3. **Full Network Block** — The IMEI is added to the blacklist. All services — calls, SMS, and data — are blocked across all four operators simultaneously.
4. **Permanent Block** — If a device is blocked for non-registration of an illegal import, it may be permanently blacklisted and unable to be re-registered without MoTC intervention.

A permanently blocked phone retains its hardware functionality (Wi-Fi works) but is entirely unusable as a telephone on Myanmar's networks.

---

## Myanmar's Grey Market Phone Problem: Know Before You Buy

A large portion of phones sold in Yangon's **Bo Gyoke Aung San Market** and Mandalay's phone districts are grey-market imports — often purchased in bulk from China or Thailand and sold without documentation. These phones are at the highest risk of:

- Having **cloned or invalid IMEIs** (multiple phones sharing one IMEI number)
- Already being on the **CEIR blacklist** from another country
- Having a **non-Myanmar firmware** that may not support local frequency bands

Before purchasing any second-hand or grey-market phone in Myanmar, always run an IMEI check first:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — Verify the brand and model match what is on the box
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — Confirm the phone has not been reported stolen internationally

An international blacklist hit means the phone is very likely to be added to Myanmar's CEIR blacklist during the next cross-border data sync.

---

## Dual-SIM Phones: Register Both IMEIs

Most phones sold in Myanmar are dual-SIM. Both IMEI numbers must be registered separately on ceir.gov.mm. Registering only IMEI 1 and leaving IMEI 2 unregistered is a common mistake — the second SIM slot will be blocked even if the first appears active.

To find both IMEIs:
- **Dial `*#06#`** — both IMEI numbers will be displayed on dual-SIM devices
- **Settings → About Phone** — look for IMEI 1 and IMEI 2

---

## Frequently Asked Questions

**Q: Is ceir.gov.mm the only official IMEI check portal for Myanmar?**
A: Yes. ceir.gov.mm is the authoritative portal operated by the Ministry of Transport and Communications. Any other site claiming to check Myanmar CEIR status is unofficial.

**Q: My phone currently works fine in Myanmar. Do I still need to register?**
A: Yes. The fact that your phone works now does not mean it is registered. CEIR enforcement operates in waves — your phone may be blocked during the next sweep if it is not on the whitelist.

**Q: I bought a phone in Thailand and brought it to Myanmar. Is it automatically registered?**
A: No. Phones purchased abroad are not automatically registered in Myanmar's CEIR. You must register the IMEI yourself through ceir.gov.mm or at an operator service centre.

**Q: What if my phone is blacklisted by mistake?**
A: Contact MoTC directly or visit the nearest operator service centre with your purchase documentation. Wrongful blacklisting can be disputed, but you will need proof of lawful purchase.

**Q: Can I use my phone on Wi-Fi if it is blocked?**
A: Yes. A CEIR block only prevents the phone from connecting to cellular networks (calls, SMS, mobile data). Wi-Fi functionality is unaffected.

**Q: How long does registration take on ceir.gov.mm?**
A: Online registration is typically processed within 1–3 business days. In-person registration at operator service centres is usually completed the same day.

---

## Check and Protect Your Phone Right Now

Whether you are a Myanmar resident, an expat, or a traveller with a foreign device, take two minutes to verify your IMEI status before enforcement reaches your device:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — Instantly verify your phone's brand, model, and basic compliance info
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — Check if your phone has been reported stolen in any country connected to the global GSMA blacklist database

Registering on ceir.gov.mm takes less than ten minutes. The alternative — a permanently blocked phone — costs far more to resolve.
MD,
    ],

    [
        'slug'    => 'togo-arcep-imei-check-stolen-phone-guide',
        'title'   => 'Togo IMEI Check: ARCEP Rules & Stolen Phone Guide 2026',
        'excerpt' => 'Buying a used phone in Lomé? Here is how ARCEP Togo\'s device-identity rules work and how to check an IMEI before you pay.',
        'date'    => '2026-08-16',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Why an IMEI check matters before buying a phone in Togo

Togo's second-hand phone market — concentrated around Lomé's **Grand Marché** and the many small electronics stalls along Boulevard du 13 Janvier — moves a high volume of imported devices, many of them re-sold several times before reaching a final buyer. Because Togo, like its West African neighbours, has moved toward device-identity oversight through its telecom regulator, a phone that looks fine in a five-minute test at the stall can still turn out to be reported stolen, cloned, or otherwise unusable once you try to register it.

This guide explains what an IMEI is, how Togo's regulatory approach to device identity works in practice, and the checks you should run before handing over cash for any second-hand or imported phone.

## Who regulates mobile devices in Togo?

Telecommunications in Togo are regulated by **ARCEP Togo** (Autorité de Réglementation des Communications Électroniques et des Postes), the national authority responsible for licensing mobile operators, managing spectrum, and — increasingly, in line with a broader West African trend — device identity and anti-counterfeiting oversight. Togo's two main mobile operators, **Togocom** and **Moov Africa Togo**, both rely on the IMEI embedded in every GSM device to identify it on their networks.

The regional direction is clear even where implementation timelines differ from country to country: neighbouring markets (Ghana's NCA, Nigeria's NCC, Sierra Leone's NATCOM) have each rolled out — or are rolling out — an Equipment Identity Register (EIR) that lets a regulator or operator block a phone network-wide based on its IMEI. Togo buyers should assume the same direction of travel applies locally, and treat IMEI verification as standard due diligence rather than an optional extra.

## What actually gets checked when you verify an IMEI

An IMEI check is not a single lookup — it answers several separate questions, and a phone can pass one and fail another:

- **Does the IMEI match the phone?** The first eight digits (the TAC, or Type Allocation Code) identify the brand and model. If a seller claims "Samsung Galaxy A54" but the IMEI decodes to a different device, that is a hard stop.
- **Is the IMEI valid at all?** A 15-digit number that fails the Luhn checksum, or that is obviously duplicated across multiple listings, points to a cloned or counterfeit unit.
- **Has the device been reported lost or stolen?** International blacklist databases aggregate reports from carriers and law-enforcement partners across many countries — relevant for phones that arrived in Togo via Nigeria, Ghana, or further afield.
- **Is the device locked to a previous owner's account?** Apple's iCloud Activation Lock and Samsung's Knox Guard are the two most common reasons a buyer in Lomé ends up with a phone that cannot be reset or reactivated.

## Step-by-step: checking a phone before you buy in Lomé

**Step 1 — Get the IMEI from the phone itself, not just the box.**
Dial `*#06#` on the handset and compare the number shown on screen to the number printed on the box and on the SIM tray. All three should match; if the seller can only show you a box or a screenshot, treat that as a warning sign.

**Step 2 — Note both IMEI numbers on dual-SIM phones.**
Most phones sold in Togo are dual-SIM. Each SIM slot has its own IMEI, and both need to be clean — a device can pass on IMEI 1 and still be blocked on IMEI 2.

**Step 3 — Run a free IMEI lookup to confirm brand and model.**
This is the fastest way to catch a mismatched or cloned device before spending any money: [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check).

**Step 4 — Run a blacklist check.**
A [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) cross-references international carrier and law-enforcement blacklist databases — useful given how many devices in Togo's market have passed through several countries.

**Step 5 — Check account locks for Apple and Samsung devices.**
For iPhones, confirm iCloud Activation Lock is off. For Samsung devices, confirm the Knox Guard status. Both are common reasons a phone that "works fine" in the shop becomes unusable the moment you try to set it up with your own account.

## Red flags specific to Togo's market

- **A price that is noticeably below the going rate for that model.** In Lomé's competitive resale market, an unusually cheap iPhone or Samsung flagship is more often explained by a blacklist or lock problem than by a genuine bargain.
- **A seller who is reluctant to let you dial `*#06#` before paying.** A legitimate seller has no reason to object to a 30-second check.
- **Packaging that does not match the phone's language settings or regional model number.** A device boxed for a different market (different plug, different model suffix) can indicate a device that was never intended for sale in Togo — and may carry a higher risk of being reported elsewhere.
- **No functioning receipt or proof of purchase.** Ask for one; it protects you if a dispute over the device's origin comes up later.

## A quick reference: which checks for which situation

| Situation | Checks to run |
| --- | --- |
| Brand-new phone, sealed box, from an authorised dealer | Free IMEI check only |
| Second-hand phone from a Grand Marché stall | Free IMEI + Blacklist + Account lock (iCloud/Knox) |
| Phone imported informally from Ghana, Nigeria, or Benin | Free IMEI + Blacklist + Model/region check |
| Any iPhone, regardless of source | Free IMEI + iCloud Activation Lock check |

## Frequently asked questions

**Q: Does Togo currently block phones the way Nigeria or Ghana do?**
A: Device-identity oversight is expanding across West Africa, with ARCEP Togo aligned to the same regional direction as neighbouring regulators. Whether or not network-level blocking is active at the moment you read this, an IMEI that is already flagged internationally (stolen, cloned, or duplicated) is a real risk regardless of local enforcement status — it can still fail activation with either Togocom or Moov Africa Togo, or become unusable if it travels to a country actively enforcing a blacklist.

**Q: I bought a phone that turned out to be iCloud-locked. What can I do?**
A: Very little without the original owner's Apple ID and password. This is why checking iCloud status *before* paying is far more effective than trying to resolve it afterward — always confirm with an [iCloud check](https://imeihub.net/service.php?slug=apple-icloud) before buying any second-hand iPhone.

**Q: Both my SIM slots show different IMEI numbers — is that normal?**
A: Yes, this is normal for any dual-SIM phone. Each physical SIM slot has its own distinct IMEI, and both should be checked separately.

**Q: Where can I check an IMEI from Togo?**
A: Any internet-connected device works — [imeihub.net](https://imeihub.net) runs checks from a browser with no local installation required, which is convenient for verifying a phone at the point of sale before you commit to buying it.

## Check before you commit

A used-phone purchase in Togo is a considered expense for most buyers — running two or three checks before paying takes a few minutes and costs a fraction of what a blacklisted or locked device would cost you afterward.

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)
- [Full Services List](https://imeihub.net/services.php)
MD,
    ],

];
