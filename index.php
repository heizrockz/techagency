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

    require_once __DIR__ . '/controllers/AdminController.php';

    if ($path === '/admin/dashboard' || $path === '/admin') {
        adminDashboard();
    } elseif ($path === '/admin/inbox') {
        adminInbox();
    } elseif ($path === '/admin/marketing') {
        adminEmailMarketing();
    } elseif ($path === '/admin/sitemap') {
        adminSitemap();
    } elseif ($path === '/admin/bookings') {
        adminBookings();
    } elseif ($path === '/admin/booking-fields') {
        adminBookingFields();
    } elseif ($path === '/admin/services') {
        adminServices();
    } elseif ($path === '/admin/clients') {
        adminClients();
    } elseif ($path === '/admin/products') {
        adminProducts();
    } elseif ($path === '/admin/portfolio') {
        adminPortfolio();
    } elseif ($path === '/admin/blogs') {
        adminBlogs();
    } elseif ($path === '/admin/team') {
        adminTeam();
    } elseif ($path === '/admin/testimonials') {
        adminTestimonials();
    } elseif ($path === '/admin/chatbot') {
        adminChatbot();
    } elseif ($path === '/admin/translations') {
        adminTranslations();
    } elseif ($path === '/admin/settings') {
        adminSettings();
    } elseif ($path === '/admin/content') {
        adminContent();
    } elseif ($path === '/admin/seo') {
        adminSeo();
    } elseif ($path === '/admin/invoices') {
        adminInvoices();
    } elseif ($path === '/admin/contacts') {
        adminContacts();
    } elseif ($path === '/admin/profile') {
        adminProfile();
    } elseif ($path === '/admin/logout') {
        adminLogout();
    } else {
        // Handle other admin routes or 404
        if (file_exists(__DIR__ . '/views/errors/404.php')) {
            require_once __DIR__ . '/views/errors/404.php';
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
} else {
    // 404
    if (file_exists(__DIR__ . '/views/errors/404.php')) {
        require_once __DIR__ . '/views/errors/404.php';
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
}
