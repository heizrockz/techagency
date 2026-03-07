<?php
require 'config.php';
require 'includes/db.php';
$db = getDB();
try {
    $db->exec("ALTER TABLE app_licenses ADD COLUMN IF NOT EXISTS use_count INT(11) DEFAULT 0 AFTER activated_devices");
    $db->exec("ALTER TABLE app_licenses ADD COLUMN IF NOT EXISTS max_use_count INT(11) DEFAULT 0 AFTER use_count");
    echo "Columns added successfully.\n";
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
