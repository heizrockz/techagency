<?php
/**
 * Mico Sage — Production Diagnostics
 * Use this to verify DB schema and environment on hosted site.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

echo "<h1>Mico Sage Diagnostics</h1>";

try {
    $db = getDB();
    echo "<p style='color:green'>✅ Database Connected Successfully</p>";

    $tables = [
        'blogs' => ['id', 'slug', 'is_active'],
        'chatbot_sessions' => ['id', 'user_ip', 'status', 'user_email', 'user_phone'],
        'chatbot_messages' => ['id', 'session_id', 'sender', 'message'],
        'chatbot_nodes' => ['id', 'pos_x', 'pos_y', 'reply_type']
    ];

    foreach ($tables as $table => $cols) {
        try {
            $stmt = $db->query("DESCRIBE `$table` ");
            $dbCols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>Table: $table</h3>";
            echo "<ul>";
            foreach ($cols as $c) {
                if (in_array($c, $dbCols)) {
                    echo "<li style='color:green'>✅ Column '$c' exists</li>";
                } else {
                    echo "<li style='color:red'>❌ Column '$c' MISSING!</li>";
                }
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p style='color:red'>❌ Table '$table' MISSING or error: " . $e->getMessage() . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Connection Failed: " . $e->getMessage() . "</p>";
}

echo "<h3>PHP Environment</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Base URL: '" . BASE_URL . "'<br>";
?>
