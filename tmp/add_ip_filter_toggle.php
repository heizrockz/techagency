<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

try {
    $db->exec("ALTER TABLE `admins` ADD COLUMN `ip_filter_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_salesperson` ");
    echo "Column 'ip_filter_enabled' added successfully to 'admins' table.\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column name')) {
        echo "Column 'ip_filter_enabled' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
