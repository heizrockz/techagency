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

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }
}

function attemptLogin(string $username, string $password): bool {
    $db = getDB();
    $stmt = $db->prepare('SELECT id, password FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        startSecureSession();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $username;
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
