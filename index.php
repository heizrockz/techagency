<?php
/**
 * Mico Sage — Front Controller / Router
 */
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Parse request URI
$requestUri = $_SERVER['REQUEST_URI'];
$basePath   = BASE_URL;

// Extract path part (remove query string)
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove base path from request path
if ($basePath && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Ensure path starts with /
if (empty($path) || $path[0] !== '/') {
    $path = '/' . $path;
}

// Routing
if ($path === '/' || $path === '/index.php' || $path === '') {
    require_once __DIR__ . '/controllers/HomeController.php';
    homeIndex();
} elseif ($path === '/blogs') {
    require_once __DIR__ . '/controllers/BlogController.php';
    $blogController = new BlogController();
    $blogController->showAll();
} elseif (preg_match('#^/blog/([^/]+)$#', $path, $matches)) {
    require_once __DIR__ . '/controllers/BlogController.php';
    $blogController = new BlogController();
    $blogController->showDetail($matches[1]);
} elseif ($path === '/admin/login') {
    require_once __DIR__ . '/controllers/AdminController.php';
    adminLogin();
} elseif (strpos($path, '/admin') === 0) {
    // Admin middleware
    if (!isAdminLoggedIn()) {
        header('Location: ' . baseUrl('/admin/login'));
        exit;
    }

    if ($path === '/admin/dashboard') {
        require_once __DIR__ . '/controllers/AdminController.php';
        adminDashboard();
    } elseif ($path === '/admin/marketing') {
        require_once __DIR__ . '/controllers/AdminController.php';
        adminEmailMarketing();
    } elseif ($path === '/admin/sitemap') {
        require_once __DIR__ . '/controllers/AdminController.php';
        adminSitemap();
    } elseif ($path === '/admin/logout') {
        require_once __DIR__ . '/controllers/AdminController.php';
        adminLogout();
    } else {
        // Handle other admin routes or 404
        require_once __DIR__ . '/views/errors/404.php';
    }
} else {
    // 404
    require_once __DIR__ . '/views/errors/404.php';
}
