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
  `book_ids_json`  JSON            DEFAULT NULL,    -- [1,3,5] for multi-book single checkout
  `amount`         DECIMAL(10,2)   NOT NULL,
  `currency`       ENUM('INR','USD') NOT NULL,
  `payment_method` ENUM('razorpay','cashfree','paypal') NOT NULL,
  `payment_id`     VARCHAR(255)    DEFAULT NULL,    -- razorpay payment_id / cashfree cf_order_id / paypal capture id
  `order_id`       VARCHAR(255)    DEFAULT NULL,    -- razorpay order_id / cashfree order_id / paypal order id
  `status`         ENUM('created','completed','failed') NOT NULL DEFAULT 'created',
  `setup_token`    CHAR(64)        DEFAULT NULL,    -- sha256(one-time setup token)
  `setup_used`     TINYINT(1)      NOT NULL DEFAULT 0,
  `completed_at`   TIMESTAMP       NULL DEFAULT NULL,
  `failed_at`      TIMESTAMP       NULL DEFAULT NULL,
  `failure_reason` VARCHAR(255)    DEFAULT NULL,
  `gateway_status` VARCHAR(64)     DEFAULT NULL,
  `updated_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email`       (`email`),
  KEY `idx_email_created` (`email`, `created_at`),
  KEY `idx_status_created` (`status`, `created_at`),
  KEY `idx_payment_lookup` (`setup_token`, `setup_used`, `status`, `completed_at`),
  UNIQUE KEY `uq_provider_order` (`payment_method`, `order_id`),
  UNIQUE KEY `uq_provider_payment` (`payment_method`, `payment_id`),
  UNIQUE KEY `uq_setup_token` (`setup_token`)
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
  `payment_method` ENUM('razorpay','cashfree','paypal') NOT NULL,
  `payment_id`     VARCHAR(255)    DEFAULT NULL,
  `session_version` INT UNSIGNED   NOT NULL DEFAULT 1,
  `password_changed_at` TIMESTAMP  NULL DEFAULT NULL,
  `status`         ENUM('active','suspended') NOT NULL DEFAULT 'active',
  `last_login`     TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_status_created` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Password reset tokens ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(255) NOT NULL,
  `token`      CHAR(64)     NOT NULL,               -- sha256(reset token)
  `used`       TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_token` (`token`),
  KEY `idx_email_used_created` (`email`, `used`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Auth rate limits (DB-backed anti-abuse) ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `auth_rate_limits` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bucket_key`      CHAR(64)        NOT NULL,
  `action_key`      VARCHAR(64)     NOT NULL,
  `identifier_hash` CHAR(64)        NOT NULL,
  `attempt_count`   INT UNSIGNED    NOT NULL DEFAULT 0,
  `window_start`    INT UNSIGNED    NOT NULL,
  `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bucket_key` (`bucket_key`),
  KEY `idx_updated_at` (`updated_at`),
  KEY `idx_action_updated` (`action_key`, `updated_at`)
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
    CASE u.currency WHEN 'INR' THEN 299 ELSE 9.00 END
  ELSE
    CASE u.currency WHEN 'INR' THEN 99 ELSE 2.99 END
  END AS `revenue`
FROM users u
ORDER BY u.created_at DESC;
