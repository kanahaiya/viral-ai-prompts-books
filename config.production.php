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

// ── Razorpay (live keys) ─────────────────────────────────────────────────────
define('RAZORPAY_KEY_ID',     envOrDefault('RAZORPAY_KEY_ID', 'rzp_live_XXXXXXXXXXXX'));
define('RAZORPAY_KEY_SECRET', envOrDefault('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX'));

// ── PayPal (live keys) ───────────────────────────────────────────────────────
define('PAYPAL_CLIENT_ID',     envOrDefault('PAYPAL_CLIENT_ID', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'));
define('PAYPAL_CLIENT_SECRET', envOrDefault('PAYPAL_CLIENT_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'));
define('PAYPAL_MODE',          envOrDefault('PAYPAL_MODE', 'live')); // sandbox/live
define('PAYPAL_API_URL',       envOrDefault('PAYPAL_API_URL', 'https://api-m.paypal.com'));

// ── WhatsApp Community ────────────────────────────────────────────────────────
define('WHATSAPP_INVITE_LINK', envOrDefault('WHATSAPP_INVITE_LINK', 'https://chat.whatsapp.com/XXXXXXXXXXXXXXXXXXXXXXXXX'));

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
