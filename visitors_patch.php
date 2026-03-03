<?php
/**
 * Visitor Analytics Database Patch
 * 
 * Instructions:
 * 1. Upload this file to your public HTML root directory.
 * 2. Visit https://your-domain.com/visitors_patch.php in your browser.
 * 3. Delete this file from your server after successful execution.
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require the database connection
require_once __DIR__ . '/includes/db.php';

echo "<h1>Applying Database Patch...</h1>";

try {
    $db = getDB();

    $sql = "
    CREATE TABLE IF NOT EXISTS site_visitors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        country VARCHAR(100),
        country_code VARCHAR(5),
        city VARCHAR(100),
        region VARCHAR(100),
        isp VARCHAR(200),
        user_agent TEXT,
        page_url VARCHAR(500),
        visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    
    echo "<p style='color: green; font-weight: bold;'>✅ Success: The 'site_visitors' table has been created successfully!</p>";
    echo "<p>You can now safely delete this file (<code>" . basename(__FILE__) . "</code>) from your server.</p>";
    echo "<p><a href='/admin/dashboard'>Return to Admin Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Error: Could not apply patch.</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Error: Something went wrong.</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
