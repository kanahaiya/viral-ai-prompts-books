# Production Checklist

Use this checklist before launching `AI Prompt Books` to production.

## 1) Pre-Deploy Security

- [ ] Set `APP_ENV=production` on the server (vhost/env config).
- [ ] Confirm production startup fails fast when required secrets are missing (expected 500 guard).
- [ ] Confirm `config.production.php` contains real production values:
  - [ ] MySQL DB credentials
  - [ ] Razorpay live key + secret
  - [ ] PayPal live client ID + secret
  - [ ] Correct `SITE_URL`
  - [ ] Real `TELEGRAM_INVITE_LINK`
- [ ] Confirm `config.production.php` is not publicly accessible:
  - [ ] `.htaccess` rule is present and active
  - [ ] direct URL access to config files returns `403`
- [ ] Ensure `display_errors=0` in production config.
- [ ] Rotate payment secrets if any were shared or committed before.

## 2) Files to Exclude From Production

- [ ] Do NOT deploy `setup-local.php`.
- [ ] Do NOT deploy `local.db`.
- [ ] Do NOT deploy any local backup/temp files (`*.bak`, `*.orig`, etc.).

## 3) Server / Access Hardening

- [ ] Confirm root `.htaccess` is active (HTTPS force, headers, CSP, deny rules).
- [ ] Configure `admin/.htaccess` with a real `AuthUserFile` absolute path.
- [ ] Generate strong admin basic-auth credentials (`htpasswd`).
- [ ] Verify `/admin/` prompts for authentication.
- [ ] Verify directory listing is disabled.

## 4) Database Readiness

- [ ] Import `db.sql` into production MySQL.
- [ ] Confirm tables exist: `users`, `payments`.
- [ ] Confirm charset/collation is `utf8mb4`.
- [ ] Confirm DB user has only required privileges.
- [ ] Confirm `auth_rate_limits` table exists.

## 5) Payment Integration Checks

- [ ] Razorpay (India):
  - [ ] create order works
  - [ ] payment succeeds
  - [ ] signature verify succeeds
- [ ] PayPal (International):
  - [ ] order create works
  - [ ] capture succeeds
- [ ] Confirm successful payment writes DB rows with:
  - [ ] `payments.status = completed`
  - [ ] `payment_id` populated
  - [ ] `setup_token` present

## 6) Account & Access Flow

- [ ] Setup link opens from payment success.
- [ ] Setup token expires correctly (24-hour window).
- [ ] Invalid/used token is rejected with safe message.
- [ ] CSRF-protected forms work:
  - [ ] login form
  - [ ] setup-account form
- [ ] Login redirects only to safe internal paths.
- [ ] Bundle buyer sees all books unlocked.
- [ ] Single buyer sees only purchased books + bonus.

## 7) Landing Page & Conversion QA

- [ ] Hero renders correctly on mobile and desktop.
- [ ] Primary CTA always scrolls/opens expected checkout flow.
- [ ] Pricing currency switch INR/USD updates all relevant UI text.
- [ ] Guarantee copy is consistent everywhere:
  - [ ] pricing trust line
  - [ ] guarantee section
  - [ ] FAQ
  - [ ] checkout modal microcopy
- [ ] Images load quickly and are not broken.

## 8) Operational Readiness

- [ ] Enable server-side error logging (file or centralized logs).
- [ ] Disable verbose debug output in production.
- [ ] Set up daily DB backups.
- [ ] Run and document monthly backup restore drill.
- [ ] Document rollback plan (last known-good deploy).
- [ ] Verify rollback procedure by redeploying last-known-good commit in a dry run.
- [ ] Keep a payment incident contact process (email/support).

## 9) Final Smoke Test (Live Domain)

- [ ] Visit homepage over HTTPS.
- [ ] Complete one INR test purchase (real low-value if needed).
- [ ] Complete one USD test purchase.
- [ ] Set password and login.
- [ ] Open purchased book(s).
- [ ] Verify Telegram invite path.
- [ ] Verify logout/login cycle.
- [ ] Verify `/admin/` auth + dashboard loads.

## 10) Go/No-Go Gate

Launch only when all are true:

- [ ] Security blockers resolved.
- [ ] Payments verified end-to-end.
- [ ] Account setup/login/access verified.
- [ ] Policy text legally consistent and visible.
- [ ] Monitoring + backups enabled.

