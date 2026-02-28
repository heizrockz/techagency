<?php
/**
 * Database Patch Applicator
 * Run this script to apply database_patch.sql to your database.
 * Usage: Visit this file in your browser (`http://yourdomain.com/apply_patch.php`)
 */

require_once __DIR__ . '/config.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlFile = __DIR__ . '/database_patch.sql';

    if (!file_exists($sqlFile)) {
        die("Error: The patch file 'database_patch.sql' does not exist in the root directory.\n");
    }

    $sql = file_get_contents($sqlFile);
    
    // Execute the SQL patch
    $db->exec($sql);
    
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h2 style='color: green;'>✅ Patch Applied Successfully</h2>";
    echo "<p>All tables and updates from <strong>database_patch.sql</strong> have been executed on the database!</p>";
    echo "<p style='color: red; font-weight: bold;'>Security Warning: Please delete this file (`apply_patch.php`) and `database_patch.sql` from your server now.</p>";
    echo "<a href='" . BASE_URL . "/admin/dashboard'>Go back to Dashboard</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h2 style='color: red;'>❌ Patch Failed</h2>";
    echo "<p>An error occurred while executing the SQL patch:</p>";
    echo "<pre style='background: #eee; padding: 10px;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}
