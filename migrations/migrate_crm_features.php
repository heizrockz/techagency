<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $db = getDB();
    
    // 1. CRM Opportunities (Pipeline)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `crm_opportunities` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `contact_id` int(11) DEFAULT NULL,
            `email` varchar(255) DEFAULT '',
            `phone` varchar(50) DEFAULT '',
            `expected_revenue` decimal(15,2) DEFAULT 0.00,
            `revenue_type` varchar(50) DEFAULT 'Total', -- e.g., 'Total', 'Monthly'
            `stage` enum('New Lead', 'Know Your Client', 'Post Casting', 'Quote & Proposal', 'LPO', 'Casting & Production', 'Won', 'Lost') DEFAULT 'New Lead',
            `probability` decimal(5,2) DEFAULT 0.00,
            `expected_closing` date DEFAULT NULL,
            `tags` varchar(500) DEFAULT '', -- comma separated or JSON
            `priority` int(11) DEFAULT 0, -- 0 to 3 stars
            `salesperson_id` int(11) DEFAULT NULL,
            `notes` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
            FOREIGN KEY (`salesperson_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // 2. CRM Log Notes (Timeline / Internal Notes)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `crm_log_notes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `opportunity_id` int(11) NOT NULL,
            `admin_id` int(11) NOT NULL,
            `note_type` enum('note', 'email', 'call', 'meeting') DEFAULT 'note',
            `content` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`opportunity_id`) REFERENCES `crm_opportunities` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. CRM Attachments (Files & Images with Previews)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `crm_attachments` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `linked_type` enum('opportunity', 'log_note') NOT NULL,
            `linked_id` int(11) NOT NULL,
            `file_name` varchar(255) NOT NULL,
            `file_path` varchar(500) NOT NULL,
            `file_type` varchar(100) DEFAULT '',
            `file_size` int(11) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Update Invoices for Payment Terms & Split Payments
    // Check if column exists, if not add it
    $stmt = $db->query("SHOW COLUMNS FROM `invoices` LIKE 'payment_receipt_url'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE `invoices` ADD COLUMN `payment_receipt_url` varchar(500) DEFAULT NULL");
        $db->exec("ALTER TABLE `invoices` ADD COLUMN `amount_paid` decimal(15,2) DEFAULT 0.00");
    }

    // 5. CRM Products (Standalone product list for opportunities/quotes)
    $db->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Successfully updated database schema for CRM features.\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
