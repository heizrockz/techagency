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

// If base path is specified and present at start of path, strip it
if (!empty($basePath) && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Ensure path starts with / and is trimmed
$path = '/' . trim($path, '/');

// Handle locale switch
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LOCALES)) {
    appSetLocale($_GET['lang']);
    $redirect = strtok($requestUri, '?');
    header('Location: ' . $redirect);
    exit;
}

// Handle dynamic blog route: /blog/{slug}
if (preg_match('~^/blog/([^/]+)$~', $path, $matches)) {
    $blogSlug = $matches[1];
    require __DIR__ . '/controllers/BlogController.php';
    showBlogDetail($blogSlug);
    exit;
}

// ─── Routing ──────────────────────────────────────────────
switch ($path) {

    // ── User pages ───────────────────────────────
    case '/':
    case '':
        require __DIR__ . '/controllers/HomeController.php';
        break;

    case '/portfolio':
        require __DIR__ . '/controllers/PortfolioController.php';
        break;

    case '/contact':
        require __DIR__ . '/controllers/ContactController.php';
        break;

    case '/booking/submit':
        require __DIR__ . '/controllers/BookingController.php';
        handleBookingSubmit();
        break;

    case '/booking/success':
        require __DIR__ . '/controllers/BookingController.php';
        showBookingSuccess();
        break;

    // ── Admin pages ──────────────────────────────
    case '/admin':
    case '/admin/':
    case '/admin/dashboard':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminDashboard();
        break;

    case '/admin/login':
        require __DIR__ . '/controllers/AdminController.php';
        adminLogin();
        break;

    case '/admin/chatbot':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminChatbot();
        break;

    case '/admin/team':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminTeam();
        break;

    case '/admin/testimonials':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminTestimonials();
        break;

    case '/admin/invoices':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminInvoices();
        break;

    case '/admin/inbox':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminInbox();
        break;

    case '/admin/contacts':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminContacts();
        break;

    case '/admin/profile':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminProfile();
        break;

    case '/admin/logout':
        adminLogout();
        break;

    case '/admin/bookings':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminBookings();
        break;

    case '/admin/bookings/update-status':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminUpdateBookingStatus();
        break;

    case '/admin/services':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminServices();
        break;

    case '/admin/clients':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminClients();
        break;

    case '/admin/products':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminProducts();
        break;

    case '/admin/booking-fields':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminBookingFields();
        break;

    case '/admin/settings':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminSettings();
        break;

    case '/admin/content':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminContent();
        break;

    case '/admin/seo':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminSeo();
        break;

    case '/admin/translations':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminTranslations();
        break;

    case '/admin/sitemap':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminSitemap();
        break;

    case '/admin/portfolio':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminPortfolio();
        break;

    case '/admin/blogs':
        requireAdmin();
        require __DIR__ . '/controllers/AdminController.php';
        adminBlogs();
        break;

    case '/api/chatbot_save.php':
        require __DIR__ . '/api/chatbot_save.php';
        break;

    default:
        http_response_code(404);
        $viewFile = '404';
        require __DIR__ . '/views/layouts/main.php';
        break;
}
