<?php
/**
 * Mico Sage — License Verification API (v2)
 * POST /api/v1/license
 *
 * Accepts: {"product_code": "MS-XXXX-..."} or {"license_key": "MS-XXXX-..."}
 * Returns: {"status":"active","type":"pro","features":{...}}
 */

// Debug logging
file_put_contents(__DIR__ . '/log.txt', date('[Y-m-d H:i:s] ') . $_SERVER['REQUEST_METHOD'] . ' ' . file_get_contents('php://input') . " | GET: " . json_encode($_GET) . "\n", FILE_APPEND);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

// 1. Identify the license key
$licenseKey = trim($input['license_key'] ?? $input['key'] ?? $_GET['key'] ?? $_GET['license_key'] ?? '');

// 2. Identify the hardware ID candidates (excluding product_code for now if it might be the key)
$hardwareId = trim($input['hardware_id'] ?? $input['machine_id'] ?? $_GET['hardware_id'] ?? '');

// 3. Handle "product_code" fallback (common in Java app)
$productCode = trim($input['product_code'] ?? $_GET['product_code'] ?? '');

if (empty($licenseKey) && !empty($productCode)) {
    // If we only have product_code, it MUST be the license key (legacy)
    $licenseKey = $productCode;
} elseif (!empty($productCode) && empty($hardwareId)) {
    // If we have both, and hardware_id is missing, product_code is the hardware_id
    $hardwareId = $productCode;
}

// 4. Trace the request
file_put_contents(__DIR__ . '/log.txt', date('[Y-m-d H:i:s] ') . "REQ: Key=$licenseKey, HW=$hardwareId (Method: " . $_SERVER['REQUEST_METHOD'] . ")\n", FILE_APPEND);

