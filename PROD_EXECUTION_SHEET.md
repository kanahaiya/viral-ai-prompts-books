# Production Execution Sheet

This runbook is for **production only** and assumes stage is already fully green.

---

## 0) Production Gate (Must Be True Before You Start)

- [ ] Stage runbook completed successfully.
- [ ] Stage functional tests all passed.
- [ ] Final code already merged to `main`.
- [ ] GitHub production environment approvals are configured.
- [ ] Production DB/user already created:
  - `u589539001_prod_aipromptbooks`
  - `u589539001_usr_prod_aipromptbooks`

If any item is false, stop here.

---

## 1) Hostinger UI — Verify Production DB Mapping

In **hPanel -> Databases -> MySQL Databases** confirm:

- DB: `u589539001_prod_aipromptbooks`
- User: `u589539001_usr_prod_aipromptbooks`
- Grant: user has privileges on this DB only

---

## 2) Production Runtime Environment Variables

Set on production host:

- `APP_ENV=production`
- `DB_HOST=localhost`
- `DB_NAME=u589539001_prod_aipromptbooks`
- `DB_USER=u589539001_usr_prod_aipromptbooks`
- `DB_PASS=<prod-db-password>`
- Production payment keys (live), not sandbox/test.

Your runtime guards enforce `u589539001_prod_` and `u589539001_usr_prod_` prefixes.

---

## 3) GitHub Production Environment/Secrets Check

In GitHub `Settings -> Environments -> production`, verify:

- Required reviewers configured
- (Optional) wait timer configured
- Secrets present:
  - `HOSTINGER_SFTP_SERVER`
  - `HOSTINGER_SFTP_PORT`
  - `HOSTINGER_SFTP_USERNAME`
  - `HOSTINGER_SFTP_PASSWORD`
  - `HOSTINGER_SFTP_REMOTE_DIR=domains/aipromptbooks.in/public_html`
  - `SITE_SMOKE_URL=https://www.aipromptbooks.in`

---

## 4) Production DB Migration — Run in phpMyAdmin (Copy-Paste)

Select DB `u589539001_prod_aipromptbooks` and run:

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

If any column/index already exists, skip that line and continue.

---

## 5) Production Schema Verification SQL (Copy-Paste)

```sql
SHOW TABLES;
SHOW COLUMNS FROM payments;
SHOW COLUMNS FROM users;
SHOW COLUMNS FROM password_resets;
SHOW COLUMNS FROM auth_rate_limits;
```

Expected critical fields:
- `payments`: `completed_at`, `failed_at`, `failure_reason`, `gateway_status`, `updated_at`
- `users`: `session_version`, `password_changed_at`, `updated_at`

---

## 6) Deploy to Production

Deploy from `main` via your workflow:
- `.github/workflows/deploy-hostinger.yml`

Wait for workflow completion.

---

## 7) Production Post-Deploy Smoke Checks

Check:

- `https://www.aipromptbooks.in/`
- `https://www.aipromptbooks.in/login.php`
- `https://www.aipromptbooks.in/health-live.php` -> HTTP 200
- `https://www.aipromptbooks.in/health-ready.php` -> HTTP 200
- `http://aipromptbooks.in` redirects to `https://www.aipromptbooks.in`

---

## 8) Production Functional Verification Checklist

- [ ] Login works for existing active user.
- [ ] Password reset works.
- [ ] Multi-session invalidation works after reset.
- [ ] One real payment success flow works (order -> pay -> verify -> setup).
- [ ] Re-verify same order behaves idempotently (no duplicate completion).
- [ ] Rate-limit behavior returns 429 under repeated abuse attempts.
- [ ] Failed payment path records `failed_at` and `failure_reason`.

---

## 9) Go/No-Go Decision

**GO** only if all checks in sections 7 and 8 pass.

If any check fails -> **NO-GO**, execute rollback.

---

## 10) Rollback (Minimal, Safe)

1. Re-deploy last known good commit from workflow.
2. Keep DB schema changes (they are backward-compatible for current code paths).
3. Re-run smoke checks:
   - `/health-live.php`
   - `/health-ready.php`
   - login
   - one payment flow

---

## 11) Naming Convention (Enforced)

- Prod DB: `u589539001_prod_<app>`
- Prod user: `u589539001_usr_prod_<app>`
- Stage DB: `u589539001_stage_<app>`
- Stage user: `u589539001_usr_stage_<app>`

Runtime config guards already enforce these conventions.
