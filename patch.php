<?php
/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║  Mico Sage — Unified Database Migration & Cleanup Patch     ║
 * ║  Run once from CLI:  php patch.php                          ║
 * ║  Or via browser:     https://yoursite.com/patch.php         ║
 * ║  Self-deletes old scripts after successful migration.       ║
 * ╚══════════════════════════════════════════════════════════════╝
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$isCli = (php_sapi_name() === 'cli');
$nl = $isCli ? "\n" : "<br>";
$hr = $isCli ? str_repeat('─', 60) . "\n" : "<hr>";

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

// ─────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────
$results = ['ok' => 0, 'skip' => 0, 'fail' => 0];

function out(string $msg, string $type = 'info')
{
    global $nl, $isCli;
    $prefix = match ($type) {
            'ok' => $isCli ? "  ✅ " : "  ✅ ",
            'skip' => $isCli ? "  ⏭️  " : "  ⏭️  ",
            'fail' => $isCli ? "  ❌ " : "  ❌ ",
            'head' => $isCli ? "\n🔷 " : "<b>🔷 ",
            default => "  ℹ️  ",
        };
    $suffix = ($type === 'head' && !$isCli) ? "</b>" : "";
    echo $prefix . $msg . $suffix . $nl;
}

function tableExists(PDO $db, string $table): bool
{
    $stmt = $db->query("SHOW TABLES LIKE '$table'");
    return $stmt->rowCount() > 0;
}

function columnExists(PDO $db, string $table, string $column): bool
{
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    }
    catch (Exception $e) {
        return false;
    }
}

function safeExec(PDO $db, string $sql, string $label): void
{
    global $results;
    try {
        $db->exec($sql);
        out($label, 'ok');
        $results['ok']++;
    }
    catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'already exists') || str_contains($e->getMessage(), 'Duplicate')) {
            out("$label (already exists, skipped)", 'skip');
            $results['skip']++;
        }
        else {
            out("$label — " . $e->getMessage(), 'fail');
            $results['fail']++;
        }
    }
}

function addColumn(PDO $db, string $table, string $column, string $definition): void
{
    global $results;
    if (columnExists($db, $table, $column)) {
        out("Column `$table`.`$column` already exists", 'skip');
        $results['skip']++;
    }
    else {
        safeExec($db, "ALTER TABLE `$table` ADD COLUMN `$column` $definition", "Added `$column` to `$table`");
    }
}

// ─────────────────────────────────────────────────────────────
echo $hr;
out("Mico Sage — Database Migration Patch", 'head');
echo $hr;

try {
    $db = getDB();
    out("Database connection OK (" . DB_NAME . ")", 'ok');
}
catch (Exception $e) {
    out("Cannot connect to database: " . $e->getMessage(), 'fail');
    exit(1);
}

// ═════════════════════════════════════════════════════════════
// STEP 1 — Core admin tables & columns
// ═════════════════════════════════════════════════════════════
out("STEP 1 — Admin tables & columns", 'head');

// admins columns
addColumn($db, 'admins', 'role', "varchar(50) NOT NULL DEFAULT 'standard' AFTER `password`");
addColumn($db, 'admins', 'permissions', "text DEFAULT NULL AFTER `role`");
addColumn($db, 'admins', 'is_salesperson', "tinyint(1) NOT NULL DEFAULT 0 AFTER `role`");
addColumn($db, 'admins', 'recovery_email', "varchar(255) DEFAULT NULL");
addColumn($db, 'admins', 'recovery_phone', "varchar(50) DEFAULT NULL");
addColumn($db, 'admins', 'full_name', "varchar(100) DEFAULT NULL");
addColumn($db, 'admins', 'avatar_emoji', "varchar(10) DEFAULT NULL");

// Ensure first admin is super_admin
try {
    $db->exec("UPDATE `admins` SET `role` = 'super_admin' WHERE `id` = 1 AND `role` = 'standard'");
}
catch (Exception $e) {
}

