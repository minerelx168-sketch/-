# Phase 1 Roadmap — Blue Ocean Capture (Bangladesh / Egypt / Nigeria)

**Window**: Month 1–2 (8 weeks)
**Source**: `seo-analysis/seo_report.md` — Top-3 Blue Ocean ranked countries
**Goal**: Ship 3 country-specific landing pages + 15 localized articles + complete local-payment integration research before competitors localize.

This file is the canonical Phase 1 task list. **Tick checkboxes as work completes** so the SessionStart hook can surface live progress. Each task has an ID, acceptance criteria, and a suggested approach so any session can pick a task and execute without re-planning.

---

## Workstreams

### A. Landing Pages (3 tasks, IDs `LP-*`)

A localized homepage per country, replacing the generic `index.html` for that locale.

#### LP-BD-01 — Bangladesh Bengali homepage (`/bn/`)
- [ ] **Status**: todo
- **Acceptance criteria**:
  - URL: `/bn/index.html` (and `/bn/` redirect)
  - All UI strings translated to Bengali (Bangla)
  - Hero CTA: "আপনার মোবাইল IMEI চেক করুন" with prominent IMEI input
  - Embedded BTRC NEIR explainer (3-step) directly below the form
  - hreflang tags: `bn-BD`, `en-BD`, `x-default`
  - Schema.org `WebPage` + `Service` markup with `inLanguage="bn-BD"`
  - Trust signals: "BTRC NEIR–compatible", carrier logos (Grameenphone, Robi, Banglalink)
  - Footer link "English version" → `/index.html`
- **Suggested approach**: Copy `gh-pages:index.html` as the base, replace all visible strings, swap hero copy to NEIR-focused angle, link the 5 BD articles in a "Read more" rail.
- **Dependencies**: None — can ship before articles.

#### LP-EG-01 — Egypt Arabic homepage (`/ar/eg/`)
- [ ] **Status**: todo
- **Acceptance criteria**:
  - URL: `/ar/eg/index.html`
  - RTL layout (`dir="rtl"` on `<html>`, mirrored nav, right-anchored inputs)
  - All UI strings in Arabic (Egyptian colloquial OK for hero, MSA for technical sections)
  - Hero CTA: "احسب ضريبة جمارك موبايلك" → opens an Egypt 38.5% tax calculator
  - NTRA Telephony app explainer embedded
  - hreflang tags: `ar-EG`, `en-EG`, `x-default`
  - Schema.org `WebPage` + `Service` markup with `inLanguage="ar-EG"`
  - Direct link to https://www.tra.gov.eg/ as trust signal
- **Suggested approach**: Build the calculator first (IMEI → TAC database → device value → ×38.5%) as a separate JS module, then wrap it in the localized page.
- **Dependencies**: Requires existing TAC database (already in `assets/js/main-static.js` on `gh-pages`).

#### LP-NG-01 — Nigeria English homepage (`/ng/`)
- [ ] **Status**: todo
- **Acceptance criteria**:
  - URL: `/ng/index.html`
  - Nigeria-specific copy (carrier names, currency in NGN, local examples)
  - Hero CTA: "Is your phone in NCC-DMS?" → IMEI input
  - NCC-DMS process explainer (4 steps: register → MNO sync → verify → certify)
  - hreflang tags: `en-NG`, `x-default`
  - Schema.org `WebPage` + `Service` markup
  - Trust signals: NCC logo reference, MTN/Glo/Airtel/9mobile carrier rail
- **Suggested approach**: Lowest lift of the three — same English UI, swap copy + add NCC-DMS section. Largest market with brand-new mandate = highest urgency.
- **Dependencies**: None.

---

### B. Articles (15 tasks, IDs `AR-*`)

Each article: **800–1,200 words**, 1 H1 + 4–6 H2s, FAQ schema markup, internal link to the corresponding landing page.

#### Bangladesh (5 articles, Bengali)

##### AR-BD-01 — NEIR কীভাবে নিবন্ধন করবেন: ধাপে ধাপে গাইড ২০২৬
- [ ] **Status**: todo
- **Target keyword**: "NEIR নিবন্ধন" / "neir registration bangladesh"
- **Outline**: NEIR কী → কেন বাধ্যতামূলক → neir.btrc.gov.bd স্টেপ-বাই-স্টেপ → সাধারণ সমস্যা → FAQ
- **Acceptance**: Bengali native (not machine-translated), screenshots of NEIR portal, FAQ schema, internal link to LP-BD-01.

##### AR-BD-02 — বিদেশ থেকে আনা মোবাইল বৈধ করার নিয়ম
- [ ] **Status**: todo
- **Target keyword**: "বিদেশ থেকে আনা মোবাইল" / "foreign phone register bangladesh"
- **Outline**: 30-day SMS rule → প্রয়োজনীয় ডকুমেন্ট → BTRC ফর্ম → ট্যাক্স ক্যালকুলেটর → কাস্টমস টিপস
- **Acceptance**: Detailed walkthrough of post-arrival registration flow with the official 30-day SMS rule referenced.

