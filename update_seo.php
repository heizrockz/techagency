<?php
require_once __DIR__ . '/tech-agency/config.php';
require_once __DIR__ . '/tech-agency/includes/db.php';

try {
    $db = getDB();
    
    // 1. Shorten Home Page Meta Title
    // Original: "Mico Sage | Web Development, Windows Apps & Digital Marketing"
    // New: "Mico Sage | Web Development & Digital Marketing"
    $newTitle = "Mico Sage | Web Development & Digital Marketing";
    $stmt = $db->prepare("UPDATE seo_meta SET title = ? WHERE page = 'home' AND locale = 'en'");
    $stmt->execute([$newTitle]);
    echo "SEO Title updated.\n";
    
    // 2. Lengthen H1 Hero Title
    // Original: "We Build The Future" (19 chars)
    // New: "We Build The Digital Future" (27 chars)
    $newH1 = "We Build The Digital Future";
    $stmt = $db->prepare("UPDATE contents SET value = ? WHERE section_key = 'hero_title' AND locale = 'en'");
    $stmt->execute([$newH1]);
    echo "Hero Title updated.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
