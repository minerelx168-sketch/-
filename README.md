# CloudForCash V2 — POS Core (Phase 1 + 2)

ระบบทวงหนี้และตรวจสลิปผ่าน LINE OA สำหรับธุรกิจขายสินค้าผ่อนชำระ พร้อม Mini POS ในตัว
ดู spec แบบเต็มได้ที่ docs/spec.md (หรือไฟล์อ้างอิงเดิมจากผู้สั่งงาน).

## สถานะปัจจุบัน — Phase 1 + 2 + 3

- ✅ Drizzle schema: products, customers, contracts, installment_schedule, payments,
  audit_logs, payment_adjustments, discounts, line_messages, webhook_logs, system_settings
- ✅ Business logic (shared/): safeNum, round2, calculatePaymentAmount, matchAmount,
  generateSchedule, generateContractNumber, parseLineText, TtlSet, LINE signature
  verify — ทั้งหมดมี Vitest tests
- ✅ tRPC routers: auth, product, customer, contract, installment, payment, audit, line
- ✅ Express server + JWT cookie auth + CSV import/export + **LINE webhook**
  (HMAC verify, respond 200 first, async dedup processing)
- ✅ LINE flow: contract binding (`CF-…` text), balance query, slip verification
  via Thunder API v2, Flex Message replies (nano bubbles, สี: เขียว/ส้ม/แดง/เทา)
- ✅ React UI: Home, Products, Customers, Contracts, New Contract, Contract Detail,
  Messages, LINE Connections (พร้อม bot on/off toggle)

ยังไม่ทำในเฟสถัดไป: escalation ladder (Phase 4), LIFF /pay (Phase 5),
admin override system (Phase 6).

## Quickstart

```bash
cp .env.example .env       # ใส่ DATABASE_URL ของ MySQL/TiDB
npm install
npm run db:generate        # generate SQL migration จาก drizzle/schema.ts
npm run db:migrate         # apply migration ไปยัง DB
npm run dev                # รัน server + client พร้อมกัน (port 3000 + 5173)
npm test                   # vitest
```

Login เริ่มต้น: ใช้ค่า `ADMIN_USERNAME` / `ADMIN_PASSWORD` จาก `.env` (default admin/admin).

## โครงสร้างโฟลเดอร์

```
server/          Express + tRPC + db helpers
shared/          Pure business logic + types (testable, no DB/network)
drizzle/         Drizzle schema + migrations
client/          React 19 + Vite + Tailwind + wouter
```

ดูรายละเอียดการออกแบบทั้งหมดได้จาก spec ต้นทาง (CloudForCash V2 prompt).
