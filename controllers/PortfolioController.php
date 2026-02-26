<?php
/**
 * PortfolioController — renders the portfolio page
 */

$seo = getSeoMeta('portfolio');
$seo['canonical_link'] = baseUrl('portfolio');
$locale = getCurrentLocale();
$dir = isRTL() ? 'rtl' : 'ltr';
$viewFile = 'portfolio';

// Load view via the shared layout
require __DIR__ . '/../views/layouts/main.php';
