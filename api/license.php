<?php
/**
 * Mico Sage — License Verification API (v2)
 * POST /api/v1/license
 *
 * Accepts: {"product_code": "MS-XXXX-..."} or {"license_key": "MS-XXXX-..."}
 * Returns: {"status":"active","type":"pro","features":{...}}
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status' => 'success', 'message' => 'License API v2 running. POST with license_key to verify.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

try {
    $db->prepare("INSERT INTO app_device_logs (device_id, event_type, details) VALUES (?, ?, ?)")
       ->execute([0, 'error', "Payload: " . file_get_contents('php://input')]);
} catch (\Exception $e) {}

// Extract the license key and the hardware ID
// Depending on how the client sends it, we handle common permutations
$licenseKey = trim($input['license_key'] ?? $input['key'] ?? '');
$hardwareId = trim($input['hardware_id'] ?? $input['product_code'] ?? $input['machine_id'] ?? '');

// Fallback for legacy clients that only sent "product_code" as the license key itself
if (empty($licenseKey) && !empty($hardwareId)) {
    $licenseKey = $hardwareId;
    // We don't have a distinct hardware ID in this legacy scenario
}

if (empty($licenseKey)) {
    http_response_code(400);
    echo json_encode(['error' => 'license_key is required']);
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
                http_response_code(403);
                echo json_encode(['status' => 'invalid', 'error' => 'Invalid key. Please check your purchase details.']);
                exit;
            }
        }
        // ───────────────────────────────

        // Legacy compatibility: map features to old fields
        $recoveryLimit = isset($features['recovery_limit']) ? (int)$features['recovery_limit'] : -1;
        $aboutText = $features['about_text'] ?? "Mico Sage\nLicensed to: " . ($license['label'] ?: $license['license_key']);

        echo json_encode([
            'status' => $license['status'],
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
