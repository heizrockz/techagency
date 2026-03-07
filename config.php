<?php
/**
 * Mico Sage Tech Agency — Configuration
 */

// ─── Environment Detection ─────────────────────────────────
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])
    || (($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1')
    || (php_sapi_name() === 'cli' && (!isset($_SERVER['REMOTE_ADDR'])))
    || strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false
    || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

if ($isLocal) {
    // ── Local Development ──
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'tech_agency');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('BASE_URL', ''); // local root
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
else {
    // ── Production (Ionos) ──
    // ⚠️ UPDATE THESE with your Ionos database credentials
    define('DB_HOST', 'db5019872306.hosting-data.io');
    define('DB_NAME', 'dbs15358907');
    define('DB_USER', 'dbu2080337');
    define('DB_PASS', '=p.bAN.a5%4rbbd');
    define('BASE_URL', ''); // site is at domain root
    error_reporting(0);
    ini_set('display_errors', '0');
}

// App
define('APP_NAME', 'Mico Sage');
define('DEFAULT_LOCALE', 'en');
define('SUPPORTED_LOCALES', ['en', 'ar']);

// Session
define('SESSION_LIFETIME', 3600 * 8);
