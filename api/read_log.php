<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
try {
    $db = getDB();
    $logs = $db->query("SELECT * FROM app_device_logs WHERE event_type = 'error' ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($logs, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
