# Phase 1 Content Index — สรุปทั้งหมดสำหรับนำไปแปะหน้าเว็บ

ที่อยู่ทั้งหมดบน branch `claude/loving-cray-IBMXQ` ภายใต้ `phase1/` ส่วน payment research อยู่ใน `docs/payment-research/`

---

## 📍 Landing Pages (3 ไฟล์)

| URL deploy | ไฟล์ | ภาษา | จุดเด่น |
|------------|------|------|---------|
| `imeihub.com/bn/` | `phase1/landing-pages/bn/index.html` | Bengali (LTR) | NEIR card, KYD 16002 SMS step, carrier rail GP/Robi/Airtel/Banglalink/Teletalk |
| `imeihub.com/ar/eg/` | `phase1/landing-pages/ar-eg/index.html` | Arabic (RTL) | **live tax calculator 38.5%** (vanilla JS, ใส่ USD แล้วคำนวณทันที), NTRA rules box |
| `imeihub.com/ng/` | `phase1/landing-pages/ng/index.html` | English-NG (LTR) | NEW badge, NCC-DMS 4-step flow, MTN/Glo/Airtel/9mobile rail |

**ทุก LP มี**: `<link rel="alternate" hreflang="...">`, Schema.org JSON-LD (WebPage + Service + BreadcrumbList), IMEI form POST ไปที่ `api/check.php` พร้อม `locale` hidden field, mobile-first responsive

**Note**: CSS ของ LP ทั้ง 3 link ไปที่ `../../assets/css/style.css` (ของ Bengali/Nigeria) และ `../../assets/css/style.css` (ของ Egypt) ซึ่งตอน deploy บน gh-pages ควรชี้ไปที่ root `assets/css/style.css` แก้ path ก่อน upload

---

## 📝 Articles (15 ไฟล์)

### 🇧🇩 Bangladesh (Bengali, ~800-900 words each)

| ID | ไฟล์ | URL แนะนำ | Title (native) | Keyword หลัก |
|----|------|-----------|---------------|--------------|
| AR-BD-01 | `phase1/content/bd/AR-BD-01.md` | `/bn/neir-registration-step-by-step-guide-2026/` | NEIR কীভাবে নিবন্ধন করবেন — ধাপে ধাপে গাইড ২০২৬ | `NEIR নিবন্ধন` |
| AR-BD-02 | `phase1/content/bd/AR-BD-02.md` | `/bn/foreign-phone-register-bangladesh/` | বিদেশ থেকে আনা মোবাইল বৈধ করার নিয়ম — ৩০ দিনের সময়সীমা ও পুরো প্রক্রিয়া | `বিদেশ থেকে আনা মোবাইল` |
| AR-BD-03 | `phase1/content/bd/AR-BD-03.md` | `/bn/grameenphone-robi-banglalink-imei-check/` | Grameenphone, Robi, Banglalink IMEI চেক — অপারেটরভিত্তিক সম্পূর্ণ গাইড | `Grameenphone IMEI` |
| AR-BD-04 | `phase1/content/bd/AR-BD-04.md` | `/bn/kyd-16002-sms-imei-verification/` | KYD ১৬০০২ এসএমএস দিয়ে মোবাইল বৈধতা যাচাই — সম্পূর্ণ ব্যাখ্যা | `KYD 16002` |
| AR-BD-05 | `phase1/content/bd/AR-BD-05.md` | `/bn/btrc-unblock-phone-process/` | মোবাইল ব্লক হলে কী করবেন — BTRC আনব্লক প্রসেস ২০২৬ | `মোবাইল ব্লক` |

**สรุปเนื้อหา** (Thai overview):
1. **AR-BD-01** — สอนใช้ neir.btrc.gov.bd ทีละขั้น ตั้งแต่เปิดบัญชี → กรอก NID → ใส่ IMEI → ยืนยัน
2. **AR-BD-02** — กฎ 30 วันสำหรับมือถือนำเข้าจาก ตปท. + เอกสารต้องเตรียม + ข้อยกเว้นภาษี (2 เครื่อง/ปี/คน)
3. **AR-BD-03** — USSD code + วิธีเช็ค IMEI แยกตามค่ายของแต่ละโอเปอเรเตอร์
4. **AR-BD-04** — วิธีส่ง SMS `KYD <IMEI>` ไป 16002 + อ่าน reply 3 สถานการณ์ (valid / under-review / unauthorized)
5. **AR-BD-05** — สาเหตุมือถือถูกบล็อก + BTRC contact + เอกสาร + timeline 5-15 วัน

