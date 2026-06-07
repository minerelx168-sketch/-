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
        'tag'     => 'Basics',
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

    // ── 30-Day Blue Ocean Content Calendar ─────────────────────────────────

    [
        'slug'    => 'kenya-kra-imei-declaration-f88-form',
        'title'   => 'Kenya KRA IMEI Declaration: F88 Form Guide 2025',
        'excerpt' => 'Kenya now requires you to declare your phone IMEI at the airport. Learn how the KRA F88 form works and what happens if you skip it.',
        'date'    => '2026-05-29',
        'tag'     => 'Regulation',
        'body'    => <<<MD
## Kenya's IMEI Declaration Rule: What Changed in January 2025

Starting **January 2025**, the Kenya Revenue Authority (KRA) made it
mandatory to declare any mobile phone brought into Kenya through an
international airport or border crossing. The policy targets devices
not previously registered on Kenyan networks, closing a loophole that
allowed millions of untaxed or stolen handsets to enter the country.

If you arrive at **Jomo Kenyatta International Airport (JKIA)** or any
other port of entry with a foreign-purchased phone, you must declare it
on the **Customs Declaration Form F88** before you clear the immigration
queue.

## What Is the KRA F88 Form?

The F88 is Kenya's standard traveller goods declaration form. It has
been updated to include a dedicated field for **mobile device IMEI
numbers**. You are required to:

1. List every mobile phone you are carrying (personal use or otherwise).
2. Enter the 15-digit IMEI for each device (dial `*#06#` to retrieve it).
3. Declare the estimated value of each device in USD or KES.
4. Sign and submit the form to a KRA customs officer at the port of entry.

Devices declared under the personal-use exemption (currently one phone
per traveller valued under **KES 75,000**) attract **zero duty**. Above
that threshold, import duty of **25%** plus 16% VAT applies.

## Why Kenya Introduced Mandatory IMEI Declaration

Kenya's **Communications Authority (CA)** estimates that more than
**4 million unregistered devices** were active on local networks before
the crackdown. These phones are a problem for three reasons:

- Many are **counterfeit** — cloned IMEIs cause network interference and
  make it impossible to trace stolen handsets.
- Revenue losses from untaxed imports run into billions of KES annually.
- Stolen phones exported from Europe or the US continue to work freely
  on Kenyan networks without a blacklist check.

The IMEI declaration system feeds directly into the **Device
Management System (DMS)** jointly operated by KRA and the Communications
Authority. Declared IMEIs are cross-referenced against the GSMA
international blacklist.

## What Happens If You Don't Declare?

Failure to declare a device on the F88 form can result in:

- **Confiscation** of the undeclared phone at the border.
- A **fine of up to KES 500,000** under the East African Community
  Customs Management Act.
- The device's IMEI being flagged in the DMS — it may be blocked from
  Kenyan networks even if it physically crosses the border.

KRA officers do conduct spot checks. Roaming data logs can also reveal
a device operating in Kenya that was never declared.

## How to Check Your IMEI Before Travelling to Kenya

Before you pack your bag, run a quick IMEI check to make sure your
device is clean:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — confirm your model and manufacturer details match your physical device.
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — verify your IMEI is not listed as stolen or lost in any international database.

A clean IMEI report takes 30 seconds and gives you documentary evidence
to show customs if questioned.

## Step-by-Step: Completing the F88 IMEI Declaration

| Step | Action | Where |
| --- | --- | --- |
| 1 | Dial `*#06#` and note your IMEI | On your phone before the flight |
| 2 | Pick up the F88 form on the plane or in arrivals | Cabin crew or customs desk |
| 3 | Fill Section C: Personal Effects & Electronics | List each phone + IMEI |
| 4 | Proceed to the Green Channel if value is within exemption | JKIA Arrivals Hall |
| 5 | Hand the completed form to the KRA officer | Customs clearance desk |

## Frequently Asked Questions

**Do Kenyan residents returning home need to declare?**
Yes, if you are bringing in a device purchased abroad that has never
been used on a Kenyan network, you must declare it.

**What if I have two phones?**
You may carry multiple devices, but only **one** qualifies for the
personal-use duty-free exemption. Additional phones are subject to
full import duty.

**Can I declare after arrival if I forgot?**
KRA has a voluntary disclosure window of **72 hours** after arrival.
Go to any KRA office or use the iTax portal, but penalties may still apply.

**Does my phone need to be registered even after declaration?**
Declaration at the border triggers automatic registration in the
CA's Device Management System. You do not need to take any additional
steps for registration.

For the most up-to-date duty rates and exemption thresholds, always
check the official **Kenya Revenue Authority** website at kra.go.ke
before you travel.
MD,
    ],
    [
        'slug'    => 'myanmar-ceir-imei-registration-guide',
        'title'   => 'Myanmar CEIR IMEI Registration: ceir.gov.mm Guide 2025',
        'excerpt' => 'Myanmar blocks unregistered phones on all networks. Learn how to check and register your IMEI on the ceir.gov.mm portal in minutes.',
        'date'    => '2026-05-30',
        'tag'     => 'Guide',
        'body'    => <<<MD
## What Is Myanmar's CEIR System?

Myanmar's **Central Equipment Identity Register (CEIR)** is a national
database managed by the **Posts and Telecommunications Department (PTD)**
under the Ministry of Transport and Communications. Every mobile device
operating in Myanmar must have its IMEI registered in the CEIR — if it
is not, the device will eventually be **blocked from all Myanmar
networks** (MPT, Telenor, Ooredoo, and Mytel).

Searches for **ceir.gov.mm** surged by over **3,750%** in 2024–2025,
reflecting mass awareness campaigns and network blocking enforcement
events that left hundreds of thousands of phones unable to make calls.

## Why Myanmar Introduced CEIR

Before CEIR, counterfeit and cloned-IMEI devices flooded the market —
particularly cheap handsets imported through informal border trade with
China and India. The PTD introduced mandatory registration to:

- **Eliminate duplicate IMEIs** that cause call-routing failures.
- **Enable rapid blocking** of stolen or lost devices.
- Ensure that only **type-approved** handsets (meeting safety and
  radio frequency standards) operate on Myanmar's spectrum.

## How to Check If Your IMEI Is Registered

Before registering, check whether your device is already in the CEIR:

1. Go to **ceir.gov.mm** (the official government portal).
2. Click **"IMEI Status Check"** (စစ်ဆေးရန်).
3. Enter your 15-digit IMEI (dial `*#06#` on your phone to find it).
4. Click Submit and read the result.

You can also run a pre-check using our [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
to confirm your device details are correct before submitting to the
government portal.

## CEIR Status Results Explained

| Status | Meaning | Action Required |
| --- | --- | --- |
| **Registered** | IMEI is in the CEIR and active | None — your phone is good |
| **Not Registered** | IMEI has not been submitted | Register immediately (see below) |
| **Blocked** | PTD has blocked this IMEI | Contact your carrier or PTD |
| **Duplicate** | Same IMEI found on another device | Report to PTD — likely counterfeit |

## How to Register Your IMEI on ceir.gov.mm

### Step 1 — Gather Your Documents

You will need:
- Your **National Registration Card (NRC)** number or passport number
  (for foreigners).
- The phone's **IMEI number** (up to two IMEIs for dual-SIM devices).
- The phone's **model name** and **manufacturer**.
- Proof of purchase (receipt, invoice, or original box) — recommended
  but not always required.

### Step 2 — Create an Account or Log In

Visit **ceir.gov.mm** and click **"Register"** if you are a first-time
user. Enter your NRC/passport number and a valid phone number for OTP
verification. You will receive a one-time password via SMS.

### Step 3 — Submit Your IMEI

After logging in:

1. Click **"Register Device"** (Device မှတ်ပုံတင်ရန်).
2. Enter IMEI 1. For dual-SIM phones, enter IMEI 2 in the second field.
3. Select the brand and model from the dropdown list.
4. Enter the date of purchase and country of origin.
5. Upload a photo of your proof of purchase (JPEG, under 2 MB).
6. Click **"Submit"** and note the reference number shown on screen.

### Step 4 — Wait for Approval

Processing typically takes **1–3 working days**. You will receive an
SMS confirmation when your IMEI is approved. If your device is a
commercially imported model sold in Myanmar, approval is usually instant.

## Foreigners and Tourists: Do You Need to Register?

If you are visiting Myanmar for **fewer than 90 days**, your foreign
device may operate on a tourist SIM under a temporary IMEI exemption.
Check with your carrier (MPT or Ooredoo sell tourist SIMs at the
Yangon and Mandalay airports). For stays longer than 90 days, full
CEIR registration is required.

## What Happens If You Don't Register?

The PTD runs periodic **network sweeps** to identify unregistered IMEIs.
Devices flagged in a sweep receive:

- An SMS warning giving **30 days** to register.
- A second warning after 15 days of non-compliance.
- **Full network block** (no calls, no data) after the 30-day deadline.

Re-activation after a block requires visiting a PTD service centre in
person with your NRC and device.

## Is My Second-Hand Phone Safe to Use in Myanmar?

If you bought a used phone locally or imported one, always run a
[WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)
before registering. Registering a phone that was stolen elsewhere
can cause legal complications if the original owner reports it to PTD.

A clean blacklist report takes seconds and protects you before you
commit to the registration process.
MD,
    ],
    [
        'slug'    => 'ncc-imei-check-nigeria-verify-phone',
        'title'   => 'NCC IMEI Check Nigeria: Verify Your Phone in 2025',
        'excerpt' => 'The NCC is actively blocking counterfeit and unapproved IMEIs across Nigeria. Here is how to verify your phone and stay connected.',
        'date'    => '2026-05-31',
        'tag'     => 'Regulation',
        'body'    => <<<MD
## Nigeria's War on Counterfeit Phones: The NCC's Role

The **Nigerian Communications Commission (NCC)** is the federal
regulator for Nigeria's telecoms sector. Since 2022, the NCC has
been running an aggressive campaign to **block counterfeit, cloned,
and type-unapproved handsets** from operating on networks run by
MTN, Airtel, Glo, and 9mobile.

The NCC estimates that over **30 million** of the approximately
200 million active SIMs in Nigeria are associated with devices that
either carry a cloned IMEI, have a duplicate IMEI shared across
multiple handsets, or were never submitted for type-approval testing.
These devices pose real risks: network interference, inability to
trace stolen phones, and consumer safety issues from untested radio
hardware.

## What Is the NCC IMEI Check?

The NCC operates a **Device Management System (DMS)** in partnership
with the GSMA and all licensed mobile network operators. The DMS
maintains three lists:

| List | Meaning |
| --- | --- |
| **White List** | Valid, type-approved, active IMEI — device will work |
| **Grey List** | IMEI under review or flagged for investigation |
| **Black List** | Stolen, lost, or permanently blocked IMEI |

Any handset whose IMEI lands on the Black List — whether from a
report in Nigeria or from the international GSMA IMEI database —
will lose service on all four major Nigerian networks simultaneously.

## How to Check Your IMEI with the NCC

### Method 1: NCC Short Code

Dial **`*#06#`** to display your IMEI, then send the IMEI as a
free SMS to **NCC's short code 444**. You will receive a response
within minutes indicating whether your device is approved.

*Note: This service is available on MTN, Airtel, Glo, and 9mobile.*

### Method 2: Online Portal

Visit the NCC's official website at **ncc.gov.ng** and navigate to
the **Type Approval** section. Enter your 15-digit IMEI in the
Device Verification tool.

### Method 3: Third-Party IMEI Check

For a faster check with additional details (brand, model, network
lock status), use:

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — confirms your device identity.
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — cross-references the GSMA international blacklist.

## What Is Type Approval and Why Does It Matter?

Before a phone model can legally be **sold or distributed** in Nigeria,
the manufacturer or importer must obtain **NCC Type Approval** — a
certification that the device meets Nigeria's RF, electromagnetic
compatibility, and safety standards. Approved models receive a
**Type Approval Certificate (TAC)** number.

Devices sold informally at markets like **Ikeja Computer Village**
(Lagos) or **Wuse Market** (Abuja) frequently lack this approval.
Buying one means you could be left without service when the next
NCC enforcement sweep runs.

## Buying a Used Phone in Nigeria: Your Risk Checklist

| Check | What It Catches | Where to Do It |
| --- | --- | --- |
| Dial `*#06#` | Verify IMEI is on the device | Your phone keypad |
| Match IMEI to box | Catch cloned/replaced IMEIs | Compare physically |
| Free IMEI model check | Confirm brand & model match | imeihub.net |
| Blacklist check | Stolen / internationally blocked | imeihub.net |
| NCC SMS (444) | Nigeria-specific type approval | Any Nigerian SIM |

## Cloned IMEIs: A Specific Nigerian Market Problem

A **cloned IMEI** occurs when a manufacturer copies the IMEI of a
legitimate device (e.g., a Samsung Galaxy) and prints it on a
low-cost counterfeit. Both the original and the clone then share
one IMEI. The NCC's DMS detects this when the same IMEI is seen
simultaneously on two different towers — a physical impossibility
for a single device.

When a cloned IMEI is detected:
1. The GSMA's database is queried to find the legitimate owner's IMEI.
2. The counterfeit device is placed on the **Black List**.
3. All four networks are instructed to block the cloned IMEI.

If you unknowingly bought a cloned device, you have no recourse —
the phone will be blocked regardless of purchase price paid. This is
why a blacklist check before purchase is essential.

## What to Do If Your Phone Is Blocked by the NCC

1. Dial `*#06#` and confirm your IMEI.
2. Visit **ncc.gov.ng/consumer-protection/complain** to file a complaint.
3. Provide your IMEI, proof of purchase, and contact details.
4. The NCC will investigate — if your device was incorrectly blocked,
   it will be reinstated within 10 working days.
5. If the block is valid (genuine counterfeit), the NCC will not
   reverse it. In that case, you need to replace the device.

The NCC consumer helpline is also reachable on **0800 NCC HELP**
(0800 622 4357), which is free from all Nigerian networks.
MD,
    ],
    [
        'slug'    => 'ghana-nca-imei-registration-stolen-check',
        'title'   => 'Ghana NCA IMEI Registration: Check & Register Your Phone',
        'excerpt' => 'Ghana\'s NCA requires IMEI registration for all mobile devices. Learn how to register, check for stolen phones, and stay compliant.',
        'date'    => '2026-06-01',
        'tag'     => 'Regulation',
        'body'    => <<<MD
## Ghana's National Communications Authority and IMEI Compliance

The **National Communications Authority (NCA)** is Ghana's telecoms
regulator, operating under the Electronic Communications Act 775.
Since 2017, the NCA has partnered with all licensed Mobile Network
Operators — MTN Ghana, Vodafone Ghana, AirtelTigo, and Glo Mobile —
to build a joint **Device Management System (DMS)** that manages
a national IMEI database covering every handset on Ghana's networks.

The NCA's IMEI compliance initiative has two goals:
- **Block counterfeit and stolen phones** from operating on Ghanaian networks.
- **Ensure devices meet NCA type-approval standards** — protecting
  consumers from substandard hardware.

## How the NCA's Three-List System Works

Like many GSMA member regulators, the NCA's DMS operates three
classification lists:

| List | Status | Effect on Your Phone |
| --- | --- | --- |
| **White List** | Valid, approved IMEI | Full network access |
| **Grey List** | Under review or flagged | Limited service or warnings |
| **Black List** | Stolen, lost, or blocked | No network access on any Ghanaian carrier |

When a phone is reported stolen to any police station in Ghana, the
Ghana Police Service forwards the IMEI to the NCA, which places it
on the Black List within **24–48 hours**. All four MNOs then push
the block to their networks simultaneously.

## How to Check Your Phone's IMEI Status in Ghana

### Option 1: NCA Online Portal

Visit **nca.org.gh** and use the IMEI verification tool under the
"Consumer" section. Enter your 15-digit IMEI and the portal will
return its current status on the national DMS.

### Option 2: SMS Check

Send your IMEI to **419** (short code operated jointly by all
Ghanaian MNOs). You will receive a free SMS within two minutes
confirming whether the device is White, Grey, or Black listed.

### Option 3: Third-Party International Check

For an immediate check that also covers international blacklists
(important if you are buying an imported phone):

- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — verifies brand and model.
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — checks GSMA, CTIA, and regional blacklists globally.

This is particularly valuable for phones sourced from overseas markets
or e-commerce platforms like Jiji.com.gh or Tonaton.

## NCA Type Approval: What It Means for Buyers

Every phone model legally sold or imported into Ghana must receive
**NCA Type Approval** before distribution. An approved device will
have an NCA approval sticker on the box or documentation. You can
verify a model's approval status on the NCA portal.

Buying from informal markets — Kwame Nkrumah Circle, Suame Magazine,
or Tema Community 1 phone stalls — carries real risk of purchasing
a device that is not type-approved and may be blocked without notice.

## How to Report a Stolen Phone in Ghana

If your phone is stolen, act within 24 hours to maximise the chance
of blocking and recovery:

1. Dial `*#06#` on another phone if possible, or find the IMEI on
   your original box or purchase receipt.
2. File a **police report** at the nearest Ghana Police Service station.
   You will need the IMEI, phone model, and proof of ownership.
3. Contact your MNO (MTN: 100, Vodafone: 100, AirtelTigo: 121) and
   request emergency IMEI blocking. Most operators can block within
   **4 hours** of receiving the police report number.
4. The MNO will forward the block request to the NCA DMS.

Once blocked, the phone cannot make calls, send SMS, or use mobile
data on any Ghanaian network — even if the thief inserts a new SIM.

## Buying a Used Phone in Ghana: Practical Tips

Ghana's second-hand phone market is large and mostly informal. Follow
this checklist before paying:

- **Ask for the IMEI** (dial `*#06#`) and match it to the box and
  SIM tray label — all three should be identical.
- **Run the NCA SMS check** (send to 419) — it is free and takes
  two minutes.
- **Run a worldwide blacklist check** — a phone imported from Europe
  or the US might be clean on the NCA's domestic list but blacklisted
  internationally. imeihub.net covers this.
- **Ask for the original receipt** — if the seller cannot produce one,
  negotiate a significant discount to reflect the risk.

## Importing Phones into Ghana: Customs and NCA Registration

If you bring a phone into Ghana for personal use:
- Devices valued under **$200** are generally exempt from import duty.
- Phones above that value attract **20% import duty** plus **12.5% VAT**.
- Imported phones are automatically added to the NCA's DMS by customs
  at the port of entry using the IMEI from the declaration form.

Commercial importers must obtain NCA type approval for each model
they import before customs clearance is granted.
MD,
    ],
    [
        'slug'    => 'pakistan-pta-dirbs-imei-check-register',
        'title'   => 'Pakistan PTA DIRBS: Check & Register Your Phone 2025',
        'excerpt' => 'PTA\'s DIRBS system blocks unregistered phones after 60 days. Check your IMEI status now and learn how to register before the deadline.',
        'date'    => '2026-06-02',
        'tag'     => 'Regulation',
        'body'    => <<<MD
## What Is DIRBS and Why Does Pakistan Use It?

**DIRBS** stands for **Device Identification, Registration, and Blocking
System**. It is Pakistan's national mobile device management platform,
operated by the **Pakistan Telecommunication Authority (PTA)**. DIRBS
was developed by Qualcomm in partnership with the PTA and launched in
2019 as one of the most technically sophisticated IMEI management
systems in the world.

The system gives the PTA real-time visibility over every device active
on Pakistan's networks (Jazz, Telenor Pakistan, Zong, Ufone, and SCOM).
Any phone that is not registered in DIRBS within the prescribed
deadline is **automatically blocked** — it stops receiving calls and
data on all five networks without any manual intervention.

## The 60-Day Registration Rule

When you bring a foreign-purchased phone into Pakistan — whether as a
returning Pakistani national, a tourist, or an online shopper — the
PTA grants a **60-day grace period** to register the device. During
those 60 days the phone works normally. After day 60, if the IMEI
is not in DIRBS, the phone is blocked.

There is a separate rule for locally purchased devices: phones sold
by authorized retailers in Pakistan are pre-registered by the
manufacturer or importer before they reach the shelf. You do not
need to register them yourself.

## How to Check Your IMEI Status in DIRBS

### Method 1: PTA DIRBS Web Portal

Visit **dirbs.pta.gov.pk** and click **"Device Status"**. Enter your
15-digit IMEI and the system will return one of four statuses:

| DIRBS Status | Meaning |
| --- | --- |
| **Compliant** | Registered and fully active |
| **Non-Compliant** | Not registered — will be blocked after 60 days |
| **Blocked** | Already blocked by PTA |
| **Stolen** | Reported as lost or stolen |

### Method 2: SMS Check

Send your IMEI to **8484** from any Pakistani SIM. You will receive
an immediate free SMS reply with the DIRBS status.

### Method 3: International Pre-Check

Before travelling or purchasing:
- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — confirms your device's brand and model.
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — ensures the device is not blacklisted before you register it (registering a stolen device creates legal exposure).

## How to Register Your IMEI with the PTA

### For Travellers and Returning Nationals

The PTA provides a **Temporary Device Registration (TDR)** option for
short-stay visitors (up to 3 months) and a permanent registration
option for residents.

**Online Registration (Permanent):**

1. Visit **dirbs.pta.gov.pk** then "Device Registration" then "Personal".
2. Log in or register with your **CNIC** (Computerised National Identity
   Card) or NICOP number. Foreigners use their passport number.
3. Enter your IMEI(s). Each CNIC can register up to **5 devices** per year.
4. Pay the applicable **IMEI registration tax** online (credit/debit
   card or bank transfer).
5. Receive your DIRBS registration certificate via email. Keep this
   as proof of compliance.

**At the Airport (First-Time Arrivals):**

PTA registration desks are available at **Islamabad, Karachi, and
Lahore international airports**. You can register on arrival and
pay the tax at the desk before leaving the terminal.

## PTA Import Tax: How Much Will You Pay?

The PTA charges a **Customs Duty + Regulatory Duty + Sales Tax** on
imported phones. The exact amount depends on the phone's assessed
value in PKR. Below are approximate 2025 figures:

| Phone Category | Approx. PTA Tax (PKR) |
| --- | --- |
| Budget (under $100 value) | 1,000 – 5,000 |
| Mid-range ($100–$300) | 5,000 – 15,000 |
| iPhone 14 / Samsung S23 | 25,000 – 40,000 |
| iPhone 15 Pro / S24 Ultra | 45,000 – 75,000 |

*Rates change periodically. Use the official PTA tax calculator at
**dirbs.pta.gov.pk** for the latest PKR amount before you travel.*

## What Happens If Your Phone Is Blocked?

A blocked phone cannot make or receive calls, send SMS, or use mobile
data on any Pakistani network. However:

- Wi-Fi calling and Wi-Fi data still work (the block is at the network
  level, not the device level).
- You can re-activate a blocked device by completing DIRBS registration
  and paying the applicable tax. The block is lifted within **24–72 hours**
  after payment confirmation.
- There is **no fine** for registering late — just pay the tax and
  the phone is unblocked.

## Frequently Asked Questions

**Can I register a phone for someone else?**
Yes, but it counts against your personal CNIC allowance (5 devices
per year). Business importers have a separate commercial quota.

**Does my old Pakistani-purchased phone need re-registration?**
No. Devices that were active in Pakistan before DIRBS went live were
grandfathered in. Only new imports need registration.

**What about phones bought on Daraz or OLX?**
Any phone sold through official Pakistani e-commerce platforms should
already be compliant. Check the DIRBS status before purchasing from
an individual seller.

**Is DIRBS registration permanent?**
Yes. Once registered, the IMEI remains in DIRBS indefinitely. You
do not need to re-register.
MD,
    ],
    [
        'slug'    => 'indonesia-bea-cukai-imei-registration-guide',
        'title'   => 'Indonesia Bea Cukai IMEI Registration: Full Guide 2025',
        'excerpt' => 'Bringing a phone into Indonesia? Customs (Bea Cukai) will block it if the IMEI is unregistered. Here is how to register it and avoid disruption.',
        'date'    => '2026-06-03',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Indonesia's IMEI Policy: Why Your Phone Might Stop Working

Indonesia enforces one of Southeast Asia's strictest IMEI regulations.
Under **Government Regulation No. 46 of 2021**, any phone brought into
Indonesia that is not registered with the **Directorate General of
Customs and Excise (Direktorat Jenderal Bea Cukai)** will be
**blocked from all Indonesian networks** — Telkomsel, Indosat Ooredoo
Hutchison, XL Axiata, and Smartfren.

The rule applies to **all travellers and online shoppers**, not just
commercial importers. If you fly into Jakarta and pull out an iPhone
you bought in Singapore, you have a limited window to register before
losing signal.

## Who Needs to Register?

| Traveller Type | Registration Required? |
| --- | --- |
| Tourist (staying under 90 days) | **Yes** — temporary registration |
| Indonesian returning from abroad | **Yes** — permanent registration + tax |
| Phone bought locally from authorised retailer | **No** — pre-registered by importer |
| Phone bought on Tokopedia/Shopee (domestic seller) | **No** — should already be registered |
| Phone ordered from overseas (AliExpress, Amazon) | **Yes** — must register on arrival |

## How to Check If Your IMEI Is Registered in Indonesia

### Method 1: Official Government Portal

Visit **www.beacukai.go.id** and use the IMEI status check tool.
Enter your 15-digit IMEI.

### Method 2: Text-Based Check

Send an SMS to **08118202208** with the text:
`IMEI [your 15-digit IMEI]`

You will receive a reply from the Bea Cukai system within minutes.

### Method 3: Quick Pre-Check

Before arrival or before purchasing a second-hand phone, use:
- [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check) — verify device identity matches what the seller claims.
- [WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist) — ensure the device is not internationally blacklisted before registering.

## How to Register Your IMEI: Two Methods

### Method A: Register at the Airport (Recommended for Tourists)

**Bea Cukai registration desks** are located in the arrivals halls of:
- Soekarno-Hatta International Airport, Jakarta (Terminals 2 and 3)
- Ngurah Rai International Airport, Bali
- Juanda International Airport, Surabaya
- Kualanamu International Airport, Medan

**At the desk, you will need:**
- Your passport
- Your boarding pass
- The phone in question (turned on so the IMEI can be verified)
- Cash or card to pay any applicable import duty

**Free exemption:** Travellers may bring **one phone** valued up to
**USD 500** duty-free into Indonesia. Above that value, a customs
duty of **10%** applies plus 11% VAT.

### Method B: Online Registration (Within 60 Days of Arrival)

If you did not register at the airport:

1. Go to **www.beacukai.go.id** then "Registrasi IMEI Online".
2. Log in with your ID number (KTP for citizens, passport for foreigners).
3. Enter your IMEI(s) — up to two IMEIs for dual-SIM phones.
4. Enter your arrival date and port of entry.
5. Attach a scanned copy of your passport photo page.
6. Submit and wait for approval (1–5 working days).
7. Pay any duties via the provided Virtual Account number.

*Online registration is only available within **60 days** of arrival
in Indonesia. After 60 days, you must visit a Bea Cukai office in person.*

## How Much Will You Pay?

The **USD 500 exemption** applies per person per trip. Above that:

| Phone Value (USD) | Import Duty | VAT | Approx. Total Tax (IDR) |
| --- | --- | --- | --- |
| Up to $500 | 0% | 0% | Free |
| $500 – $1,000 | 10% | 11% | ~Rp 1.5 – 3.2 million |
| $1,000 – $2,000 | 10% | 11% | ~Rp 3.2 – 6.4 million |
| Over $2,000 | 10% + luxury tax | 11% | Ask Bea Cukai directly |

Exchange rates fluctuate — the Bea Cukai website has a live duty
calculator that gives you an exact IDR figure.

## What Happens If You Don't Register?

An unregistered IMEI is placed on Indonesia's **black list** by the
Ministry of Communication and Digital Affairs (Kominfo). When the
phone attempts to connect to any Indonesian network, the network
returns a **"SIM Card Not Registered"** or **"No Service"** error.

- Wi-Fi still works — only cellular service is blocked.
- The block is reversed once you complete registration and pay duties.
- Attempting to register a phone more than 60 days after arrival
  without a valid reason results in the application being rejected.
  You must then visit a Bea Cukai office.

## Tips for Tourists Visiting Bali and Jakarta

- **Register on arrival** — the Bali airport desk handles dozens of
  tourists per hour and the process takes fewer than 10 minutes.
- **Bring your receipt or invoice** if you purchased the phone recently.
  It speeds up the duty calculation.
- **Dual-SIM phones** count as one device — both IMEIs are registered
  under a single application.
- A **local SIM from Telkomsel or XL** is typically cheaper for data
  than international roaming. The SIM will work fine once your IMEI
  is registered.
MD,
    ],
    [
        'slug'    => 'sierra-leone-imei-check-stolen-blacklist',
        'title'   => 'Sierra Leone IMEI Check: Verify Phones Before You Buy',
        'excerpt' => 'Sierra Leone\'s second-hand phone market carries real stolen-device risk. Run an IMEI check before you buy to avoid a blocked or blacklisted handset.',
        'date'    => '2026-06-04',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Mobile Phones in Sierra Leone: A Market Overview

Sierra Leone has one of West Africa's fastest-growing mobile markets,
with networks including **Orange SL**, **Africell**, **QCell**, and
**Rokel Telecom** serving a rapidly expanding subscriber base. Mobile
penetration now exceeds **65%**, and the vast majority of devices in
use are second-hand phones sourced from Europe, the US, or regional
African markets.

This strong demand for affordable used handsets creates a parallel
problem: **stolen phones from European and American markets** regularly
end up in Freetown's phone stalls at Lumley or King Jimmy Market, and
buyers have little way to know the device's history.

## Who Regulates IMEI in Sierra Leone?

The **National Telecommunications Commission (NATCOM)** is Sierra
Leone's telecoms regulator. NATCOM works with all licensed operators
to maintain a national IMEI register aligned with the GSMA's
international standards. Devices on the **GSMA international
blacklist** — reported stolen in the UK, US, Canada, or Australia —
can be blocked on Sierra Leonean networks when they are flagged during
routine network audits.

NATCOM has not yet implemented the fully automated real-time blocking
seen in Pakistan or Indonesia, but **periodic sweeps** do catch
internationally blacklisted devices, and enforcement is increasing.

## Why You Should Check IMEI Before Buying in Sierra Leone

A used phone bought without an IMEI check carries several risks:

- **Future blocking:** If the device is on the GSMA blacklist, it may
  be blocked without notice when NATCOM runs its next sweep.
- **No recourse:** Once a phone is blocked by NATCOM instruction, the
  buyer has no legal claim against the seller under Sierra Leonean law
  if the seller has disappeared.
- **Counterfeit devices:** Chinese-manufactured clones of popular brands
  (Samsung, iPhone) carry duplicated IMEIs — two buyers can end up with
  the same IMEI, causing network conflicts.
- **No manufacturer warranty:** A device sourced through informal
  channels almost never comes with valid warranty coverage.

## How to Check a Phone's IMEI in Sierra Leone

### Step 1: Get the IMEI

Ask the seller to dial **`*#06#`** on the phone. The IMEI appears on
screen. Cross-check it against the label on the back or in the
SIM-card tray. **All three must match.**

### Step 2: Run a Free IMEI Lookup

Use [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
to instantly verify:
- Is the brand and model what the seller claims?
- Is the IMEI format valid (15 digits, passing the Luhn check)?

If the IMEI returns a completely different brand than the physical
device, walk away immediately — it is a clone.

### Step 3: Run a Worldwide Blacklist Check

This is the most important step for Sierra Leone buyers because
so many devices come from overseas markets:

[WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)

This check covers the GSMA IMEI database plus databases from major
English-speaking markets (UK, US, Canada, Australia) — the source
countries for most phones reaching Freetown. A clean result gives
you confidence the phone has not been reported stolen abroad.

### Step 4: Verify the Seller's Identity

For significant purchases (any phone over Le 500,000), ask the
seller to provide:
- Their name and phone number.
- A photo ID if they are selling from a stall.

This is not legally required, but it creates a record if you need
to make a complaint later.

## Reporting a Stolen Phone in Sierra Leone

If your phone is stolen in Sierra Leone:

1. File a **police report** at the nearest Sierra Leone Police station.
   Request a crime reference number.
2. Note your IMEI (from your box, purchase receipt, or Google/Apple
   account devices list).
3. Contact your operator (Africell: 111; Orange SL: 100; QCell: 116)
   with the IMEI and crime reference number to request blocking.
4. Email **complaints@natcom.gov.sl** with the details to escalate
   to a national block.

## Quick Tips for the Freetown Phone Market

| Do | Don't |
| --- | --- |
| Always dial `*#06#` first | Buy if the seller refuses to show IMEI |
| Run a blacklist check before paying | Trust a verbal "it's clean" guarantee |
| Ask for original box if available | Ignore a mismatched IMEI label |
| Pay by mobile money for a transaction record | Hand over cash before checking |

The 30 seconds it takes to run an IMEI check is the cheapest insurance
you can buy in the Freetown phone market.
MD,
    ],
    [
        'slug'    => 'togo-imei-verification-guide',
        'title'   => 'Togo IMEI Verification: Check Your Phone Before You Buy',
        'excerpt' => 'Buying a phone in Togo? Learn how to verify the IMEI, check for stolen or blacklisted devices, and navigate ARCEP Togo\'s regulations.',
        'date'    => '2026-06-05',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Mobile Phone Regulation in Togo

Togo's telecoms sector is regulated by the **Autorité de Régulation des
Communications Électroniques et des Postes (ARCEP Togo)**. The country's
main networks — **Togocel** (now Moov Africa Togo) and **Atlantique
Télécom Togo** — operate under ARCEP's Device Management Framework,
which requires all handsets active on Togolese networks to carry a valid,
non-duplicated IMEI.

Togo occupies a strategic position as a **major West African trade hub**
through the port of Lomé, one of the busiest container ports in the
region. This means a high volume of imported phones — including many
second-hand devices from Europe and the US — pass through Lomé, making
IMEI verification especially important for Togolese buyers.

## The IMEI Landscape in Togo

| Challenge | Detail |
| --- | --- |
| **Counterfeit handsets** | Chinese clones of Samsung, Tecno, Infinix with duplicated IMEIs are common in the Lomé market |
| **Stolen imports** | Phones stolen in France, Belgium, and Germany regularly appear at the Marché des Telecoms in Lomé |
| **Grey-market devices** | Phones imported informally from Benin or Ghana may bypass ARCEP type-approval |
| **No warranty coverage** | Informal imports have no manufacturer warranty in Togo |

## How to Verify a Phone's IMEI in Togo

### Step 1: Display the IMEI

Ask the seller to dial **`*#06#`** on the phone. The 15-digit IMEI
appears immediately. It should also match:
- The label on the **original box** (if available).
- The sticker inside the **SIM card tray**.

If any of these three differ, the phone has either been repaired with
a replacement motherboard (a red flag) or is a counterfeit clone.

### Step 2: Run a Free IMEI Identity Check

Use the [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
to confirm:
- **Brand and model** — does the check return "Samsung Galaxy A54"
  when the seller is selling a "Samsung Galaxy A54"? If it returns
  a completely different model, you have a cloned device.
- **IMEI validity** — confirms the Luhn checksum and format are correct.

This check is instant and free.

### Step 3: Run a Worldwide Blacklist Verification

Because so many phones in Togo originate from European markets, a
**worldwide blacklist check** is the single most valuable tool for
Togolese buyers:

[WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)

This check queries the GSMA international blacklist and databases from
France, Belgium, Germany, UK, and the US — the primary sources of
second-hand imports reaching Togo via the port of Lomé.

A phone stolen in Paris can be sold in Lomé the following month. The
worldwide blacklist check is the only reliable way to catch this.

## ARCEP Togo Type Approval

For a phone to be legally sold by a retailer in Togo, it must receive
**ARCEP type approval** — a technical certification that the device
meets Togo's radio frequency and safety standards. You can verify
whether a model is approved by checking the ARCEP website at
**arcep.tg** or calling the ARCEP consumer line.

Buying a phone without type approval is not illegal for consumers, but
it means the device may be blocked if ARCEP runs a non-compliant IMEI
sweep — and you will have no legal protection as a buyer.

## Buying at the Lomé Phone Market: What to Expect

Lomé's **Marché des Telecoms** (near the Grand Marché) is the main
hub for used and new phones. Hundreds of stalls sell devices ranging
from basic feature phones to the latest iPhones. Here is how to
approach buying safely:

1. **Negotiate on price first** — get the best offer before revealing
   your intention to run a check.
2. **Ask for a 5-minute test period** — all legitimate sellers agree.
3. **Dial `*#06#`** and note the IMEI on your own paper or phone.
4. **Run the free model check** on your own phone's browser — 30 seconds.
5. **Run the blacklist check** — worth the small fee for any phone
   above CFA 50,000.
6. **Insist on a handwritten receipt** — include the seller's name,
   stall number, IMEI, model, and price.

## What to Do If Your Phone Is Stolen in Togo

If your phone is stolen in Togo:

1. Contact your operator — Moov Africa Togo (**888**) or Atlantique
   Télécom (**155**) — to request SIM suspension and IMEI blocking.
2. File a complaint with the **Direction de la Police Judiciaire** in
   Lomé or the nearest police commissariat.
3. Note the **crime reference number** from the police report.
4. Email **arcep@arcep.tg** with your IMEI, crime reference, and
   operator name to request escalation to a national block.

Acting within **24 hours** significantly increases the chance of
the device being located or blocked before it changes hands.
MD,
    ],
    [
        'slug'    => 'senegal-imei-check-imported-phone-guide',
        'title'   => 'Senegal IMEI Check: Verify Your Imported Phone in 2025',
        'excerpt' => 'Senegal\'s ARTP regulates all mobile devices. Learn how to check your IMEI, spot stolen imports, and comply with local telecoms rules.',
        'date'    => '2026-06-06',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Telecoms Regulation in Senegal: The ARTP

The **Autorité de Régulation des Télécommunications et des Postes
(ARTP)** is Senegal's national telecoms regulator, established under
Law 2011-01. The ARTP oversees all mobile network operators in Senegal,
including **Orange Sénégal** (the market leader), **Free Sénégal**
(Expresso), and **Wave Mobile** (which operates as an MVNO). The ARTP
maintains a national IMEI policy aligned with GSMA standards.

Dakar is a major West African economic centre, and Senegal's port of
Dakar handles significant volumes of imported electronics — including
a large informal flow of second-hand phones from France, Belgium, and
other Francophone markets. This makes IMEI verification particularly
important for Senegalese consumers.

## Why IMEI Checks Matter in Senegal

Three practical risks drive the need for IMEI verification in Senegal:

- **Stolen French imports:** France's national blacklist is not
  automatically mirrored to Senegal's national DMS in real time.
  A phone stolen in Bordeaux or Lyon can work normally in Dakar —
  until the ARTP receives the blacklist data update and pushes it
  to Senegalese operators.
- **Counterfeit devices:** Low-cost clones of popular Tecno, Infinix,
  and Samsung models with duplicated IMEIs are sold at the **Marché
  Sandaga** and **Marché HLM** in Dakar.
- **Unregistered imports:** Phones imported through informal channels
  may not be registered in the ARTP's type-approval database and can
  face blocking during compliance sweeps.

## How to Check Your IMEI in Senegal

### Step 1: Retrieve the IMEI

On the phone you wish to verify, dial **`*#06#`**. The 15-digit IMEI
will appear on screen. Compare it to:
- The label on the original box.
- The sticker inside or near the SIM card tray.

All three must match. A mismatch is a strong indicator of a clone or
a repaired device with a replaced motherboard.

### Step 2: Free IMEI Model Check

Use the [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
to verify the brand and model registered to this IMEI. If a seller is
offering an "iPhone 14" and the IMEI check returns "Samsung Galaxy A15",
the device is counterfeit or the IMEI has been cloned.

### Step 3: International Blacklist Check

Because most imported phones in Senegal originate from French-speaking
European markets, an **international blacklist check** is essential:

[WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)

This query covers the GSMA database, French blacklist data, Belgian and
Swiss IMEI records, and reports from the US and UK — giving you
comprehensive coverage against stolen imports.

### Step 4: ARTP Type Approval Verification

For phones sold by retailers, you can verify type approval via the
ARTP's website at **artp.sn**. Look up the model in the approved
devices registry. An unapproved device may be blocked during the
ARTP's periodic network compliance audits.

## ARTP Regulations on Imported Phones

Under Senegalese telecom law, all devices sold commercially must
receive **ARTP type approval** before reaching consumers. For personal
imports (a phone you bring back from France, for example), the device
is generally permitted for personal use without formal registration.
However, any phone reported stolen in any GSMA member country is
subject to blocking if it appears on a blacklist update.

**Customs duty** on imported phones in Senegal falls under the **ECOWAS
Common External Tariff**. Mobile phones attract a standard **10% duty**
plus **18% VAT** if imported commercially. Personal-use devices brought
by travellers benefit from an exemption threshold of approximately
**CFA 250,000** (~$400 USD).

## Buying a Used Phone at Sandaga Market, Dakar

Marché Sandaga is one of West Africa's largest informal electronics
markets. Here is how to buy safely:

| Step | Action | Time |
| --- | --- | --- |
| 1 | Dial `*#06#` and write down the IMEI | 30 seconds |
| 2 | Match IMEI to box label and SIM tray | 1 minute |
| 3 | Run free model check on imeihub.net | 30 seconds |
| 4 | Run worldwide blacklist check | 1 minute |
| 5 | Ask for a handwritten receipt with IMEI | 2 minutes |

Total time: under 5 minutes. If the seller refuses any of these steps,
that is your signal to walk away.

## How to Report a Stolen Phone in Senegal

1. Note your IMEI from the box or your Google/Apple account.
2. File a complaint at the nearest **commissariat de police** or
   **brigade de gendarmerie**. Request a **récépissé de plainte**
   (complaint receipt).
3. Call your operator — Orange Sénégal: **888**; Free Sénégal: **333** —
   and provide the IMEI and complaint reference to request blocking.
4. Contact the ARTP consumer services at **artp.sn** to report the
   theft for escalation to a national block.

Early reporting (within 24 hours) is critical — once a phone is
re-sold through the Sandaga market, tracing it becomes very difficult.
MD,
    ],
    [
        'slug'    => 'how-to-register-imei-kenya-step-by-step',
        'title'   => 'How to Register Your IMEI in Kenya: Step-by-Step Guide',
        'excerpt' => 'Step-by-step tutorial for Kenyan residents on how to register a phone\'s IMEI with the Communications Authority DMS and KRA system.',
        'date'    => '2026-06-07',
        'tag'     => 'Guide',
        'body'    => <<<MD
## Why Kenyan Residents Need to Register Their IMEI

Kenya's **Communications Authority (CA)** operates the national
**Device Management System (DMS)** — a real-time database of every
IMEI active on Kenyan networks (Safaricom, Airtel Kenya, Telkom Kenya,
and Equitel). Since January 2025, this system has been linked directly
to the **Kenya Revenue Authority (KRA)** customs database, meaning any
unregistered phone brought in through an airport or border post can be
blocked from Kenyan networks within 60 days of first use.

This guide is for **Kenyan residents and returning nationals** who want
to register a phone that was:
- Purchased abroad (during travel or from an online retailer).
- Received as a gift from abroad.
- Bought locally from an informal seller whose device was not pre-registered.

If you brought a phone through JKIA and declared it on the **KRA F88
form**, your IMEI should be provisionally registered already. This
guide covers the follow-up formal registration and how to verify it.

## Before You Register: Check Your IMEI Status

### Step 1 — Find Your IMEI

Dial **`*#06#`** on your phone. Write down the 15-digit number. For
dual-SIM phones, note both IMEI 1 and IMEI 2.

### Step 2 — Verify the IMEI is Valid

Use [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
to confirm:
- The brand and model match your physical device.
- The IMEI passes the Luhn checksum (rules out typos).

### Step 3 — Run a Blacklist Check

Before registering, confirm the device is clean:

[WorldWide Blacklist Check](https://imeihub.net/service.php?slug=blacklist)

Registering a device that is internationally blacklisted creates legal
complications — the registration will likely be rejected, and your
details will be forwarded to the CA for investigation.

## Method 1: Register via the CA's Online Portal

The Communications Authority of Kenya has an online registration
portal at **ca.go.ke**.

1. **Go to** ca.go.ke then "Consumer" then "Device Registration".
2. **Create an account** using your Kenyan national ID number or passport.
   You will receive a verification code via SMS to the number linked
   to your ID.
3. **Log in** and click "Register New Device".
4. **Enter IMEI 1** (and IMEI 2 for dual-SIM). The system will
   auto-populate the manufacturer and model.
5. **Enter purchase details:** date of purchase, country of purchase,
   and purchase price in USD or KES.
6. **Upload proof of purchase** if requested (JPEG, PDF, under 5 MB).
   A bank statement showing the purchase, a receipt, or a screenshot
   of the online order is acceptable.
7. **Submit** and note the reference number. Processing takes
   **1–3 working days**.
8. You will receive an SMS from the CA confirming registration.

## Method 2: Register via a Safaricom / Airtel / Telkom Service Centre

If you prefer in-person registration or have difficulty with the
online portal, all major network operators can register your device
on your behalf at any of their **retail stores**.

**What to bring:**
- Your original Kenyan national ID or passport.
- The phone itself (turned on).
- Proof of purchase (if available).

The process is free and takes about 15 minutes at the counter.

## Method 3: Register via the KRA iTax Portal (Post-Airport Declaration)

If you declared the phone at the airport (F88 form) but did not receive
a CA registration SMS within 7 days, complete formal registration through
the **KRA iTax portal**:

1. Go to **itax.kra.go.ke** and log in with your KRA PIN.
2. Navigate to "Customs" then "IMEI Declaration".
3. Enter the reference number from your F88 form.
4. Confirm the IMEI details and submit.

The iTax entry automatically triggers a CA DMS registration. Allow
2–5 working days.

## How Long Until the Phone Is Fully Registered?

| Registration Path | Processing Time |
| --- | --- |
| CA online portal | 1–3 working days |
| Network operator store | Same day (instant) |
| KRA iTax (post-F88) | 2–5 working days |
| Airport declaration (F88) | Provisional: immediate; Formal: 3–7 days |

## How to Confirm Your Phone Is Registered

Once you have submitted your registration, confirm the status:

- **SMS check:** Dial **`*#197#`** and select "Device Status". A reply
  will confirm "Registered" or "Pending".
- **Online:** Log into your CA portal account and check under
  "My Devices".
- **Third-party check:** Run a [Free IMEI Check](https://imeihub.net/service.php?slug=free-imei-check)
  and compare the result with what you registered.

## Registration Fees

IMEI registration itself is **free** for Kenyan residents registering
personal devices. However, if you did not declare the device at the
border and it is liable for import duty, you will need to pay the
applicable **KRA import duty** before the CA will finalise registration.

Duty rates (2025):
- Phones valued under **KES 75,000** brought in by a traveller: **0%** (personal use exemption).
- Phones above KES 75,000: **25% import duty + 16% VAT**.
- Commercial imports: **25% import duty + 16% VAT** on all devices.

## Frequently Asked Questions

**My phone is already on Safaricom — does it need registration?**
If your phone was purchased from a Safaricom shop or any authorised
Kenyan retailer, it was pre-registered by the importer. Confirm by
dialling `*#197#`.

**What if my registration is rejected?**
Rejection typically means the IMEI is on the GSMA blacklist or
duplicated in the CA's database. Contact the CA at **0800 221 221**
(toll-free) for a review. Provide your proof of purchase.

**Can I register a phone for a family member?**
Yes. Each national ID can register up to **3 devices per year**. For
a fourth device, you must visit a CA office with documentation for all
devices.

**What if I sell my phone — does registration transfer?**
No. The new owner must register the device under their own ID. The
CA's system links each IMEI to one registered owner. Failure to
re-register after a sale means the buyer could face a block if you
later report the device lost.
MD,
    ],
];
