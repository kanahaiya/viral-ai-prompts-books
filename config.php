<?php
// ─────────────────────────────────────────────────────────────────────────────
// config.php  —  LOCAL DEVELOPMENT (SQLite, no real payment keys)
// Production config is saved as config.production.php
// ─────────────────────────────────────────────────────────────────────────────

// ── Database — SQLite for local dev (no MySQL needed) ─────────────────────────
define('DB_DSN',  'sqlite:' . __DIR__ . '/local.db');
define('DB_HOST', 'localhost');   // unused with SQLite
define('DB_NAME', 'u589539001_stage_aipromptbooks');
define('DB_USER', 'u589539001_usr_stage_aipromptbooks');
define('DB_PASS', '');
define('DB_NAMESPACE_PREFIX', 'u589539001_');
define('DB_STAGE_PREFIX', DB_NAMESPACE_PREFIX . 'stage_');
define('DB_STAGE_USER_PREFIX', DB_NAMESPACE_PREFIX . 'usr_stage_');

if (!defined('DB_DSN') || stripos((string)DB_DSN, 'sqlite:') !== 0) {
    $isAllowedNonProductionDb = str_starts_with(DB_NAME, DB_STAGE_PREFIX);
    if (!$isAllowedNonProductionDb) {
        throw new RuntimeException('Non-production DB_NAME must start with ' . DB_STAGE_PREFIX);
    }
    $isAllowedNonProductionDbUser = str_starts_with(DB_USER, DB_STAGE_USER_PREFIX);
    if (!$isAllowedNonProductionDbUser) {
        throw new RuntimeException('Non-production DB_USER must start with ' . DB_STAGE_USER_PREFIX);
    }
}

// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_URL',  'http://localhost:8080');
define('SITE_NAME', 'AI Prompt Books');

// ── Books path ────────────────────────────────────────────────────────────────
define('BOOKS_DIR', __DIR__ . '/books/');

// ── Payment provider switch ─────────────────────────────────────────────────────
// Allowed values: razorpay | cashfree
define('PAYMENT_PROVIDER', 'razorpay');

// ── Razorpay (test keys for staging/local) ────────────────────────────────────
define('RAZORPAY_KEY_ID',     'replace_with_razorpay_test_key_id');
define('RAZORPAY_KEY_SECRET', 'replace_with_razorpay_test_key_secret');

// ── Cashfree (test keys) ───────────────────────────────────────────────────────
define('CASHFREE_APP_ID',     'replace_with_cashfree_test_app_id');
define('CASHFREE_SECRET_KEY', 'replace_with_cashfree_test_secret_key');
define('CASHFREE_ENV',        'sandbox'); // sandbox/live

// ── PayPal (sandbox — get real ones from developer.paypal.com) ────────────────
define('PAYPAL_CLIENT_ID',     'sandbox_paypal_client_id');
define('PAYPAL_CLIENT_SECRET', 'sandbox_paypal_client_secret');
define('PAYPAL_MODE',          'sandbox');
define('PAYPAL_API_URL',       'https://api-m.sandbox.paypal.com');

// ── Community Link (Telegram) ────────────────────────────────────────────────
define('TELEGRAM_INVITE_LINK', 'https://t.me/+48-IpU25IzI4Y2E1');

// ── Email (Brevo) ─────────────────────────────────────────────────────────────
define('BREVO_API_KEY', 'brevo_api_key_placeholder');
define('MAIL_FROM_EMAIL', 'support@aipromptbooks.in');
define('MAIL_FROM_NAME', 'AI Prompt Books');

// ── Pricing ───────────────────────────────────────────────────────────────────
define('PRICE_SINGLE_INR',  99);
define('PRICE_BUNDLE_INR',  299);
define('PRICE_SINGLE_USD',  2.99);
define('PRICE_BUNDLE_USD',  9.00);

// ── Admin ─────────────────────────────────────────────────────────────────────
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'change_this_in_local_env');

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',    'aipb_session');
define('COOKIE_LIFETIME', 60 * 60 * 24 * 30);

// ── Debug (disable on live site) ──────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
