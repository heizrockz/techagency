<?php
require 'e:/Repos/Laravel/htdocs/tech-agency/config.php';
require 'e:/Repos/Laravel/htdocs/tech-agency/includes/db.php';
$db = getDB();
echo "Environment: " . (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']) ? 'Web-Local' : 'Other') . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
$stmt = $db->query("SELECT section_key, locale, value FROM contents WHERE section_key LIKE 'process_%'");
$rows = $stmt->fetchAll();
foreach($rows as $r) {
    echo "[{$r['locale']}] {$r['section_key']} = " . substr($r['value'], 0, 30) . "...\n";
}
if(empty($rows)) echo "No rows found!\n";
