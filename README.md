# AI Prompt Books

Production-ready PHP web application for selling and delivering AI prompt book access with one-time payments (Razorpay/Cashfree/PayPal), account setup links, and gated content.

## Project Overview

- **Product**: Viral AI Prompts System (11 books + bonus content)
- **Core flow**:
  - Visitor lands on `index.php`
  - Chooses single or bundle checkout
  - Completes payment
  - Gets setup link via token (`setup-account.php`)
  - Creates password and accesses gated books via `dashboard.php`
- **Current primary gateway**: Razorpay
- **Tech stack**: PHP (vanilla), MySQL/SQLite, HTML/CSS/vanilla JS

## Key Features

- Responsive premium landing page with conversion-focused sections
- Single-book and bundle purchase paths
- Payment verification API endpoints
- Email-based access recovery and password reset
- Session-based auth with CSRF and basic rate limiting
- Meta Pixel tracking helpers and funnel events
- Admin dashboard for users/payments overview
- Book access control by plan and purchased IDs

## Project Structure

```txt
.
├── index.php                      # Landing + checkout modals
├── auth.php                       # Shared bootstrapping, DB, auth, pixel helpers
├── config.php                     # Local dev config (SQLite defaults)
├── config.production.php          # Production env-driven config
├── db.sql                         # MySQL schema + admin overview view
├── dashboard.php                  # User book dashboard
├── book.php                       # Book view/download gateway
├── login.php / logout.php
├── setup-account.php              # Post-payment account setup
├── forgot-password.php
├── recover-access.php
├── reset-password.php
├── contact-details.php
├── privacy-policy.php
├── terms-and-conditions.php
├── refund-and-cancellation-policy.php
├── admin/
│   └── index.php                  # Admin dashboard (Basic Auth protected)
├── api/
│   ├── razorpay-order.php
│   ├── razorpay-verify.php
│   ├── cashfree-order.php
│   ├── cashfree-verify.php
│   ├── paypal-order.php
│   └── paypal-capture.php
├── assets/
│   ├── landing.css / landing.js
│   ├── landing-critical.css
│   ├── *.min.css / *.min.js
│   └── js/pixel-tracking.js
└── books/
    ├── chapter-01-pdf.html ... chapter-11-pdf.html
    └── bonus-guide.html
```

## Runtime Configuration

The app loads config in this order:

- If `APP_ENV=production` and `config.production.php` exists: uses production config
- Otherwise: uses `config.php` (local defaults)

### Important Environment Variables (Production)

- `APP_ENV=production`
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `SITE_URL` (example: `https://www.aipromptbooks.in`)
- `PAYMENT_PROVIDER` (`razorpay` | `cashfree`)
- `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`
- `CASHFREE_APP_ID`, `CASHFREE_SECRET_KEY`, `CASHFREE_ENV`
- `PAYPAL_CLIENT_ID`, `PAYPAL_CLIENT_SECRET`, `PAYPAL_MODE`, `PAYPAL_API_URL`
- `BREVO_API_KEY`, `MAIL_FROM_EMAIL`, `MAIL_FROM_NAME`
- `META_PIXEL_ID`, `ENABLE_META_PIXEL` (`1`/`0`)
- `TELEGRAM_INVITE_LINK`
- `ADMIN_USER`, `ADMIN_PASS`
- `SESSION_NAME`

## Local Development

### Prerequisites

- PHP 8.1+ recommended
- SQLite enabled (for local defaults in `config.php`)

### Start

```bash
php -S localhost:8080
```

Open:

- `http://localhost:8080`

### Local Data

- Local DB uses `local.db` via `DB_DSN` in `config.php`
- For MySQL/prod-like setup, use `db.sql`

## Database Schema

Defined in `db.sql`:

- `payments`
  - plan, amount, gateway IDs, status, setup token, timestamps
- `users`
  - plan, books access JSON, payment metadata, status, last login
- `password_resets`
  - reset token lifecycle
- `v_admin_overview`
  - helper view for admin reporting

## Authentication & Access Control

Handled in `auth.php`:

- Session init with secure cookie settings
- `isLoggedIn()`, `requireLogin()`, `getCurrentUser()`
- `userHasBookAccess()` with fallback reconciliation logic
- CSRF helpers (`csrfToken()`, `verifyCsrf()`)
- Session-based rate limiting helpers

## Payment Flow

1. Frontend triggers checkout from landing (`assets/landing.js`)
2. API creates order (`api/*-order.php`)
3. Gateway popup/redirect completes payment
4. API verifies payment (`api/*-verify.php` or PayPal capture)
5. Payment row marked completed + setup token generated
6. Setup email sent (Brevo) and/or user proceeds via setup link

## Email Flows

- **Setup link** after successful payment
- **Forgot password** reset token email
- **Recover access** logic:
  - Existing user: password reset
  - Payment found but account not completed: regenerate setup token

## Frontend Notes

- Landing page assets:
  - `assets/landing-critical.css` for above-the-fold
  - `assets/landing.css` for full styling
  - `assets/landing.js` for behavior/tracking
- Non-minified assets are preferred at runtime when present; minified used as fallback.
- Core pages and books include responsive hardening for desktop/tablet/mobile.

## Analytics (Meta Pixel)

- Pixel output controlled by `ENABLE_META_PIXEL` + `META_PIXEL_ID`
- Helpers:
  - `renderMetaPixelHead()`
  - `renderMetaPixelNoScript()`
- Event helpers in frontend:
  - `assets/js/pixel-tracking.js`

## Deployment Notes

- Ensure production env vars are set before deploy
- Keep secrets out of committed files
- Verify:
  - DB connectivity
  - payment provider keys/mode
  - Brevo API key
  - `SITE_URL` correctness
  - writable/runtime assumptions for session and logs

## Pre-Launch Checklist

- Landing render on desktop/tablet/mobile
- Checkout modal open/close and validation
- Razorpay order creation + verification flow
- Setup link and login flow
- Dashboard access by plan type
- Recover/forgot/reset flows
- Meta Pixel test events
- No console/network errors in primary paths

## Security Notes

- Uses prepared statements (PDO)
- CSRF enforced on sensitive forms
- Session regeneration on login
- Basic rate limiting for auth-sensitive actions
- Keep admin credentials strong and rotate regularly

## Maintenance

- Keep minified assets in sync with source assets when source changes
- Re-run responsive and accessibility checks after landing/content updates
- When adding new books, update `getBooks()` in `auth.php` and relevant UI lists

---

For operational runbooks and production setup details, see:

- `GO_LIVE_RUNBOOK.md`
- `HOSTINGER_CICD_SETUP.md`
- `PRODUCTION_CHECKLIST.md`
