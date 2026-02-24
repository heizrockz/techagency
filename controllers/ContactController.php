<?php
/**
 * ContactController — renders the contact page
 */

$seo = getSeoMeta('contact');
$locale = getCurrentLocale();
$dir = isRTL() ? 'rtl' : 'ltr';
$viewFile = 'contact';

// Load view via the shared layout
require __DIR__ . '/../views/layouts/main.php';
