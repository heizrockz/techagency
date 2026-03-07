<?php
/**
 * Mico Sage — App Heartbeat API
 * POST /api/v1/heartbeat
 *
 * Accepts: {"license_key":"MS-XXXX-...", "hardware_id":"...", "hostname":"...", "os_info":"...", "app_version":"..."}
 * Returns: {"status":"active","type":"pro","features":{...},"message":"OK"}
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
    echo json_encode(['status' => 'success', 'message' => 'Heartbeat API running. Use POST with license_key and hardware_id.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$licenseKey = trim($input['license_key'] ?? '');
$hardwareId = trim($input['hardware_id'] ?? '');
$hostname = trim($input['hostname'] ?? '');
$osInfo = trim($input['os_info'] ?? '');
$appVersion = trim($input['app_version'] ?? '');
$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

if (empty($licenseKey) || empty($hardwareId)) {
    http_response_code(400);
    echo json_encode(['error' => 'license_key and hardware_id are required']);
    exit;
}

try {
    $db = getDB();

    // Look up license
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

    // Check license status
    if ($license['status'] !== 'active') {
        echo json_encode(['status' => $license['status'], 'message' => 'License is ' . $license['status']]);
        exit;
    }

    // Check expiry
    if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
        $db->prepare("UPDATE app_licenses SET status='expired' WHERE id=?")->execute([$license['id']]);
        echo json_encode(['status' => 'expired', 'message' => 'License has expired']);
        exit;
    }

    // Find or create device
    $devStmt = $db->prepare('SELECT * FROM app_devices WHERE license_id = ? AND hardware_id = ?');
    $devStmt->execute([$license['id'], $hardwareId]);
    $device = $devStmt->fetch();

    $isNewDevice = false;
    if (!$device) {
        // Check max devices
        if ($license['activated_devices'] >= $license['max_devices'] && $license['max_devices'] > 0) {
            http_response_code(403);
            echo json_encode(['error' => 'Maximum device limit reached', 'status' => 'device_limit', 'max_devices' => $license['max_devices']]);
            exit;
        }

        // Create new device
        $db->prepare('INSERT INTO app_devices (license_id, hardware_id, hostname, os_info, ip_address, app_version, is_online, first_seen, last_heartbeat) VALUES (?,?,?,?,?,?,1,NOW(),NOW())')
            ->execute([$license['id'], $hardwareId, $hostname, $osInfo, $clientIp, $appVersion]);
        $deviceId = $db->lastInsertId();

        // Increment counters
        $db->prepare('UPDATE app_licenses SET activated_devices = activated_devices + 1 WHERE id = ?')->execute([$license['id']]);
        $db->prepare('UPDATE app_products SET total_installs = total_installs + 1 WHERE id = ?')->execute([$license['product_id']]);

        // Log connection
        $db->prepare("INSERT INTO app_device_logs (device_id, event_type, ip_address, details) VALUES (?, 'connect', ?, ?)")
            ->execute([$deviceId, $clientIp, "New device: $hostname ($osInfo)"]);

        $isNewDevice = true;
    }
    else {
        $deviceId = $device['id'];
        $wasOffline = !$device['is_online'];

        // Update heartbeat
        $db->prepare('UPDATE app_devices SET hostname=?, os_info=?, ip_address=?, app_version=?, is_online=1, last_heartbeat=NOW() WHERE id=?')
            ->execute([$hostname ?: $device['hostname'], $osInfo ?: $device['os_info'], $clientIp, $appVersion ?: $device['app_version'], $deviceId]);

        // Log if was offline and now reconnecting
        if ($wasOffline) {
            $db->prepare("INSERT INTO app_device_logs (device_id, event_type, ip_address, details) VALUES (?, 'connect', ?, ?)")
                ->execute([$deviceId, $clientIp, "Reconnected: $hostname"]);
        }
    }

    // Fetch features
    $fStmt = $db->prepare('SELECT feature_key, feature_value FROM app_license_features WHERE license_id = ?');
    $fStmt->execute([$license['id']]);
    $features = [];
    foreach ($fStmt->fetchAll() as $f) {
        $features[$f['feature_key']] = $f['feature_value'];
    }

    echo json_encode([
        'status' => 'active',
        'type' => $license['type'],
        'product' => $license['product_name'],
        'version' => $license['product_version'],
        'max_devices' => (int)$license['max_devices'],
        'expires_at' => $license['expires_at'],
        'features' => $features,
        'is_new_device' => $isNewDevice,
        'message' => 'OK'
    ]);

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'detail' => $e->getMessage()]);
}
