<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    echo "Creating app_product_translations table if not exists...<br>\n";
    
    $query = "
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
    ";
    
    $db->exec($query);
    echo "Table app_product_translations is ready.<br>\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
