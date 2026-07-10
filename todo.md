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
