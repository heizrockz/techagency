<?php
/**
 * Mico Sage — Admin Controller (all admin panel actions)
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/sitemap_generator.php';
require_once __DIR__ . '/../includes/smtp.php';

/* ═══ Login ═══ */
function adminLogin(): void {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if (attemptLogin($username, $password)) {
            header('Location: ' . baseUrl('/admin/dashboard'));
            exit;
        }
        $error = t('admin_login_error');
    }
    require __DIR__ . '/../views/admin/login.php';
}

/* ═══ Dashboard ═══ */
function adminDashboard(): void {
    $db = getDB();
    $totalBookings = $db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    $newBookings   = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn();
    $totalServices = $db->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    $totalClients  = $db->query('SELECT COUNT(*) FROM clients WHERE is_active = 1')->fetchColumn();
    
    $visitsStmt = $db->query("SELECT setting_value FROM site_settings WHERE setting_key = 'visit_count'");
    $visitCount = $visitsStmt ? (int)$visitsStmt->fetchColumn() : 0;
    
    $recentBookings = $db->query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5')->fetchAll();
    require __DIR__ . '/../views/admin/dashboard.php';
}

/* ═══ Bookings ═══ */
function adminBookings(): void {
    $db = getDB();
    $statusFilter = $_GET['status'] ?? 'all';
    if ($statusFilter !== 'all') {
        $stmt = $db->prepare('SELECT * FROM bookings WHERE status = ? ORDER BY created_at DESC');
        $stmt->execute([$statusFilter]);
    } else {
        $stmt = $db->query('SELECT * FROM bookings ORDER BY created_at DESC');
    }
    $bookings = $stmt->fetchAll();
    require __DIR__ . '/../views/admin/bookings.php';
}

