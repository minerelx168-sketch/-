# imeihub

Free IMEI Check & Phone Info Lookup — a web application that lets users identify any mobile phone by its IMEI number. Offers both free TAC-based lookups and premium paid checks via third-party IMEI APIs.

## Overview

imeihub is a full-stack IMEI checking service built with PHP and static HTML. It provides instant device identification using a bundled TAC (Type Allocation Code) database for free lookups, and integrates with external IMEI API providers for detailed premium reports including iCloud status, blacklist checks, carrier info, and more.

## Features

- **Free IMEI Check** — Brand, model, colour, storage and basic specs from any valid 15-digit IMEI
- **40+ Premium Services** — Apple iCloud, MDM, SIM-Lock, Warranty, Samsung Knox, Xiaomi Mi ID, Blacklist, and more
- **Multi-brand Support** — Apple, Samsung, Huawei, Xiaomi, OPPO, vivo, realme, OnePlus, Google Pixel, Motorola, Nokia, Sony
- **Credit System** — Users top up balance via Stripe (card + PromptPay) and pay per check
- **Google OAuth** — Sign in with Google for quick account creation
- **SEO Optimized** — Individual pages for each service, brand, and article
- **Dark Theme UI** — Modern, responsive design with dark colour scheme
- **Rate Limiting** — Protection against abuse with configurable rate limits
- **Luhn Validation** — Client-side IMEI checksum validation before API calls

## Tech Stack

| Layer | Technology |
|-------|------------|
| Frontend | HTML5, CSS3 (custom dark theme), Vanilla JavaScript |
| Backend | PHP 8.x |
| Database | MySQL 8.0 |
| Auth | Google OAuth 2.0, session-based email/password |
| Payments | Stripe Checkout (card + PromptPay) |
| IMEI API | DHRU / sickw.com / imei.info (configurable) |
| Hosting | GitHub Pages (static demo), VPS (full backend) |

## Project Structure

```
imeihub/
├── index.html              # Homepage with IMEI check form
├── services.html           # Service catalog (13 categories)
├── brands.html             # Brand directory
├── articles.html           # SEO articles listing
├── about.html              # About page
├── contact.html            # Contact page
├── login.html              # Sign in (email + Google OAuth)
├── signup.html             # Create account
├── topup.html              # Credit top-up page
├── dashboard.html          # User dashboard
├── privacy.html            # Privacy policy
├── service-*.html          # Individual service detail pages
├── brand-*.html            # Individual brand pages
├── article-*.html          # Individual article pages
├── assets/
│   ├── css/style.css       # Global stylesheet (dark theme)
│   └── js/main-static.js  # Client-side IMEI lookup (demo TAC DB)
├── includes/               # PHP backend (on VPS deployment)
│   ├── config.php          # Environment configuration
│   ├── db.php              # MySQL connection
│   ├── auth.php            # Authentication middleware
│   ├── layout.php          # HTML layout template
│   └── imei_demo.php       # Demo mode IMEI responses
├── api/
│   ├── check.php           # IMEI check endpoint
│   └── auth/               # OAuth endpoints
├── sql/
│   ├── schema.sql          # Database schema
│   └── seed.sql            # Sample data
└── .env.example            # Environment variables template
```

## Branches

| Branch | Purpose |
|--------|----------|
| `main` | Project documentation and overview |
| `gh-pages` | Static HTML demo (deployed to GitHub Pages) |
| `claude/*` | Development branches with PHP backend and advanced features |

## Getting Started

### Static Demo (GitHub Pages)

The `gh-pages` branch contains a fully functional static demo that runs IMEI lookups against a bundled TAC database (no backend required).

### Full Backend Deployment

1. Clone the repository and checkout the backend branch
2. Copy `.env.example` to `.env` and configure:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` — MySQL credentials
   - `IMEI_API_KEY` — API key from your IMEI provider
   - `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` — Google OAuth
   - `STRIPE_SECRET_KEY`, `STRIPE_PUBLISHABLE_KEY` — Stripe payments
3. Import `sql/schema.sql` into MySQL
4. Run with PHP built-in server or configure Apache/Nginx

```bash
php -S 0.0.0.0:8080
```

## Services Offered

### Free
- Basic IMEI Check (brand, model, specs)

### Premium (Apple)
- Apple Basic Info, Carrier (Lite/Pro/Pro Plus), Max Info
- iCloud ON/OFF, Clean/Lost, ID Hint
- MDM ON/OFF, MDM + FMI
- Warranty, SIM-Lock, Part Number
- Case History, Repair History, Full GSX

### Premium (Other Brands)
- Samsung Info + Knox Guard Status
- Xiaomi Info + Mi Account Lock
- Huawei Info, Honor Info
- Google Pixel Info, Motorola Info, Lenovo Info
- WorldWide Blacklist (Simple/Full)
- T-Mobile / Verizon USA Status
- HLR Lookup, Number Type, Ping-SMS

## Pricing Model

Users purchase credits via Stripe and spend them on checks. Prices range from $0.01 (iCloud ON/OFF) to $4.20 (Full GSX Report). Free checks are unlimited.

## Environment Variables

```env
APP_MODE=production
APP_URL=https://yourdomain.com
DB_HOST=localhost
DB_NAME=imei_checker
DB_USER=root
DB_PASS=
IMEI_API_KEY=your_api_key
IMEI_API_URL=https://api.provider.com
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_PUBLISHABLE_KEY=pk_live_xxx
```

## License

Private project. All rights reserved.