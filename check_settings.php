<?php
require_once __DIR__ . '/includes/db.php';
$db = getDB();
$settings = $db->query("SELECT * FROM email_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

echo "<pre>CURRENT SETTINGS:\n";
print_r($settings);
echo "</pre>";
