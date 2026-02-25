<?php
require_once __DIR__ . '/includes/db.php';
$db = getDB();
try {
    $db->exec("ALTER TABLE chatbot_sessions ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER status");
    echo "Migration Successful: is_read added to chatbot_sessions.";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Migration already applied: is_read exists.";
    } else {
        echo "Migration Failed: " . $e->getMessage();
    }
}
?>