⚠️ **8 ข้อต้อง verify ก่อน publish** (agent flag): NEIR portal screenshots, "5 handsets max per NID", customs duty rate exact %, annual duty-free allowance current version, Bangladesh Bank "73% fraud" source URL, BTRC contact accuracy, NEIR form field labels, operator-app menu names ปัจจุบัน

### 🇪🇬 Egypt (Arabic, ~1000-1200 words each)

| ID | ไฟล์ | URL แนะนำ | Title (native) | Keyword หลัก |
|----|------|-----------|---------------|--------------|
| AR-EG-01 | `phase1/content/eg/AR-EG-01.md` | `/ar/eg/telephony-app-mobile-registration-egypt-2026/` | تسجيل الموبايل في مصر ٢٠٢٦: دليل شامل لتطبيق Telephony | `تسجيل الموبايل مصر` |
| AR-EG-02 | `phase1/content/eg/AR-EG-02.md` | `/ar/eg/egypt-phone-tax-calculator-38-5-percent/` | حاسبة ضريبة الموبايل ٣٨.٥٪ مصر: كيف تحسب الجمارك قبل ما تدفع | `حاسبة ضريبة الموبايل` |
| AR-EG-03 | `phase1/content/eg/AR-EG-03.md` | `/ar/eg/egypt-customs-exemption-ended-january-2026/` | إلغاء الإعفاء الجمركي ٢١ يناير ٢٠٢٦: ماذا يعني للمسافرين المصريين | `إلغاء الإعفاء الجمركي` |
| AR-EG-04 | `phase1/content/eg/AR-EG-04.md` | `/ar/eg/ntra-90-day-grace-period-foreign-phones-egypt/` | الموبايل من الخارج: ٩٠ يوم سماح من NTRA — التايم لاين الكامل | `موبايل من الخارج` |
| AR-EG-05 | `phase1/content/eg/AR-EG-05.md` | `/ar/eg/how-to-unblock-phone-egypt-ntra/` | كيفية فك حظر الموبايل في مصر: خطوات استئناف الخدمة | `فك حظر الموبايل مصر` |

**สรุปเนื้อหา** (Thai overview):
1. **AR-EG-01** — คู่มือ Telephony app ของ NTRA ตั้งแต่ดาวน์โหลด → ลงทะเบียน → กรอก IMEI → ชำระภาษี
2. **AR-EG-02** — สูตรคำนวณภาษี 38.5% + ตัวอย่าง iPhone 15 / Samsung S24 + ตารางสกุลเงิน
3. **AR-EG-03** — ผลของการยกเลิกการยกเว้นวันที่ 21 ม.ค. 2026 ต่อผู้เดินทาง + ทางเลือก
4. **AR-EG-04** — timeline 90 วันอย่างละเอียดหลังเปิดใช้ SIM อียิปต์ครั้งแรก
5. **AR-EG-05** — วิธีปลดล็อกมือถือที่ถูกบล็อกในอียิปต์ + NTRA contact + ระยะเวลา

⚠️ **HTML pages ของ EG ต้องใช้ `dir="rtl"` บน `<html>` tag** (frontmatter ระบุไว้แล้ว)

### 🇳🇬 Nigeria (English-NG, ~1500-1600 words each)

| ID | ไฟล์ | URL แนะนำ | Title | Keyword หลัก |
|----|------|-----------|-------|--------------|
| AR-NG-01 | `phase1/content/ng/AR-NG-01.md` | `/ng/ncc-dms-registration-guide-nigeria-2026/` | NCC-DMS Registration Guide: Step-by-Step for Nigeria 2026 | `ncc dms registration` |
| AR-NG-02 | `phase1/content/ng/AR-NG-02.md` | `/ng/how-to-check-if-phone-is-stolen-nigeria/` | How to Check If a Phone Is Stolen in Nigeria (NCC + IMEI) | `check stolen phone nigeria` |
| AR-NG-03 | `phase1/content/ng/AR-NG-03.md` | `/ng/mtn-glo-airtel-9mobile-imei-check-nigeria/` | MTN, Glo, Airtel, 9mobile — IMEI Check Per Carrier in Nigeria | `mtn imei check nigeria` |
| AR-NG-04 | `phase1/content/ng/AR-NG-04.md` | `/ng/buying-used-phone-nigeria-imei-safety-checklist/` | Buying a Used Phone in Nigeria — IMEI Safety Checklist | `buying used phone nigeria` |
| AR-NG-05 | `phase1/content/ng/AR-NG-05.md` | `/ng/ncc-dms-blocked-phone-how-to-fix/` | What Happens When NCC-DMS Blocks Your Phone (and How to Fix It) | `ncc blocked phone` |

