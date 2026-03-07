<?php
/**
 * Mico Sage — Admin Controller (all admin panel actions)
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/sitemap_generator.php';
require_once __DIR__ . '/../includes/smtp.php';

/* ═══ Login ═══ */
function adminLogin(): void
{
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = isset($_POST['remember']) && $_POST['remember'] === '1';

        if (attemptLogin($username, $password, $remember)) {
            header('Location: ' . baseUrl('/admin/dashboard'));
            exit;
        }
        $error = t('admin_login_error');
    }
    require __DIR__ . '/../views/admin/login.php';
}

// ── Notifications ───────────────────────────────────────────
function adminNotifications()
{
    $db = getDB();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'mark_all_read') {
            $db->exec("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
            setFlash('All notifications marked as read', 'success');
        }
        elseif ($action === 'mark_read') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            $stmt->execute([$id]);
        }
        elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('Notification deleted', 'success');
        }
        header('Location: ' . baseUrl('admin/notifications'));
        exit;
    }

    $stmt = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 100");
    $allNotifications = $stmt->fetchAll();

    require __DIR__ . '/../views/admin/notifications.php';
}

// ── User Management & Logs ─────────────────────────────────
function adminUsers()
{
    requireSuperAdmin();
    $db = getDB();

    // Auto-migrate ip_filter_enabled column if missing
    try {
        $db->exec("ALTER TABLE `admins` ADD COLUMN `ip_filter_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_salesperson` ");
    }
    catch (Exception $e) {
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $username = trim($_POST['username'] ?? '');
            $recovery_email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'standard';
            $fullName = trim($_POST['full_name'] ?? '');
            $isSalesperson = isset($_POST['is_salesperson']) ? 1 : 0;
            $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

            if (empty($username) || empty($password)) {
                setFlash('Username and password are required', 'error');
            }
            else {
                try {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare('INSERT INTO admins (username, password, recovery_email, role, full_name, is_salesperson, permissions) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    if ($stmt->execute([$username, $hash, $recovery_email, $role, $fullName, $isSalesperson, $permissions])) {
                        logAdminActivity('create_user', "Created admin user: {$username}" . ($isSalesperson ? ' (Sales)' : ''));
                        setFlash('Admin user created successfully', 'success');
                    }
                }
                catch (Exception $e) {
                    setFlash('Error creating user (username might be taken)', 'error');
                }
            }
        }
        elseif ($action === 'toggle_salesperson') {
            $id = (int)($_POST['id'] ?? 0);
            $val = (int)($_POST['is_salesperson'] ?? 0);
            $stmt = $db->prepare('UPDATE admins SET is_salesperson = ? WHERE id = ?');
            $stmt->execute([$val ? 0 : 1, $id]);
            logAdminActivity('toggle_salesperson', "Toggled salesperson for admin ID: {$id}");
            setFlash('Sales role updated', 'success');
        }
        elseif ($action === 'toggle_ip_filter') {
            $id = (int)($_POST['id'] ?? 0);
            $val = (int)($_POST['ip_filter_enabled'] ?? 0);
            $stmt = $db->prepare('UPDATE admins SET ip_filter_enabled = ? WHERE id = ?');
            $stmt->execute([$val ? 0 : 1, $id]);
            logAdminActivity('toggle_ip_filter', "Toggled IP filter for admin ID: {$id}");
            setFlash('Security lock updated', 'success');
        }
        elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id === 1) {
                setFlash('Cannot delete the primary Super Admin account', 'error');
            }
            elseif ($id === $_SESSION['admin_id']) {
                setFlash('Cannot delete yourself', 'error');
            }
            else {
                $stmt = $db->prepare('DELETE FROM admins WHERE id = ?');
                if ($stmt->execute([$id])) {
                    logAdminActivity('delete_user', "Deleted admin user ID: {$id}");
                    setFlash('Admin user deleted successfully', 'success');
                }
            }
        }
        elseif ($action === 'update_role') {
            $id = (int)($_POST['id'] ?? 0);
            $role = $_POST['role'] ?? 'standard';
            if ($id === 1 && $role !== 'super_admin') {
                setFlash('Cannot downgrade the primary Super Admin', 'error');
            }
            else {
                $stmt = $db->prepare('UPDATE admins SET role = ? WHERE id = ?');
                if ($stmt->execute([$role, $id])) {
                    logAdminActivity('update_user_role', "Updated role for admin ID {$id} to {$role}");
                    setFlash('Role updated successfully', 'success');
                }
            }
        }
        elseif ($action === 'add_ip') {
            $adminId = (int)($_POST['admin_id'] ?? 0);
            $ip = trim($_POST['ip_address'] ?? '');
            $expires = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

            if ($adminId && $ip) {
                $stmt = $db->prepare("INSERT INTO admin_ip_whitelist (admin_id, ip_address, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$adminId, $ip, $expires]);
                logAdminActivity('add_ip_whitelist', "Added IP $ip to admin ID $adminId" . ($expires ? " (expires $expires)" : ""));
                setFlash('IP added to whitelist', 'success');
            }
        }
        elseif ($action === 'delete_ip') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $stmt = $db->prepare("DELETE FROM admin_ip_whitelist WHERE id = ?");
                $stmt->execute([$id]);
                logAdminActivity('delete_ip_whitelist', "Deleted IP whitelist entry ID: $id");
                setFlash('IP removed from whitelist', 'success');
            }
        }
        header('Location: ' . baseUrl('admin/users'));
        exit;
    }

    $stmt = $db->query("SELECT id, username, recovery_email, role, full_name, avatar_emoji, is_salesperson, ip_filter_enabled, created_at FROM admins ORDER BY id");
    $admins = $stmt->fetchAll();

    // Fetch whitelists for each admin
    foreach ($admins as &$admin) {
        $stmtW = $db->prepare("SELECT * FROM admin_ip_whitelist WHERE admin_id = ? ORDER BY created_at DESC");
        $stmtW->execute([$admin['id']]);
        $admin['whitelisted_ips'] = $stmtW->fetchAll();
    }
    unset($admin);

    require __DIR__ . '/../views/admin/users.php';
}

function adminActivityLogs()
{
    requireSuperAdmin();
    $db = getDB();

    $stmt = $db->query("SELECT l.*, a.username, a.full_name FROM admin_activity_logs l LEFT JOIN admins a ON l.admin_id = a.id ORDER BY l.created_at DESC LIMIT 500");
    $logs = $stmt->fetchAll();

    require __DIR__ . '/../views/admin/activity_logs.php';
}

/* ═══ Dashboard ═══ */
function adminDashboard(): void
{
    $db = getDB();
    $totalBookings = $db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    $newBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn();
    $totalServices = $db->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    $totalClients = $db->query('SELECT COUNT(*) FROM clients WHERE is_active = 1')->fetchColumn();

    $visitsStmt = $db->query("SELECT setting_value FROM site_settings WHERE setting_key = 'visit_count'");
    $visitCount = $visitsStmt ? (int)$visitsStmt->fetchColumn() : 0;

    // Traffic data for chart (Last 14 days)
    $trafficData = [];
    $trafficLabels = [];
    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $label = date('M d', strtotime("-$i days"));

        $stmt = $db->prepare("SELECT COUNT(*) FROM site_visitors WHERE DATE(visited_at) = ?");
        $stmt->execute([$date]);
        $count = $stmt->fetchColumn() ?: 0;

        $trafficLabels[] = $label;
        $trafficData[] = $count;
    }

    $recentBookings = $db->query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5')->fetchAll();
    require __DIR__ . '/../views/admin/dashboard.php';
}

/* ═══ Visitor Analytics ═══ */
function adminVisitors(): void
{
    requireAdmin();
    requirePermission('visitors');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    // IP Detail View
    if ($action === 'ip_detail' && isset($_GET['ip'])) {
        $ip = $_GET['ip'];
        $stmt = $db->prepare("SELECT * FROM site_visitors WHERE ip_address = ? ORDER BY visited_at DESC");
        $stmt->execute([$ip]);
        $ipVisits = $stmt->fetchAll();

        // Get summary info from first record
        $ipInfo = !empty($ipVisits) ? $ipVisits[0] : null;
        $ipIsBot = $ipInfo ? isBot($ipInfo['user_agent']) : false;
        $ipPageCount = count($ipVisits);
        $ipUniquePages = count(array_unique(array_column($ipVisits, 'page_url')));

        require __DIR__ . '/../views/admin/visitor_ip_detail.php';
        return;
    }

    // Summary logic
    $totalVisits = $db->query("SELECT setting_value FROM site_settings WHERE setting_key = 'visit_count'")->fetchColumn() ?: 0;
    $uniqueIps = $db->query("SELECT COUNT(DISTINCT ip_address) FROM site_visitors")->fetchColumn() ?: 0;
    $todayVisits = $db->query("SELECT COUNT(*) FROM site_visitors WHERE DATE(visited_at) = CURDATE()")->fetchColumn() ?: 0;
    $totalCountries = $db->query("SELECT COUNT(DISTINCT country_code) FROM site_visitors WHERE country_code != 'UNKNOWN' AND country_code != 'LOCAL'")->fetchColumn() ?: 0;

    // Bot vs Human stats
    $allUserAgents = $db->query("SELECT DISTINCT user_agent FROM site_visitors")->fetchAll(PDO::FETCH_COLUMN);
    $botIpCount = 0;
    $humanIpCount = 0;
    foreach ($allUserAgents as $ua) {
        if (isBot($ua))
            $botIpCount++;
        else
            $humanIpCount++;
    }

    // Countries group by
    $countriesData = $db->query("SELECT country, country_code, COUNT(*) as visit_count FROM site_visitors GROUP BY country, country_code HAVING visit_count > 0 ORDER BY visit_count DESC LIMIT 20")->fetchAll();

    // Filter mode
    $filter = $_GET['filter'] ?? 'all';

    // Raw paginated visitor logs
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 50;
    $offset = ($page - 1) * $limit;
    $totalLogs = $db->query("SELECT COUNT(*) FROM site_visitors")->fetchColumn() ?: 0;
    $totalPages = ceil($totalLogs / $limit);

    $visitors = $db->prepare("SELECT * FROM site_visitors ORDER BY visited_at DESC LIMIT ? OFFSET ?");
    $visitors->execute([$limit, $offset]);
    $visitorLogs = $visitors->fetchAll();

    require __DIR__ . '/../views/admin/visitors.php';
}

