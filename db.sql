-- ─────────────────────────────────────────────────────────────────────────────
-- db.sql  —  Run this in Hostinger cPanel → phpMyAdmin
-- ─────────────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ── Payments table (created first — no FK dependency) ─────────────────────────
CREATE TABLE IF NOT EXISTS `payments` (
  `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `email`          VARCHAR(255)    NOT NULL,
  `name`           VARCHAR(255)    DEFAULT NULL,
  `plan`           ENUM('single','bundle') NOT NULL,
  `book_id`        TINYINT UNSIGNED DEFAULT NULL,   -- NULL means bundle
  `amount`         DECIMAL(10,2)   NOT NULL,
  `currency`       ENUM('INR','USD') NOT NULL,
  `payment_method` ENUM('razorpay','paypal') NOT NULL,
  `payment_id`     VARCHAR(255)    DEFAULT NULL,    -- razorpay payment_id / paypal capture id
  `order_id`       VARCHAR(255)    DEFAULT NULL,    -- razorpay order_id / paypal order id
  `status`         ENUM('created','completed','failed') NOT NULL DEFAULT 'created',
  `setup_token`    CHAR(64)        DEFAULT NULL,    -- one-time token sent to setup-account.php
  `setup_used`     TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email`       (`email`),
  KEY `idx_order_id`    (`order_id`),
  KEY `idx_payment_id`  (`payment_id`),
  KEY `idx_setup_token` (`setup_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Users table ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `email`          VARCHAR(255)    NOT NULL,
  `name`           VARCHAR(255)    DEFAULT NULL,
  `password_hash`  VARCHAR(255)    NOT NULL,
  `plan`           ENUM('single','bundle') NOT NULL,
  `books_access`   JSON            DEFAULT NULL,    -- [1,3,5] for single; NULL = all (bundle)
  `currency`       ENUM('INR','USD') NOT NULL,
  `payment_method` ENUM('razorpay','paypal') NOT NULL,
  `payment_id`     VARCHAR(255)    DEFAULT NULL,
  `status`         ENUM('active','suspended') NOT NULL DEFAULT 'active',
  `last_login`     TIMESTAMP       NULL DEFAULT NULL,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Sample view: admin overview ───────────────────────────────────────────────
CREATE OR REPLACE VIEW `v_admin_overview` AS
SELECT
  u.id,
  u.email,
  u.name,
  u.plan,
  u.currency,
  u.payment_method,
  u.status,
  u.created_at,
  u.last_login,
  CASE WHEN u.plan = 'bundle' THEN
    CASE u.currency WHEN 'INR' THEN 199 ELSE 9.00 END
  ELSE
    CASE u.currency WHEN 'INR' THEN 99 ELSE 2.99 END
  END AS `revenue`
FROM users u
ORDER BY u.created_at DESC;
