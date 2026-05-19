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

// ── Razorpay (test keys) ───────────────────────────────────────────────────────
define('RAZORPAY_KEY_ID',     'rzp_test_SqXAfiLToxWQLI');
define('RAZORPAY_KEY_SECRET', 'zpFXxm3RaCUh568uiLC0PRlD');

// ── PayPal (sandbox — get real ones from developer.paypal.com) ────────────────
define('PAYPAL_CLIENT_ID',     'sandbox_paypal_client_id');
define('PAYPAL_CLIENT_SECRET', 'sandbox_paypal_client_secret');
define('PAYPAL_MODE',          'sandbox');
define('PAYPAL_API_URL',       'https://api-m.sandbox.paypal.com');

// ── WhatsApp ──────────────────────────────────────────────────────────────────
define('WHATSAPP_INVITE_LINK', 'https://chat.whatsapp.com/test-link');

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
