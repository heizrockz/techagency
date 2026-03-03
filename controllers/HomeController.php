<?php
/**
 * HomeController — renders the main user-facing page
 */

$seo = getSeoMeta('home');
$seo['canonical_link'] = baseUrl('/');

// Track website visit & geodata
try {
    $db = getDB();
    
    // Fast overall counter
    $db->exec("INSERT INTO site_settings (setting_key, setting_value) VALUES ('visit_count', '1') ON DUPLICATE KEY UPDATE setting_value = setting_value + 1");

    // Detailed per-visitor tracking
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    // Handle proxy comma-separated IPs (take first)
    $ip = explode(',', $ip)[0];
    
    // Only track valid, non-local IPs (skip 127.0.0.1 and ::1 optionally, but let's track all for now or we won't see local data)
    // Actually, let's track local as "Localhost"
    $country = 'Unknown';
    $countryCode = 'UNKNOWN';
    $city = 'Unknown';
    $region = 'Unknown';
    $isp = 'Unknown';

    // To prevent API rate limit issues (45 req/min), we could cache or only look up if session is new.
    // Let's use session to track if we already logged this user this session
    session_start();
    if (!isset($_SESSION['tracked_visit'])) {
        
        if ($ip !== '127.0.0.1' && $ip !== '::1' && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Live IP
            $geoUrl = "http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city,isp";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $geoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Don't hang the page
            $geoDataRaw = curl_exec($ch);
            curl_close($ch);

            if ($geoDataRaw) {
                $geoInfo = json_decode($geoDataRaw, true);
                if ($geoInfo && isset($geoInfo['status']) && $geoInfo['status'] === 'success') {
                    $country = $geoInfo['country'] ?? 'Unknown';
                    $countryCode = $geoInfo['countryCode'] ?? 'UNKNOWN';
                    $city = $geoInfo['city'] ?? 'Unknown';
                    $region = $geoInfo['regionName'] ?? 'Unknown';
                    $isp = $geoInfo['isp'] ?? 'Unknown';
                }
            }
        } else {
             $country = 'Local Dev';
             $countryCode = 'LOCAL';
             $city = 'Localhost';
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $pageUrl = $_SERVER['REQUEST_URI'] ?? '/';

        $stmt = $db->prepare('INSERT INTO site_visitors (ip_address, country, country_code, city, region, isp, user_agent, page_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$ip, $country, $countryCode, $city, $region, $isp, $userAgent, $pageUrl]);
        
        $_SESSION['tracked_visit'] = true;
    }
    
} catch (\Throwable $e) {
    // Ignore tracking errors so homepage doesn't crash on DB issues
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
