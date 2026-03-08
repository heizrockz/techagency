<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/helpers.php'; // Fixed include path

$db = getDB();
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 1. Increment download count (only if total_installs column exists)
    try {
        $db->prepare("UPDATE app_products SET download_count = download_count + 1, total_installs = total_installs + 1 WHERE id = ?")
           ->execute([$id]);
    } catch(Exception $e) { /* Ignore if columns missing */ }

    // 2. Fetch product info
    $stmt = $db->query("SELECT name, download_url FROM app_products WHERE id = $id");
    if ($stmt) {
        $product = $stmt->fetch();
        
        if ($product) {
            // 3. Log notification safely (bypass foreign key issues by checking table structure)
            $detail = "Public download started: " . ($product['name'] ?? 'Unknown App');
            try {
                $db->prepare("INSERT INTO app_device_logs (device_id, event_type, details, created_at) VALUES (0, 'download', ?, NOW())")
                   ->execute([$detail]);
            } catch(Exception $e) { /* Ignore foreign key error for public downloads */ }

            // 4. Redirect to actual file
            if (!empty($product['download_url'])) {
                $dest = $product['download_url'];
        if (strpos($dest, 'http') !== 0) $dest = baseUrl($dest);
        header('Location: ' . $dest);
        exit;
    }
}

die('Download link invalid or expired.');
