<?php
require_once __DIR__ . '/includes/db.php';
$db = getDB();
$db->exec("UPDATE email_settings SET smtp_encryption = 'ssl' WHERE smtp_port = 465");
$db->exec("UPDATE email_settings SET smtp_encryption = 'tls' WHERE smtp_port = 587");
echo "Updated database encryption settings successfully.\n";
