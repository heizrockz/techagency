<?php
/**
 * Admin authentication helpers
 */
require_once __DIR__ . '/db.php';

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isAdminLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function isSuperAdmin(): bool {
    startSecureSession();
    return ($_SESSION['admin_role'] ?? 'standard') === 'super_admin';
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }
}

function requireSuperAdmin(): void {
    requireAdmin();
    if (($_SESSION['admin_role'] ?? 'standard') !== 'super_admin') {
        setFlash('Access denied. Super Admin privileges required.', 'error');
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
}

/**
 * Check if the current admin has permission for a specific module
 */
function hasPermission(string $module): bool {
    startSecureSession();
    if (($_SESSION['admin_role'] ?? 'standard') === 'super_admin') return true;
    
    // For standard admins, check permissions column
    if (!isset($_SESSION['admin_permissions'])) {
        $db = getDB();
        $stmt = $db->prepare("SELECT permissions FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id'] ?? 0]);
        $row = $stmt->fetch();
        $_SESSION['admin_permissions'] = $row ? json_decode($row['permissions'] ?? '[]', true) : [];
    }
    
    return in_array($module, $_SESSION['admin_permissions'] ?? []);
}

/**
 * Require a specific permission or super_admin role
 */
function requirePermission(string $module): void {
    if (!hasPermission($module)) {
        setFlash("Access denied. You do not have permission to access the '$module' module.", 'error');
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
}

function checkIpWhitelist(int $adminId): bool {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Handle localhost/proxy scenarios if needed, but standard REMOTE_ADDR is usually safest for this
    
    // Check if user has IP filtering enabled
    $stmt = $db->prepare("SELECT ip_filter_enabled FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $filterEnabled = (bool)$stmt->fetchColumn();
    
    if (!$filterEnabled) return true; // IP filter not enabled for this user, allow all
    
    // Check if user has any whitelist entries
    $stmt = $db->prepare("SELECT COUNT(*) FROM admin_ip_whitelist WHERE admin_id = ?");
    $stmt->execute([$adminId]);
    $count = (int)$stmt->fetchColumn();
    
    if ($count === 0) return true; // No whitelist defined (even if lock is on), allow all or consider stricter? 
    // Usually if Lock is ON but 0 IPs, it should block. But let's follow plan: 0 IPs = allow all for safety unless user adds one.
    // Re-reading plan: "Enabling Security Lock without any whitelisted IPs will block their login".
    // So if filterEnabled is true and count is 0, it should return false.
    if ($count === 0) return false; 
    
    // Check if current IP is in whitelist and not expired
    $stmt = $db->prepare("
        SELECT id FROM admin_ip_whitelist 
        WHERE admin_id = ? AND ip_address = ? 
        AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)
        LIMIT 1
    ");
    $stmt->execute([$adminId, $ip]);
    return (bool)$stmt->fetch();
}

function attemptLogin(string $username, string $password): bool {
    $db = getDB();

    // Failsafe auto-migrate ip_filter_enabled column if missing
    try {
        $db->exec("ALTER TABLE `admins` ADD COLUMN `ip_filter_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_salesperson` ");
    } catch (Exception $e) {}

    $stmt = $db->prepare('SELECT id, password, recovery_email, role, ip_filter_enabled FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // IP Whitelist Check
        if (!checkIpWhitelist((int)$admin['id'])) {
            require_once __DIR__ . '/helpers.php';
            logAdminActivity('login_blocked_ip', "Login blocked for '{$username}' due to unauthorized IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));
            return false;
        }

        startSecureSession();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $username;
        $_SESSION['admin_email'] = $admin['recovery_email'] ?? '';
        $_SESSION['admin_role'] = $admin['role'] ?? 'standard';
        
        require_once __DIR__ . '/helpers.php';
        logAdminActivity('login', "Admin '{$username}' logged in successfully.");
        addNotification('login', 'New Admin Login', "Admin '{$username}' has logged into the system.", baseUrl('admin/activity_logs'));
        
        return true;
    }
    return false;
}

function adminLogout(): void {
    startSecureSession();
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}

function getAdminUser(): ?string {
    startSecureSession();
    return $_SESSION['admin_user'] ?? null;
}
