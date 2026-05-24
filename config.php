<?php
// ─────────────────────────────────────────────────────────────────────────────
// config.php  —  LOCAL DEVELOPMENT (SQLite, no real payment keys)
// Production config is saved as config.production.php
// ─────────────────────────────────────────────────────────────────────────────

// ── Database — SQLite for local dev (no MySQL needed) ─────────────────────────
define('DB_DSN',  'sqlite:' . __DIR__ . '/local.db');
define('DB_HOST', 'localhost');   // unused with SQLite
define('DB_NAME', 'local');
define('DB_USER', '');
define('DB_PASS', '');

// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_URL',  'http://localhost:8080');
define('SITE_NAME', 'AI Prompt Books');

// ── Books path ────────────────────────────────────────────────────────────────
define('BOOKS_DIR', __DIR__ . '/books/');

// ── Razorpay (paused for now) ──────────────────────────────────────────────────
// define('RAZORPAY_KEY_ID',     'rzp_test_SqXAfiLToxWQLI');
// define('RAZORPAY_KEY_SECRET', 'zpFXxm3RaCUh568uiLC0PRlD');

// ── Cashfree (test keys) ───────────────────────────────────────────────────────
define('CASHFREE_APP_ID',     'TEST110811490ab21522763ffa8f50eb94118011');
define('CASHFREE_SECRET_KEY', 'cfsk_ma_test_92d6c2576fc97f4f501aa9f8513f895c_0ad79cec');
define('CASHFREE_ENV',        'sandbox'); // sandbox/live

// ── PayPal (sandbox — get real ones from developer.paypal.com) ────────────────
define('PAYPAL_CLIENT_ID',     'sandbox_paypal_client_id');
define('PAYPAL_CLIENT_SECRET', 'sandbox_paypal_client_secret');
define('PAYPAL_MODE',          'sandbox');
define('PAYPAL_API_URL',       'https://api-m.sandbox.paypal.com');

// ── Community Link (Telegram) ────────────────────────────────────────────────
define('TELEGRAM_INVITE_LINK', 'https://t.me/+48-IpU25IzI4Y2E1');

// ── Meta Pixel ────────────────────────────────────────────────────────────────
define('META_PIXEL_ID', '');
define('ENABLE_META_PIXEL', false);

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