##### AR-BD-03 — Grameenphone, Robi, Banglalink IMEI চেক
- [ ] **Status**: todo
- **Target keyword**: "Grameenphone IMEI" / "Robi IMEI check" / "Banglalink IMEI"
- **Outline**: ক্যারিয়ার-নির্দিষ্ট কোড → IMEI ফর্ম্যাট → ক্যারিয়ার লক চেক → আনলক প্রসেস
- **Acceptance**: Per-carrier sections with USSD codes + IMEI check links.

##### AR-BD-04 — KYD 16002 SMS দিয়ে মোবাইল বৈধতা যাচাই
- [ ] **Status**: todo
- **Target keyword**: "KYD 16002" / "BTRC SMS check"
- **Outline**: KYD ফরম্যাট → SMS পাঠানোর উপায় → রিপ্লাই পড়া → বৈধ vs অবৈধ → পরের ধাপ
- **Acceptance**: Cover all 3 SMS reply scenarios (valid / under-review / unauthorized) with action steps.

##### AR-BD-05 — মোবাইল ব্লক হলে কী করবেন? BTRC আনব্লক প্রসেস
- [ ] **Status**: todo
- **Target keyword**: "মোবাইল ব্লক" / "btrc unblock phone"
- **Outline**: ব্লকের কারণ → BTRC contact → ডকুমেন্ট চেকলিস্ট → আনব্লক টাইমলাইন → প্রতিরোধ
- **Acceptance**: Lists BTRC contact channels + realistic resolution timeline (currently 5–15 days per BTRC support docs).

#### Egypt (5 articles, Arabic)

##### AR-EG-01 — تسجيل الموبايل في مصر ٢٠٢٦: دليل شامل لتطبيق Telephony
- [ ] **Status**: todo
- **Target keyword**: "تسجيل الموبايل مصر" / "Telephony app egypt"
- **Outline**: تطبيق Telephony شرح → خطوات التسجيل → دفع الضريبة → التحقق → الأسئلة
- **Acceptance**: Native MSA Arabic, screenshots of Telephony app flow, FAQ schema.

##### AR-EG-02 — حاسبة ضريبة الموبايل ٣٨.٥٪ مصر
- [ ] **Status**: todo
- **Target keyword**: "حاسبة ضريبة الموبايل" / "egypt phone tax calculator"
- **Outline**: قاعدة ٣٨.٥٪ → كيف تُحسب → أمثلة لـ iPhone 15 / Samsung S24 → عملة → جدول
- **Acceptance**: Embedded interactive calculator OR clear example table with TAC-based device values.

