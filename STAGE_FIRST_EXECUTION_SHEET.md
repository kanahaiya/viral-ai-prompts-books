# Stage-First Execution Sheet

This is the exact, copy-paste run order to set up and validate **stage first**, then promote to production.

---

## 0) Safety Rule (Read First)

- Do **not** run production DB migrations yet.
- Do **not** deploy production yet.
- Complete stage verification end-to-end first.

---

## 1) Hostinger UI — Create Databases and Users (One-Time)

Go to **hPanel -> Databases -> MySQL Databases**

Create DBs:
- `u589539001_stage_aipromptbooks`
- `u589539001_prod_aipromptbooks`

Create users:
- `u589539001_usr_stage_aipromptbooks`
- `u589539001_usr_prod_aipromptbooks`

Grant mapping:
- `u589539001_usr_stage_aipromptbooks` -> `u589539001_stage_aipromptbooks` (ALL PRIVILEGES on this DB only)
- `u589539001_usr_prod_aipromptbooks` -> `u589539001_prod_aipromptbooks` (ALL PRIVILEGES on this DB only)

---

## 2) Hostinger UI — Create Stage Site Target

Create stage destination (subdomain/folder), e.g.:
- URL: `https://stage.aipromptbooks.in`
- Remote dir example: `domains/stage.aipromptbooks.in/public_html`

Ensure DNS for `stage.aipromptbooks.in` points to the same host.

---

## 3) GitHub Secrets — Stage Values

In GitHub repo secrets (or environment secrets), set **stage-only** values:

- `HOSTINGER_SFTP_SERVER`
- `HOSTINGER_SFTP_PORT`
- `HOSTINGER_SFTP_USERNAME`
- `HOSTINGER_SFTP_PASSWORD`
- `HOSTINGER_SFTP_REMOTE_DIR=domains/stage.aipromptbooks.in/public_html`
- `SITE_SMOKE_URL=https://stage.aipromptbooks.in`

For app runtime env on stage host, set:
- `APP_ENV=stage`
- `DB_HOST=localhost`
- `DB_NAME=u589539001_stage_aipromptbooks`
- `DB_USER=u589539001_usr_stage_aipromptbooks`
- `DB_PASS=<stage-db-password>`
- Stage payment keys (sandbox/test), not live.

---

## 4) Stage DB Init — Import Base Schema

In phpMyAdmin, select DB `u589539001_stage_aipromptbooks`.

Import the repo file:
- `db.sql`

Or paste and run from `db.sql`.

---

## 5) Stage DB Migration — Run Hardening SQL (Copy-Paste)

Run in `u589539001_stage_aipromptbooks`:

```sql
-- A) payments: lifecycle + integrity + observability
ALTER TABLE payments
  ADD COLUMN completed_at TIMESTAMP NULL DEFAULT NULL AFTER setup_used,
  ADD COLUMN failed_at TIMESTAMP NULL DEFAULT NULL AFTER completed_at,
  ADD COLUMN failure_reason VARCHAR(255) DEFAULT NULL AFTER failed_at,
  ADD COLUMN gateway_status VARCHAR(64) DEFAULT NULL AFTER failure_reason,
  ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER gateway_status;

UPDATE payments
SET completed_at = created_at
WHERE status = 'completed' AND completed_at IS NULL;

ALTER TABLE payments
  ADD UNIQUE KEY uq_provider_order (payment_method, order_id),
  ADD UNIQUE KEY uq_provider_payment (payment_method, payment_id),
  ADD UNIQUE KEY uq_setup_token (setup_token),
  ADD KEY idx_payment_lookup (setup_token, setup_used, status, completed_at),
  ADD KEY idx_email_created (email, created_at),
  ADD KEY idx_status_created (status, created_at);

-- B) users: session invalidation + audit columns + index
ALTER TABLE users
  ADD COLUMN session_version INT UNSIGNED NOT NULL DEFAULT 1 AFTER payment_id,
  ADD COLUMN password_changed_at TIMESTAMP NULL DEFAULT NULL AFTER session_version,
  ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER last_login,
  ADD KEY idx_status_created (status, created_at);

UPDATE users
SET password_changed_at = COALESCE(password_changed_at, created_at)
WHERE password_hash IS NOT NULL AND password_hash <> '';

-- C) password_resets: lookup performance
ALTER TABLE password_resets
  ADD KEY idx_email_used_created (email, used, created_at);

-- D) auth_rate_limits: cleanup/query performance
ALTER TABLE auth_rate_limits
  ADD KEY idx_action_updated (action_key, updated_at);
```

If any key/column already exists, skip that line and continue.

---

## 6) Stage Schema Verification SQL (Copy-Paste)

```sql
SHOW TABLES;
SHOW COLUMNS FROM payments;
SHOW COLUMNS FROM users;
SHOW COLUMNS FROM password_resets;
SHOW COLUMNS FROM auth_rate_limits;
```

Expected important columns:
- `payments`: `completed_at`, `failed_at`, `failure_reason`, `gateway_status`, `updated_at`
- `users`: `session_version`, `password_changed_at`, `updated_at`

---

## 7) Deploy to Stage

Run your deployment workflow with stage secrets/remote dir configured.

---

## 8) Stage Smoke + Functional Tests (Copy/Paste Checklist)

### 8.1 Quick URL checks

- `https://stage.aipromptbooks.in/`
- `https://stage.aipromptbooks.in/login.php`
- `https://stage.aipromptbooks.in/health-live.php` -> HTTP 200
- `https://stage.aipromptbooks.in/health-ready.php` -> HTTP 200

### 8.2 Auth flow checks

- Login works with valid user.
- Invalid login rate limits after repeated failures.
- Forgot/reset password works.
- Session invalidation works:
  - Log in from Browser A and B.
  - Reset password in A.
  - Access protected page in B -> should be forced to re-login.

### 8.3 Payment checks

- Create order -> complete payment -> verify -> setup account.
- Retry verify/capture on same order -> idempotent safe response (no duplicate completion).
- Payment endpoint abuse test -> eventually returns HTTP 429.

### 8.4 Failure observability

Trigger at least one controlled failure path (e.g., incomplete/cancelled flow), then verify corresponding row has:
- `status='failed'`
- `failed_at` populated
- `failure_reason` populated

---

## 9) Stage Exit Criteria (Must Pass All)

- [ ] Stage health-live returns 200
- [ ] Stage health-ready returns 200
- [ ] Auth flows pass (including session invalidation)
- [ ] Payment success flow passes
- [ ] Payment idempotency passes
- [ ] Payment rate limiting returns 429 under abuse
- [ ] Failure logging fields are populated on failed path
- [ ] No critical PHP/runtime errors in logs

If any item fails, do **not** proceed to production.

---

## 10) Production Promotion (Only After Stage Green)

Repeat same process on production:

1. Apply DB migrations to `u589539001_prod_aipromptbooks`
2. Set production env:
   - `APP_ENV=production`
   - `DB_NAME=u589539001_prod_aipromptbooks`
   - `DB_USER=u589539001_usr_prod_aipromptbooks`
3. Deploy to prod remote dir
4. Run prod smoke checks

---

## 11) Rollback (Minimal)

- Re-deploy last known good commit.
- Keep schema changes (they are backward-compatible for this app path).
- Re-check:
  - `/health-live.php`
  - `/health-ready.php`
  - login + one payment flow

---

## 12) Naming Convention (Final)

- Prod DB: `u589539001_prod_<app>`
- Stage DB: `u589539001_stage_<app>`
- Prod user: `u589539001_usr_prod_<app>`
- Stage user: `u589539001_usr_stage_<app>`

This is already enforced in runtime config guards.
