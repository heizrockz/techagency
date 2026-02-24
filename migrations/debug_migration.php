<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('CLI_FORCE_LOCAL', true); // Add a flag to help config.php if needed, but I'll just adjust config.php or this script.

require 'e:/Repos/Laravel/htdocs/tech-agency/config.php';
require 'e:/Repos/Laravel/htdocs/tech-agency/includes/db.php';

$sqlFile = 'e:/Repos/Laravel/htdocs/tech-agency/migrations/alter_seo_and_blogs.sql';
if (!file_exists($sqlFile)) die("SQL File not found\n");
$sqlContent = file_get_contents($sqlFile);

$db = getDB();
echo "Connected to DB: " . DB_NAME . "\n";

// Split queries carefully
$queries = preg_split("/;(?=(?:[^'\"`]*['\"`][^'\"`]*['\"`])*[^'\"`]*$)/", $sqlContent);

foreach($queries as $q) {
    $q = trim($q);
    if(empty($q)) continue;
    
    echo "Executing: " . substr($q, 0, 80) . "...\n";
    try {
        $db->exec($q);
        echo "SUCCESS\n";
    } catch(PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
echo "\nMigration Process Finished\n";