// admin_activity_logs
safeExec($db, "
    CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `admin_id` int(11) DEFAULT NULL,
        `action_type` varchar(100) NOT NULL,
        `details` text DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `admin_activity_logs`");

// admin_ip_whitelist
safeExec($db, "
    CREATE TABLE IF NOT EXISTS `admin_ip_whitelist` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `admin_id` int(11) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `expires_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `admin_ip_whitelist`");

// ═════════════════════════════════════════════════════════════
// STEP 2 — Notifications
// ═════════════════════════════════════════════════════════════
out("STEP 2 — Notifications", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(50) NOT NULL,
        `title` varchar(255) NOT NULL,
        `content` text DEFAULT NULL,
        `link` varchar(255) DEFAULT NULL,
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `notifications`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `admin_notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(50) NOT NULL DEFAULT 'system',
        `title` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `link_url` varchar(255) DEFAULT '',
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `admin_notifications`");

// ═════════════════════════════════════════════════════════════
// STEP 11 — Cleanup old scripts
// ═════════════════════════════════════════════════════════════
out("STEP 11 — Cleanup old scripts", 'head');

// ═════════════════════════════════════════════════════════════
// STEP 3 — Contacts & Invoices
// ═════════════════════════════════════════════════════════════
out("STEP 3 — Contacts & Invoices", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `contacts` (
        `id` int(11) AUTO_INCREMENT PRIMARY KEY,
        `type` ENUM('company', 'individual') DEFAULT 'company',
        `name` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `vat_number` varchar(100) DEFAULT NULL,
        `website` varchar(255) DEFAULT NULL,
        `location` varchar(255) DEFAULT NULL,
        `country` varchar(100) DEFAULT NULL,
        `poc_details` text DEFAULT NULL,
        `source` varchar(100) DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `contacts`");

addColumn($db, 'invoices', 'contact_id', "int(11) DEFAULT NULL");
addColumn($db, 'invoices', 'invoice_currency', "varchar(10) DEFAULT 'USD'");
addColumn($db, 'invoices', 'payment_terms', "text DEFAULT NULL");
addColumn($db, 'invoices', 'salesperson_id', "int(11) DEFAULT NULL");
addColumn($db, 'invoices', 'payment_receipt_url', "varchar(500) DEFAULT NULL");
addColumn($db, 'invoices', 'amount_paid', "decimal(15,2) DEFAULT 0.00");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `announcement_history` (
        `id` int(11) AUTO_INCREMENT PRIMARY KEY,
        `message_en` text,
        `message_ar` text,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `announcement_history`");

// ═════════════════════════════════════════════════════════════
// STEP 4 — CRM tables
// ═════════════════════════════════════════════════════════════
out("STEP 4 — CRM tables", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_opportunities` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `contact_id` int(11) DEFAULT NULL,
        `email` varchar(255) DEFAULT '',
        `phone` varchar(50) DEFAULT '',
        `expected_revenue` decimal(15,2) DEFAULT 0.00,
        `revenue_type` varchar(50) DEFAULT 'Total',
        `stage` enum('New Lead', 'Know Your Client', 'Post Casting', 'Quote & Proposal', 'LPO', 'Casting & Production', 'Won', 'Lost') DEFAULT 'New Lead',
        `probability` decimal(5,2) DEFAULT 0.00,
        `expected_closing` date DEFAULT NULL,
        `tags` varchar(500) DEFAULT '',
        `priority` int(11) DEFAULT 0,
        `salesperson_id` int(11) DEFAULT NULL,
        `color_code` varchar(20) DEFAULT '',
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_opportunities`");

addColumn($db, 'crm_opportunities', 'salesperson_id', "int(11) DEFAULT NULL AFTER `contact_id`");
addColumn($db, 'crm_opportunities', 'color_code', "varchar(20) DEFAULT '' AFTER `notes`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_log_notes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `opportunity_id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        `note_type` enum('note', 'email', 'call', 'meeting') DEFAULT 'note',
        `content` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_log_notes`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_attachments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `linked_type` enum('opportunity', 'log_note', 'payment') NOT NULL,
        `linked_id` int(11) NOT NULL,
        `file_name` varchar(255) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_type` varchar(100) DEFAULT '',
        `file_size` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_attachments`");

// Fix enum if table already existed with old enum
try {
    $db->exec("ALTER TABLE `crm_attachments` MODIFY COLUMN `linked_type` ENUM('opportunity', 'log_note', 'payment') NOT NULL");
    out("Fixed `crm_attachments`.`linked_type` enum", 'ok');
    $results['ok']++;
}
catch (Exception $e) {
    $results['skip']++;
}

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `price` decimal(15,2) DEFAULT 0.00,
        `cost` decimal(15,2) DEFAULT 0.00,
        `category` varchar(100) DEFAULT '',
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_items`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_payments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `category` varchar(100) DEFAULT 'Expenditure',
        `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
        `payment_date` date NOT NULL,
        `opportunity_id` int(11) DEFAULT NULL,
        `admin_id` int(11) NOT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_payments`");

// ═════════════════════════════════════════════════════════════
// STEP 5 — Multi-salesperson junction tables
// ═════════════════════════════════════════════════════════════
out("STEP 5 — Multi-salesperson junction tables", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_opportunity_salespeople` (
        `opportunity_id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        PRIMARY KEY (`opportunity_id`, `admin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Table `crm_opportunity_salespeople`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_invoice_salespeople` (
        `invoice_id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        PRIMARY KEY (`invoice_id`, `admin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Table `crm_invoice_salespeople`");

// ═════════════════════════════════════════════════════════════
// STEP 6 — Chatbot schema fixes
// ═════════════════════════════════════════════════════════════
out("STEP 6 — Chatbot & Blog schema fixes", 'head');

if (tableExists($db, 'chatbot_sessions')) {
    addColumn($db, 'chatbot_sessions', 'user_email', "varchar(255) AFTER id");
    addColumn($db, 'chatbot_sessions', 'user_phone', "varchar(50) AFTER user_email");
    try {
        $db->exec("ALTER TABLE `chatbot_sessions` MODIFY COLUMN `session_uuid` varchar(100) NULL");
        $db->exec("ALTER TABLE `chatbot_sessions` MODIFY COLUMN `status` ENUM('Open', 'Closed') DEFAULT 'Open'");
    }
    catch (Exception $e) {
    }
}

if (tableExists($db, 'chatbot_nodes')) {
    addColumn($db, 'chatbot_nodes', 'pos_x', "int DEFAULT 0");
    addColumn($db, 'chatbot_nodes', 'pos_y', "int DEFAULT 0");
    addColumn($db, 'chatbot_nodes', 'reply_type', "ENUM('preset','user_input') DEFAULT 'preset'");
    addColumn($db, 'chatbot_nodes', 'input_var_name', "varchar(100) DEFAULT ''");
}

if (tableExists($db, 'blog_translations')) {
    addColumn($db, 'blog_translations', 'content', "LONGTEXT AFTER description");
}

// ═════════════════════════════════════════════════════════════
// STEP 7 — Visitor tracking table
// ═════════════════════════════════════════════════════════════
out("STEP 7 — Visitor tracking", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `site_visitors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `ip_address` varchar(45) DEFAULT NULL,
        `country` varchar(100) DEFAULT 'Unknown',
        `country_code` varchar(10) DEFAULT 'UNKNOWN',
        `city` varchar(100) DEFAULT 'Unknown',
        `region` varchar(100) DEFAULT 'Unknown',
        `isp` varchar(255) DEFAULT 'Unknown',
        `user_agent` text DEFAULT NULL,
        `page_url` varchar(500) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `site_visitors`");

// ═════════════════════════════════════════════════════════════
// STEP 8 — CRM Pipeline stages table
// ═════════════════════════════════════════════════════════════
out("STEP 8 — Pipeline stages", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `crm_pipeline_stages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `sort_order` int(11) DEFAULT 0,
        `is_collapsed` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `crm_pipeline_stages`");

// ═════════════════════════════════════════════════════════════
// STEP 9 — Success page content seeds
// ═════════════════════════════════════════════════════════════
out("STEP 9 — Seed success page content", 'head');

if (tableExists($db, 'contents')) {
    $seeds = [
        ['success_page_title', 'en', 'Success!'],
        ['success_page_title', 'ar', 'تم بنجاح!'],
        ['success_page_message', 'en', 'Thank you! Your booking request has been submitted. Our team will contact you shortly.'],
        ['success_page_message', 'ar', 'شكراً لك! تم استلام الطلب وسنتواصل معك قريباً.'],
        ['success_page_button', 'en', 'Return to Home'],
        ['success_page_button', 'ar', 'العودة للرئيسية'],
    ];
    $stmt = $db->prepare('INSERT IGNORE INTO contents (section_key, locale, value) VALUES (?, ?, ?)');
    foreach ($seeds as $s) {
        $stmt->execute($s);
    }
    out("Success page content seeded", 'ok');
    $results['ok']++;
}

// ═════════════════════════════════════════════════════════════
// STEP 10 — App Ecosystem (MicoStore & Licensing)
// ═════════════════════════════════════════════════════════════
out("STEP 10 — App Ecosystem (MicoStore & Licensing)", 'head');

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `icon` varchar(100) DEFAULT 'ph-cube',
        `color` varchar(50) DEFAULT 'cyan',
        `description` text DEFAULT NULL,
        `sort_order` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_categories`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_products` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `category_id` int(11) DEFAULT NULL,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `version` varchar(50) DEFAULT '1.0.0',
        `icon_url` varchar(500) DEFAULT NULL,
        `header_image` varchar(500) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `features` text DEFAULT NULL,
        `pricing_model` enum('free','paid','subscription','one_time','monthly','yearly') DEFAULT 'free',
        `price` decimal(15,2) DEFAULT 0.00,
        `compare_price` decimal(15,2) DEFAULT NULL,
        `download_url` varchar(500) DEFAULT NULL,
        `show_buy_button` tinyint(1) DEFAULT 1,
        `buy_url` varchar(500) DEFAULT NULL,
        `is_public` tinyint(1) DEFAULT 1,
        `show_price` tinyint(1) DEFAULT 1,
        `is_active` tinyint(1) DEFAULT 1,
        `total_installs` int(11) DEFAULT 0,
        `meta_description` text DEFAULT NULL,
        `meta_keywords` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        KEY `category_id` (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_products`");

// Ensure columns exist for older tables
addColumn($db, 'app_categories', 'icon', "varchar(100) DEFAULT 'ph-cube'");
addColumn($db, 'app_categories', 'color', "varchar(50) DEFAULT 'cyan'");
addColumn($db, 'app_categories', 'description', "text DEFAULT NULL");
addColumn($db, 'app_categories', 'sort_order', "int(11) DEFAULT 0");
addColumn($db, 'app_categories', 'is_active', "tinyint(1) DEFAULT 1");

addColumn($db, 'app_products', 'category_id', "int(11) DEFAULT NULL AFTER `id`");
addColumn($db, 'app_products', 'version', "varchar(50) DEFAULT '1.0.0' AFTER `slug`");
addColumn($db, 'app_products', 'icon_url', "varchar(500) DEFAULT NULL AFTER `version`");
addColumn($db, 'app_products', 'header_image', "varchar(500) DEFAULT NULL AFTER `icon_url`");
addColumn($db, 'app_products', 'features', "text DEFAULT NULL AFTER `description`");
addColumn($db, 'app_products', 'download_url', "varchar(500) DEFAULT NULL AFTER `price`");
addColumn($db, 'app_products', 'show_buy_button', "tinyint(1) DEFAULT 1 AFTER `download_url`");
addColumn($db, 'app_products', 'buy_url', "varchar(500) DEFAULT NULL AFTER `show_buy_button`");
addColumn($db, 'app_products', 'is_public', "tinyint(1) DEFAULT 1 AFTER `buy_url`");
addColumn($db, 'app_products', 'show_price', "tinyint(1) DEFAULT 1 AFTER `is_public`");
addColumn($db, 'app_products', 'total_installs', "int(11) DEFAULT 0 AFTER `is_active`");
addColumn($db, 'app_products', 'meta_description', "text DEFAULT NULL");
addColumn($db, 'app_products', 'meta_keywords', "text DEFAULT NULL");

// Rename download_count if it was added by mistake in a previous partial update
if (columnExists($db, 'app_products', 'download_count')) {
    safeExec($db, "ALTER TABLE `app_products` CHANGE `download_count` `total_installs` int(11) DEFAULT 0", "Renamed download_count to total_installs");
}

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_licenses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `license_key` varchar(128) NOT NULL,
        `label` varchar(255) DEFAULT '',
        `status` enum('active','suspended','expired','revoked') DEFAULT 'active',
        `type` enum('trial','standard','pro','enterprise') DEFAULT 'standard',
        `max_devices` int(11) DEFAULT 1,
        `activated_devices` int(11) DEFAULT 0,
        `use_count` int(11) DEFAULT 0,
        `max_use_count` int(11) DEFAULT 0,
        `expires_at` datetime DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `license_key` (`license_key`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_licenses`");

addColumn($db, 'app_licenses', 'use_count', "int(11) DEFAULT 0 AFTER `activated_devices`");
addColumn($db, 'app_licenses', 'max_use_count', "int(11) DEFAULT 0 AFTER `use_count`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_license_features` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `license_id` int(11) NOT NULL,
        `feature_key` varchar(100) NOT NULL,
        `feature_value` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `lic_feat` (`license_id`,`feature_key`),
        KEY `license_id` (`license_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_license_features`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_devices` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `license_id` int(11) NOT NULL,
        `hardware_id` varchar(255) NOT NULL,
        `hostname` varchar(255) DEFAULT '',
        `os_info` varchar(255) DEFAULT '',
        `ip_address` varchar(45) DEFAULT '',
        `app_version` varchar(30) DEFAULT '',
        `is_online` tinyint(1) DEFAULT 0,
        `first_seen` timestamp NOT NULL DEFAULT current_timestamp(),
        `last_heartbeat` datetime DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `license_hw` (`license_id`,`hardware_id`),
        KEY `license_id` (`license_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_devices`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_device_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `device_id` int(11) NOT NULL,
        `event_type` enum('connect','disconnect','heartbeat','error','download') DEFAULT 'connect',
        `ip_address` varchar(45) DEFAULT '',
        `details` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `device_id` (`device_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_device_logs`");

// Update enum if 'download' is missing
try {
    $db->exec("ALTER TABLE `app_device_logs` MODIFY COLUMN `event_type` enum('connect','disconnect','heartbeat','error','download') DEFAULT 'connect'");
}
catch (Exception $e) {
}

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_product_images` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `image_path` varchar(500) NOT NULL,
        `sort_order` int(11) DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_product_images`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_sections` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `sort_order` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_sections`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_section_products` (
        `section_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `sort_order` int(11) DEFAULT 0,
        PRIMARY KEY (`section_id`,`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_section_products`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_reviews` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `name` varchar(255) NOT NULL,
        `rating` int(1) NOT NULL DEFAULT 5,
        `comment` text NOT NULL,
        `admin_reply` text DEFAULT NULL,
        `status` enum('pending','approved','rejected') DEFAULT 'pending',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
", "Table `app_reviews`");

safeExec($db, "
    CREATE TABLE IF NOT EXISTS `app_product_translations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `locale` varchar(10) NOT NULL,
      `name` varchar(255) DEFAULT NULL,
      `short_description` text DEFAULT NULL,
      `description` text DEFAULT NULL,
      `features` text DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `product_locale` (`product_id`,`locale`),
      CONSTRAINT `fk_app_prod_trans` FOREIGN KEY (`product_id`) REFERENCES `app_products` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
", "Table `app_product_translations`");

addColumn($db, 'app_products', 'long_description', 'TEXT DEFAULT NULL AFTER description');
addColumn($db, 'app_products', 'os_requirements', "varchar(255) DEFAULT 'Windows 10+, 64-bit Architecture' AFTER long_description");
addColumn($db, 'app_product_translations', 'long_description', 'TEXT DEFAULT NULL AFTER description');
addColumn($db, 'app_product_translations', 'os_requirements', "varchar(255) DEFAULT 'Windows 10+, 64-bit Architecture' AFTER long_description");

    out("STEP 12 — Cleanup old scripts", 'head');

    $publicFilesToDelete = [
        __DIR__ . '/dbfix.php',
        __DIR__ . '/dbfix_notif.php',
        __DIR__ . '/dbfix_salesperson.php',
        __DIR__ . '/dbfix_salesperson_crm.php',
        __DIR__ . '/db_fix_app_translations.php',
        __DIR__ . '/db_fix_products.php',
        __DIR__ . '/db_trigger.php',
        __DIR__ . '/dbtest.php',
        __DIR__ . '/dbtest2.php',
        __DIR__ . '/get_schema.php',
        __DIR__ . '/seed_success.php',
    ];

$migrationFilesToDelete = [
    __DIR__ . '/migrations/add_multi_salesperson.php',
    __DIR__ . '/migrations/apply_blog_fix.php',
    __DIR__ . '/migrations/create_crm_payments_table.php',
    __DIR__ . '/migrations/create_ip_whitelist_table.php',
    __DIR__ . '/migrations/create_notifications_table.php',
    __DIR__ . '/migrations/debug_migration.php',
    __DIR__ . '/migrations/migrate_crm_features.php',
    __DIR__ . '/migrations/migrate_notifications_users.php',
    __DIR__ . '/migrations/run_email_schema.php',
    __DIR__ . '/migrations/run_update.php',
    __DIR__ . '/migrations/schema_sync.php',
    __DIR__ . '/migrations/alter_seo_and_blogs.sql',
    __DIR__ . '/migrations/email_schema.sql',
    __DIR__ . '/migrations/missing_content_inserts.sql',
    __DIR__ . '/migrations/seed_chatbot.sql',
];

// Files to KEEP in migrations/:
//   migration.sql   — full initial schema reference
//   setup.sql       — clean setup reference
//   v2_expansion.sql — expansion reference

$deleted = 0;
$allFiles = array_merge($publicFilesToDelete, $migrationFilesToDelete);

foreach ($allFiles as $file) {
    if (file_exists($file)) {
        if (@unlink($file)) {
            out("Deleted: " . basename($file), 'ok');
            $deleted++;
        }
        else {
            out("Cannot delete: " . basename($file) . " (check permissions)", 'fail');
            $results['fail']++;
        }
    }
    else {
        out("Already gone: " . basename($file), 'skip');
    }
}

out("Deleted $deleted old script(s)", 'info');

// ═════════════════════════════════════════════════════════════
// Summary
// ═════════════════════════════════════════════════════════════
echo $hr;
out("MIGRATION COMPLETE", 'head');
echo $nl;
out("Successful:  {$results['ok']}", 'ok');
out("Skipped:     {$results['skip']}", 'skip');
out("Failed:      {$results['fail']}", $results['fail'] > 0 ? 'fail' : 'ok');
echo $hr;

if ($results['fail'] === 0) {
    echo $nl;
    out("All migrations applied successfully. This patch file can now be safely deleted.", 'info');
    echo $nl;
    out("Files KEPT in migrations/:", 'head');
    out("  migration.sql     — Full initial schema (reference)", 'info');
    out("  setup.sql         — Clean database setup (reference)", 'info');
    out("  v2_expansion.sql  — V2 expansion schema (reference)", 'info');
}
