<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM app_licenses");
    $licenses = $stmt->fetchAll();
    
    echo "APP LICENSES:\n";
    foreach ($licenses as $l) {
        echo "ID: {$l['id']} | Key: {$l['license_key']} | Status: {$l['status']} | Type: {$l['type']}\n";
    }
    
    $stmt = $db->query("SELECT * FROM app_license_features");
    $features = $stmt->fetchAll();
    echo "\nLICENSE FEATURES:\n";
    foreach ($features as $f) {
        echo "License ID: {$f['license_id']} | Key: {$f['feature_key']} | Value: {$f['feature_value']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