/* ═══ Bookings ═══ */
function adminBookings(): void
{
    requireAdmin();
    requirePermission('bookings');
    $db = getDB();
    $statusFilter = $_GET['status'] ?? 'all';
    if ($statusFilter !== 'all') {
        $stmt = $db->prepare('SELECT * FROM bookings WHERE status = ? ORDER BY created_at DESC');
        $stmt->execute([$statusFilter]);
    }
    else {
        $stmt = $db->query('SELECT * FROM bookings ORDER BY created_at DESC');
    }
    $bookings = $stmt->fetchAll();
    require __DIR__ . '/../views/admin/bookings.php';
}

function adminUpdateBookingStatus(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid = ['new', 'viewed', 'contacted', 'completed', 'cancelled'];
        if ($id > 0 && in_array($status, $valid)) {
            $db = getDB();
            $stmt = $db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);
        }
    }
    header('Location: ' . baseUrl('/admin/bookings'));
    exit;
}

/* ═══ Services CRUD ═══ */
function adminServices(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    // Delete
    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM services WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/services'));
        exit;
    }

    // Save (create or update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $icon = trim($_POST['icon'] ?? 'code');
        $color = trim($_POST['color'] ?? 'cobalt');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE services SET icon=?, color=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$icon, $color, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO services (icon, color, sort_order, is_active) VALUES (?, ?, ?, ?)')
                ->execute([$icon, $color, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc = trim($_POST['desc_' . $loc] ?? '');
            $db->prepare('INSERT INTO service_translations (service_id, locale, title, description)
                VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description)')
                ->execute([$id, $loc, $title, $desc]);
        }
        $saved = true;
        $action = 'list';
    }

    // Edit form
    $editService = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editService = $stmt->fetch();
        if ($editService) {
            $stmt2 = $db->prepare('SELECT * FROM service_translations WHERE service_id = ?');
            $stmt2->execute([$editService['id']]);
            $editService['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editService['translations'][$t['locale']] = $t;
            }
        }
    }

    // List
    $services = $db->query('SELECT s.*, GROUP_CONCAT(CONCAT(st.locale,":",st.title) SEPARATOR "|") as trans
        FROM services s LEFT JOIN service_translations st ON s.id = st.service_id
        GROUP BY s.id ORDER BY s.sort_order')->fetchAll();

    require __DIR__ . '/../views/admin/services.php';
}

/* ═══ Clients CRUD ═══ */
function adminClients(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM clients WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/clients'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $logoUrl = trim($_POST['logo_url'] ?? '');
        $websiteUrl = trim($_POST['website_url'] ?? '');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE clients SET name=?, logo_url=?, website_url=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$name, $logoUrl, $websiteUrl, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO clients (name, logo_url, website_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
                ->execute([$name, $logoUrl, $websiteUrl, $sortOrder, $isActive]);
        }
        $saved = true;
    }

    $editClient = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editClient = $stmt->fetch();
    }

    $clients = $db->query('SELECT * FROM clients ORDER BY sort_order')->fetchAll();
    require __DIR__ . '/../views/admin/clients.php';
}

/* ═══ Products CRUD ═══ */
function adminProducts(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM products WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/products'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $icon = trim($_POST['icon'] ?? 'globe');
        $category = trim($_POST['category'] ?? 'website');
        $color = trim($_POST['color'] ?? 'cobalt');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE products SET icon=?, category=?, color=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$icon, $category, $color, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO products (icon, category, color, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
                ->execute([$icon, $category, $color, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc = trim($_POST['desc_' . $loc] ?? '');
            $db->prepare('INSERT INTO product_translations (product_id, locale, title, description)
                VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description)')
                ->execute([$id, $loc, $title, $desc]);
        }
        $saved = true;
        $action = 'list';
    }

    $editProduct = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editProduct = $stmt->fetch();
        if ($editProduct) {
            $stmt2 = $db->prepare('SELECT * FROM product_translations WHERE product_id = ?');
            $stmt2->execute([$editProduct['id']]);
            $editProduct['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editProduct['translations'][$t['locale']] = $t;
            }
        }
    }

    $products = $db->query('SELECT p.*, GROUP_CONCAT(CONCAT(pt.locale,":",pt.title) SEPARATOR "|") as trans
        FROM products p LEFT JOIN product_translations pt ON p.id = pt.product_id
        GROUP BY p.id ORDER BY p.sort_order')->fetchAll();
    require __DIR__ . '/../views/admin/products.php';
}

/* ═══ Booking Fields CRUD ═══ */
function adminBookingFields(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM booking_fields WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/booking-fields'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $fieldName = trim($_POST['field_name'] ?? '');
        $fieldType = trim($_POST['field_type'] ?? 'text');
        $options = trim($_POST['options'] ?? '');
        $isRequired = isset($_POST['is_required']) ? 1 : 0;
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE booking_fields SET field_name=?, field_type=?, options=?, is_required=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$fieldName, $fieldType, $options, $isRequired, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO booking_fields (field_name, field_type, options, is_required, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$fieldName, $fieldType, $options, $isRequired, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        foreach (SUPPORTED_LOCALES as $loc) {
            $label = trim($_POST['label_' . $loc] ?? '');
            $placeholder = trim($_POST['placeholder_' . $loc] ?? '');
            $db->prepare('INSERT INTO booking_field_translations (field_id, locale, label, placeholder)
                VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder)')
                ->execute([$id, $loc, $label, $placeholder]);
        }
        $saved = true;
        $action = 'list';
    }

    $editField = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM booking_fields WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editField = $stmt->fetch();
        if ($editField) {
            $stmt2 = $db->prepare('SELECT * FROM booking_field_translations WHERE field_id = ?');
            $stmt2->execute([$editField['id']]);
            $editField['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editField['translations'][$t['locale']] = $t;
            }
        }
    }

    $fields = $db->query('SELECT bf.*, GROUP_CONCAT(CONCAT(bft.locale,":",bft.label) SEPARATOR "|") as trans
        FROM booking_fields bf LEFT JOIN booking_field_translations bft ON bf.id = bft.field_id
        GROUP BY bf.id ORDER BY bf.sort_order')->fetchAll();
    require __DIR__ . '/../views/admin/booking-fields.php';
}

/* ═══ Site Settings ═══ */
function adminSettings(): void
{
    requirePermission('settings');
    $db = getDB();
    $saved = false;

    // Auto-migrate announcement_history (since CLI php is unavailable in current ENV)
    $db->exec("CREATE TABLE IF NOT EXISTS announcement_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message_en TEXT,
        message_ar TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $settings = $_POST['settings'] ?? [];

        // Check if announcement changed to log history
        $oldMsgEn = getSetting('announcement_message', '');
        $oldMsgAr = getSetting('announcement_message_ar', '');
        $oldActive = getSetting('announcement_active', '0');

        $newMsgEn = trim($settings['announcement_message'] ?? '');
        $newMsgAr = trim($settings['announcement_message_ar'] ?? '');
        $newActive = isset($_POST['settings']['announcement_active']) ? '1' : '0';

        // If it's active and either message changed, log to history
        if ($newActive === '1' && ($newMsgEn !== $oldMsgEn || $newMsgAr !== $oldMsgAr || $oldActive === '0')) {
            if (!empty($newMsgEn) || !empty($newMsgAr)) {
                $hstmt = $db->prepare('INSERT INTO announcement_history (message_en, message_ar) VALUES (?, ?)');
                $hstmt->execute([$newMsgEn, $newMsgAr]);
            }
        }

        foreach ($settings as $key => $value) {
            $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')
                ->execute([$key, $value]);
        }
        // Handle checkboxes (sections toggles)
        $toggles = [
            'show_clients_section', 'show_products_section', 'show_stats_section',
            'show_marketing_section', 'show_team', 'show_testimonials',
            'show_tagline_section', 'show_process_section', 'show_blog_section',
            'show_booking_section', 'show_contact_section', 'announcement_active'
        ];
        foreach ($toggles as $toggle) {
            $val = isset($_POST['settings'][$toggle]) ? '1' : '0';
            $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')
                ->execute([$toggle, $val]);
        }

        // Handle File Uploads (Logo)
        $uploadFields = ['site_logo'];
        $uploadDir = __DIR__ . '/../assets/uploads/';

        foreach ($uploadFields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $fileInfo = pathinfo($_FILES[$field]['name']);
                $ext = strtolower($fileInfo['extension'] ?? '');
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'ico'])) {
                    $filename = $field . '_' . time() . '.' . $ext;
                    $targetFile = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                        $fileUrl = 'assets/uploads/' . $filename;
                        $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')
                            ->execute([$field, $fileUrl]);
                    }
                }
            }
        }

        $saved = true;
    }

    $rows = $db->query('SELECT * FROM site_settings ORDER BY setting_group, setting_key')->fetchAll();
    $settings = [];
    foreach ($rows as $r) {
        $settings[$r['setting_key']] = $r;
    }

    // Fetch history
    $historyCount = $db->query('SELECT COUNT(*) FROM announcement_history')->fetchColumn();
    $announcementHistory = $db->query('SELECT * FROM announcement_history ORDER BY created_at DESC LIMIT 15')->fetchAll();

    require __DIR__ . '/../views/admin/settings.php';
}

