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
];
