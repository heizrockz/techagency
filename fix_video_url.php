<?php
require_once __DIR__ . '/includes/db.php';
try {
    $db = getDB();
    $sql = "UPDATE blogs SET media_url = 'https://youtu.be/dQw4w9WgXcQ?si=ab3ASPhEGHDrkesH' WHERE slug = 'modern-windows-ui-design'";
    $db->exec($sql);
    echo "Successfully updated video URL. Please delete this script and check the blog page.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
