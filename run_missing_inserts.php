<?php
require_once __DIR__ . '/includes/db.php';
$sql = file_get_contents(__DIR__ . '/migrations/missing_content_inserts.sql');
try {
    $db = getDB();
    $db->exec($sql);
    echo "Successfully executed missing inserts.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
