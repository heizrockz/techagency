<?php
/**
 * Test script to verify license activation scenarios against the local/production API
 */

$baseUrl = 'http://localhost/tech-agency'; // Change this if testing remotely

function runTest($label, $params, $method = 'GET') {
    global $baseUrl;
    $url = $baseUrl . '/api/v1/license';
    
    echo "\n--- $label ---\n";
    $ch = curl_init();
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    } else {
        $queryString = http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $queryString);
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $httpCode\n";
    echo "Response: $response\n";
}

// Scenario 1: Java Activation (Legacy style GET)
// app-manager uses this for activation
runTest("Scenario 1: Java Activation (GET)", [
    'key' => 'D6F2-6FDD-A9D4-264A',
    'hardware_id' => 'MS-REC-TEST-UUID'
]);

// Scenario 2: Java Background Check (POST)
// SubscriptionManager uses this
runTest("Scenario 2: Java Background Check (POST)", [
    'product_code' => 'MS-REC-TEST-UUID'
], 'POST');

// Scenario 3: Potential Mismatch Case (product_code used for both)
runTest("Scenario 3: Legacy Fallback (product_code as key)", [
    'product_code' => 'D6F2-6FDD-A9D4-264A'
], 'POST');
