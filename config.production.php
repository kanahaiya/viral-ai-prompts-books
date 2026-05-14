<?php
// ─────────────────────────────────────────────────────────────────────────────
// config.php  —  Replace ALL placeholder values before going live
// ─────────────────────────────────────────────────────────────────────────────

// ── Database (Hostinger cPanel → MySQL Databases) ─────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');       // e.g. u123456789_prompts
define('DB_USER', 'your_db_user');       // e.g. u123456789_admin
define('DB_PASS', 'your_db_password');

// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_URL',  'https://yourdomain.com');   // no trailing slash
define('SITE_NAME', 'AI Prompt Books');

// ── Books path (relative to this file) ───────────────────────────────────────
// On Hostinger: upload all chapter-XX-pdf.html files into /books/ folder
define('BOOKS_DIR', __DIR__ . '/books/');

// ── Razorpay (India payments — https://dashboard.razorpay.com/app/keys) ──────
define('RAZORPAY_KEY_ID',     'rzp_live_XXXXXXXXXXXX');
define('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX');

// ── PayPal (International — https://developer.paypal.com/dashboard/) ─────────
define('PAYPAL_CLIENT_ID',     'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('PAYPAL_CLIENT_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('PAYPAL_MODE',          'live');  // 'sandbox' for testing, 'live' for production
define('PAYPAL_API_URL',       'https://api-m.paypal.com');   // change to https://api-m.sandbox.paypal.com for sandbox

// ── WhatsApp Community ────────────────────────────────────────────────────────
define('WHATSAPP_INVITE_LINK', 'https://chat.whatsapp.com/XXXXXXXXXXXXXXXXXXXXXXXXX');

// ── Pricing ───────────────────────────────────────────────────────────────────
define('PRICE_SINGLE_INR',  99);
define('PRICE_BUNDLE_INR',  199);
define('PRICE_SINGLE_USD',  2.99);
define('PRICE_BUNDLE_USD',  9.00);

// ── Admin credentials (change these!) ────────────────────────────────────────
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'change_this_password_now');  // stored as plain text for .htpasswd style check

// ── Session security ──────────────────────────────────────────────────────────
define('SESSION_NAME',    'aipb_session');
define('COOKIE_LIFETIME', 60 * 60 * 24 * 30);  // 30 days

// ── Error reporting (production-safe defaults) ────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 0);
