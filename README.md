# CloudForCash V2 — POS Core (Phase 1 + 2)

ระบบทวงหนี้และตรวจสลิปผ่าน LINE OA สำหรับธุรกิจขายสินค้าผ่อนชำระ พร้อม Mini POS ในตัว
ดู spec แบบเต็มได้ที่ docs/spec.md (หรือไฟล์อ้างอิงเดิมจากผู้สั่งงาน).

## สถานะปัจจุบัน — Phase 1 + 2 (POS Core)

- ✅ Drizzle schema: products, customers, contracts, installment_schedule, payments,
  audit_logs, payment_adjustments, discounts, line_messages, webhook_logs, system_settings
- ✅ Business logic (shared/): safeNum, round2, calculatePaymentAmount, matchAmount,
  generateSchedule, generateContractNumber พร้อม Vitest tests
- ✅ tRPC routers: auth, product, customer, contract, installment, payment, audit
- ✅ Express server + JWT cookie auth + CSV import/export endpoints
- ✅ React 19 + Vite + Tailwind admin UI: Home, Products, Customers, Contracts,
  New Contract, Contract Detail (พร้อม pricing quote / ตารางงวด / manual payment / audit log)

ยังไม่ทำในเฟสนี้: LINE webhook, slip verify, escalation, LIFF /pay, broadcast.
รออนุมัติก่อนเริ่ม Phase 3+.

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
