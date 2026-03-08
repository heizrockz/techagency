<?php
/**
 * Quick Fix for missing columns in app_products
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    echo "Checking app_products table...<br>";
    
    $columns = [
        'header_image' => "varchar(500) DEFAULT NULL AFTER `icon_url` line",
        'features' => "text DEFAULT NULL AFTER `description`",
        'download_url' => "varchar(500) DEFAULT NULL AFTER `price`",
        'show_buy_button' => "tinyint(1) DEFAULT 1 AFTER `download_url`",
        'buy_url' => "varchar(500) DEFAULT NULL AFTER `show_buy_button`",
        'is_public' => "tinyint(1) DEFAULT 1 AFTER `buy_url`",
        'show_price' => "tinyint(1) DEFAULT 1 AFTER `is_public`",
        'meta_description' => "text DEFAULT NULL",
        'meta_keywords' => "text DEFAULT NULL"
    ];

    foreach ($columns as $column => $def) {
        $stmt = $db->query("SHOW COLUMNS FROM `app_products` LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            echo "Adding column $column... ";
            $db->exec("ALTER TABLE `app_products` ADD COLUMN `$column` $def");
            echo "OK<br>";
        } else {
            echo "Column $column already exists.<br>";
        }
    }
    
    echo "Database fix complete.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
