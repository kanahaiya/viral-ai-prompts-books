<?php
// ─────────────────────────────────────────────────────────────────────────────
// config.production.php — Production runtime config
// Reads secrets from environment variables first.
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Return environment variable value when available, otherwise default.
 */
function envOrDefault(string $name, string $default): string {
    $value = getenv($name);
    if ($value === false) {
        return $default;
    }
    return trim((string)$value);
}

// ── Database (set in hosting env whenever possible) ──────────────────────────
define('DB_HOST', envOrDefault('DB_HOST', 'localhost'));
define('DB_NAME', envOrDefault('DB_NAME', 'your_db_name'));
define('DB_USER', envOrDefault('DB_USER', 'your_db_user'));
define('DB_PASS', envOrDefault('DB_PASS', 'your_db_password'));

// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_URL',  envOrDefault('SITE_URL', 'https://www.aipromptbooks.in'));
define('SITE_NAME', envOrDefault('SITE_NAME', 'AI Prompt Books'));

// ── Books path (relative to this file) ───────────────────────────────────────
define('BOOKS_DIR', __DIR__ . '/books/');

// ── Razorpay (paused for now) ─────────────────────────────────────────────────
// define('RAZORPAY_KEY_ID',     envOrDefault('RAZORPAY_KEY_ID', 'rzp_live_XXXXXXXXXXXX'));
// define('RAZORPAY_KEY_SECRET', envOrDefault('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX'));

// ── Cashfree (live/test keys) ─────────────────────────────────────────────────
define('CASHFREE_APP_ID',     envOrDefault('CASHFREE_APP_ID', 'replace_with_cashfree_app_id'));
define('CASHFREE_SECRET_KEY', envOrDefault('CASHFREE_SECRET_KEY', 'replace_with_cashfree_secret_key'));
define('CASHFREE_ENV',        envOrDefault('CASHFREE_ENV', 'live')); // sandbox/live

// ── PayPal (live keys) ───────────────────────────────────────────────────────
define('PAYPAL_CLIENT_ID',     envOrDefault('PAYPAL_CLIENT_ID', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'));
define('PAYPAL_CLIENT_SECRET', envOrDefault('PAYPAL_CLIENT_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'));
define('PAYPAL_MODE',          envOrDefault('PAYPAL_MODE', 'live')); // sandbox/live
define('PAYPAL_API_URL',       envOrDefault('PAYPAL_API_URL', 'https://api-m.paypal.com'));

// ── Community Link (Telegram) ────────────────────────────────────────────────
define('TELEGRAM_INVITE_LINK', envOrDefault('TELEGRAM_INVITE_LINK', 'https://t.me/+48-IpU25IzI4Y2E1'));

// ── Email (Brevo transactional API) ───────────────────────────────────────────
define('BREVO_API_KEY', envOrDefault('BREVO_API_KEY', 'brevo_api_key_placeholder'));
define('MAIL_FROM_EMAIL', envOrDefault('MAIL_FROM_EMAIL', 'support@aipromptbooks.in'));
define('MAIL_FROM_NAME', envOrDefault('MAIL_FROM_NAME', 'AI Prompt Books'));

// ── Pricing ───────────────────────────────────────────────────────────────────
define('PRICE_SINGLE_INR',  99);
define('PRICE_BUNDLE_INR',  299);
define('PRICE_SINGLE_USD',  2.99);
define('PRICE_BUNDLE_USD',  9.00);

// ── Admin credentials ─────────────────────────────────────────────────────────
define('ADMIN_USER', envOrDefault('ADMIN_USER', 'admin'));
define('ADMIN_PASS', envOrDefault('ADMIN_PASS', 'change_this_password_now'));

// ── Session security ──────────────────────────────────────────────────────────
define('SESSION_NAME',    envOrDefault('SESSION_NAME', 'aipb_session'));
define('COOKIE_LIFETIME', 60 * 60 * 24 * 30); // 30 days

// ── Error reporting (production-safe defaults) ───────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');
