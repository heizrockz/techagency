<?php
$url = 'https://micosage.com/api/v1/license'; // Public URL to test HTTP API

function testApi($payload, $label) {
    global $url;
    echo "\n=== Testing: $label ===\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $code\n";
    echo $response . "\n";
}

// 1. Test generic key with NO hardware limitation (from screenshot: D6F2...)
testApi([
    'license_key' => 'D6F2-6FDD-A9D4-264A',
    'product_code' => 'TEST-HARDWARE-ID-123'
], "Key without restriction works with any hardware ID");

// 2. We'll simulate bounding that key directly in the DB
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
$db = getDB();

$db->query("INSERT INTO app_license_features (license_id, feature_key, feature_value) 
            SELECT id, 'bound_hardware_id', 'M3-REC-TEST' 
            FROM app_licenses WHERE license_key = 'D6F2-6FDD-A9D4-264A' 
            ON DUPLICATE KEY UPDATE feature_value = 'M3-REC-TEST'");

testApi([
    'license_key' => 'D6F2-6FDD-A9D4-264A',
    'product_code' => 'TEST-HARDWARE-ID-123' // Wrong hardware
], "Key WITH restriction fails on WRONG hardware ID");

testApi([
    'license_key' => 'D6F2-6FDD-A9D4-264A',
    'product_code' => 'M3-REC-TEST' // Right hardware
], "Key WITH restriction succeeds on EXACT hardware ID");

// Cleanup
$db->query("DELETE FROM app_license_features WHERE feature_key = 'bound_hardware_id'");
echo "\nTest complete.\n";
