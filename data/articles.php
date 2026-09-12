<?php
declare(strict_types=1);

/**
 * Lightweight article catalog. Each entry has Markdown-like body that
 * we render with a tiny in-house formatter (paragraphs + lists + h2/h3).
 */

return [
    [
        'slug' => 'icloud-imei-check-on-off-vs-clean-lost',
        'title' => 'iCloud IMEI Check: ON/OFF vs. Clean/Lost Results',
        'meta_title' => 'iCloud IMEI Check: ON/OFF vs. Clean/Lost',
        'excerpt' => 'An iCloud IMEI check can show ON/OFF or Clean/Lost. Learn what each report answers, what it cannot prove, and which check fits your next decision.',
        'date' => '2026-09-12',
        'tag' => 'Apple',
        'body' => <<<'ICLOUD_STATUS_COMPARISON'
An **iCloud IMEI check** may offer an ON/OFF result or a Clean/Lost result, but those labels do not answer exactly the same question. If you are checking a used iPhone or iPad, choosing the wrong report can leave the issue you care about unresolved. This guide explains what each result is designed to tell you, what it cannot prove, and which checks still need to happen on the device.

## Key Takeaways

- An **ON/OFF report** is intended to indicate whether Find My is enabled for the supplied identifier.
- A **Clean/Lost report** is intended to return the service's Clean or Lost status for that identifier.
- Find My and Activation Lock are connected, while Lost Mode is a separate action an owner can take.
- Neither report removes Activation Lock, unlocks a carrier restriction, proves ownership, or replaces a hands-on setup check.
- imeihub's basic brand-and-model lookup is free; its iCloud status reports are paid services with separate scopes.

## Why iCloud IMEI Check Results Use Different Labels

Online checkers package different data points into different products. The name of a report therefore matters as much as the result itself. An iCloud IMEI checker that returns ON/OFF is answering a narrower status question than one sold specifically as a Clean/Lost report.

Apple's terminology provides the essential context. [Apple explains that Activation Lock turns on automatically when Find My is enabled](https://support.apple.com/en-us/108794). The feature is designed to stop another person from using an iPhone or iPad without the owner's Apple Account credentials. Lost Mode, by contrast, is an action the owner can use to lock a missing device and display contact information.

Third-party labels such as Clean/Lost should be interpreted according to the exact service description. They are not a substitute for Apple's on-device messages during setup.

## ON/OFF vs. Clean/Lost: What Is the Difference?

| Report or check | Main question it answers | What it does not establish |
|---|---|---|
| Free basic IMEI lookup | Does the IMEI map to the expected brand and model? | iCloud status, ownership, blacklist status, warranty or carrier unlock |
| iCloud ON/OFF report | Is Find My reported as ON or OFF for this identifier? | Whether the seller owns the device or whether all other risks are clear |
| iCloud Clean/Lost report | Does the selected service return Clean or Lost? | A complete ownership history, carrier status or a permanent future guarantee |
| Hands-on setup check | Can setup proceed without the previous owner's Apple Account? | Network blacklist, warranty and physical condition |

### What an ON Result Means

An ON result indicates that Find My is reported as enabled. Because Apple ties Find My to Activation Lock, treat this as a sign that the device may still be linked to an Apple Account. A buyer should not assume that an erase alone will make the device ready for a new owner.

The seller should remove the device from their account through Apple's supported process. You should then verify the actual device again rather than relying on a screenshot of an earlier report.

### What an OFF Result Means

An OFF result indicates that Find My is reported as disabled for the submitted identifier. That is relevant to Activation Lock, but it is not a universal “safe to buy” result.

An OFF result does not prove that:

- the identifier belongs to the device in front of you;
- the seller is the lawful owner;
- the IMEI is not blocked by a carrier or network database;
- the phone is carrier-unlocked;
- the warranty, battery and hardware condition are acceptable.

Compare the identifier in the report with the one shown in **Settings → General → About**. Apple's [identifier guide](https://support.apple.com/en-us/108037) explains where to find an iPhone or iPad IMEI and serial number. Avoid publishing the full number in a public listing or forum.

### What Clean and Lost Mean in This Report Type

The imeihub [iCloud Activation Lock (Clean / Lost) service](https://imeihub.net/service.php?slug=apple-icloud) is described as returning a Clean or Lost status. Use those terms only within that report's stated scope.

A Clean result should not be expanded into claims the report did not make. It does not automatically mean the device has a clean network blacklist status, an active warranty, valid proof of purchase, or no carrier lock. Those are separate questions.

A Lost result is a serious reason to pause. Do not try to bypass a lock or proceed on the promise that it can be removed later. Ask the seller to resolve the account status through Apple and demonstrate successful setup before money changes hands.

## Which iCloud Report Should You Choose?

Start with the decision you need to make, not the most detailed-looking product name.

### Choose ON/OFF for a Focused Find My Question

Use the [imeihub iCloud ON/OFF check](https://imeihub.net/service.php?slug=apple-icloud-status) when your immediate question is whether Find My is reported as enabled or disabled. This is the more focused option when you do not need a Clean/Lost label.

### Choose Clean/Lost for That Specific Status Label

Choose the Clean/Lost report when you specifically need the service's Clean or Lost result. Read the service description before ordering so you know what the output includes. Do not infer blacklist, warranty or SIM-lock information unless the selected report explicitly provides it.

### Start Free When You Only Need Device Identification

If you first need to confirm the device model, use imeihub's [free basic IMEI lookup](https://imeihub.net/). Brand-and-model identification can catch a mistyped or mismatched identifier, but it does not include the paid iCloud status reports.

Review the current service description and displayed price before purchasing any report. A paid report supplies information; it does not remove Activation Lock or change the device's account status.

## How to Use an iCloud Check Online Before Buying

### Verify the Identifier First

- Ask the seller to open **Settings → General → About** on the actual device.
- Copy the IMEI carefully and confirm whether the report accepts IMEI, serial number, or both.
- Compare the returned brand and model with the device being offered.
- Resolve any mismatch before ordering another check or paying the seller.

### Match the Report to One Question

Write down the decision you are trying to make. If the question is “Is Find My reported as enabled?”, choose ON/OFF. If it is “Does this service report Clean or Lost?”, choose the Clean/Lost report. Use separate checks for blacklist, warranty or carrier-lock questions.

### Confirm the Result on the Device

Ask the seller to erase the device and begin setup while you are present. Apple advises a buyer not to take ownership of a used iPhone or iPad if setup asks for the previous owner's Apple Account. The device is ready for a new owner only after the former account has been removed and setup can proceed normally. Apple's [used iPhone buying guide](https://support.apple.com/en-us/104999) includes this and other physical inspection steps.

Do not accept a report as a substitute for this test. Reports can help you decide what to investigate; the setup screen shows whether the actual device still demands the previous owner's credentials at that moment.

## Common Interpretation Mistakes

### Mistaking iCloud Status for Carrier Lock

Activation Lock protects the Apple device account relationship. A carrier lock restricts which mobile network the phone can use. They are independent. An iCloud result does not unlock a SIM restriction, and a carrier-unlocked iPhone can still be protected by Activation Lock.

### Treating Clean as Proof of Ownership

No status label replaces proof of purchase, the seller's identity, or a documented transfer. A report reflects the data and scope of that service; it cannot reconstruct every event in the device's history.

### Checking a Screenshot Instead of the Phone

A screenshot may refer to another identifier or an earlier time. Enter the identifier from the actual device, confirm the model, and complete setup before the transaction.

### Assuming a Report Can Remove a Lock

An iCloud Activation Lock check is informational. Removing the account association requires the owner to use Apple's supported process and may require their Apple Account password or proof of purchase. imeihub does not claim that a lookup removes Activation Lock or a carrier lock.

## Frequently Asked Questions

### Is There a Free iCloud IMEI Checker on imeihub?

imeihub offers a free basic lookup for brand and model information. Its iCloud ON/OFF and Clean/Lost checks are separate paid reports. A search phrase containing “free” should not be taken as a promise that detailed iCloud status is included at no cost.

### Is Find My OFF the Same as Carrier Unlocked?

No. Find My status relates to Apple's account-protection features. Carrier-lock status relates to mobile-network restrictions. Check each separately.

### Can a Clean Result Guarantee the Device Will Stay Clear?

No. Treat it as the result returned for the identifier and report scope at the time of the check. It is not a permanent guarantee about future reports, ownership claims, blacklist changes or hardware condition.

### Can an iCloud IMEI Check Remove Activation Lock?

No. A lookup only reports information. It does not remove an account, bypass Apple's security, or unlock a carrier restriction.

## Choose the Check That Answers Your Real Question

An iCloud IMEI check is most useful when you understand the label before ordering. Use ON/OFF for a focused Find My status question, Clean/Lost when that specific result is required, and the free basic lookup when you only need brand-and-model identification. Then confirm the seller has removed their account by testing setup on the actual device.

Ready to check an identifier? Start with the [free imeihub IMEI lookup](https://imeihub.net/), or review the [ON/OFF](https://imeihub.net/service.php?slug=apple-icloud-status) and [Clean/Lost](https://imeihub.net/service.php?slug=apple-icloud) report descriptions to choose the service that matches your next decision.
ICLOUD_STATUS_COMPARISON,
    ],

    [
        'slug' => 't-mobile-imei-check-compatibility-vs-carrier-lock',
        'title' => 'T-Mobile IMEI Check: Compatible Does Not Mean Unlocked',
        'meta_title' => 'T-Mobile IMEI Check: Compatibility vs. Carrier Lock',
        'excerpt' => 'Run a T-Mobile IMEI check and understand compatibility, carrier locks, and eSIM results before switching. Know when a separate report helps.',
        'date' => '2026-09-11',
        'tag' => 'Guide',
        'body' => <<<'TMOBILE_COMPATIBILITY_GUIDE'
A **T-Mobile IMEI check** can help you assess a phone before switching networks, but a compatibility result is not a substitute for checking whether the device is carrier-locked. If one screen says the phone is compatible while another shows a restriction, you may be looking at answers to two different questions.

This guide focuses on bringing an existing phone to T-Mobile in the United States: where to check, how to interpret the information, and when a separate status report is relevant.

## Key Takeaways

- Use T-Mobile's own checker for its network-compatibility assessment.
- Read compatibility, blocking, and SIM or eSIM information separately.
- For an iPhone, carrier-lock status can also be checked directly in Settings.
- A paid SIM-lock report does not activate a phone or change its lock status.

## Start with the Official T-Mobile IMEI Check

Open [T-Mobile's bring-your-own-phone checker](https://www.t-mobile.com/resources/bring-your-own-phone) and enter the IMEI from your device. The page provides compatibility information and can display separate blocking and SIM-related results. Read the complete response, not just its headline.

For an iPhone, open **Settings → General → About**, locate the IMEI, and touch and hold it to copy. Apple lists other places to find identifiers if Settings is unavailable in its [device-identifier guide](https://support.apple.com/en-us/108037).

Before submitting, compare the field label with the identifier you copied. An IMEI, serial number, and EID are not interchangeable. Keep the labels if you save information for a support conversation.

### If You Are Checking a Phone Remotely

Ask for the identifier from the actual device, and compare it again when you receive the phone. An accurate lookup against the wrong handset's number does not answer your question.

Keep a private record of the check date and response. Do not publish the full IMEI in a public listing or comment just to ask whether the result looks right.

## How to Read Compatibility and SIM Results

Use this as a reading aid, not a claim that every result will show every field:

| Information shown | What to investigate next |
| --- | --- |
| Network compatibility | Whether the stated level meets your intended use |
| Device blocking | Any carrier restriction described in that result |
| SIM or eSIM support | The activation route offered for the device |
| Missing or uncertain information | The specific field the checker could not establish |

T-Mobile's checker distinguishes network compatibility from other device information, including blocking and eSIM support. If it cannot establish eSIM compatibility, it may request an EID. Follow the instructions attached to your result rather than entering another identifier into the IMEI field.

### Partial Compatibility Needs a Practical Decision

T-Mobile explains that a partially compatible device may lack some supported network technologies or frequencies, affecting coverage or data speeds. Its [BYOD support guide](https://www.t-mobile.com/support/devices/not-sold-by-t-mobile/bring-your-own-device-byod-guide) also notes limits to what it can guarantee for devices obtained elsewhere.

Write down what matters to you before deciding: reliable service at work, coverage at home, or a particular calling feature. Then ask about those requirements explicitly. A broad compatibility label is less useful than an answer about the way you plan to use the phone.

## Why an Unlocked iPhone Still Needs a Network Check

On an iPhone, **Settings → General → About → Carrier Lock** shows the carrier-lock information. Apple says **“No SIM restrictions”** means the iPhone is unlocked. See [Apple's carrier-unlock guidance](https://support.apple.com/en-us/109316).

That answers the carrier-lock question. It does not replace the intended network's assessment of the device. Conversely, do not infer an unlocked state simply from a compatible model.

For a fuller explanation of this distinction, see [imeihub's carrier-lock guide](https://imeihub.net/article/apple-carrier-lock-check-understanding-your-iphones-sim-lock-status).

### If the Phone Is Locked to Another Carrier

Ask that carrier about the required unlock process. Apple states that it cannot unlock an iPhone for another network; the carrier handles the request. Avoid treating a third-party lookup receipt as confirmation that an unlock has occurred.

Keep the sequence clear in your records: status checked, carrier contacted, response received, and activation attempted. This makes it easier to identify which step still needs attention.

## When Is a Paid Report Useful?

Start with the question you cannot yet answer. If the only question is whether T-Mobile accepts the device, begin with T-Mobile's checker. Buying an unrelated report is not a substitute.

If you need an IMEI-based iPhone SIM-lock status report, imeihub lists a separate [paid Apple SIM-Lock Status check](https://imeihub.net/service.php?slug=apple-sim-lock). Its description says it returns SIM-lock status only. Do not assume it also identifies the original carrier, determines unlock eligibility, or confirms T-Mobile activation.

For example, someone evaluating a phone remotely may want a status report before arranging a handover. That is a possible workflow, not proof that a paid check is necessary for every purchase. If the phone is already available and Settings answers the question, avoid ordering a redundant report.

### Free Identification Is a Different Service

imeihub's basic brand and model lookup is free. Its detailed reports are separate paid services; free identification should not be read as a free blacklist, iCloud, or warranty report.

Before ordering, check the supported identifier, report scope, and current price. If any part is unclear, clarify it first. A lookup does not remove Activation Lock or a carrier lock.

## What to Do When the Results Seem to Conflict

Keep the actual responses side by side and ask:

- Were both checks performed on the same device identifier?
- Did one answer a compatibility question and the other a lock-status question?
- Was a field unavailable, rather than reporting a definite status?
- Which organization can explain or change the specific restriction?

Send the relevant support team its own result reference and exact wording. Describe the outcome you need, such as activating the phone on T-Mobile, rather than asking only whether the phone is “clear.”

## Choose the Check That Answers Your Next Question

A T-Mobile IMEI check is a useful starting point for a network move. Match the identifier to the phone, separate compatibility from carrier-lock status, and resolve the specific unanswered field before proceeding.

If you only need to identify the device, start with [imeihub's free IMEI lookup](https://imeihub.net/). If SIM-lock status is the remaining question, [review the paid SIM-lock report and its scope](https://imeihub.net/service.php?slug=apple-sim-lock) before ordering.
TMOBILE_COMPATIBILITY_GUIDE,
    ],
    [
        'slug' => 'iphone-blacklist-check-clean-status-change',
        'title' => 'iPhone Blacklist Check: Why a Clean Result Can Change',
        'meta_title' => 'iPhone Blacklist Check: Can Clean Status Change?',
        'excerpt' => 'An iPhone blacklist check is a snapshot. Learn why a clean result can change, when to recheck, and what to do if two IMEI reports disagree.',
        'date' => '2026-09-10',
        'tag' => 'Apple',
        'body' => <<<'IPHONE_BLACKLIST_STATUS_CHANGE'
An **iPhone blacklist check** that looks clear today can return a different answer later. If a seller sends you a clean report before shipping a phone, the important question is not just what the result says: it is which IMEI was checked, when it was checked, and what records the service examined.

A report helps you make a decision at a particular moment. Understanding that timing makes it more useful, especially when several days separate the listing, payment, and delivery.

## Key Takeaways

- A clean result describes the records checked at that time; it is not a permanent certificate.
- Later reporting or data updates can change what a subsequent lookup returns.
- Compare the identifier, check date, and service scope before deciding that two reports conflict.
- A paid blacklist report answers a different question from free brand and model identification. Neither prevents a future block.

## What Does a Clean IMEI Result Actually Mean?

Read the result using the provider's definition. For example, CTIA's Stolen Phone Checker explains that its green result means the device was not flagged on the GSMA Block List when the query was made. That is a statement about recorded status at the time of the search. [See CTIA's result definitions](https://stolenphonechecker.org/spc/faqs.jsp).

Keep three details together whenever you save a report: **the exact identifier, the check time, and the type of check**. A screenshot that crops out those details is difficult to evaluate, even if it displays a reassuring status.

The word “clean” also needs context. Do not assume that every website uses it to describe the same records or includes the same checks.

## Why Can a Clean iPhone Become Blacklisted Later?

### A Report Can Be Made After Your Check

A loss or theft may be reported after you run the lookup. An earlier result cannot reflect a report that has not yet been submitted.

### Reporting and Database Updates Are Not Simultaneous

GSMA explains that uploading a reported status can take up to 24 hours and sometimes longer. That is not a guaranteed waiting period after which a phone becomes safe to buy. Its terms explicitly acknowledge that status can change after a check. [Read GSMA's explanation of reporting and update timing](https://devicecheck.gsma.com/rtlapp/termsOfUse).

### A Carrier Restriction May Be a Different Issue

GSMA also notes that a carrier can restrict a device for billing reasons without that device appearing on its Block List. Ask the carrier about the specific restriction; do not treat a lost/stolen lookup as proof that all account obligations are settled.

## How to Compare Two IMEI Blacklist Check Results

If one report appears clear and another indicates a problem, compare them before buying more checks or accusing either provider of being wrong.

| Detail to compare | Question to ask | Useful next step |
| --- | --- | --- |
| Identifier | Were both reports run against the same IMEI? | Compare every digit with the actual iPhone. |
| Check time | Were the reports generated at different times? | Keep both dates and ask about the sequence of results. |
| Service type | Was one only a model lookup or SIM-lock check? | Compare reports answering the same question. |
| Coverage | Do the providers describe the same records? | Read each service description or request clarification. |
| Result wording | Is there an actual status, or only an unavailable result? | Do not translate missing data into “clean.” |

On an iPhone, find the IMEI under **Settings → General → About**. Apple explains how to locate and copy identifiers in its [iPhone identification guide](https://support.apple.com/en-us/108037). Match the phone you receive to the identifier used in the report, rather than relying only on the seller's message.

If two services checked the same identifier close together and still disagree, send each provider its own report reference and ask what the result covers. Avoid posting complete device identifiers or account information in public comments.

## When Is It Sensible to Recheck?

Rechecking is most useful when it supports a new decision, not simply because a timer has expired. Consider these points in a transaction:

- **Before committing:** Review a relevant result alongside the seller's description and terms.
- **At handover or delivery:** Match the actual phone's identifier and resolve any difference from the earlier report.
- **Before passing on stored inventory:** Consider whether the old result still suits the decision you are making now.
- **After a confirmed status correction:** Follow the carrier's guidance about when to verify the change.

For a hypothetical example, a shop might review a report when discussing a purchase on Monday, receive the device on Friday, and compare the current information before accepting it into stock. Those are separate decision points. The Monday screenshot should remain part of the record, but it should not silently stand in for a Friday check.

There is no universal report age that guarantees a trouble-free purchase. Choose a checking process proportionate to the transaction, and keep the receipt and agreed return terms with the report.

## What If Your iPhone Is Now Blacklisted?

### Confirm the Result and Contact the Right Party

First confirm the identifier and the actual status being reported. If you believe a GSMA Block List entry is incorrect, GSMA directs consumers to their wireless provider, which can investigate the account and device activity. [See GSMA's guidance for disputed status](https://devicecheck.gsma.com/rtlapp/faqs/).

Prepare a concise record for the conversation:

- The IMEI from the phone and the relevant report reference.
- The dates of the earlier and later checks.
- Your purchase documentation and the seller's description.
- The carrier's explanation or case reference, if one already exists.

If you bought the device recently, also contact the seller or marketplace through its documented support process. Ask what the available next steps are under its terms; do not assume a lookup service can resolve the underlying ownership or account issue.

### Keep Blacklist, Carrier Lock, and Activation Lock Separate

A blacklist result is not a SIM-lock result. Apple explains that the Carrier Lock setting addresses whether an iPhone is unlocked for use with another carrier. [Read Apple's carrier-lock guidance](https://support.apple.com/en-us/109316).

Activation Lock is another separate issue involving the previous owner's Apple Account. Check that during device setup using [Apple's Activation Lock guidance](https://support.apple.com/en-us/108794). Ordering a lookup does not remove a blacklist entry, a carrier lock, or Activation Lock.

## Free Identification or a Paid Blacklist Report?

On imeihub, **basic brand and model lookup is free**. Use the [free IMEI checker](https://imeihub.net/) when identifying the device is your immediate question.

The [WorldWide Blacklist service](https://imeihub.net/service.php?slug=blacklist) is listed as a paid check. Before ordering, review its current description and price, and ask about coverage if that determines whether the report is useful to you. Do not assume the free lookup includes blacklist, iCloud, or warranty results.

A paid report can be relevant when your remaining question concerns the blacklist records it checks. The reason to select it is that fit, not a promise that paying makes the result permanent or prevents future restrictions.

## Use the Result for the Decision in Front of You

An iPhone blacklist check is most useful when you can connect it to the correct device, a clear question, and a recorded point in time. Keep earlier reports, investigate inconsistencies, and direct disputed restrictions to the party able to review them.

Need blacklist information for an iPhone you are evaluating now? [Review imeihub's paid blacklist check](https://imeihub.net/service.php?slug=blacklist), confirm its scope and price, and use the result alongside your transaction records.
IPHONE_BLACKLIST_STATUS_CHANGE,
    ],
    [
        'slug'    => 'apple-imei-verification',
        'title'   => 'Apple IMEI Verification: Can It Prove an iPhone Is Genuine?',
        'meta_title' => 'Apple IMEI Verification: What It Can Really Tell You',
        'excerpt' => 'Learn what Apple IMEI verification can confirm, why it cannot prove an iPhone is genuine, and which checks to use for parts, locks, and coverage.',
        'date'    => '2026-09-09',
        'tag'     => 'Apple',
        'body'    => <<<'APPLE_IMEI_VERIFICATION'
Apple IMEI verification is useful for checking whether an iPhone's cellular identifier maps to the expected device family, but it is not a one-step certificate of authenticity. A matching brand and model is helpful evidence; it does not prove that every component is genuine, that the seller owns the phone, or that the device is free from account and carrier restrictions. The safest approach is to match each question with the identifier or on-device check designed to answer it.

## What an iPhone IMEI Check Can Confirm

An IMEI is a cellular-device identifier. According to the [GSMA TAC database](https://imeidb.gsma.com/imei/loginpage), the first eight digits form the Type Allocation Code, or TAC, which identifies a device's make and model. That is why a basic IMEI lookup can return useful identity information before you order a more specialized report.

A basic result can help you check whether:

- The IMEI is in the expected 15-digit format.
- Its TAC points to Apple and the expected iPhone model family.
- The number copied from the phone produces the same basic identity as the device you are examining.

This comparison is most useful when you read the IMEI from the iPhone itself. Apple shows the serial number, EID, IMEI/MEID and ICCID as separate fields in **Settings > General > About**. Touch and hold the IMEI to copy it, following [Apple's identifier guide](https://support.apple.com/en-us/108037). If the phone displays more than one IMEI, keep each complete number separate and check the one relevant to the cellular line or report.

## Why a Matching IMEI Does Not Prove the Whole iPhone Is Genuine

An IMEI lookup reports database information associated with an identifier. It does not physically inspect the logic board, display, battery, cameras, housing or packaging. Therefore, a result that says “Apple” and names the expected model should be treated as an identity match—not as proof that every part of the device is original.

The same limit applies in the other direction. A mismatch deserves investigation, but it does not by itself tell you why the records differ. Possible explanations include an incorrectly copied number, checking an IMEI from a different device or box, or a lookup source that does not have the expected record. Recopy the identifier and compare the on-device details before drawing a conclusion.

### IMEI and serial number answer different questions

Do not substitute one identifier for the other simply because a form accepts text. Apple's current [Check Coverage](https://checkcoverage.apple.com/) service asks for a serial number and is designed to show device coverage and benefits. An Apple coverage result can help with warranty questions, but its stated purpose is not to certify ownership, physical condition or every installed part.

For a coverage question, use Apple's own coverage page as the authoritative starting point. If you need different report fields, review the scope of the relevant service before ordering.

## A Better Apple IMEI Verification Process

Use these checks together. Each step answers a different part of the authenticity question.

### 1. Copy the identifiers from Settings

Open **Settings > General > About** and record:

- IMEI or IMEIs.
- Serial number.
- Model number.
- iOS version.

Avoid relying only on a marketplace screenshot, printed label or loose box. The number on the device is the correct starting point for checking that device.

### 2. Run a basic IMEI model lookup

Use the [free imeihub IMEI lookup](https://imeihub.net/) to compare the returned brand and model with the phone in your hand. imeihub describes this free check as basic brand, model and specification identification. Do not interpret it as a free blacklist, iCloud or warranty report; those are separate premium services in the [imeihub service catalogue](https://imeihub.net/services.php).

Check for consistency rather than hunting for a single “genuine” label:

- Does the result identify Apple?
- Does the model family agree with the phone?
- If there are two IMEIs, did you enter one complete identifier without mixing their digits?
- Did you copy the number from Settings rather than another device's packaging?

### 3. Compare Apple's model number

In **Settings > General > About**, the value beside Model Number may initially be a part number. Tap it to display the model number, then compare that number with [Apple's iPhone model list](https://support.apple.com/en-us/108044). Apple also documents the exact steps for revealing the model number in its [model-number guide](https://support.apple.com/en-us/106343).

This is a direct device-side cross-check. It is more informative than judging an iPhone only by its color, camera layout or seller description.

### 4. Review Parts and Service History

On supported iPhones with iOS 15.2 or later, go to **Settings > General > About**. If the device has been repaired, a Parts and Service History section may appear. Apple's current guide explains labels such as Genuine, Used, Unknown and Finish Repair, with the information available depending on the iPhone model, part and software version. Read [Apple's Parts and Service History guide](https://support.apple.com/en-us/102658) before interpreting a label.

This check answers a parts-and-repair question that an IMEI model lookup cannot answer. An “Unknown” message can have several causes listed by Apple; do not turn it into a broader claim that the entire phone is counterfeit.

### 5. Check Activation Lock on the device

Activation Lock is an account-protection issue, not an IMEI authenticity test. Apple advises buyers to make sure a used iPhone is erased and no longer linked to the previous owner's account. If the setup screen says **iPhone Locked to Owner**, Apple says not to take ownership of it. Follow [Apple's Activation Lock guidance](https://support.apple.com/en-us/108794).

An online lookup does not remove Activation Lock. The previous owner must properly remove the device from their account, or an eligible owner can follow Apple's documented support process.

## What to Do When the Details Do Not Match

Pause the transaction or repair intake until you can explain the inconsistency. Work through the evidence in this order:

- **Step 1.** Recopy the IMEI from Settings and check every digit.
- **Step 2.** Confirm that you used an IMEI, not the serial number, EID or ICCID.
- **Step 3.** Compare the model number with Apple's official model list.
- **Step 4.** Check whether the lookup covers the exact information you expected.
- **Step 5.** Ask the seller for the original purchase documentation when ownership or purchase history matters.
- **Step 6.** If the phone can be erased with the seller present, confirm that it reaches setup without an Activation Lock warning.

Do not keep ordering unrelated reports in the hope that one will “authenticate” the phone. A blacklist report answers a lost-or-stolen status question; an iCloud report answers a Find My or Activation Lock status question; a warranty report answers coverage questions. None changes the device's lock status, and none replaces the on-device checks above.

## Choose the Right imeihub Check

The [imeihub service catalogue](https://imeihub.net/services.php) currently separates its checks by purpose:

| Question | Relevant option | Free or premium |
| --- | --- | --- |
| What brand and model does this IMEI map to? | Free IMEI Check | Free basic lookup |
| What basic Apple model, color and storage data is offered? | Apple Check | Premium |
| Is the device reported lost or stolen in covered carrier records? | WorldWide Blacklist | Premium |
| What Find My or Activation Lock status does the selected service report? | iCloud checks | Premium |
| What activation date and remaining warranty coverage is offered? | Apple Warranty & Activation | Premium |

Review the current service description before submitting an identifier. The catalogue summary does not promise that every record will be available for every device, and a lookup does not unlock a carrier or Apple account restriction.

## Verify the Device, Not Just the Number

Apple IMEI verification works best as one layer of a careful identity check. Use the IMEI to compare basic make-and-model data, the Apple model number to confirm the device family, Parts and Service History to understand supported repair information, and Apple's setup flow to check for Activation Lock. Together, these checks provide a clearer answer than any identifier alone.

Start with the [free imeihub IMEI lookup](https://imeihub.net/) to compare the iPhone's basic identity, then choose a premium report from the [service catalogue](https://imeihub.net/services.php) only if it answers a separate question you still need resolved.
APPLE_IMEI_VERIFICATION,
    ],
    [
        'slug'    => 'samsung-tablet-imei-number',
        'title'   => 'Samsung Tablet IMEI Number: Find It or Know Why It\'s Missing',
        'meta_title' => 'Samsung Tablet IMEI Number: How to Find Yours',
        'excerpt' => 'Find your Samsung tablet IMEI number, learn why Wi-Fi models do not have one, and see what a free online IMEI check can tell you.',
        'date'    => '2026-09-06',
        'tag'     => 'Samsung',
        'body'    => <<<'SAMSUNG_TABLET_IMEI'
Can't find your Samsung tablet IMEI number when a form asks for it? Before you keep searching, check whether your Galaxy Tab is a cellular or Wi-Fi-only model. A Wi-Fi-only tablet does not have an IMEI, so an empty field is not automatically a fault. The [GSMA's device identifier guide](https://devicecheck.gsma.com/rtlapp/faqs/) confirms this distinction.

For a cellular Galaxy tablet, start with **Settings > About tablet**. For a Wi-Fi-only model, you may need the serial number instead—but only if the form accepts it. Here's how to find the right identifier and decide what to do next.

## How to Find the IMEI Number on a Samsung Tablet

### Start in Settings

- **Step 1.** Open **Settings** on the tablet.
- **Step 2.** Select **About tablet**.
- **Step 3.** Look for the entry labelled **IMEI**, rather than the model or serial number.
- **Step 4.** Copy the number carefully, keeping each identifier separate if several are displayed.

Samsung documents this route in its [phone and tablet identification guide](https://www.samsung.com/us/support/answer/ANS10002504/). Menu details can vary with the device and software. The familiar phone shortcut `*#06#` is not a universal tablet method; use Settings first.

### If the tablet will not turn on

Check the original packaging, small print on the back, and any carrier account or purchase paperwork that records the device identifier. These are alternative locations listed by Samsung in the same guide; not every source will include an IMEI.

If you own several tablets, match the paperwork to the correct unit before submitting a number. A box from another device can lead to a perfectly valid lookup for the wrong tablet.

## Why Does My Samsung Tablet Have No IMEI?

### Wi-Fi-only models do not need one

An IMEI is a 15-digit identifier associated with cellular equipment. Devices that rely only on Wi-Fi do not have one, according to the [GSMA's IMEI FAQ](https://devicecheck.gsma.com/rtlapp/faqs/). Connecting a Wi-Fi tablet to a phone's hotspot does not give the tablet its own IMEI.

If a registration or trade-in form requires an IMEI, confirm that you selected the correct Wi-Fi variant. Look for a serial-number option. If the form still refuses to proceed, ask the form's operator how to register that model; do not enter another device's IMEI to get past validation.

### A cellular model needs a different follow-up

If your purchase details identify a cellular tablet but you cannot locate its IMEI, first confirm the exact model in About tablet and compare it with the original order or packaging.

Treat an absent or “unknown” identifier as a question for Samsung support, not a diagnosis you can make from that field alone. Describe the model, where you looked, and the exact message shown. Avoid factory-resetting or modifying the device merely to satisfy an online lookup form.

## Samsung Tab A IMEI: Check the Exact Variant

When searching for a Samsung Tab A IMEI, the family name is not enough to decide which identifier to use. Check your particular model's connectivity specification: Wi-Fi only or cellular.

Keep these three identifiers distinct:

| Identifier | What it identifies | When it helps |
| --- | --- | --- |
| Model number | The product model, shared by units of that model | Finding the correct specifications and support information |
| Serial number, or S/N | An individual unit in the manufacturer's records | Manufacturer support or a form that explicitly requests a serial number |
| IMEI | Cellular equipment | An IMEI lookup or a carrier request for the device's IMEI |

Samsung explains the different roles of these numbers in its [product identification guide](https://www.samsung.com/uk/support/mobile-devices/how-to-find-the-model-and-serial-number-of-my-product/). A serial number is not an alternative format of IMEI, and changing how you type it will not turn it into one.

## What Can an Online IMEI Checker Tell You?

Once you have an IMEI from a cellular tablet, an online lookup can be a starting point for checking its identity. On imeihub, basic brand and model lookup is free; premium reports are separate and require sign-in. See the [imeihub IMEI checker](https://imeihub.net/).

Do not assume that every Galaxy tablet variant is covered. The [Samsung brand page](https://imeihub.net/brand.php?slug=samsung) currently highlights phone models rather than a complete tablet compatibility list. A missing model result is not, by itself, proof that your tablet is defective or counterfeit.

Before interpreting a result:

- Recheck the number against the tablet, not just a seller's message or an old screenshot.
- Make sure you entered an IMEI rather than an S/N or model code.
- Compare any returned model details with the tablet's own information.
- If the result is unavailable or inconsistent, resolve the discrepancy before relying on it.

The [service catalogue](https://imeihub.net/services.php) lists Samsung Info + Knox Guard and blacklist checks as premium services. Review the relevant description and confirm support for your exact tablet before paying. Free basic identification does not include a free warranty, blacklist or lock-status report, and a lookup does not remove an activation or carrier lock.

## Find the Right Number Before Running a Check

The key is to identify your tablet's variant first. Use the IMEI entry on a cellular model; for a Wi-Fi-only device, use its serial number where the receiving service allows it. Keep model, serial and IMEI fields separate so you investigate the right device.

Have an IMEI ready? Start with [imeihub's free basic lookup](https://imeihub.net/) to see whether model information is available for your cellular Samsung tablet.
SAMSUNG_TABLET_IMEI,
    ],
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

];
