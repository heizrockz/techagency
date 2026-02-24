<?php
require 'e:/Repos/Laravel/htdocs/tech-agency/config.php';
require 'e:/Repos/Laravel/htdocs/tech-agency/includes/db.php';
$sqlFile = 'e:/Repos/Laravel/htdocs/tech-agency/migrations/alter_seo_and_blogs.sql';
if (!file_exists($sqlFile)) die("File not found");
$sql = file_get_contents($sqlFile);
$db = getDB();
foreach(explode(';', $sql) as $q) {
    if(trim($q)) {
        try {
            $db->exec($q);
            echo "Ok: " . substr(trim($q), 0, 50) . "...\n";
        } catch(Exception $e) {
            echo "Skipped: " . $e->getMessage() . "\n";
        }
    }
}
echo "Migration Complete";
unlink(__FILE__);
