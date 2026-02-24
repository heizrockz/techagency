<?php
/**
 * Mico Sage — Final Production Patch
 * Resolves schema gaps for Blogs and Chatbot sessions.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$db = getDB();

function logMsg($msg) {
    echo $msg . "<br>";
}

function addCol($db, $table, $column, $definition) {
    try {
        $check = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            logMsg("✅ Added column '$column' to table '$table'.");
        } else {
            logMsg("ℹ️ Column '$column' already exists in '$table'.");
        }
    } catch (Exception $e) {
        logMsg("❌ Error adding column '$column' to '$table': " . $e->getMessage());
    }
}

logMsg("<h2>Mico Sage Production Recovery Patch</h2>");

// 1. Fix chatbot_sessions
addCol($db, 'chatbot_sessions', 'user_email', 'VARCHAR(255) AFTER id');
addCol($db, 'chatbot_sessions', 'user_phone', 'VARCHAR(50) AFTER user_email');
try {
    // Ensure ENUM is correct
    $db->exec("ALTER TABLE chatbot_sessions MODIFY COLUMN status ENUM('Open', 'Closed', 'active', 'closed') DEFAULT 'Open'");
    $db->exec("UPDATE chatbot_sessions SET status = 'Open' WHERE status = 'active' OR status IS NULL");
    $db->exec("UPDATE chatbot_sessions SET status = 'Closed' WHERE status = 'closed'");
    $db->exec("ALTER TABLE chatbot_sessions MODIFY COLUMN status ENUM('Open', 'Closed') DEFAULT 'Open'");
    logMsg("✅ Updated chatbot_sessions status ENUM.");
} catch (Exception $e) {
    logMsg("⚠️ Note on chatbot_sessions ENUM: " . $e->getMessage());
}

// 2. Fix blog_translations
addCol($db, 'blog_translations', 'content', 'LONGTEXT AFTER description');

// 3. Ensure sample blogs are functional
try {
    // Enable all blogs
    $db->exec("UPDATE blogs SET is_active = 1");
    logMsg("✅ Set all blogs to active.");

    // Check if translations exist for blog 1
    $check = $db->query("SELECT id FROM blog_translations WHERE blog_id = 1 AND locale = 'en'")->fetch();
    if (!$check) {
        $db->exec("INSERT INTO blog_translations (blog_id, locale, title, description, content) VALUES (1, 'en', 'The Future of AI Engineering', 'Exploring Agentic AI.', 'Detailed content here...')");
        logMsg("✅ Created missing translation for 'future-of-ai-engineering'.");
    } else {
        // Ensure content is not empty
        $db->exec("UPDATE blog_translations SET content = '<h2>Overview</h2><p>This is a detailed view of the blog post. Agentic AI is revolutionizing the industry.</p>' WHERE blog_id = 1 AND (content IS NULL OR content = '')");
        logMsg("✅ Verified content for 'future-of-ai-engineering'.");
    }
} catch (Exception $e) {
    logMsg("❌ Error seeding blog data: " . $e->getMessage());
}

logMsg("<br><b>Patch Completed!</b> Please delete this file from your server now.");
