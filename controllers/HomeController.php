<?php
/**
 * HomeController — renders the main user-facing page
 */

$seo = getSeoMeta('home');

// Track website visit
try {
    $db = getDB();
    $db->exec("INSERT INTO site_settings (setting_key, setting_value) VALUES ('visit_count', '1') ON DUPLICATE KEY UPDATE setting_value = setting_value + 1");
} catch (\Throwable $e) {
    // Ignore tracking errors
}

$locale = getCurrentLocale();
$dir = isRTL() ? 'rtl' : 'ltr';

// Load view
require __DIR__ . '/../views/layouts/main.php';
