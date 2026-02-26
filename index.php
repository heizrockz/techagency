<?php
/**
 * Mico Sage — Front Controller / Router
 */
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// ── Language Handling ─────────────────────────────────────
if (isset($_GET['lang'])) {
    appSetLocale($_GET['lang']);
}

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
} elseif ($path === '/blogs') {
    require_once __DIR__ . '/controllers/BlogController.php';
    showAll();
} elseif (preg_match('#^/blog/([^/]+)$#', $path, $matches)) {
    require_once __DIR__ . '/controllers/BlogController.php';
    showBlogDetail($matches[1]);
} elseif ($path === '/portfolio') {
    require_once __DIR__ . '/controllers/PortfolioController.php';
} elseif ($path === '/booking/submit') {
    require_once __DIR__ . '/controllers/BookingController.php';
    handleBookingSubmit();
} elseif ($path === '/booking/success') {
    require_once __DIR__ . '/controllers/BookingController.php';
    showBookingSuccess();
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
        // Handle other admin routes or 404 by redirecting to home
        require_once __DIR__ . '/controllers/HomeController.php';
    }
} else {
    // Catch-all: Route unknown requests to the home page instead of 404
    require_once __DIR__ . '/controllers/HomeController.php';
}
