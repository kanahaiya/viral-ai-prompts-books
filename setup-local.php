<?php
// ─────────────────────────────────────────────────────────────────────────────
// setup-local.php  —  One-time script to create SQLite DB + seed test accounts
// Run: php setup-local.php
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config.php';

if (!defined('DB_DSN') || !str_starts_with(DB_DSN, 'sqlite:')) {
    die("❌ This script only runs with SQLite config. Check config.php\n");
}

$db = new PDO(DB_DSN, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// ── Schema ────────────────────────────────────────────────────────────────────
$db->exec("CREATE TABLE IF NOT EXISTS payments (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    email          TEXT NOT NULL,
    name           TEXT,
    plan           TEXT NOT NULL,
    book_id        INTEGER,
    book_ids_json  TEXT,
    amount         REAL NOT NULL DEFAULT 0,
    currency       TEXT NOT NULL DEFAULT 'INR',
    payment_method TEXT NOT NULL DEFAULT 'cashfree',
    payment_id     TEXT,
    order_id       TEXT,
    status         TEXT NOT NULL DEFAULT 'created',
    setup_token    TEXT,
    setup_used     INTEGER NOT NULL DEFAULT 0,
    created_at     TEXT NOT NULL DEFAULT (datetime('now'))
)");

// Backfill old local DBs that were created before book_ids_json existed.
$paymentsColumns = $db->query("PRAGMA table_info(payments)")->fetchAll(PDO::FETCH_ASSOC);
$hasBookIdsJsonColumn = false;
foreach ($paymentsColumns as $columnInfo) {
    if (($columnInfo['name'] ?? '') === 'book_ids_json') {
        $hasBookIdsJsonColumn = true;
        break;
    }
}
if (!$hasBookIdsJsonColumn) {
    $db->exec("ALTER TABLE payments ADD COLUMN book_ids_json TEXT");
}

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    email          TEXT NOT NULL UNIQUE,
    name           TEXT,
    password_hash  TEXT NOT NULL,
    plan           TEXT NOT NULL,
    books_access   TEXT,
    currency       TEXT NOT NULL DEFAULT 'INR',
    payment_method TEXT NOT NULL DEFAULT 'cashfree',
    payment_id     TEXT,
    status         TEXT NOT NULL DEFAULT 'active',
    last_login     TEXT,
    created_at     TEXT NOT NULL DEFAULT (datetime('now'))
)");

echo "✅ Tables created\n";

// ── Seed test users ───────────────────────────────────────────────────────────
$users = [
    ['bundle@test.com', 'Bundle Tester', 'test123', 'bundle', null],
    ['single@test.com', 'Single Tester', 'test123', 'single', '[1,2]'],
];

$stmt = $db->prepare(
    'INSERT OR IGNORE INTO users (email, name, password_hash, plan, books_access, currency, payment_method, payment_id)
     VALUES (?, ?, ?, ?, ?, "INR", "cashfree", "local_test")'
);

foreach ($users as [$email, $name, $pass, $plan, $books]) {
    $stmt->execute([$email, $name, password_hash($pass, PASSWORD_DEFAULT), $plan, $books]);
}

echo "✅ Test accounts seeded\n\n";
echo "─────────────────────────────────────────────\n";
echo "  Test accounts (password: test123)\n";
echo "─────────────────────────────────────────────\n";
echo "  BUNDLE user  : bundle@test.com\n";
echo "                 → All 11 books + bonus unlocked\n\n";
echo "  SINGLE user  : single@test.com\n";
echo "                 → Books 1 & 2 only (rest locked)\n";
echo "─────────────────────────────────────────────\n";
echo "\nNow run:  php -S localhost:8080 -t " . __DIR__ . "\n";
echo "Open:     http://localhost:8080\n\n";
