<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php'; // For baseUrl if needed

$db = getDB();
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 1. Increment download count
    $db->prepare("UPDATE app_products SET download_count = download_count + 1, total_installs = total_installs + 1 WHERE id = ?")
       ->execute([$id]);

    // 2. Fetch product info
    $product = $db->query("SELECT name, download_url FROM app_products WHERE id = $id")->fetch();

    // 3. Log notification
    $detail = "Public download started: " . ($product['name'] ?? 'Unknown App');
    $db->prepare("INSERT INTO app_device_logs (device_id, event_type, details, created_at) VALUES (0, 'download', ?, NOW())")
       ->execute([$detail]);

    // 4. Redirect to actual file
    if ($product && $product['download_url']) {
        $dest = $product['download_url'];
        if (strpos($dest, 'http') !== 0) $dest = baseUrl($dest);
        header('Location: ' . $dest);
        exit;
    }
}

die('Download link invalid or expired.');