/* ═══ Content Editor ═══ */
function adminContent(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $items = $_POST['content'] ?? [];
        foreach ($items as $key => $locales) {
            foreach ($locales as $locale => $value) {
                $db->prepare('INSERT INTO contents (section_key, locale, value) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE value = VALUES(value)')
                    ->execute([$key, $locale, $value]);
            }
        }
        $saved = true;
    }

    $rows = $db->query('SELECT * FROM contents ORDER BY section_key, locale')->fetchAll();
    $contents = [];
    foreach ($rows as $row) {
        $contents[$row['section_key']][$row['locale']] = $row['value'];
    }

    // Ensure all critical sections exist in the array so they render in the admin panel
    $expectedKeys = [
        'hero_title', 'hero_subtitle', 'hero_cta',
        'about_title', 'about_text',
        'services_title', 'clients_title', 'products_title', 'products_subtitle',
        'team_title', 'team_subtitle',
        'testimonials_title', 'testimonials_subtitle',
        'booking_title', 'booking_subtitle',
        'marketing_title', 'marketing_subtitle',
        'marketing_seo_title', 'marketing_seo_desc',
        'marketing_social_title', 'marketing_social_desc',
        'marketing_ppc_title', 'marketing_ppc_desc',
        'marketing_brand_title', 'marketing_brand_desc',
        'portfolio_title', 'portfolio_subtitle',
        'tagline1_icon', 'tagline1_title', 'tagline1_desc',
        'tagline2_icon', 'tagline2_title', 'tagline2_desc',
        'tagline3_icon', 'tagline3_title', 'tagline3_desc',
        'process_title', 'process_subtitle',
        'process_step1_title', 'process_step1_desc',
        'process_step2_title', 'process_step2_desc',
        'process_step3_title', 'process_step3_desc',
        'process_step4_title', 'process_step4_desc',
        'success_page_title', 'success_page_message', 'success_page_button',
        'blog_title', 'blog_subtitle',
        'contact_us', 'contact_title', 'contact_subtitle', 'footer_tagline', 'footer_text'
    ];

    foreach ($expectedKeys as $key) {
        if (!isset($contents[$key])) {
            $contents[$key] = [];
            foreach (SUPPORTED_LOCALES as $loc) {
                $contents[$key][$loc] = '';
            }
        }
        else {
            foreach (SUPPORTED_LOCALES as $loc) {
                if (!isset($contents[$key][$loc])) {
                    $contents[$key][$loc] = '';
                }
            }
        }
    }
    require __DIR__ . '/../views/admin/content.php';
}

/* ═══ SEO Editor ═══ */
function adminSeo(): void
{
    requirePermission('seo');
    $db = getDB();
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $items = $_POST['seo'] ?? [];
        foreach ($items as $page => $locales) {
            foreach ($locales as $locale => $fields) {
                $db->prepare('INSERT INTO seo_meta (page, locale, title, description, keywords, canonical_link) VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), keywords = VALUES(keywords), canonical_link = VALUES(canonical_link)')
                    ->execute([$page, $locale, $fields['title'] ?? '', $fields['description'] ?? '', $fields['keywords'] ?? '', $fields['canonical_link'] ?? '']);
            }
        }

        // Handle global SEO files and string values into site_settings
        $uploadFields = ['seo_favicon', 'seo_og_image'];
        $uploadDir = __DIR__ . '/../assets/uploads/';

        foreach ($uploadFields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $fileInfo = pathinfo($_FILES[$field]['name']);
                $ext = strtolower($fileInfo['extension'] ?? '');
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'ico'])) {
                    $filename = $field . '_' . time() . '.' . $ext;
                    $targetFile = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                        $fileUrl = 'assets/uploads/' . $filename;
                        $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')
                            ->execute([$field, $fileUrl]);
                    }
                }
            }
        }

        $saved = true;
    }

    $rows = $db->query('SELECT * FROM seo_meta ORDER BY page, locale')->fetchAll();
    $seoData = [];
    foreach ($rows as $row) {
        $seoData[$row['page']][$row['locale']] = $row;
    }

    // Ensure critical pages always show up
    $expectedPages = ['home', 'portfolio', 'contact'];
    foreach ($expectedPages as $page) {
        if (!isset($seoData[$page])) {
            $seoData[$page] = [];
            foreach (SUPPORTED_LOCALES as $loc) {
                $seoData[$page][$loc] = ['title' => '', 'description' => '', 'keywords' => '', 'canonical_link' => ''];
            }
        }
        else {
            foreach (SUPPORTED_LOCALES as $loc) {
                if (!isset($seoData[$page][$loc])) {
                    $seoData[$page][$loc] = ['title' => '', 'description' => '', 'keywords' => '', 'canonical_link' => ''];
                }
                // Ensure canonical link exists
                if (!isset($seoData[$page][$loc]['canonical_link'])) {
                    $seoData[$page][$loc]['canonical_link'] = '';
                }
            }
        }
    }

    // Also fetch global seo settings to display
    $globalSeoStmt = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('seo_favicon', 'seo_og_image')");
    $globalSeo = [];
    foreach ($globalSeoStmt->fetchAll() as $r) {
        $globalSeo[$r['setting_key']] = $r['setting_value'];
    }
    require __DIR__ . '/../views/admin/seo.php';
}

/* ═══ Translations Editor ═══ */
function adminTranslations(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'save';

        if ($action === 'delete' && isset($_POST['trans_key'])) {
            $db->prepare('DELETE FROM translations WHERE trans_key = ?')->execute([$_POST['trans_key']]);
            $saved = true;
        }
        elseif ($action === 'save') {
            $items = $_POST['trans'] ?? [];
            foreach ($items as $key => $locales) {
                $group = $_POST['groups'][$key] ?? 'general';
                foreach ($locales as $locale => $value) {
                    $db->prepare('INSERT INTO translations (trans_key, locale, trans_value, trans_group) VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE trans_value = VALUES(trans_value), trans_group = VALUES(trans_group)')
                        ->execute([$key, $locale, $value, $group]);
                }
            }
            $saved = true;
        }

        // Add new key
        if (!empty($_POST['new_key'])) {
            $newKey = trim($_POST['new_key']);
            $newGroup = trim($_POST['new_group'] ?? 'general');
            foreach (SUPPORTED_LOCALES as $loc) {
                $newVal = trim($_POST['new_value_' . $loc] ?? '');
                $db->prepare('INSERT INTO translations (trans_key, locale, trans_value, trans_group) VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE trans_value = VALUES(trans_value)')
                    ->execute([$newKey, $loc, $newVal, $newGroup]);
            }
            $saved = true;
        }
    }

    $rows = $db->query('SELECT * FROM translations ORDER BY trans_group, trans_key, locale')->fetchAll();
    $translations = [];
    foreach ($rows as $row) {
        $translations[$row['trans_key']][$row['locale']] = $row;
    }
    require __DIR__ . '/../views/admin/translations.php';
}

/* ═══ Portfolio CRUD ═══ */
function adminPortfolio(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    // Delete
    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM portfolio_projects WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/portfolio'));
        exit;
    }

    // Save (create or update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $demoUrl = trim($_POST['demo_url'] ?? '');
        $category = trim($_POST['category'] ?? 'website');
        $color = trim($_POST['color'] ?? 'cobalt');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE portfolio_projects SET slug=?, image_url=?, demo_url=?, category=?, color=?, sort_order=?, is_active=?, is_featured=? WHERE id=?')
                ->execute([$slug, $imageUrl, $demoUrl, $category, $color, $sortOrder, $isActive, $isFeatured, $id]);
        }
        else {
            $db->prepare('INSERT INTO portfolio_projects (slug, image_url, demo_url, category, color, sort_order, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([$slug, $imageUrl, $demoUrl, $category, $color, $sortOrder, $isActive, $isFeatured]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc = trim($_POST['desc_' . $loc] ?? '');
            $clientName = trim($_POST['client_' . $loc] ?? '');
            $tags = trim($_POST['tags_' . $loc] ?? '');
            $db->prepare('INSERT INTO portfolio_project_translations (project_id, locale, title, description, client_name, tags)
                VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), client_name = VALUES(client_name), tags = VALUES(tags)')
                ->execute([$id, $loc, $title, $desc, $clientName, $tags]);
        }
        $saved = true;
        $action = 'list';
    }

    // Edit form
    $editProject = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM portfolio_projects WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editProject = $stmt->fetch();
        if ($editProject) {
            $stmt2 = $db->prepare('SELECT * FROM portfolio_project_translations WHERE project_id = ?');
            $stmt2->execute([$editProject['id']]);
            $editProject['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editProject['translations'][$t['locale']] = $t;
            }
        }
    }

    // List
    $projects = $db->query('SELECT p.*, GROUP_CONCAT(CONCAT(pt.locale,":",pt.title) SEPARATOR "|") as trans
        FROM portfolio_projects p LEFT JOIN portfolio_project_translations pt ON p.id = pt.project_id
        GROUP BY p.id ORDER BY p.sort_order')->fetchAll();

    require __DIR__ . '/../views/admin/portfolio.php';
}

