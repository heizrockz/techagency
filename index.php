<?php
/**
 * Mico Sage — Front Controller / Router
 */
session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Global logger to catch the Java app request headers and details
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    $db = getDB();
    try {
        $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $payload = file_get_contents('php://input');
        $query = json_encode($_GET);
        $headers = json_encode(getallheaders());
        $db->prepare("INSERT INTO app_device_logs (device_id, event_type, details) VALUES (?, 'error', ?)")
           ->execute([0, "Method: $method | URI: $uri | Headers: $headers | Payload: $payload | Query: $query"]);
    } catch (\Exception $e) {}
}

// ── Language Handling ─────────────────────────────────────
if (isset($_GET['lang'])) {
    appSetLocale($_GET['lang']);
}

// Track website visit & geodata
trackVisit();

// Parse request URI with fallback for built-in PHP server
$requestUri = $_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'] ?? '/';
$basePath = BASE_URL;

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
}
elseif ($path === '/blogs') {
    require_once __DIR__ . '/controllers/BlogController.php';
    showAll();
}
elseif (preg_match('#^/blog/([^/]+)$#', $path, $matches)) {
    require_once __DIR__ . '/controllers/BlogController.php';
    showBlogDetail($matches[1]);
}
elseif ($path === '/portfolio') {
    require_once __DIR__ . '/controllers/PortfolioController.php';
}
elseif ($path === '/booking/submit') {
    require_once __DIR__ . '/controllers/BookingController.php';
    handleBookingSubmit();
}
elseif ($path === '/booking/success') {
    require_once __DIR__ . '/controllers/BookingController.php';
    showBookingSuccess();
}
elseif ($path === '/api/v1/license' || $path === '/api/check-license') {
    require_once __DIR__ . '/api/license.php';
}
elseif ($path === '/api/v1/heartbeat') {
    require_once __DIR__ . '/api/app_heartbeat.php';
}
elseif ($path === '/software') {
    require_once __DIR__ . '/controllers/AppController.php';
    publicSoftwareStore();
}
elseif ($path === '/software/review' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/controllers/AppController.php';
    handleReviewSubmit();
}
elseif (preg_match('#^/software/([^/]+)$#', $path, $matches)) {
    require_once __DIR__ . '/controllers/AppController.php';
    publicSoftwareDetails($matches[1]);
}
elseif ($path === '/admin/login') {
    require_once __DIR__ . '/controllers/AdminController.php';
    adminLogin();
}
elseif (strpos($path, '/admin') === 0) {
    // Admin middleware
    if (!isAdminLoggedIn()) {
        header('Location: ' . baseUrl('/admin/login'));
        exit;
    }

    require_once __DIR__ . '/controllers/AdminController.php';

    if ($path === '/admin/dashboard' || $path === '/admin') {
        adminDashboard();
    }
    elseif ($path === '/admin/visitors') {
        adminVisitors();
    }
    elseif ($path === '/admin/inbox') {
        adminInbox();
    }
    elseif ($path === '/admin/marketing') {
        adminEmailMarketing();
    }
    elseif ($path === '/admin/notifications') {
        adminNotifications();
    }
    elseif ($path === '/admin/users') {
        adminUsers();
    }
    elseif ($path === '/admin/activity_logs') {
        adminActivityLogs();
    }
    elseif ($path === '/admin/sitemap') {
        adminSitemap();
    }
    elseif ($path === '/admin/bookings') {
        adminBookings();
    }
    elseif ($path === '/admin/booking-fields') {
        adminBookingFields();
    }
    elseif ($path === '/admin/services') {
        adminServices();
    }
    elseif ($path === '/admin/clients') {
        adminClients();
    }
    elseif ($path === '/admin/products') {
        adminProducts();
    }
    elseif ($path === '/admin/portfolio') {
        adminPortfolio();
    }
    elseif ($path === '/admin/blogs') {
        adminBlogs();
    }
    elseif ($path === '/admin/team') {
        adminTeam();
    // CRM Routes
    }
    elseif ($path === '/admin/crm_pipeline') {
        require_once __DIR__ . '/controllers/CrmController.php';
        adminCrmPipeline();
    }
    elseif (strpos($path, '/admin/crm_opportunity') === 0) {
        require_once __DIR__ . '/controllers/CrmController.php';
        $id = $_GET['id'] ?? null;
        adminCrmOpportunity($id);
    }
    elseif ($path === '/admin/crm_products') {
        require_once __DIR__ . '/controllers/CrmController.php';
        adminCrmProducts();
    }
    elseif ($path === '/admin/crm_payments') {
        require_once __DIR__ . '/controllers/CrmController.php';
        adminCrmPayments();
    // Marketing/Content routes
    }
    elseif ($path === '/admin/testimonials') {
        adminTestimonials();
    }
    elseif ($path === '/admin/chatbot') {
        adminChatbot();
    }
    elseif ($path === '/admin/translations') {
        adminTranslations();
    }
    elseif ($path === '/admin/settings') {
        adminSettings();
    }
    elseif ($path === '/admin/content') {
        adminContent();
    }
    elseif ($path === '/admin/seo') {
        adminSeo();
    }
    elseif ($path === '/admin/invoices') {
        adminInvoices();
    }
    elseif ($path === '/admin/contacts') {
        adminContacts();
    }
    elseif ($path === '/admin/subscriptions') {
        adminSubscriptions();
    }
    elseif ($path === '/admin/app-manager') {
        adminAppManager();
    }
    elseif ($path === '/admin/app-categories') {
        adminAppCategories();
    }
    elseif ($path === '/admin/app-products') {
        adminAppProducts();
    }
    elseif ($path === '/admin/app-sections') {
        adminAppSections();
    }
    elseif ($path === '/admin/app-reviews') {
        adminAppReviews();
    }
    elseif ($path === '/admin/app-licenses') {
        adminAppLicenses();
    }
    elseif ($path === '/admin/app-devices') {
        adminAppDevices();
    }
    elseif ($path === '/admin/profile') {
        adminProfile();
    }
    elseif ($path === '/admin/logout') {
        adminLogout();
    }
    else {
        // Serve the custom 404 page
        http_response_code(404);
        $viewFile = '404';
        require_once __DIR__ . '/views/layouts/main.php';
    }
}
else {
    // Catch-all: Route unknown requests to the custom 404 page
    http_response_code(404);
    $viewFile = '404';
    require_once __DIR__ . '/views/layouts/main.php';
}
