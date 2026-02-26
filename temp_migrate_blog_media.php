<?php
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    $sql = "CREATE TABLE IF NOT EXISTS blog_media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        blog_id INT NOT NULL,
        media_type ENUM('image', 'video', 'video_link') NOT NULL DEFAULT 'image',
        media_url TEXT NOT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "Table 'blog_media' created successfully or already exists.\n";

    // Migrate existing media if any
    $blogs = $db->query("SELECT id, media_type, media_url FROM blogs WHERE media_url IS NOT NULL AND media_url != ''")->fetchAll();
    foreach ($blogs as $blog) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM blog_media WHERE blog_id = ? AND media_url = ?");
        $stmt->execute([$blog['id'], $blog['media_url']]);
        if ($stmt->fetchColumn() == 0) {
            $db->prepare("INSERT INTO blog_media (blog_id, media_type, media_url, sort_order) VALUES (?, ?, ?, 0)")
               ->execute([$blog['id'], $blog['media_type'], $blog['media_url']]);
            echo "Migrated media for blog ID: " . $blog['id'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