/* ═══ Blogs CRUD ═══ */
function adminBlogs(): void
{
    requireAdmin();
    requirePermission('blogs');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    // Migration: Create blog_media table if not exists
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS blog_media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blog_id INT NOT NULL,
            media_type ENUM('image', 'video', 'video_link') NOT NULL DEFAULT 'image',
            media_url TEXT NOT NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
    catch (Exception $e) { /* ignore if already exists or other issues */
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM blogs WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('admin/blogs'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        $mediaType = trim($_POST['media_type'] ?? 'image');
        $mediaUrl = trim($_POST['media_url'] ?? '');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Handle file upload
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $fileInfo = pathinfo($_FILES['media_file']['name']);
            $ext = strtolower($fileInfo['extension']);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'mp4', 'webm'])) {
                $filename = 'blog_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['media_file']['tmp_name'], $targetFile)) {
                    $mediaUrl = 'assets/uploads/' . $filename;
                }
            }
        }

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($_POST['title_en'] ?? 'blog-' . time())));
        }

        if ($id > 0) {
            $db->prepare('UPDATE blogs SET slug=?, media_type=?, media_url=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$slug, $mediaType, $mediaUrl, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO blogs (slug, media_type, media_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
                ->execute([$slug, $mediaType, $mediaUrl, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc = trim($_POST['desc_' . $loc] ?? '');
            $content = $_POST['content_' . $loc] ?? '';
            $db->prepare('INSERT INTO blog_translations (blog_id, locale, title, description, content)
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), content = VALUES(content)')
                ->execute([$id, $loc, $title, $desc, $content]);
        }

        // Handle Additional Media
        $db->prepare('DELETE FROM blog_media WHERE blog_id = ?')->execute([$id]);

        // Save primary media to gallery too if it's the first time or if it's explicitly wanted
        // Actually, let's just save whatever is in the multi-media inputs
        if (isset($_POST['media_items']) && is_array($_POST['media_items'])) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            foreach ($_POST['media_items'] as $index => $item) {
                $mType = $item['type'] ?? 'image';
                $mUrl = $item['url'] ?? '';
                $mSort = intval($item['sort'] ?? 0);

                // Handle file upload if present for this row
                if (isset($_FILES['media_files']['name'][$index]) && $_FILES['media_files']['error'][$index] === UPLOAD_ERR_OK) {
                    $fileInfo = pathinfo($_FILES['media_files']['name'][$index]);
                    $ext = strtolower($fileInfo['extension']);
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'mp4', 'webm'])) {
                        $filename = 'blog_gallery_' . $id . '_' . $index . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['media_files']['tmp_name'][$index], $uploadDir . $filename)) {
                            $mUrl = 'assets/uploads/' . $filename;
                        }
                    }
                }

                if (!empty($mUrl)) {
                    $db->prepare('INSERT INTO blog_media (blog_id, media_type, media_url, sort_order) VALUES (?, ?, ?, ?)')
                        ->execute([$id, $mType, $mUrl, $mSort]);

                    // Update main blog media_url if this is the first item (thumb)
                    if ($index === 0) {
                        $db->prepare('UPDATE blogs SET media_type = ?, media_url = ? WHERE id = ?')
                            ->execute([$mType, $mUrl, $id]);
                    }
                }
            }
        }

        $saved = true;
        // if want to redirect instead of stay: 
        header('Location: ' . baseUrl('admin/blogs'));
        exit;
    }

    $editBlog = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM blogs WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editBlog = $stmt->fetch();
        if ($editBlog) {
            $stmt2 = $db->prepare('SELECT * FROM blog_translations WHERE blog_id = ?');
            $stmt2->execute([$editBlog['id']]);
            $editBlog['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editBlog['translations'][$t['locale']] = $t;
            }

            // Fetch Media Gallery
            $stmt3 = $db->prepare('SELECT * FROM blog_media WHERE blog_id = ? ORDER BY sort_order ASC');
            $stmt3->execute([$editBlog['id']]);
            $editBlog['media'] = $stmt3->fetchAll();
        }
    }

    $blogs = $db->query('SELECT b.*, GROUP_CONCAT(CONCAT(bt.locale,":",bt.title) SEPARATOR "|") as trans
        FROM blogs b LEFT JOIN blog_translations bt ON b.id = bt.blog_id
        GROUP BY b.id ORDER BY b.sort_order DESC, b.created_at DESC')->fetchAll();

    require __DIR__ . '/../views/admin/blogs.php';
}

/* ═══ Team Members CRUD ═══ */
function adminTeam(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    // Delete
    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM team_members WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/team'));
        exit;
    }

    // Save (create or update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $imageUrl = trim($_POST['image_url'] ?? '');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE team_members SET image_url=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$imageUrl, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO team_members (image_url, sort_order, is_active) VALUES (?, ?, ?)')
                ->execute([$imageUrl, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $name = trim($_POST['name_' . $loc] ?? '');
            $role = trim($_POST['role_' . $loc] ?? '');
            $bio = trim($_POST['bio_' . $loc] ?? '');
            $db->prepare('INSERT INTO team_member_translations (member_id, locale, name, role, bio)
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), bio=VALUES(bio)')
                ->execute([$id, $loc, $name, $role, $bio]);
        }
        $saved = true;
        $action = 'list';
    }

    // Edit form
    $editMember = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM team_members WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editMember = $stmt->fetch();
        if ($editMember) {
            $stmt2 = $db->prepare('SELECT * FROM team_member_translations WHERE member_id = ?');
            $stmt2->execute([$editMember['id']]);
            $editMember['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editMember['translations'][$t['locale']] = $t;
            }
        }
    }

    // List
    $members = $db->query('SELECT m.*, GROUP_CONCAT(CONCAT(t.locale,":",t.name) SEPARATOR "|") as trans
        FROM team_members m LEFT JOIN team_member_translations t ON m.id = t.member_id
        GROUP BY m.id ORDER BY m.sort_order')->fetchAll();

    require __DIR__ . '/../views/admin/team.php';
}

/* ═══ Testimonials CRUD ═══ */
function adminTestimonials(): void
{
    requireAdmin();
    requirePermission('content');
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

    // Delete
    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM testimonials WHERE id = ?')->execute([intval($_GET['id'])]);
        header('Location: ' . baseUrl('/admin/testimonials'));
        exit;
    }

    // Save (create or update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $clientImageUrl = trim($_POST['client_image_url'] ?? '');
        $rating = max(1, min(5, intval($_POST['rating'] ?? 5)));
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE testimonials SET client_image_url=?, rating=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$clientImageUrl, $rating, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare('INSERT INTO testimonials (client_image_url, rating, sort_order, is_active) VALUES (?, ?, ?, ?)')
                ->execute([$clientImageUrl, $rating, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $name = trim($_POST['client_name_' . $loc] ?? '');
            $company = trim($_POST['client_company_' . $loc] ?? '');
            $content = trim($_POST['content_' . $loc] ?? '');
            $db->prepare('INSERT INTO testimonial_translations (testimonial_id, locale, client_name, client_company, content)
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE client_name=VALUES(client_name), client_company=VALUES(client_company), content=VALUES(content)')
                ->execute([$id, $loc, $name, $company, $content]);
        }
        $saved = true;
        $action = 'list';
    }

    // Edit form
    $editTestimonial = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $editTestimonial = $stmt->fetch();
        if ($editTestimonial) {
            $stmt2 = $db->prepare('SELECT * FROM testimonial_translations WHERE testimonial_id = ?');
            $stmt2->execute([$editTestimonial['id']]);
            $editTestimonial['translations'] = [];
            foreach ($stmt2->fetchAll() as $t) {
                $editTestimonial['translations'][$t['locale']] = $t;
            }
        }
    }

    // List
    $testimonials = $db->query('SELECT t.*, GROUP_CONCAT(CONCAT(tt.locale,":",tt.client_name) SEPARATOR "|") as trans
        FROM testimonials t LEFT JOIN testimonial_translations tt ON t.id = tt.testimonial_id
        GROUP BY t.id ORDER BY t.sort_order')->fetchAll();

    require __DIR__ . '/../views/admin/testimonials.php';
}

