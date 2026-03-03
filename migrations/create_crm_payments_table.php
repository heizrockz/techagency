<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure only admin can run this
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

try {
    $db = getDB();
    
    // Create crm_payments table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `crm_payments` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `category` varchar(100) DEFAULT 'Expenditure', -- e.g., 'Expenditure', 'Salary', 'Office', 'Marketing'
            `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
            `payment_date` date NOT NULL,
            `opportunity_id` int(11) DEFAULT NULL,
            `admin_id` int(11) NOT NULL,
            `notes` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`opportunity_id`) REFERENCES `crm_opportunities` (`id`) ON DELETE SET NULL,
            FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Successfully created crm_payments table.\n";
    
    // Self-destruct or provide a link to go back
    echo "<br><a href='/admin/crm_payments'>Go to CRM Payments</a>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
