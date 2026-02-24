<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/includes/db.php';

try {
    $db = getDB();

    $queries = [
        "ALTER TABLE admins ADD COLUMN recovery_email VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE admins ADD COLUMN recovery_phone VARCHAR(50) DEFAULT NULL",
        "ALTER TABLE admins ADD COLUMN full_name VARCHAR(100) DEFAULT NULL",
        "ALTER TABLE admins ADD COLUMN avatar_emoji VARCHAR(10) DEFAULT '👤'",
        
        "CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('company', 'individual') DEFAULT 'company',
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            vat_number VARCHAR(100) DEFAULT NULL,
            website VARCHAR(255) DEFAULT NULL,
            location VARCHAR(255) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            poc_details TEXT DEFAULT NULL,
            source VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "ALTER TABLE invoices ADD COLUMN contact_id INT DEFAULT NULL",
        "ALTER TABLE invoices ADD COLUMN invoice_currency VARCHAR(10) DEFAULT 'USD'",
        "ALTER TABLE invoices ADD COLUMN payment_terms TEXT DEFAULT NULL"
    ];

    foreach ($queries as $sql) {
        try {
            $db->exec($sql);
            echo "Successfully executed: " . substr($sql, 0, 50) . "...\n";
        } catch (\PDOException $e) {
            echo "Skipped (maybe already exists): " . $e->getMessage() . "\n";
        }
    }
    
    echo "Migration script completed successfully.\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