/* ═══ Chatbot Editor ═══ */
function adminChatbot(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();

    // ── AJAX API Endpoints ──────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_action'])) {
        header('Content-Type: application/json');
        $apiAction = $_POST['api_action'];

        try {
            if ($apiAction === 'get_all') {
                // Return all nodes with translations and options
                $nodes = $db->query('SELECT n.* FROM chatbot_nodes n ORDER BY n.id')->fetchAll(PDO::FETCH_ASSOC);
                $result = [];
                foreach ($nodes as $node) {
                    $trans = $db->prepare('SELECT locale, message FROM chatbot_node_translations WHERE node_id = ?');
                    $trans->execute([$node['id']]);
                    $node['translations'] = [];
                    foreach ($trans->fetchAll(PDO::FETCH_ASSOC) as $t) {
                        $node['translations'][$t['locale']] = $t['message'];
                    }

                    $optStmt = $db->prepare('SELECT o.id, o.node_id, o.next_node_id, o.action_type, o.action_value, o.sort_order FROM chatbot_options o WHERE o.node_id = ? ORDER BY o.sort_order');
                    $optStmt->execute([$node['id']]);
                    $node['options'] = [];
                    foreach ($optStmt->fetchAll(PDO::FETCH_ASSOC) as $opt) {
                        $otStmt = $db->prepare('SELECT locale, label FROM chatbot_option_translations WHERE option_id = ?');
                        $otStmt->execute([$opt['id']]);
                        $opt['translations'] = [];
                        foreach ($otStmt->fetchAll(PDO::FETCH_ASSOC) as $ot) {
                            $opt['translations'][$ot['locale']] = $ot['label'];
                        }
                        $node['options'][] = $opt;
                    }
                    $result[] = $node;
                }
                echo json_encode(['success' => true, 'nodes' => $result]);
                exit;

            }
            elseif ($apiAction === 'save_node') {
                $id = intval($_POST['node_id'] ?? 0);
                $name = trim($_POST['name'] ?? 'New Node');
                $isRoot = intval($_POST['is_root'] ?? 0);
                $posX = intval($_POST['pos_x'] ?? 100);
                $posY = intval($_POST['pos_y'] ?? 100);
                $replyType = $_POST['reply_type'] ?? 'preset';
                $inputVarName = trim($_POST['input_var_name'] ?? '');

                if ($isRoot) {
                    $db->query('UPDATE chatbot_nodes SET is_root = 0');
                }

                if ($id > 0) {
                    $db->prepare('UPDATE chatbot_nodes SET name=?, is_root=?, pos_x=?, pos_y=?, reply_type=?, input_var_name=? WHERE id=?')
                        ->execute([$name, $isRoot, $posX, $posY, $replyType, $inputVarName, $id]);
                }
                else {
                    $db->prepare('INSERT INTO chatbot_nodes (name, is_root, pos_x, pos_y, reply_type, input_var_name) VALUES (?,?,?,?,?,?)')
                        ->execute([$name, $isRoot, $posX, $posY, $replyType, $inputVarName]);
                    $id = $db->lastInsertId();
                }

                // Save translations
                foreach (SUPPORTED_LOCALES as $loc) {
                    $message = trim($_POST['message_' . $loc] ?? '');
                    $db->prepare('INSERT INTO chatbot_node_translations (node_id, locale, message) VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE message = VALUES(message)')
                        ->execute([$id, $loc, $message]);
                }
                echo json_encode(['success' => true, 'id' => $id]);
                exit;

            }
            elseif ($apiAction === 'save_option') {
                $id = intval($_POST['option_id'] ?? 0);
                $nodeId = intval($_POST['node_id'] ?? 0);
                $actionType = $_POST['action_type'] ?? 'goto_node';
                $nextId = !empty($_POST['next_node_id']) ? intval($_POST['next_node_id']) : null;
                $actionVal = trim($_POST['action_value'] ?? '');
                $sort = intval($_POST['sort_order'] ?? 0);

                if ($id > 0) {
                    $db->prepare('UPDATE chatbot_options SET action_type=?, next_node_id=?, action_value=?, sort_order=? WHERE id=?')
                        ->execute([$actionType, $nextId, $actionVal, $sort, $id]);
                }
                else {
                    $db->prepare('INSERT INTO chatbot_options (node_id, action_type, next_node_id, action_value, sort_order) VALUES (?,?,?,?,?)')
                        ->execute([$nodeId, $actionType, $nextId, $actionVal, $sort]);
                    $id = $db->lastInsertId();
                }

                foreach (SUPPORTED_LOCALES as $loc) {
                    $label = trim($_POST['label_' . $loc] ?? '');
                    $db->prepare('INSERT INTO chatbot_option_translations (option_id, locale, label) VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE label = VALUES(label)')
                        ->execute([$id, $loc, $label]);
                }
                echo json_encode(['success' => true, 'id' => $id]);
                exit;

            }
            elseif ($apiAction === 'delete_node') {
                $nodeId = intval($_POST['node_id'] ?? 0);
                if ($nodeId > 0) {
                    $db->beginTransaction();
                    try {
                        // Delete node translations
                        $db->prepare('DELETE FROM chatbot_node_translations WHERE node_id = ?')->execute([$nodeId]);

                        // Find all options of this node and delete their translations
                        $opts = $db->prepare('SELECT id FROM chatbot_options WHERE node_id = ?');
                        $opts->execute([$nodeId]);
                        $optIds = $opts->fetchAll(PDO::FETCH_COLUMN);
                        if (!empty($optIds)) {
                            $placeholders = implode(',', array_fill(0, count($optIds), '?'));
                            $db->prepare("DELETE FROM chatbot_option_translations WHERE option_id IN ($placeholders)")->execute($optIds);
                        }

                        // Delete the options
                        $db->prepare('DELETE FROM chatbot_options WHERE node_id = ?')->execute([$nodeId]);

                        // Break incoming connections from other nodes
                        $db->prepare('UPDATE chatbot_options SET next_node_id = NULL WHERE next_node_id = ?')->execute([$nodeId]);

                        // Finally delete the node
                        $db->prepare('DELETE FROM chatbot_nodes WHERE id = ?')->execute([$nodeId]);

                        $db->commit();
                        echo json_encode(['success' => true]);
                    }
                    catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
                else {
                    echo json_encode(['success' => true]);
                }
                exit;

            }
            elseif ($apiAction === 'delete_option') {
                $optId = intval($_POST['option_id'] ?? 0);
                if ($optId > 0) {
                    $db->prepare('DELETE FROM chatbot_option_translations WHERE option_id = ?')->execute([$optId]);
                    $db->prepare('DELETE FROM chatbot_options WHERE id = ?')->execute([$optId]);
                }
                echo json_encode(['success' => true]);
                exit;

            }
            elseif ($apiAction === 'save_positions') {
                $positions = json_decode($_POST['positions'] ?? '[]', true);
                if (is_array($positions)) {
                    $stmt = $db->prepare('UPDATE chatbot_nodes SET pos_x = ?, pos_y = ? WHERE id = ?');
                    foreach ($positions as $pos) {
                        $stmt->execute([intval($pos['x']), intval($pos['y']), intval($pos['id'])]);
                    }
                }
                echo json_encode(['success' => true]);
                exit;
            }
        }
        catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // ── Render the visual builder page ───────────────────────
    require __DIR__ . '/../views/admin/chatbot.php';
}

/* ═══ Chatbot Inbox ═══ */
function adminInbox(): void
{
    requireAdmin();
    requirePermission('inbox');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';
    $selectedId = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Fetch all sessions for the sidebar
    $sessions = $db->query('
        SELECT s.*, count(m.id) as msg_count 
        FROM chatbot_sessions s 
        LEFT JOIN chatbot_messages m ON s.id = m.session_id 
        GROUP BY s.id 
        ORDER BY s.updated_at DESC
    ')->fetchAll();

    $sessionData = null;
    $messages = [];

    if ($selectedId) {
        $session = $db->prepare('SELECT * FROM chatbot_sessions WHERE id = ?');
        $session->execute([$selectedId]);
        $sessionData = $session->fetch();

        if ($sessionData) {
            // Mark session as read
            if ($sessionData['is_read'] == 0) {
                $db->prepare('UPDATE chatbot_sessions SET is_read = 1 WHERE id = ?')->execute([$selectedId]);
                $sessionData['is_read'] = 1;
            }

            $messagesStmt = $db->prepare('SELECT * FROM chatbot_messages WHERE session_id = ? ORDER BY created_at ASC');
            $messagesStmt->execute([$selectedId]);
            $messages = $messagesStmt->fetchAll();
        }
    }

    require __DIR__ . '/../views/admin/inbox.php';
}

/* ═══ Invoices & Quotes ═══ */
function adminInvoices(): void
{
    requireAdmin();
    requirePermission('crm');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        try {
            $db->prepare('DELETE FROM invoices WHERE id = ?')->execute([intval($_GET['id'])]);
            setFlash('Invoice deleted successfully.', 'success');
        }
        catch (PDOException $e) {
            setFlash('Cannot delete invoice because it contains items or payments.', 'error');
        }
        header('Location: ' . baseUrl('admin/invoices'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? 'invoice';
        $invoiceNumber = trim($_POST['invoice_number'] ?? '');
        $clientName = trim($_POST['client_name'] ?? '');
        $clientEmail = trim($_POST['client_email'] ?? '');
        $clientPhone = trim($_POST['client_phone'] ?? '');
        $clientAddress = trim($_POST['client_address'] ?? '');
        $discount = floatval($_POST['discount'] ?? 0);
        $vatRate = floatval($_POST['vat_rate'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        $notes = trim($_POST['notes'] ?? '');
        $terms = trim($_POST['terms'] ?? '');
        $contactId = intval($_POST['contact_id'] ?? 0);
        $invoiceCurrency = $_POST['invoice_currency'] ?? 'AED';
        $paymentTerms = trim($_POST['payment_terms'] ?? '');
        $amountPaid = floatval($_POST['amount_paid'] ?? 0);
        $salespersonId = intval($_POST['salesperson_id'] ?? 0);

        // Check uniqueness of invoice number (skip own record)
        $checkStmt = $db->prepare('SELECT id FROM invoices WHERE invoice_number = ? AND id != ?');
        $checkStmt->execute([$invoiceNumber, $id]);
        if ($checkStmt->fetch()) {
            $invoiceNumber .= '-' . rand(100, 999); // Append random suffix if duplicate
        }

        if ($id > 0) {
            $db->prepare('UPDATE invoices SET type=?, invoice_number=?, client_name=?, client_email=?, client_phone=?, client_address=?, discount=?, vat_rate=?, status=?, notes=?, terms=?, contact_id=?, invoice_currency=?, payment_terms=?, amount_paid=?, salesperson_id=? WHERE id=?')
                ->execute([$type, $invoiceNumber, $clientName, $clientEmail, $clientPhone, $clientAddress, $discount, $vatRate, $status, $notes, $terms, $contactId ?: null, $invoiceCurrency, $paymentTerms, $amountPaid, $salespersonId ?: null, $id]);
        }
        else {
            $db->prepare('INSERT INTO invoices (type, invoice_number, client_name, client_email, client_phone, client_address, discount, vat_rate, status, notes, terms, contact_id, invoice_currency, payment_terms, amount_paid, salesperson_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([$type, $invoiceNumber, $clientName, $clientEmail, $clientPhone, $clientAddress, $discount, $vatRate, $status, $notes, $terms, $contactId ?: null, $invoiceCurrency, $paymentTerms, $amountPaid, $salespersonId ?: null]);
            $id = $db->lastInsertId();
        }

        // Handle Extra Salespeople
        $db->prepare('DELETE FROM crm_invoice_salespeople WHERE invoice_id = ?')->execute([$id]);
        if (isset($_POST['extra_salesperson_ids']) && is_array($_POST['extra_salesperson_ids'])) {
            $stmtExtra = $db->prepare('INSERT INTO crm_invoice_salespeople (invoice_id, admin_id) VALUES (?, ?)');
            foreach ($_POST['extra_salesperson_ids'] as $extraId) {
                if ($extraId == $salespersonId)
                    continue; // Skip if same as primary
                $stmtExtra->execute([$id, intval($extraId)]);
            }
        }

        // Log salesperson change if applicable
        if (isset($invoice) && $invoice['salesperson_id'] != $salespersonId) {
            logActivity('Invoice Update', "Changed primary salesperson for invoice #{$invoiceNumber}");
        }
        elseif (!isset($invoice) && $salespersonId) {
            logActivity('Invoice Create', "Assigned primary salesperson to invoice #{$invoiceNumber}");
        }

        // Handle Items
        $db->prepare('DELETE FROM invoice_items WHERE invoice_id = ?')->execute([$id]);
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $stmt = $db->prepare('INSERT INTO invoice_items (invoice_id, service_name, description, qty, unit_price, vat_rate) VALUES (?, ?, ?, ?, ?, ?)');
            foreach ($_POST['items'] as $item) {
                if (empty(trim($item['service_name'])))
                    continue;
                $stmt->execute([
                    $id,
                    trim($item['service_name']),
                    trim($item['description'] ?? ''),
                    floatval($item['qty'] ?? 1),
                    floatval($item['unit_price'] ?? 0),
                    floatval($item['vat_rate'] ?? 0)
                ]);
            }
        }

        header('Location: ' . baseUrl('admin/invoices?action=edit&id=' . $id . '&saved=1'));
        exit;
    }

    // Fetch contacts for dropdown
    $contacts = $db->query('SELECT * FROM contacts ORDER BY name ASC')->fetchAll();

    // Fetch salespersons for dropdown
    $salespersons = $db->query("SELECT id, username, full_name, avatar_emoji, is_salesperson FROM admins WHERE is_salesperson = 1 OR username = 'admin' ORDER BY full_name, username")->fetchAll();

    if ($action === 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM invoices WHERE id = ?";
        $params = [$id];
        if (!isSuperAdmin()) {
            $adminId = (int)$_SESSION['admin_id'];
            $sql .= " AND (salesperson_id = ? OR id IN (SELECT invoice_id FROM crm_invoice_salespeople WHERE admin_id = ?))";
            $params[] = $adminId;
            $params[] = $adminId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            setFlash('Access denied or invoice not found.', 'error');
            redirect('admin/invoices');
        }

        $itemsStmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC');
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll();

        // Fetch Extra Salespeople
        $stmtEx = $db->prepare("SELECT admin_id FROM crm_invoice_salespeople WHERE invoice_id = ?");
        $stmtEx->execute([$id]);
        $extra_salesperson_ids = $stmtEx->fetchAll(PDO::FETCH_COLUMN);

        require __DIR__ . '/../views/admin/invoice_edit.php';
        return;
    }

    if ($action === 'new') {
        // Auto-generate sequential unique number like S0000001
        $lastNum = $db->query("SELECT invoice_number FROM invoices WHERE invoice_number LIKE 'S%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        if ($lastNum) {
            $numPart = intval(substr($lastNum, 1));
            $invoiceNumber = 'S' . str_pad($numPart + 1, 7, '0', STR_PAD_LEFT);
        }
        else {
            $invoiceNumber = 'S0000001';
        }
        $defaultTerms = getSetting('invoice_terms', 'Payment is due within 15 days of issue.');
        require __DIR__ . '/../views/admin/invoice_edit.php';
        return;
    }

    if ($action === 'print' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM invoices WHERE id = ?";
        $params = [$id];
        if (!isSuperAdmin()) {
            $adminId = (int)$_SESSION['admin_id'];
            $sql .= " AND (salesperson_id = ? OR id IN (SELECT invoice_id FROM crm_invoice_salespeople WHERE admin_id = ?))";
            $params[] = $adminId;
            $params[] = $adminId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoice = $stmt->fetch();
        if (!$invoice)
            die('Access denied or invoice not found.');

        $itemsStmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC');
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll();

        require __DIR__ . '/../views/admin/invoice_print.php';
        return;
    }

    if ($action === 'receipt' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM invoices WHERE id = ?";
        $params = [$id];
        if (!isSuperAdmin()) {
            $adminId = (int)$_SESSION['admin_id'];
            $sql .= " AND (salesperson_id = ? OR id IN (SELECT invoice_id FROM crm_invoice_salespeople WHERE admin_id = ?))";
            $params[] = $adminId;
            $params[] = $adminId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoice = $stmt->fetch();
        if (!$invoice)
            die('Access denied or invoice not found.');

        $itemsStmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC');
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll();

        require __DIR__ . '/../views/admin/invoice_receipt.php';
        return;
    }

    // List
    $sql = "SELECT * FROM invoices";
    $params = [];
    if (!isSuperAdmin()) {
        $adminId = (int)$_SESSION['admin_id'];
        $sql .= " WHERE (salesperson_id = ? OR id IN (SELECT invoice_id FROM crm_invoice_salespeople WHERE admin_id = ?))";
        $params[] = $adminId;
        $params[] = $adminId;
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll();
    require __DIR__ . '/../views/admin/invoice_list.php';
}

/* ═══ Contacts / CRM ═══ */
function adminContacts(): void
{
    requireAdmin();
    requirePermission('crm');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        try {
            $db->prepare('DELETE FROM contacts WHERE id = ?')->execute([intval($_GET['id'])]);
            setFlash('Contact deleted successfully.', 'success');
        }
        catch (PDOException $e) {
            setFlash('Cannot delete contact because it is linked to invoices or opportunities.', 'error');
        }
        header('Location: ' . baseUrl('admin/contacts'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $data = [
            trim($_POST['name'] ?? ''),
            $_POST['type'] ?? 'company',
            trim($_POST['phone'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['vat_number'] ?? ''),
            trim($_POST['website'] ?? ''),
            trim($_POST['location'] ?? ''),
            trim($_POST['country'] ?? ''),
            trim($_POST['poc_details'] ?? ''),
            trim($_POST['source'] ?? '')
        ];

        if ($id > 0) {
            $db->prepare('UPDATE contacts SET name=?, type=?, phone=?, email=?, vat_number=?, website=?, location=?, country=?, poc_details=?, source=? WHERE id=?')
                ->execute(array_merge($data, [$id]));
        }
        else {
            $db->prepare('INSERT INTO contacts (name, type, phone, email, vat_number, website, location, country, poc_details, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute($data);
            $id = $db->lastInsertId();
        }
        header('Location: ' . baseUrl('admin/contacts?action=edit&id=' . $id . '&saved=1'));
        exit;
    }

    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $db->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([intval($_GET['id'])]);
        $contact = $stmt->fetch();
        require __DIR__ . '/../views/admin/contacts.php';
        return;
    }

    if ($action === 'new') {
        $contact = null;
        require __DIR__ . '/../views/admin/contacts.php';
        return;
    }

    // List
    $contacts = $db->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
    require __DIR__ . '/../views/admin/contacts.php';
}

/* ═══ Admin Profile ═══ */
function adminProfile(): void
{
    $db = getDB();
    $saved = false;
    $error = '';
    $adminId = $_SESSION['admin_id'] ?? 0;

    $stmt = $db->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = trim($_POST['full_name'] ?? '');
        $recoveryEmail = trim($_POST['recovery_email'] ?? '');
        $recoveryPhone = trim($_POST['recovery_phone'] ?? '');
        $avatarEmoji = trim($_POST['avatar_emoji'] ?? '👤');

        $db->prepare('UPDATE admins SET full_name=?, recovery_email=?, recovery_phone=?, avatar_emoji=? WHERE id=?')
            ->execute([$fullName, $recoveryEmail, $recoveryPhone, $avatarEmoji, $adminId]);

        // Handle password change
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';
        if (!empty($newPass)) {
            if ($newPass === $confirmPass) {
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                $db->prepare('UPDATE admins SET password=? WHERE id=?')->execute([$hashed, $adminId]);
            }
            else {
                $error = 'Passwords do not match.';
            }
        }

        // Re-fetch after save
        $stmt = $db->prepare('SELECT * FROM admins WHERE id = ?');
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();

        if (empty($error))
            $saved = true;
    }

    require __DIR__ . '/../views/admin/profile.php';
}
/* ═══ Sitemap Editor ═══ */
function adminSitemap(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();
    $saved = false;
    $sitemapPath = __DIR__ . '/../sitemap.xml';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'save';

        if ($action === 'regenerate') {
            $xml = generateDynamicSitemap();
            file_put_contents($sitemapPath, $xml);
            $saved = true;
        }
        elseif ($action === 'save') {
            $content = $_POST['sitemap_content'] ?? '';
            file_put_contents($sitemapPath, $content);
            $saved = true;
        }
    }

    $currentContent = file_exists($sitemapPath) ? file_get_contents($sitemapPath) : '';
    require __DIR__ . '/../views/admin/sitemap.php';
}

/* ═══ Email Marketing ═══ */
function adminEmailMarketing(): void
{
    requireAdmin();
    requirePermission('crm');
    $db = getDB();
    $saved = false;
    $sent = false;
    $error = '';

    // Ensure tables exist
    try {
        $db->query("SELECT 1 FROM email_settings LIMIT 1");
        $db->query("SELECT 1 FROM marketing_campaigns LIMIT 1");
        $db->query("SELECT 1 FROM marketing_recipients LIMIT 1");
    }
    catch (Exception $e) {
        // Tables missing - try to apply schema
        $schemaFile = __DIR__ . '/../migrations/email_schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            // Split by semicolon and filter empty statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $db->exec($stmt);
                }
            }
        }
        else {
            die("Marketing tables missing and schema file not found.");
        }
    }

    // Fetch settings
    $settings = $db->query('SELECT * FROM email_settings LIMIT 1')->fetch();
    if (!$settings) {
        $db->exec('INSERT INTO email_settings (id, from_name) VALUES (1, "Mico Sage Team")');
        $settings = $db->query('SELECT * FROM email_settings LIMIT 1')->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_settings') {
            $smtpHost = $_POST['smtp_host'] ?? '';
            $smtpPort = intval($_POST['smtp_port'] ?? 587);
            $smtpUser = $_POST['smtp_user'] ?? '';
            $smtpPass = $_POST['smtp_pass'] ?? '';

            // Auto-detect encryption based on standard ports
            if ($smtpPort === 465) {
                $smtpEnc = 'ssl';
            }
            elseif ($smtpPort === 587) {
                $smtpEnc = 'tls';
            }
            else {
                $smtpEnc = 'none';
            }

            // Test Connection
            $tester = new MicoSMTP($smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEnc);
            $testRes = $tester->testConnection();

            if ($testRes === true) {
                $db->prepare('UPDATE email_settings SET 
                    smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?, smtp_encryption=?, 
                    from_email=?, from_name=?, imap_host=?, imap_port=?, signature_html=? WHERE id=1')
                    ->execute([
                    $smtpHost,
                    $smtpPort,
                    $smtpUser,
                    $smtpPass,
                    $smtpEnc,
                    $_POST['from_email'] ?? '',
                    $_POST['from_name'] ?? '',
                    $_POST['imap_host'] ?? '',
                    intval($_POST['imap_port'] ?? 993),
                    $_POST['signature_html'] ?? ''
                ]);
                $saved = true;
                // Re-fetch
                $settings = $db->query('SELECT * FROM email_settings LIMIT 1')->fetch();
            }
            else {
                $error = "SMTP Verification Failed: " . $testRes;
            }
        }

        if ($action === 'send_campaign') {
            $subject = trim($_POST['subject'] ?? '');
            $body = trim($_POST['body'] ?? '');
            $type = $_POST['send_type'] ?? 'bulk';

            $emails = [];
            if ($type === 'single') {
                $rawEmails = $_POST['single_recipients'] ?? '';
                $parts = explode(',', $rawEmails);
                foreach ($parts as $p) {
                    $email = trim($p);
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $email;
                    }
                }
                if (empty($emails))
                    $error = "No valid recipient emails provided.";
            }
            else {
                // Bulk / CSV Upload
                if (!empty($_FILES['email_list']['tmp_name'])) {
                    if (($handle = fopen($_FILES['email_list']['tmp_name'], "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $data[0];
                            }
                        }
                        fclose($handle);
                    }
                }
                if (empty($emails))
                    $error = "No valid emails found in the uploaded CSV.";
            }

            if (empty($error) && !empty($emails)) {
                // Register campaign in DB
                $db->prepare('INSERT INTO marketing_campaigns (subject, body, total_emails, status) VALUES (?, ?, ?, ?)')
                    ->execute([$subject, $body, count($emails), 'sending']);
                $campaignId = $db->lastInsertId();

                // Initialize SMTP
                $mailer = new MicoSMTP(
                    $settings['smtp_host'],
                    $settings['smtp_port'],
                    $settings['smtp_user'],
                    $settings['smtp_pass'],
                    $settings['smtp_encryption']
                    );

                $successCount = 0;
                $failCount = 0;

                foreach ($emails as $email) {
                    $res = $mailer->send($email, $settings['from_email'], $settings['from_name'], $subject, $body, $settings['signature_html']);

                    $status = $res ? 'sent' : 'failed';
                    if ($res)
                        $successCount++;
                    else
                        $failCount++;

                    $db->prepare('INSERT INTO marketing_recipients (campaign_id, email, status, sent_at) VALUES (?, ?, ?, NOW())')
                        ->execute([$campaignId, $email, $status]);
                }

                $db->prepare('UPDATE marketing_campaigns SET sent_count=?, failed_count=?, status=? WHERE id=?')
                    ->execute([$successCount, $failCount, 'completed', $campaignId]);

                $sent = true;
            }
        }
    }

    // Fetch campaigns
    $campaigns = $db->query('SELECT * FROM marketing_campaigns ORDER BY created_at DESC LIMIT 20')->fetchAll();

    require __DIR__ . '/../views/admin/email_marketing.php';
}

// ── App Ecosystem ──────────────────────────────────────────

function adminAppManager(): void
{
    requireAdmin();
    requirePermission('settings');
    _ensureAppTables();
    $db = getDB();

    $totalProducts = $db->query("SELECT COUNT(*) FROM app_products")->fetchColumn();
    $totalLicenses = $db->query("SELECT COUNT(*) FROM app_licenses")->fetchColumn();
    $activeLicenses = $db->query("SELECT COUNT(*) FROM app_licenses WHERE status='active'")->fetchColumn();
    $onlineDevices = $db->query("SELECT COUNT(*) FROM app_devices WHERE is_online=1")->fetchColumn();
    $totalInstalls = $db->query("SELECT SUM(total_installs) FROM app_products")->fetchColumn() ?: 0;

    $categoryStats = $db->query("SELECT c.name, c.icon, c.color, (SELECT COUNT(*) FROM app_products p WHERE p.category_id = c.id) as product_count FROM app_categories c ORDER BY c.sort_order")->fetchAll();
    $licenseStatusDist = $db->query("SELECT status, COUNT(*) as cnt FROM app_licenses GROUP BY status")->fetchAll();

    // Unified Feed: Heartbeats + Downloads
    $recentConnections = $db->query("SELECT l.*, d.hostname, d.ip_address, d.app_version, lic.license_key, p.name as product_name 
        FROM app_device_logs l 
        LEFT JOIN app_devices d ON l.device_id = d.id 
        LEFT JOIN app_licenses lic ON d.license_id = lic.id
        LEFT JOIN app_products p ON lic.product_id = p.id
        ORDER BY l.created_at DESC LIMIT 15")->fetchAll();

    require __DIR__ . '/../views/admin/app_manager.php';
}

function adminAppCategories(): void
{
    requireAdmin();
    requirePermission('settings');
    _ensureAppTables();
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM app_categories WHERE id = ?')->execute([intval($_GET['id'])]);
        setFlash('Category deleted.', 'success');
        header('Location: ' . baseUrl('admin/app-categories'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug))
            $slug = strtolower(str_replace(' ', '-', $name));
        $icon = trim($_POST['icon'] ?? 'ph-cube');
        $color = trim($_POST['color'] ?? 'cyan');
        $desc = trim($_POST['description'] ?? '');
        $sort = intval($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $db->prepare('UPDATE app_categories SET name=?, slug=?, icon=?, color=?, description=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$name, $slug, $icon, $color, $desc, $sort, $active, $id]);
        }
        else {
            $db->prepare('INSERT INTO app_categories (name, slug, icon, color, description, sort_order, is_active) VALUES (?,?,?,?,?,?,?)')
                ->execute([$name, $slug, $icon, $color, $desc, $sort, $active]);
        }
        setFlash('Category saved.', 'success');
        header('Location: ' . baseUrl('admin/app-categories'));
        exit;
    }

    $categories = $db->query("SELECT c.*, (SELECT COUNT(*) FROM app_products p WHERE p.category_id = c.id) as product_count FROM app_categories c ORDER BY c.sort_order")->fetchAll();
    require __DIR__ . '/../views/admin/app_categories.php';
}

function adminAppProducts(): void
{
    requireAdmin();
    requirePermission('settings');
    _ensureAppTables();
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM app_products WHERE id = ?')->execute([intval($_GET['id'])]);
        setFlash('Product deleted.', 'success');
        header('Location: ' . baseUrl('admin/app-products'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $catId = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug))
            $slug = strtolower(str_replace(' ', '-', $name));
        $version = trim($_POST['version'] ?? '1.0.0');
        $iconUrl = trim($_POST['icon_url'] ?? '');
        $headerImage = trim($_POST['header_image'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $downloadUrl = trim($_POST['download_url'] ?? '');
        $buyUrl = trim($_POST['buy_url'] ?? '');
        $model = $_POST['pricing_model'] ?? 'free';
        $price = floatval($_POST['price'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        $showBuy = isset($_POST['show_buy_button']) ? 1 : 0;
        $isPublic = isset($_POST['is_public']) ? 1 : 0;
        $showPrice = isset($_POST['show_price']) ? 1 : 0;
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeys = trim($_POST['meta_keywords'] ?? '');

        // Handle Uploads
        $uploadDir = __DIR__ . '/../uploads/apps/';
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0755, true);

        $uploadFields = [
            'icon_file' => 'iconUrl',
            'header_file' => 'headerImage',
            'software_file' => 'downloadUrl'
        ];

        foreach ($uploadFields as $field => $targetVar) {
            if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $newName = $slug . '_' . $field . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $newName)) {
                    $$targetVar = 'uploads/apps/' . $newName;
                }
            }
        }

        if ($id > 0) {
            $db->prepare('UPDATE app_products SET category_id=?, name=?, slug=?, version=?, icon_url=?, header_image=?, description=?, features=?, download_url=?, buy_url=?, show_buy_button=?, pricing_model=?, price=?, is_active=?, is_public=?, show_price=?, meta_description=?, meta_keywords=? WHERE id=?')
                ->execute([$catId, $name, $slug, $version, $iconUrl, $headerImage, $desc, $features, $downloadUrl, $buyUrl, $showBuy, $model, $price, $active, $isPublic, $showPrice, $metaDesc, $metaKeys, $id]);
        }
        else {
            $db->prepare('INSERT INTO app_products (category_id, name, slug, version, icon_url, header_image, description, features, download_url, buy_url, show_buy_button, pricing_model, price, is_active, is_public, show_price, meta_description, meta_keywords) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$catId, $name, $slug, $version, $iconUrl, $headerImage, $desc, $features, $downloadUrl, $buyUrl, $showBuy, $model, $price, $active, $isPublic, $showPrice, $metaDesc, $metaKeys]);
            $id = $db->lastInsertId();
        }

        // Handle gallery uploads
        if (!empty($_FILES['gallery_files']['name'][0])) {
            foreach ($_FILES['gallery_files']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery_files']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['gallery_files']['name'][$key], PATHINFO_EXTENSION);
                    $newName = $slug . '_gallery_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                        $db->prepare("INSERT INTO app_product_images (product_id, image_path) VALUES (?, ?)")
                            ->execute([$id, 'uploads/apps/' . $newName]);
                    }
                }
            }
        }

        setFlash('Product saved.', 'success');
        header('Location: ' . baseUrl('admin/app-products'));
        exit;
    }

    $products = $db->query("SELECT p.*, c.name as category_name, c.color as category_color, 
        (SELECT COUNT(*) FROM app_licenses l WHERE l.product_id = p.id) as license_count,
        (SELECT COUNT(*) FROM app_licenses l2 WHERE l2.product_id = p.id AND l2.status='active') as active_license_count
        FROM app_products p 
        LEFT JOIN app_categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC")->fetchAll();
    $categories = $db->query("SELECT id, name FROM app_categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    require __DIR__ . '/../views/admin/app_products.php';
}

function adminAppDownloadTrack(): void
{
    requireAdmin();
    $db = getDB();
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0)
        die('Invalid ID');

    $db->prepare("UPDATE app_products SET download_count = download_count + 1, total_installs = total_installs + 1 WHERE id = ?")->execute([$id]);

    // Add a log entry for notification
    $product = $db->query("SELECT name FROM app_products WHERE id = $id")->fetch();
    $db->prepare("INSERT INTO app_device_logs (device_id, event_type, details) VALUES (1, 'download', ?)")
        ->execute(["Download started: " . ($product['name'] ?? 'Unknown App')]);

    setFlash('Download tracking updated.', 'success');
    header('Location: ' . baseUrl('admin/app-manager'));
    exit;
}

function adminAppLicenses(): void
{
    requireAdmin();
    requirePermission('settings');
    _ensureAppTables();
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare("DELETE FROM app_licenses WHERE id = ?")->execute([intval($_GET['id'])]);
        setFlash('License revoked and deleted.', 'success');
        header('Location: ' . baseUrl('admin/app-licenses'));
        exit;
    }

    if ($action === 'bulk') {
        $ids = $_POST['selected_ids'] ?? [];
        $bulkAction = $_POST['bulk_action'] ?? '';
        if (!empty($ids) && in_array($bulkAction, ['suspend', 'revoke', 'activate'])) {
            $statusMap = ['suspend' => 'suspended', 'revoke' => 'revoked', 'activate' => 'active'];
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $db->prepare("UPDATE app_licenses SET status=? WHERE id IN ($placeholders)")
                ->execute(array_merge([$statusMap[$bulkAction]], array_map('intval', $ids)));
            setFlash(count($ids) . ' license(s) updated.', 'success');
        }
        header('Location: ' . baseUrl('admin/app-licenses'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'bulk') {
        $id = intval($_POST['id'] ?? 0);
        $productId = intval($_POST['product_id'] ?? 0);
        $licenseKey = trim($_POST['license_key'] ?? '');
        if (empty($licenseKey))
            $licenseKey = _generateLicenseKey();
        $label = trim($_POST['label'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $type = $_POST['type'] ?? 'standard';
        $maxDevices = intval($_POST['max_devices'] ?? 1);
        $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        $notes = trim($_POST['notes'] ?? '');

        $aboutText = trim($_POST['about_text'] ?? '');
        $useCount = intval($_POST['use_count'] ?? 0);
        $maxUseCount = intval($_POST['max_use_count'] ?? 0);

        if ($id > 0) {
            $db->prepare('UPDATE app_licenses SET product_id=?, license_key=?, label=?, status=?, type=?, max_devices=?, expires_at=?, notes=?, about_text=?, use_count=?, max_use_count=? WHERE id=?')
                ->execute([$productId, $licenseKey, $label, $status, $type, $maxDevices, $expiresAt, $notes, $aboutText, $useCount, $maxUseCount, $id]);
        }
        else {
            $db->prepare('INSERT INTO app_licenses (product_id, license_key, label, status, type, max_devices, expires_at, notes, about_text, use_count, max_use_count) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$productId, $licenseKey, $label, $status, $type, $maxDevices, $expiresAt, $notes, $aboutText, $useCount, $maxUseCount]);
            $id = $db->lastInsertId();
        }

        if (isset($_POST['feature_keys']) && is_array($_POST['feature_keys'])) {
            $db->prepare('DELETE FROM app_license_features WHERE license_id = ?')->execute([$id]);
            $fStmt = $db->prepare('INSERT INTO app_license_features (license_id, feature_key, feature_value) VALUES (?,?,?)');
            foreach ($_POST['feature_keys'] as $i => $fk) {
                $fk = trim($fk);
                $fv = trim($_POST['feature_values'][$i] ?? '');
                if (!empty($fk))
                    $fStmt->execute([$id, $fk, $fv]);
            }
        }

        $boundHardwareId = trim($_POST['bound_hardware_id'] ?? '');
        if (!empty($boundHardwareId)) {
            $db->prepare('INSERT INTO app_license_features (license_id, feature_key, feature_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE feature_value = VALUES(feature_value)')
                ->execute([$id, 'bound_hardware_id', $boundHardwareId]);
        }
        else {
            $db->prepare("DELETE FROM app_license_features WHERE license_id = ? AND feature_key = 'bound_hardware_id'")->execute([$id]);
        }

        setFlash('License saved.', 'success');
        header('Location: ' . baseUrl('admin/app-licenses'));
        exit;
    }

    $licenses = $db->query("SELECT l.*, p.name as product_name, 
        (SELECT COUNT(*) FROM app_devices d WHERE d.license_id = l.id) as device_count,
        (SELECT COUNT(*) FROM app_devices d2 WHERE d2.license_id = l.id AND d2.is_online=1) as online_count
        FROM app_licenses l JOIN app_products p ON l.product_id = p.id
        ORDER BY l.created_at DESC")->fetchAll();

    $allProducts = $db->query("SELECT id, name FROM app_products WHERE is_active=1 ORDER BY name")->fetchAll();

    $editLicense = null;
    $editFeatures = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $editId = intval($_GET['id']);
        $stmt = $db->prepare("SELECT * FROM app_licenses WHERE id = ?");
        $stmt->execute([$editId]);
        $editLicense = $stmt->fetch();

        $fStmt = $db->prepare('SELECT feature_key, feature_value FROM app_license_features WHERE license_id = ?');
        $fStmt->execute([$editId]);
        foreach ($fStmt->fetchAll() as $f) {
            $editFeatures[$f['feature_key']] = $f;
        }
    }

    require __DIR__ . '/../views/admin/app_licenses.php';
}

function adminAppDevices(): void
{
    requireAdmin();
    requirePermission('settings');
    _ensureAppTables();
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'disconnect' && isset($_GET['id'])) {
        $db->prepare('UPDATE app_devices SET is_online = 0 WHERE id = ?')->execute([intval($_GET['id'])]);
        setFlash('Device disconnected.', 'success');
        header('Location: ' . baseUrl('admin/app-devices'));
        exit;
    }

    // Auto-offline stale devices
    $db->query("UPDATE app_devices SET is_online = 0 WHERE is_online = 1 AND last_heartbeat < DATE_SUB(NOW(), INTERVAL 10 MINUTE)");

    $devices = $db->query("SELECT d.*, l.license_key, l.label as license_label, p.name as product_name
        FROM app_devices d
        JOIN app_licenses l ON d.license_id = l.id
        JOIN app_products p ON l.product_id = p.id
        ORDER BY d.is_online DESC, d.last_heartbeat DESC")->fetchAll();

    $deviceLogs = [];
    foreach ($devices as $dev) {
        $stmt = $db->prepare('SELECT * FROM app_device_logs WHERE device_id = ? ORDER BY created_at DESC LIMIT 5');
        $stmt->execute([$dev['id']]);
        $deviceLogs[$dev['id']] = $stmt->fetchAll();
    }

    require __DIR__ . '/../views/admin/app_devices.php';
}

function adminSubscriptions(): void
{
    header('Location: ' . baseUrl('admin/app-manager'));
    exit;
}

function adminAppSections(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $productIds = $_POST['product_ids'] ?? [];

        if ($id > 0) {
            $db->prepare("UPDATE app_sections SET title=?, sort_order=?, is_active=? WHERE id=?")
                ->execute([$title, $sortOrder, $isActive, $id]);
        }
        else {
            $db->prepare("INSERT INTO app_sections (title, sort_order, is_active) VALUES (?, ?, ?)")
                ->execute([$title, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Update products in section
        $db->prepare("DELETE FROM app_section_products WHERE section_id = ?")->execute([$id]);
        $pStmt = $db->prepare("INSERT INTO app_section_products (section_id, product_id, sort_order) VALUES (?, ?, ?)");
        foreach ($productIds as $idx => $pid) {
            $pStmt->execute([$id, intval($pid), $idx]);
        }

        setFlash('Section saved.', 'success');
        header('Location: ' . baseUrl('admin/app-sections'));
        exit;
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db->prepare("DELETE FROM app_sections WHERE id = ?")->execute([$id]);
        $db->prepare("DELETE FROM app_section_products WHERE section_id = ?")->execute([$id]);
        setFlash('Section deleted.', 'success');
        header('Location: ' . baseUrl('admin/app-sections'));
        exit;
    }

    $sections = $db->query("SELECT * FROM app_sections ORDER BY sort_order ASC")->fetchAll();
    foreach ($sections as &$sec) {
        $sec['products'] = $db->query("SELECT p.id, p.name FROM app_products p JOIN app_section_products sp ON p.id = sp.product_id WHERE sp.section_id = " . $sec['id'] . " ORDER BY sp.sort_order")->fetchAll();
    }

    $allProducts = $db->query("SELECT id, name FROM app_products WHERE is_active=1 ORDER BY name")->fetchAll();
    require __DIR__ . '/../views/admin/app_sections.php';
}

function adminAppReviews(): void
{
    requireAdmin();
    requirePermission('settings');
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $reply = trim($_POST['admin_reply'] ?? '');
        $status = $_POST['status'] ?? 'pending';

        $db->prepare("UPDATE app_reviews SET admin_reply=?, status=? WHERE id=?")
            ->execute([$reply, $status, $id]);

        setFlash('Review updated.', 'success');
        header('Location: ' . baseUrl('admin/app-reviews'));
        exit;
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare("DELETE FROM app_reviews WHERE id=?")->execute([intval($_GET['id'])]);
        setFlash('Review deleted.', 'success');
        header('Location: ' . baseUrl('admin/app-reviews'));
        exit;
    }

    $reviews = $db->query("SELECT r.*, p.name as product_name FROM app_reviews r JOIN app_products p ON r.product_id = p.id ORDER BY r.created_at DESC")->fetchAll();
    require __DIR__ . '/../views/admin/app_reviews.php';
}

// ── Helpers ────────────────────────────────────────────────

function _ensureAppTables()
{
    $db = getDB();
    try {
        $db->prepare("ALTER TABLE app_licenses ADD COLUMN IF NOT EXISTS about_text TEXT NULL AFTER notes")->execute();
        $db->prepare("ALTER TABLE app_licenses ADD COLUMN IF NOT EXISTS use_count INT NOT NULL DEFAULT 0 AFTER about_text")->execute();
        $db->prepare("ALTER TABLE app_licenses ADD COLUMN IF NOT EXISTS max_use_count INT NOT NULL DEFAULT 0 AFTER use_count")->execute();

        $db->exec("CREATE TABLE IF NOT EXISTS app_devices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            license_id INT NOT NULL,
            hardware_id VARCHAR(255) NOT NULL,
            hostname VARCHAR(255),
            ip_address VARCHAR(45),
            app_version VARCHAR(20),
            is_online TINYINT(1) DEFAULT 1,
            last_heartbeat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unq_hw (hardware_id)
        )");

        $db->exec("CREATE TABLE IF NOT EXISTS app_device_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            device_id INT NOT NULL,
            event_type VARCHAR(50),
            details TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }
    catch (Exception $e) {
    }
}

function _generateLicenseKey()
{
    return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
        substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
        substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
        substr(md5(uniqid(mt_rand(), true)), 0, 4));
}
