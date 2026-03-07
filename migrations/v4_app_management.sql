-- ═══════════════════════════════════════════════════════
-- Mico Sage — App Management / Subscription System v4
-- ═══════════════════════════════════════════════════════

-- Drop the old flat table
DROP TABLE IF EXISTS `app_subscriptions`;

-- ── App Categories ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL UNIQUE,
    `icon`        VARCHAR(60)  NOT NULL DEFAULT 'ph-cube',
    `color`       VARCHAR(20)  NOT NULL DEFAULT 'cyan',
    `description` TEXT DEFAULT NULL,
    `sort_order`  INT NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── App Products ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_products` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`     INT UNSIGNED DEFAULT NULL,
    `name`            VARCHAR(255) NOT NULL,
    `slug`            VARCHAR(255) NOT NULL UNIQUE,
    `version`         VARCHAR(30)  DEFAULT '1.0.0',
    `icon_url`        VARCHAR(500) DEFAULT NULL,
    `description`     TEXT DEFAULT NULL,
    `pricing_model`   ENUM('free','one_time','monthly','yearly') NOT NULL DEFAULT 'free',
    `price`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
    `total_installs`  INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (`category_id`),
    INDEX idx_active (`is_active`),
    CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `app_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── App Licenses ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_licenses` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id`        INT UNSIGNED NOT NULL,
    `license_key`       VARCHAR(128) NOT NULL UNIQUE,
    `label`             VARCHAR(255) DEFAULT '',
    `status`            ENUM('active','suspended','expired','revoked') NOT NULL DEFAULT 'active',
    `type`              ENUM('trial','standard','pro','enterprise') NOT NULL DEFAULT 'standard',
    `max_devices`       INT NOT NULL DEFAULT 1,
    `activated_devices` INT NOT NULL DEFAULT 0,
    `expires_at`        DATETIME DEFAULT NULL COMMENT 'NULL = never expires',
    `notes`             TEXT DEFAULT NULL,
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product (`product_id`),
    INDEX idx_status (`status`),
    INDEX idx_type (`type`),
    INDEX idx_key (`license_key`),
    CONSTRAINT `fk_license_product` FOREIGN KEY (`product_id`) REFERENCES `app_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── App Devices ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_devices` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `license_id`     INT UNSIGNED NOT NULL,
    `hardware_id`    VARCHAR(255) NOT NULL,
    `hostname`       VARCHAR(255) DEFAULT '',
    `os_info`        VARCHAR(255) DEFAULT '',
    `ip_address`     VARCHAR(45)  DEFAULT '',
    `app_version`    VARCHAR(30)  DEFAULT '',
    `is_online`      TINYINT(1) NOT NULL DEFAULT 0,
    `first_seen`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_heartbeat` DATETIME DEFAULT NULL,
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_license (`license_id`),
    INDEX idx_hw (`hardware_id`),
    INDEX idx_online (`is_online`),
    UNIQUE KEY `uq_license_hw` (`license_id`, `hardware_id`),
    CONSTRAINT `fk_device_license` FOREIGN KEY (`license_id`) REFERENCES `app_licenses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── App Device Logs ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_device_logs` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id`   INT UNSIGNED NOT NULL,
    `event_type`  ENUM('connect','disconnect','heartbeat','error') NOT NULL DEFAULT 'connect',
    `ip_address`  VARCHAR(45) DEFAULT '',
    `details`     TEXT DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_device (`device_id`),
    INDEX idx_event (`event_type`),
    INDEX idx_created (`created_at`),
    CONSTRAINT `fk_log_device` FOREIGN KEY (`device_id`) REFERENCES `app_devices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── App License Features ────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_license_features` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `license_id`    INT UNSIGNED NOT NULL,
    `feature_key`   VARCHAR(100) NOT NULL,
    `feature_value` VARCHAR(500) NOT NULL DEFAULT '',
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_license (`license_id`),
    UNIQUE KEY `uq_license_feature` (`license_id`, `feature_key`),
    CONSTRAINT `fk_feature_license` FOREIGN KEY (`license_id`) REFERENCES `app_licenses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed Default Categories ─────────────────────────────
INSERT INTO `app_categories` (`name`, `slug`, `icon`, `color`, `description`, `sort_order`) VALUES
('Desktop App',  'desktop-app',  'ph-desktop',        'cyan',    'Windows / macOS / Linux desktop applications', 1),
('Mobile App',   'mobile-app',   'ph-device-mobile',  'violet',  'iOS and Android mobile applications',          2),
('Web App',      'web-app',      'ph-globe',          'emerald', 'Browser-based web applications',               3),
('CLI Tool',     'cli-tool',     'ph-terminal',       'orange',  'Command-line interface tools',                  4);