**สรุปเนื้อหา** (Thai overview):
1. **AR-NG-01** — คู่มือลงทะเบียน NCC-DMS แบบครบ + อ้างอิง Type Approval Business Rule 2024 + TechCabal ต.ค. 2025
2. **AR-NG-02** — วิธีเช็คว่ามือถือถูกขโมยใน Nigeria ผ่าน NCC + IMEI + ขั้นตอนแจ้งความ
3. **AR-NG-03** — USSD code + วิธีเช็ค IMEI แยก carrier (MTN, Glo, Airtel, 9mobile)
4. **AR-NG-04** — checklist ก่อนซื้อมือถือมือสองที่ Computer Village Lagos + การตรวจสอบ iCloud/MDM
5. **AR-NG-05** — ทำยังไงเมื่อ NCC-DMS บล็อกมือถือ + ทุก contact channel + 3 example scenarios

---

## 🏦 Payment Research (3 ไฟล์ — สำหรับ reference)

> ผู้ใช้เลือก crypto-only ดังนั้นไฟล์เหล่านี้เก็บไว้เป็น **market intelligence** ไม่ได้ใช้ implement แต่มีข้อมูลตลาดเชิงลึกที่อาจมีประโยชน์ในอนาคต

| File | Word count | คำแนะนำ | Foreign-merchant |
|------|-----------|---------|------------------|
| `docs/payment-research/bkash.md` | 2,463 | **DEPENDS** — แนะนำ SSLCOMMERZ aggregator | ⚠️ ต้องยืนยัน USD settlement ไปบัญชีต่างประเทศ |
| `docs/payment-research/egypt-payments.md` | 3,061 | **Fawry via Checkout.com/EBANX MoR** | ✅ Resolved (block direct ทุกตัว, ใช้ international acquirer ปลด) |
| `docs/payment-research/nigeria-payments.md` | 2,799 | **Flutterwave** | ✅ Resolved (self-serve "Rest of the World" รับ foreign entity) |

---

## 🎯 Recommended Publish Order (ตามความง่าย)

1. **Nigeria first** (5 articles + 1 LP) — English ตรงตัว, ไม่ต้อง translate ตรวจสอบ, NCC-DMS เพิ่งเปิด มี SEO momentum
2. **Bangladesh** (5 articles + 1 LP) — Bengali พร้อม แต่ verify 8 ข้อก่อน publish + capture NEIR portal screenshots
3. **Egypt** (5 articles + 1 LP) — Arabic + RTL ต้องเช็ค layout บน production CSS ก่อน

**ก่อน upload ทุกบทความ ตรวจ checklist นี้**:
- [ ] เปลี่ยน internal link จาก `/bn/`, `/ar/eg/`, `/ng/` ให้ตรงกับ URL structure จริงของ site
- [ ] Add canonical URL ใน `<head>` ของแต่ละหน้า
- [ ] Add Open Graph + Twitter Card meta tags
- [ ] FAQ schema JSON-LD อยู่ในไฟล์แล้ว — copy ลง `<script type="application/ld+json">` ในหน้าตัวจริง
- [ ] รัน Lighthouse SEO check (target ≥ 90)
- [ ] Submit sitemap.xml ให้ Google Search Console หลังขึ้น production

---

## 📊 Phase 1 Final Stats

- **18 ไฟล์**: 3 landing pages (HTML) + 15 articles (Markdown with frontmatter + FAQ schema)
- **~17,000 คำเนื้อหา** (รวมทั้ง 3 ภาษา)
- **~95 KB total content** (Bengali + Arabic + English)
- **7 commits** บน `claude/loving-cray-IBMXQ` ใน PR #6
- **3 payment research notes** (8,300+ words) เก็บไว้เป็น market reference

ทุกอย่างพร้อม pull ไป gh-pages เมื่อต้องการ — บอกได้ถ้าจะให้ช่วย deploy
