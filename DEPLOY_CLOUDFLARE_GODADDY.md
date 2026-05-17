# Cloudflare + GoDaddy Deployment Runbook

This runbook implements the lowest-cost production setup for `www.aipromptbooks.in` using free Cloudflare SSL in front of your PHP hosting origin.

## 1) Cloudflare DNS cutover (free SSL)

1. Create a Cloudflare account and add `aipromptbooks.in`.
2. Cloudflare will provide 2 nameservers.
3. In GoDaddy domain settings, replace existing nameservers with the Cloudflare ones.
4. Wait for DNS delegation to complete (usually minutes, can take up to 24h).

## 2) DNS records inside Cloudflare

Create these records (Proxy status: ON / orange cloud):

- `A` record: `@` -> `<your_origin_server_ip>`
- `A` record: `www` -> `<your_origin_server_ip>`

If your host gives a hostname instead of IP, use `CNAME` for `www`.

## 3) SSL/TLS settings in Cloudflare

Set:

- SSL/TLS encryption mode: `Full`
- Always Use HTTPS: `On`
- Automatic HTTPS Rewrites: `On`

Later hardening:

- Install Cloudflare Origin Certificate on origin host.
- Change SSL mode to `Full (strict)`.

## 4) Application deploy

1. Upload app files to webroot (`public_html`).
2. Import `db.sql` into production MySQL.
3. Set environment variable:
   - `APP_ENV=production`
4. Update `config.production.php` with real credentials and real links.

## 5) Canonical domain rules (already configured in `.htaccess`)

Expected behavior:

- `http://aipromptbooks.in` -> `https://www.aipromptbooks.in`
- `http://www.aipromptbooks.in` -> `https://www.aipromptbooks.in`
- `https://aipromptbooks.in` -> `https://www.aipromptbooks.in`

## 6) Verify before payment onboarding

Confirm these pass:

- Homepage loads on `https://www.aipromptbooks.in`.
- `/api/razorpay-order.php` reachable from checkout flow.
- `/setup-account.php` token flow works.
- `/admin/` prompts for HTTP basic auth.

## 7) Rollback

If anything breaks:

1. In Cloudflare DNS, temporarily set records to `DNS only` (gray cloud) to bypass proxy.
2. Restore previous file snapshot on hosting.
3. Restore latest DB backup.
