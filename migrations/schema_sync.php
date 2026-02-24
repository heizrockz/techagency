<?php
/**
 * Database Schema Synchronization Script
 * Fixes missing columns for Blogs and Chatbot features.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

function addColumnIfNotExist($db, $table, $column, $definition) {
    try {
        $check = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            echo "Added column $column to $table.<br>";
        } else {
            echo "Column $column already exists in $table.<br>";
        }
    } catch (Exception $e) {
        echo "Error checking column $column in $table: " . $e->getMessage() . "<br>";
    }
}

echo "Starting schema sync...<br>";

// 1. Fix chatbot_sessions schema
addColumnIfNotExist($db, 'chatbot_sessions', 'user_email', 'VARCHAR(255) AFTER id');
addColumnIfNotExist($db, 'chatbot_sessions', 'user_phone', 'VARCHAR(50) AFTER user_email');
try {
    $db->exec("ALTER TABLE chatbot_sessions MODIFY COLUMN session_uuid VARCHAR(100) NULL");
    $db->exec("ALTER TABLE chatbot_sessions MODIFY COLUMN status ENUM('Open', 'Closed') DEFAULT 'Open'");
} catch(Exception $e) {}

// Fix chatbot_nodes schema
addColumnIfNotExist($db, 'chatbot_nodes', 'pos_x', 'INT DEFAULT 0');
addColumnIfNotExist($db, 'chatbot_nodes', 'pos_y', 'INT DEFAULT 0');
addColumnIfNotExist($db, 'chatbot_nodes', 'reply_type', "ENUM('preset','user_input') DEFAULT 'preset'");
addColumnIfNotExist($db, 'chatbot_nodes', 'input_var_name', "VARCHAR(100) DEFAULT ''");

// 2. Fix blog_translations schema
addColumnIfNotExist($db, 'blog_translations', 'content', 'LONGTEXT AFTER description');

$queries = [
    // 3. Update existing statuses if needed
    "UPDATE chatbot_sessions SET status = 'Open' WHERE status = 'active'",
    "UPDATE chatbot_sessions SET status = 'Closed' WHERE status = 'closed'",
    
    // 4. Seed some sample content to the new blog content column
    "UPDATE blog_translations SET content = '<h2>Overview</h2><p>This is a detailed view of the blog post. Agentic AI is revolutionizing the industry by allowing autonomous agents to perform complex tasks.</p><h3>Key Features</h3><ul><li>Autonomous Decision Making</li><li>Real-time Learning</li><li>Scalable Architectures</li></ul>' WHERE blog_id = 1 AND locale = 'en'",
    "UPDATE blog_translations SET content = '<h2>نظرة عامة</h2><p>هذا عرض مفصل للمقال. الذكاء الاصطناعي الوكيل يحدث ثورة في الصناعة من خلال السماح للوكلاء المستقلين بأداء مهام معقدة.</p>' WHERE blog_id = 1 AND locale = 'ar'",
    "UPDATE blog_translations SET content = '<h2>Design Systems</h2><p>Creating modern interfaces requires a deep understanding of user psychology and visual hierarchy. Glassmorphism is a key trend in 2026.</p>' WHERE blog_id = 2 AND locale = 'en'",
    "UPDATE blog_translations SET content = '<h2>Marketing Strategy</h2><p>Data-driven decisions are at the heart of every successful digital campaign in the modern era.</p>' WHERE blog_id = 3 AND locale = 'en'"
];

foreach ($queries as $sql) {
    try {
        $db->exec($sql);
        echo "Executed query update.<br>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}

echo "Schema sync completed!<br>";
