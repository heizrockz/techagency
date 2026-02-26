<?php
/**
 * HomeController — renders the main user-facing page
 */

$seo = getSeoMeta('home');
$seo['canonical_link'] = baseUrl('/');

// Track website visit
try {
    $db = getDB();
    $db->exec("INSERT INTO site_settings (setting_key, setting_value) VALUES ('visit_count', '1') ON DUPLICATE KEY UPDATE setting_value = setting_value + 1");
} catch (\Throwable $e) {
    // Ignore tracking errors
}

$locale = getCurrentLocale();
$dir = isRTL() ? 'rtl' : 'ltr';

// Fetch dynamic content required by home.php
// These are used in the view included by main.php
$services = getServices();
$products = getProducts();
$clients = getClients();
$teamMembers = getTeamMembers();
$testimonials = getTestimonials();
$blogPosts = getBlogs();
$bookingFields = getBookingFields();

// Load the layout which will include the home view
require __DIR__ . '/../views/layouts/main.php';
