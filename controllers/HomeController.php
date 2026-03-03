<?php
/**
 * HomeController — renders the main user-facing page
 */

$seo = getSeoMeta('home');
$seo['canonical_link'] = baseUrl('/');

// Tracking is now handled globally in index.php via trackVisit()

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
