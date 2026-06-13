<?php
// ─────────────────────────────────────────────────────────────────────────────
// init-db.php  —  Initialize SQLite database for local development
// ─────────────────────────────────────────────────────────────────────────────

require_once __DIR__ . '/config.php';

try {
    // Create/connect to SQLite database
    $db = new PDO(DB_DSN);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[✓] Connected to SQLite database\n";

    // ── Payments table ────────────────────────────────────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            email           TEXT NOT NULL,
            name            TEXT,
            plan            TEXT CHECK(plan IN ('single', 'bundle')),
            book_id         INTEGER,
            book_ids_json   TEXT,
            amount          REAL NOT NULL,
            currency        TEXT CHECK(currency IN ('INR', 'USD')),
            payment_method  TEXT CHECK(payment_method IN ('razorpay', 'cashfree', 'paypal')),
            payment_id      TEXT,
            order_id        TEXT,
            status          TEXT DEFAULT 'created' CHECK(status IN ('created', 'completed', 'failed')),
            setup_token     TEXT,
            setup_used      INTEGER DEFAULT 0,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "[✓] Created 'payments' table\n";

    // Create index on order_id
    $db->exec("CREATE INDEX IF NOT EXISTS idx_order_id ON payments(order_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_email ON payments(email)");

    // ── Users table ───────────────────────────────────────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            email           TEXT NOT NULL UNIQUE,
            name            TEXT,
            password_hash   TEXT NOT NULL,
            plan            TEXT CHECK(plan IN ('single', 'bundle')),
            books_access    TEXT,
            currency        TEXT CHECK(currency IN ('INR', 'USD')),
            payment_method  TEXT CHECK(payment_method IN ('razorpay', 'cashfree', 'paypal')),
            payment_id      TEXT,
            status          TEXT DEFAULT 'active' CHECK(status IN ('active', 'suspended')),
            last_login      TIMESTAMP,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "[✓] Created 'users' table\n";

    // ── Password reset tokens ─────────────────────────────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            email       TEXT NOT NULL,
            token       TEXT NOT NULL,
            used        INTEGER DEFAULT 0,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "[✓] Created 'password_resets' table\n";

    // Create indexes
    $db->exec("CREATE INDEX IF NOT EXISTS idx_email ON password_resets(email)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_token ON password_resets(token)");

    echo "\n[✓] Database initialization complete!\n";
    echo "Database file: " . DB_DSN . "\n";

} catch (PDOException $e) {
    echo "[✗] Database error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