function adminUpdateBookingStatus(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid = ['new','viewed','contacted','completed','cancelled'];
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
function adminServices(): void {
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
        } else {
            $db->prepare('INSERT INTO services (icon, color, sort_order, is_active) VALUES (?, ?, ?, ?)')
               ->execute([$icon, $color, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc  = trim($_POST['desc_' . $loc] ?? '');
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
function adminClients(): void {
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
        } else {
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
function adminProducts(): void {
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
        } else {
            $db->prepare('INSERT INTO products (icon, category, color, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
               ->execute([$icon, $category, $color, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc  = trim($_POST['desc_' . $loc] ?? '');
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
function adminBookingFields(): void {
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
        } else {
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
function adminSettings(): void {
    $db = getDB();
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $settings = $_POST['settings'] ?? [];
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
            'show_booking_section', 'show_contact_section'
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
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
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
    require __DIR__ . '/../views/admin/settings.php';
}

/* ═══ Content Editor ═══ */
function adminContent(): void {
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
        } else {
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
function adminSeo(): void {
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
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
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
        } else {
            foreach (SUPPORTED_LOCALES as $loc) {
                if (!isset($seoData[$page][$loc])) {
                    $seoData[$page][$loc] = ['title' => '', 'description' => '', 'keywords' => '', 'canonical_link' => ''];
                }
                // Ensure canonical link exists
                if(!isset($seoData[$page][$loc]['canonical_link'])) {
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
function adminTranslations(): void {
    $db = getDB();
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'save';

        if ($action === 'delete' && isset($_POST['trans_key'])) {
            $db->prepare('DELETE FROM translations WHERE trans_key = ?')->execute([$_POST['trans_key']]);
            $saved = true;
        } elseif ($action === 'save') {
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
function adminPortfolio(): void {
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
        } else {
            $db->prepare('INSERT INTO portfolio_projects (slug, image_url, demo_url, category, color, sort_order, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
               ->execute([$slug, $imageUrl, $demoUrl, $category, $color, $sortOrder, $isActive, $isFeatured]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc  = trim($_POST['desc_' . $loc] ?? '');
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
function adminBlogs(): void {
    $db = getDB();
    $saved = false;
    $action = $_GET['action'] ?? 'list';

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
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
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
        } else {
            $db->prepare('INSERT INTO blogs (slug, media_type, media_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
               ->execute([$slug, $mediaType, $mediaUrl, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        foreach (SUPPORTED_LOCALES as $loc) {
            $title = trim($_POST['title_' . $loc] ?? '');
            $desc  = trim($_POST['desc_' . $loc] ?? '');
            $db->prepare('INSERT INTO blog_translations (blog_id, locale, title, description)
                VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description)')
               ->execute([$id, $loc, $title, $desc]);
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
        }
    }

    $blogs = $db->query('SELECT b.*, GROUP_CONCAT(CONCAT(bt.locale,":",bt.title) SEPARATOR "|") as trans
        FROM blogs b LEFT JOIN blog_translations bt ON b.id = bt.blog_id
        GROUP BY b.id ORDER BY b.sort_order')->fetchAll();

    require __DIR__ . '/../views/admin/blogs.php';
}

/* ═══ Team Members CRUD ═══ */
function adminTeam(): void {
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
        } else {
            $db->prepare('INSERT INTO team_members (image_url, sort_order, is_active) VALUES (?, ?, ?)')
               ->execute([$imageUrl, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $name = trim($_POST['name_' . $loc] ?? '');
            $role = trim($_POST['role_' . $loc] ?? '');
            $bio  = trim($_POST['bio_'  . $loc] ?? '');
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
function adminTestimonials(): void {
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
        } else {
            $db->prepare('INSERT INTO testimonials (client_image_url, rating, sort_order, is_active) VALUES (?, ?, ?, ?)')
               ->execute([$clientImageUrl, $rating, $sortOrder, $isActive]);
            $id = $db->lastInsertId();
        }

        // Save translations
        foreach (SUPPORTED_LOCALES as $loc) {
            $name    = trim($_POST['client_name_' . $loc] ?? '');
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
function adminChatbot(): void {
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

            } elseif ($apiAction === 'save_node') {
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
                } else {
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

            } elseif ($apiAction === 'save_option') {
                $id = intval($_POST['option_id'] ?? 0);
                $nodeId = intval($_POST['node_id'] ?? 0);
                $actionType = $_POST['action_type'] ?? 'goto_node';
                $nextId = !empty($_POST['next_node_id']) ? intval($_POST['next_node_id']) : null;
                $actionVal = trim($_POST['action_value'] ?? '');
                $sort = intval($_POST['sort_order'] ?? 0);

                if ($id > 0) {
                    $db->prepare('UPDATE chatbot_options SET action_type=?, next_node_id=?, action_value=?, sort_order=? WHERE id=?')
                       ->execute([$actionType, $nextId, $actionVal, $sort, $id]);
                } else {
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

            } elseif ($apiAction === 'delete_node') {
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
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                } else {
                    echo json_encode(['success' => true]);
                }
                exit;

            } elseif ($apiAction === 'delete_option') {
                $optId = intval($_POST['option_id'] ?? 0);
                if ($optId > 0) {
                    $db->prepare('DELETE FROM chatbot_option_translations WHERE option_id = ?')->execute([$optId]);
                    $db->prepare('DELETE FROM chatbot_options WHERE id = ?')->execute([$optId]);
                }
                echo json_encode(['success' => true]);
                exit;

            } elseif ($apiAction === 'save_positions') {
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
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // ── Render the visual builder page ───────────────────────
    require __DIR__ . '/../views/admin/chatbot.php';
}

/* ═══ Chatbot Inbox ═══ */
function adminInbox(): void {
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
function adminInvoices(): void {
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM invoices WHERE id = ?')->execute([intval($_GET['id'])]);
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

        // Check uniqueness of invoice number (skip own record)
        $checkStmt = $db->prepare('SELECT id FROM invoices WHERE invoice_number = ? AND id != ?');
        $checkStmt->execute([$invoiceNumber, $id]);
        if ($checkStmt->fetch()) {
            $invoiceNumber .= '-' . rand(100, 999); // Append random suffix if duplicate
        }

        if ($id > 0) {
            $db->prepare('UPDATE invoices SET type=?, invoice_number=?, client_name=?, client_email=?, client_phone=?, client_address=?, discount=?, vat_rate=?, status=?, notes=?, terms=?, contact_id=?, invoice_currency=?, payment_terms=? WHERE id=?')
               ->execute([$type, $invoiceNumber, $clientName, $clientEmail, $clientPhone, $clientAddress, $discount, $vatRate, $status, $notes, $terms, $contactId ?: null, $invoiceCurrency, $paymentTerms, $id]);
        } else {
            $db->prepare('INSERT INTO invoices (type, invoice_number, client_name, client_email, client_phone, client_address, discount, vat_rate, status, notes, terms, contact_id, invoice_currency, payment_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
               ->execute([$type, $invoiceNumber, $clientName, $clientEmail, $clientPhone, $clientAddress, $discount, $vatRate, $status, $notes, $terms, $contactId ?: null, $invoiceCurrency, $paymentTerms]);
            $id = $db->lastInsertId();
        }

        // Handle Items
        $db->prepare('DELETE FROM invoice_items WHERE invoice_id = ?')->execute([$id]);
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $stmt = $db->prepare('INSERT INTO invoice_items (invoice_id, service_name, description, qty, unit_price, vat_rate) VALUES (?, ?, ?, ?, ?, ?)');
            foreach ($_POST['items'] as $item) {
                if (empty(trim($item['service_name']))) continue;
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

    if ($action === 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $db->prepare('SELECT * FROM invoices WHERE id = ?');
        $stmt->execute([$id]);
        $invoice = $stmt->fetch();
        
        $itemsStmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC');
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll();

        require __DIR__ . '/../views/admin/invoice_edit.php';
        return;
    }

    if ($action === 'new') {
        // Auto-generate sequential unique number like S0000001
        $lastNum = $db->query("SELECT invoice_number FROM invoices WHERE invoice_number LIKE 'S%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        if ($lastNum) {
            $numPart = intval(substr($lastNum, 1));
            $invoiceNumber = 'S' . str_pad($numPart + 1, 7, '0', STR_PAD_LEFT);
        } else {
            $invoiceNumber = 'S0000001';
        }
        $defaultTerms = getSetting('invoice_terms', 'Payment is due within 15 days of issue.');
        require __DIR__ . '/../views/admin/invoice_edit.php';
        return;
    }

    if ($action === 'print' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $db->prepare('SELECT * FROM invoices WHERE id = ?');
        $stmt->execute([$id]);
        $invoice = $stmt->fetch();
        
        $itemsStmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC');
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll();

        require __DIR__ . '/../views/admin/invoice_print.php';
        return;
    }

    // List
    $invoices = $db->query('SELECT * FROM invoices ORDER BY created_at DESC')->fetchAll();
    require __DIR__ . '/../views/admin/invoice_list.php';
}

/* ═══ Contacts / CRM ═══ */
function adminContacts(): void {
    $db = getDB();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'delete' && isset($_GET['id'])) {
        $db->prepare('DELETE FROM contacts WHERE id = ?')->execute([intval($_GET['id'])]);
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
        } else {
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
function adminProfile(): void {
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
            } else {
                $error = 'Passwords do not match.';
            }
        }

        // Re-fetch after save
        $stmt = $db->prepare('SELECT * FROM admins WHERE id = ?');
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();

        if (empty($error)) $saved = true;
    }

    require __DIR__ . '/../views/admin/profile.php';
}
/* ═══ Sitemap Editor ═══ */
function adminSitemap(): void {
    $db = getDB();
    $saved = false;
    $sitemapPath = __DIR__ . '/../sitemap.xml';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'save';
        
        if ($action === 'regenerate') {
            $xml = generateDynamicSitemap();
            file_put_contents($sitemapPath, $xml);
            $saved = true;
        } elseif ($action === 'save') {
            $content = $_POST['sitemap_content'] ?? '';
            file_put_contents($sitemapPath, $content);
            $saved = true;
        }
    }

    $currentContent = file_exists($sitemapPath) ? file_get_contents($sitemapPath) : '';
    require __DIR__ . '/../views/admin/sitemap.php';
}

/* ═══ Email Marketing ═══ */
function adminEmailMarketing(): void {
    $db = getDB();
    $saved = false;
    $sent = false;
    $error = '';

    // Ensure tables exist
    try {
        $db->query("SELECT 1 FROM email_settings LIMIT 1");
        $db->query("SELECT 1 FROM marketing_campaigns LIMIT 1");
        $db->query("SELECT 1 FROM marketing_recipients LIMIT 1");
    } catch (Exception $e) {
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
        } else {
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
            } elseif ($smtpPort === 587) {
                $smtpEnc = 'tls';
            } else {
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
            } else {
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
                if (empty($emails)) $error = "No valid recipient emails provided.";
            } else {
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
                if (empty($emails)) $error = "No valid emails found in the uploaded CSV.";
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
                    if ($res) $successCount++; else $failCount++;

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
