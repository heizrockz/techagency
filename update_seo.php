<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $db = getDB();
    
    // 1. Shorten Home Page Meta Title
    $newTitle = "Mico Sage | Web Development & Digital Marketing";
    $stmt = $db->prepare("UPDATE seo_meta SET title = ? WHERE page = 'home' AND locale = 'en'");
    $stmt->execute([$newTitle]);
    echo "SEO Title updated.\n";
    
    // 2. Lengthen H1 Hero Title
    $newH1 = "We Build The Digital Future";
    $stmt = $db->prepare("UPDATE contents SET value = ? WHERE section_key = 'hero_title' AND locale = 'en'");
    $stmt->execute([$newH1]);
    echo "Hero Title updated.\n";

    // 3. Lengthen Meta Description (150-220 chars)
    $newDesc = "Mico Sage is a premium tech agency specializing in high-performance web engineering, custom Windows desktop applications, and data-driven digital marketing solutions tailored for growth.";
    // Length: 185 chars
    $stmt = $db->prepare("UPDATE seo_meta SET description = ? WHERE page = 'home' AND locale = 'en'");
    $stmt->execute([$newDesc]);
    echo "Meta Description updated.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