if (empty($licenseKey)) {
    // Check if we can find a license by hardware ID binding
    if (!empty($hardwareId)) {
        $stmt = $db->prepare('SELECT l.license_key FROM app_licenses l 
            JOIN app_license_features f ON l.id = f.license_id 
            WHERE f.feature_key = "bound_hardware_id" AND f.feature_value = ? LIMIT 1');
        $stmt->execute([$hardwareId]);
        $foundKey = $stmt->fetchColumn();
        if ($foundKey) {
            $licenseKey = $foundKey;
        }
    }
}

if (empty($licenseKey)) {
    http_response_code(400);
    echo json_encode(['error' => 'license_key is required', 'status' => 'invalid']);
    exit;
}

try {
    // Check if new tables exist; if not, check old table for backwards compatibility
    $useNewSchema = true;
    try {
        $db->query("SELECT 1 FROM app_licenses LIMIT 1");
    }
    catch (Exception $e) {
        $useNewSchema = false;
    }

    if ($useNewSchema) {
        // ── New Schema ──
        $stmt = $db->prepare('SELECT l.*, p.name as product_name, p.version as product_version
            FROM app_licenses l JOIN app_products p ON l.product_id = p.id
            WHERE l.license_key = ?');
        $stmt->execute([$licenseKey]);
        $license = $stmt->fetch();

        if (!$license && !empty($hardwareId)) {
            // Check if we can find a license by hardware ID binding
            $stmt = $db->prepare('SELECT l.*, p.name as product_name, p.version as product_version
                FROM app_licenses l 
                JOIN app_products p ON l.product_id = p.id
                JOIN app_license_features f ON l.id = f.license_id 
                WHERE f.feature_key = "bound_hardware_id" AND f.feature_value = ? LIMIT 1');
            $stmt->execute([$hardwareId]);
            $license = $stmt->fetch();
        }

        if (!$license) {
            http_response_code(404);
            echo json_encode(['error' => 'Invalid license key', 'status' => 'invalid']);
            exit;
        }

        // Check expiry
        if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
            $db->prepare("UPDATE app_licenses SET status='expired' WHERE id=?")->execute([$license['id']]);
            $license['status'] = 'expired';
        }

        if ($license['status'] !== 'active') {
            echo json_encode(['status' => $license['status'], 'message' => 'License is ' . $license['status']]);
            exit;
        }

        // Fetch features
        $fStmt = $db->prepare('SELECT feature_key, feature_value FROM app_license_features WHERE license_id = ?');
        $fStmt->execute([$license['id']]);
        $features = [];
        foreach ($fStmt->fetchAll() as $f) {
            $features[$f['feature_key']] = $f['feature_value'];
        }

        // ── Hardware ID Binding Check ──
        if (!empty($features['bound_hardware_id'])) {
            $boundId = $features['bound_hardware_id'];
            if (empty($hardwareId) || $hardwareId !== $boundId) {
                file_put_contents(__DIR__ . '/log.txt', date('[Y-m-d H:i:s] ') . "MISMATCH: Provided HW [$hardwareId] !== Bound HW [$boundId] for key [$licenseKey]\n", FILE_APPEND);
                http_response_code(403);
                echo json_encode(['status' => 'invalid', 'error' => 'Invalid key. Please check your purchase details.']);
                exit;
            }
        }
        // ───────────────────────────────

        // ── Device Tracking ──
        if (!empty($hardwareId)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $hostname = $input['hostname'] ?? $_GET['hostname'] ?? null;
            $appVer = $input['app_version'] ?? $_GET['app_version'] ?? null;
            
            $db->prepare("INSERT INTO app_devices (license_id, hardware_id, ip_address, hostname, app_version, is_online, last_heartbeat) 
                VALUES (?, ?, ?, ?, ?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE license_id=VALUES(license_id), ip_address=VALUES(ip_address), 
                hostname=IFNULL(VALUES(hostname), hostname), app_version=IFNULL(VALUES(app_version), app_version), 
                is_online=1, last_heartbeat=NOW()")
                ->execute([$license['id'], $hardwareId, $ip, $hostname, $appVer]);
        }
        // ──────────────────

        // Legacy compatibility: map features to old fields
        $recoveryLimit = isset($features['recovery_limit']) ? (int)$features['recovery_limit'] : -1;
        $aboutText = $license['about_text'] ?: ($features['about_text'] ?? "Mico Sage\nLicensed to: " . ($license['label'] ?: $license['license_key']));

        echo json_encode([
            'status' => strtoupper($license['status']),
            'type' => $license['type'],
            'product' => $license['product_name'],
            'recovery_limit' => $recoveryLimit,
            'about_text' => $aboutText,
            'features' => $features,
            'max_devices' => (int)$license['max_devices'],
            'expires_at' => $license['expires_at']
        ]);

    }
    else {
        // ── Legacy Schema (app_subscriptions) ──
        $stmt = $db->prepare('SELECT * FROM app_subscriptions WHERE product_code = ?');
        $stmt->execute([$licenseKey]);
        $sub = $stmt->fetch();

        if (!$sub) {
            // Auto-register
            $db->prepare('INSERT INTO app_subscriptions (product_code, status, type, recovery_limit, last_seen, about_text) VALUES (?, ?, ?, ?, NOW(), ?)')
                ->execute([$licenseKey, 'active', 'free', 10, "Mico Sage Forensic Tool\nFree Tier — 10 recoveries\nUpgrade at micosage.com"]);
            echo json_encode(['status' => 'active', 'type' => 'free', 'recovery_limit' => 10, 'about_text' => "Mico Sage Forensic Tool\nFree Tier — 10 recoveries"]);
            exit;
        }

        $db->prepare('UPDATE app_subscriptions SET last_seen = NOW() WHERE id = ?')->execute([$sub['id']]);
        $limit = (int)$sub['recovery_limit'];
        $remaining = $limit === -1 ? -1 : max(0, $limit - (int)$sub['recoveries_used']);

        echo json_encode([
            'status' => $sub['status'],
            'type' => $sub['type'],
            'recovery_limit' => $remaining,
            'about_text' => $sub['about_text'] ?: "Mico Sage\nLicensed to: " . ($sub['label'] ?: $sub['product_code'])
        ]);
    }

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'detail' => $e->getMessage()]);
}
