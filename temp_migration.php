<?php
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    $db->exec("ALTER TABLE blogs ADD COLUMN IF NOT EXISTS view_count INT DEFAULT 0");
    echo "Migration successful: view_count column added.";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
