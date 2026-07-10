# Project TODO

- [x] Database schema: credits table (user balance) + transactions table (top-up history)
- [x] tRPC procedures: getBalance, getTransactions (protected)
- [x] Webhook endpoint: /api/lemon-webhook for order_created event
- [x] Landing page with IMEI check branding and CTA
- [x] Top-Up Credits page with 4 packages ($25, $50, $100, $250) + Lemon.js overlay
- [x] User Dashboard showing credit balance and transaction history
- [x] Success/Thank You page after checkout
- [x] Protected routes (dashboard requires login)
- [x] Lemon.js script integration for overlay checkout
- [x] Vitest tests for webhook handler and credit procedures
- [x] Add placeholder variant IDs for $100 and $250 packages (real IDs after live mode)
- [x] Wire checkout success redirect to /success page
- [x] Add idempotency protection to webhook handler
- [x] Initialize Lemon.js properly in the app
- [x] Add webhook handler vitest tests

## Phase 2: Match Original Production Code + Lemon Squeezy

- [x] Update DB schema: add topup_orders table, credit_transactions table, webhook_events table (audit log)
- [x] Update TopUp page: support presets $5/$10/$25/$50/$100/$250 + custom amount + fee display
- [x] Replace Stripe with Lemon Squeezy as Card payment method (5% fee display)
- [x] Update webhook handler: full audit log pattern (logWebhookEvent → dedupe → process → markProcessed)
- [x] Update Dashboard: show balance as USD string, transaction history with type badges
- [x] Add proper topup order flow: PENDING → Lemon Squeezy → webhook credits (server-side createOrder)
- [x] Add transaction types: TOPUP, USAGE, REFUND, ADJUSTMENT, BONUS with balanceAfter
- [x] Update all vitest tests to match new schema and logic (21 tests passing)
- [ ] Add Credit History page with filter pills + pagination
- [ ] Push code to GitHub repo minerelx168-sketch/imeihub
- [ ] Create real Lemon Squeezy variants for $5, $10, $100, $250 (currently using $25 placeholder)
- [ ] Switch to Lemon Squeezy "pay what you want" or per-amount variants for production
