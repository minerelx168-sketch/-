# imeihub

A baseimei.com-style IMEI lookup site built with **PHP + MySQL**.
Users enter a 15-digit IMEI, the backend calls an external provider
(sickw.com / imei.info style), and the device brand / model / details
are rendered in a clean responsive UI.

## Features

- 15-digit IMEI input with client + server side Luhn validation
- Calls an external IMEI API (sickw by default, easy to swap)
- MySQL-backed lookup cache (24h) to avoid burning API credits on repeats
- Per-IP rate limit (10 requests / minute)
- Responsive landing page with hero, FAQ, "How it works" sections

## Quick start

1. **Database**

   ```bash
   mysql -uroot -p < sql/schema.sql
   ```

2. **Configure**

   ```bash
   cp .env.example .env
   # then edit .env and set IMEI_API_KEY etc.
   ```

3. **Serve**

   Point your webserver (Apache / Nginx / `php -S`) at the project root.
   For a quick local test:

   ```bash
   php -S 127.0.0.1:8080
   ```

   Then open <http://127.0.0.1:8080>.

## Provider configuration

The default provider is **sickw.com**. Sign up there (or at imei.info, etc.),
copy your API key, and put it in `.env`:

```
IMEI_API_PROVIDER=sickw
IMEI_API_KEY=your-real-key
IMEI_API_URL=https://sickw.com/api.php
IMEI_API_DEFAULT_SERVICE=0
```

`IMEI_API_DEFAULT_SERVICE` is the service ID of the lookup you want to run
(e.g. "Phone Basic Info"). Check your provider's dashboard for the list.

To plug in a different provider, extend `imei_provider_lookup()` and
`imei_provider_parse()` in `includes/imei_provider.php`.

## Project layout

```
.
├── index.php               # Landing page + lookup form
├── api/check.php           # JSON endpoint the form posts to
├── includes/
│   ├── config.php          # Env loader + config
│   ├── db.php              # PDO singleton
│   ├── functions.php       # Validation, rate limiting, cache helpers
│   └── imei_provider.php   # External API client
├── assets/
│   ├── css/style.css
│   └── js/main.js
└── sql/schema.sql
```

## Notes

- The Luhn check rejects mistyped IMEIs before we ever hit the API.
- Cached responses are flagged in the UI with a blue "Cached" badge.
- `.env` is git-ignored — never commit your API key.

## Operations

### Integration tests

`scripts/run-tests.php` exercises the financial invariants the brief
calls out:

```bash
php scripts/run-tests.php
```

Covers: signup → balance 0, top-up happy path, webhook replay safety
(no double credit), deduct happy path, insufficient-balance rejection,
provider-failure auto-refund (with idempotent re-call), concurrent
deduct (10 forked workers on a 100 $ wallet). Every test re-checks the
`users.cached_balance == SUM(credit_transactions)` invariant.

Exit code: 0 = all pass, 1 = any failure.

### Balance reconciliation

`scripts/reconcile-balance.php` detects drift between
`users.cached_balance` and the ledger. Run nightly:

```bash
# report only
php scripts/reconcile-balance.php

# repair drift (writes cached_balance from SUM(ledger))
php scripts/reconcile-balance.php --fix
```

If `--fix` ever has work to do, that points at a code bug somewhere in
`includes/credits_write.php`, not a finance problem - the ledger is
always source of truth.
