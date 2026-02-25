<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$db = getDB();
$sql = file_get_contents(__DIR__ . '/migrations/email_schema.sql');

try {
    $db->exec($sql);
    echo "Email schema applied successfully.\n";
} catch (Exception $e) {
    echo "Error applying schema: " . $e->getMessage() . "\n";
}
