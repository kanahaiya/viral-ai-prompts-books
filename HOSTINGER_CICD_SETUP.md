# Hostinger CI/CD Setup (GitHub Actions)

This project deploys automatically to Hostinger over SFTP whenever commits are pushed to `main`.

## 1) Workflow location

- `.github/workflows/deploy-hostinger.yml`

Trigger:

- push to `main`
- manual run from GitHub Actions (`workflow_dispatch`)

## 2) Create GitHub repository secrets

In GitHub:

1. Open repository -> Settings -> Secrets and variables -> Actions.
2. Add these secrets:

- `HOSTINGER_SFTP_SERVER` (for example: `147.93.17.220`)
- `HOSTINGER_SFTP_USERNAME`
- `HOSTINGER_SFTP_PASSWORD`
- `HOSTINGER_SFTP_PORT` (from hPanel SSH Access, often a custom port like `65002`)
- `HOSTINGER_SFTP_REMOTE_DIR` (for example: `domains/aipromptbooks.in/public_html`)
- Optional: `SITE_SMOKE_URL` (defaults to `https://www.aipromptbooks.in`)

Backward compatibility:
- Existing `HOSTINGER_FTP_*` secrets still work as fallback, but new setup should use `HOSTINGER_SFTP_*`.

## 3) Hostinger SFTP values source

In Hostinger hPanel:

1. Websites -> Manage (`aipromptbooks.in`)
2. Advanced -> SSH Access
3. Copy host/IP, username, password, and SSH port
4. Set `HOSTINGER_SFTP_REMOTE_DIR` to your writable web root (usually `domains/aipromptbooks.in/public_html`)

## 4) Branch strategy

- Work in feature/stage branches.
- Merge to `main` only when production-ready.
- Every push to `main` deploys automatically to `HOSTINGER_SFTP_REMOTE_DIR`.

## 5) Safety checks included

Before deploy, the workflow runs:
- repository-wide PHP syntax lint
- SFTP connectivity preflight
- deploy to configured remote dir
- post-deploy smoke checks (`/`, `/login.php`, API non-500, canonical redirect)

## 6) What is intentionally excluded from deployment

The workflow excludes:

- `.github/`
- `.cursor/`
- `.git/`
- `.env*` files
- `.htaccess` (managed directly on Hostinger for server-specific env/caching rules)
- `config.production.php` (managed directly on Hostinger for runtime secrets)
- `.ftp-deploy-sync-state.json`
- `local.db`
- `db.sql`
- `setup-local.php`
- markdown docs
- local machine artifacts

## 7) First deployment verification

After first successful action run:

1. Visit `https://www.aipromptbooks.in`.
2. Verify homepage and checkout flow load.
3. Confirm `/admin/` still requires authentication.