##### AR-EG-03 — إلغاء الإعفاء الجمركي ٢١ يناير ٢٠٢٦: ماذا يعني للمسافرين
- [ ] **Status**: todo
- **Target keyword**: "إلغاء الإعفاء الجمركي" / "egypt exemption ended"
- **Outline**: ما هو الإعفاء القديم → ما تغيّر في ٢١ يناير → تأثير على المسافرين → خيارات بديلة
- **Acceptance**: Cites NTRA official announcement [seo_report.md ref #4].

##### AR-EG-04 — الموبايل من الخارج: ٩٠ يوم سماح من NTRA
- [ ] **Status**: todo
- **Target keyword**: "موبايل من الخارج" / "ntra 90 day grace"
- **Outline**: قاعدة ٩٠ يوم → متى تبدأ → ماذا يحدث بعدها → كيف تدفع قبل الانتهاء
- **Acceptance**: Day-by-day timeline of what happens to a foreign IMEI after first Egyptian SIM activation.

##### AR-EG-05 — كيفية فك حظر الموبايل في مصر
- [ ] **Status**: todo
- **Target keyword**: "فك حظر الموبايل مصر" / "unblock phone egypt"
- **Outline**: أسباب الحظر → دفع الضريبة المتأخرة → تواصل NTRA → استئناف الخدمة → نصائح
- **Acceptance**: Lists all official NTRA contact channels + realistic resolution timeline.

#### Nigeria (5 articles, English)

##### AR-NG-01 — NCC-DMS Registration Guide: Step-by-Step for Nigeria 2026
- [ ] **Status**: todo
- **Target keyword**: "ncc dms registration" / "nigeria phone register"
- **Outline**: What NCC-DMS is → who must register → step-by-step portal walkthrough → fees → next steps
- **Acceptance**: Cites NCC Type Approval Business Rule 2024 + TechCabal Oct 2025 article.

##### AR-NG-02 — How to Check If a Phone Is Stolen in Nigeria (NCC + IMEI)
- [ ] **Status**: todo
- **Target keyword**: "check stolen phone nigeria" / "is phone stolen ncc"
- **Outline**: Stolen-phone problem in Nigeria → NCC-DMS stolen flag → how to check → what to do if positive → buyer protection
- **Acceptance**: Free IMEI check widget embedded, NCC contact info for reporting.

##### AR-NG-03 — MTN, Glo, Airtel, 9mobile: IMEI Check Per Carrier
- [ ] **Status**: todo
- **Target keyword**: "mtn imei check nigeria" / "glo imei" / "airtel imei nigeria"
- **Outline**: Per-carrier USSD codes → IMEI binding rules → carrier-lock check → unlock options
- **Acceptance**: One H2 per carrier with USSD code + portal link.

##### AR-NG-04 — Buying a Used Phone in Nigeria: IMEI Safety Checklist
- [ ] **Status**: todo
- **Target keyword**: "buying used phone nigeria" / "used iphone nigeria check"
- **Outline**: Used-phone market context → IMEI verification → NCC-DMS check → iCloud/MDM check → red flags → checklist
- **Acceptance**: Downloadable PDF checklist (1-pager) + embedded IMEI checker.

##### AR-NG-05 — What Happens When NCC-DMS Blocks Your Phone (and How to Fix It)
- [ ] **Status**: todo
- **Target keyword**: "ncc blocked phone" / "nigeria phone disconnect"
- **Outline**: Why NCC-DMS blocks → how you know → unblock pathways → registration recovery → prevention
- **Acceptance**: Lists every NCC contact channel + 3 real example scenarios with resolution.

---

### C. Payment Research (3 tasks, IDs `PR-*`)

Pre-launch research only — no integration yet. Output: a Markdown research note per provider with all info Phase 2 will need to decide on integration.

#### PR-BKASH-01 — bKash for Bangladesh
- [ ] **Status**: todo
- **Output**: `docs/payment-research/bkash.md`
- **Required sections**: API docs URL · merchant onboarding requirements (KYC/business reg) · pricing/fees · settlement timeline · supported currencies · SDK availability (PHP/JS) · webhook structure · sandbox URL · production go-live checklist · failure rate notes · 3 competitor sites already using bKash for similar service
- **Suggested sources**: developer.bkash.com, bKash Merchant Solutions page, Bangladeshi e-commerce dev blogs.

#### PR-FAWRY-01 — Fawry / Vodafone Cash / InstaPay for Egypt
- [ ] **Status**: todo
- **Output**: `docs/payment-research/egypt-payments.md`
- **Required sections** (per provider): API docs URL · onboarding requirements · pricing/fees · settlement timeline · supported currencies · SDK availability · webhook structure · sandbox URL · 3 competitor sites · Egypt-specific compliance (CBE regulations on e-payments, foreign-merchant restrictions)
- **Decision**: which 1–2 providers to prioritize for imeihub integration.
- **Suggested sources**: developer.fawry.com, vodafonecash.com.eg, instapay.eg, ITIDA reports.

#### PR-PAYSTACK-01 — Paystack / Flutterwave for Nigeria
- [ ] **Status**: todo
- **Output**: `docs/payment-research/nigeria-payments.md`
- **Required sections** (per provider): API docs URL · onboarding · pricing (including conversion fees from NGN) · settlement timeline · SDK · webhook · sandbox · 3 competitor sites · CBN compliance notes
- **Decision**: which provider to prioritize — Paystack is the local default but Flutterwave handles multi-currency better.
- **Suggested sources**: paystack.com/docs, developer.flutterwave.com, CBN payment service provider guidance.

---

## Phase 1 Success Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| Landing pages shipped | 3/3 | Files exist in repo at `/bn/`, `/ar/eg/`, `/ng/` with passing Lighthouse SEO ≥ 90 |
| Articles published | 15/15 | All `AR-*` tasks checked, FAQ schema validates on schema.org validator |
| Payment research complete | 3/3 | All `PR-*` markdown files written, each provider has a YES/NO/DEPENDS recommendation |
| Pages indexed (gate before Phase 2) | ≥ 15/18 | Google Search Console — wait 30 days post-publish |

## Phase 1 Suggested Weekly Cadence

- **Week 1**: LP-BD-01, LP-EG-01, LP-NG-01 (all three landing pages — biggest blocker for indexing)
- **Week 2**: PR-BKASH-01, PR-FAWRY-01, PR-PAYSTACK-01 (payment research — runs in parallel, no code deps)
- **Week 3–4**: AR-BD-01, AR-BD-02, AR-EG-01, AR-EG-02, AR-NG-01, AR-NG-02 (6 articles — first round)
- **Week 5–6**: AR-BD-03, AR-BD-04, AR-EG-03, AR-EG-04, AR-NG-03, AR-NG-04 (6 articles — second round)
- **Week 7**: AR-BD-05, AR-EG-05, AR-NG-05 (last 3 articles)
- **Week 8**: GSC submission, sitemap update, internal linking audit, prep for Phase 2

## When to Abandon Phase 1 Plan

Skip ahead to Phase 2 reprioritization if any of these become true:
- A Tier-1 international IMEI competitor (imei.info / sickw.com) ships Bengali or Arabic-Egypt content → Blue Ocean window closes for that country, fall back to High Volume targets (Turkey, Indonesia).
- Bangladesh BTRC extends NEIR enforcement deadline by >6 months → urgency drops, redeploy budget.
- Validated GSC data after Week 4 shows top-3 ranking is wrong by ≥1.0 score → re-run `seo-country-analyzer` with measured data.

---

*Roadmap version 1 — generated 2026-05-28 from `seo-analysis/seo_report.md`. Update the checkboxes as work completes; the SessionStart hook reads this file to surface live progress.*
