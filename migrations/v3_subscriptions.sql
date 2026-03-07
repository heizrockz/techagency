-- ═══════════════════════════════════════════════════════
-- Mico Sage — App Subscription / License Management
-- ═══════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `app_subscriptions` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_code`    VARCHAR(128) NOT NULL UNIQUE,
    `label`           VARCHAR(255) DEFAULT '' COMMENT 'Admin-friendly name for this device',
    `status`          ENUM('active','blocked','expired') NOT NULL DEFAULT 'active',
    `type`            ENUM('free','monthly','yearly') NOT NULL DEFAULT 'free',
    `recovery_limit`  INT NOT NULL DEFAULT 10 COMMENT '-1 = unlimited',
    `recoveries_used` INT NOT NULL DEFAULT 0,
    `about_text`      TEXT DEFAULT NULL COMMENT 'Dynamic text shown in app About dialog',
    `last_seen`       DATETIME DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (`status`),
    INDEX idx_product_code (`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
