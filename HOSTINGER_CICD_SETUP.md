# Hostinger CI/CD Setup (GitHub Actions)

This project deploys automatically to Hostinger whenever commits are pushed to `main`.

## 1) Workflow location

- `.github/workflows/deploy-hostinger.yml`

Trigger:

- push to `main`
- manual run from GitHub Actions (`workflow_dispatch`)

## 2) Create GitHub repository secrets

In GitHub:

1. Open repository -> Settings -> Secrets and variables -> Actions.
2. Add these secrets:

- `HOSTINGER_FTP_SERVER` (for example: `ftp.yourdomain.com`)
- `HOSTINGER_FTP_USERNAME`
- `HOSTINGER_FTP_PASSWORD`
- `HOSTINGER_FTP_PORT` (usually `21` for FTPS on Hostinger shared hosting)

## 3) Hostinger FTP values source

In Hostinger hPanel:

1. Websites -> Manage (`aipromptbooks.in`)
2. Files -> FTP Accounts
3. Copy server host, username, password, and port

Current workflow uses FTP on port 21 for compatibility with Hostinger shared hosting.
If your Hostinger account supports stable FTPS from GitHub runners, you can switch protocol later.

## 4) Branch strategy

- Work in feature/stage branches.
- Merge to `main` only when production-ready.
- Every push to `main` deploys automatically to `/public_html/`.

## 5) Safety checks included

Before deploy, the workflow runs PHP syntax checks for core app files and API handlers.

## 6) What is intentionally excluded from deployment

The workflow excludes:

- `.github/`
- `.cursor/`
- `local.db`
- `setup-local.php`
- markdown docs
- local machine artifacts

## 7) First deployment verification

After first successful action run:

1. Visit `https://www.aipromptbooks.in`.
2. Verify homepage and checkout flow load.
3. Confirm `/admin/` still requires authentication.
