# Go-Live Runbook (`www.aipromptbooks.in`)

Use this after DNS and deploy are done.

## A) Live smoke tests

Run these in order:

**0. Verify PHP cURL is enabled (prerequisite)**
- [ ] Contact hosting provider if not done; cURL must be enabled for payments
- [ ] Test: Load a temporary diagnostic page or check hosting control panel for cURL in enabled extensions

1. Open `https://www.aipromptbooks.in` and confirm no browser SSL warnings.
2. Verify redirects:
   - `http://aipromptbooks.in` -> `https://www.aipromptbooks.in`
   - `https://aipromptbooks.in` -> `https://www.aipromptbooks.in`
3. Start checkout from landing page and confirm:
   - order create succeeds (if order creation fails with cURL error, cURL is not enabled)
   - Razorpay checkout opens
   - verification returns setup token
4. Complete account setup and confirm:
   - password set succeeds
   - login works
   - logout works
5. Access validation:
   - single buyer only sees purchased book + bonus
   - bundle buyer sees all books
6. Open `/admin/`:
   - HTTP basic auth prompt appears
   - dashboard loads only after valid credentials

## B) Payment dashboard checks

1. In Razorpay dashboard:
   - website URL is `https://www.aipromptbooks.in`
   - webhook/callback URLs point to `https://www.aipromptbooks.in`
2. In PayPal dashboard:
   - app mode is live
   - return/cancel/webhook URLs use `https://www.aipromptbooks.in`

## C) Operations checks

1. Enable daily DB backups in hosting panel.
2. Enable PHP/server error logs.
3. Verify `display_errors=0` in production.
4. Export a rollback snapshot:
   - app files archive
   - latest DB export
5. Save credentials in a password manager:
   - hosting panel
   - DB
   - Cloudflare
   - Razorpay
   - PayPal

## D) Go/No-Go gate

Go live only if all are true:

- SSL is valid on `https://www.aipromptbooks.in`
- Payments work end-to-end
- Account setup and login work
- Access control works by plan type
- Admin endpoint is protected
- Backups + logs are enabled
