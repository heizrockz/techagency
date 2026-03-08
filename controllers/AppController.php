<?php
/**
 * Public App Ecosystem Controller
 */

function _translationsTableExists(PDO $db): bool
{
    static $exists = null;
    if ($exists === null) {
        try {
            $stmt = $db->query("SHOW TABLES LIKE 'app_product_translations'");
            $exists = $stmt->rowCount() > 0;
        } catch (Exception $e) {
            $exists = false;
        }
    }
    return $exists;
}

function publicSoftwareStore(): void
{
    $db = getDB();
    $locale = getCurrentLocale();
    $hasTrans = _translationsTableExists($db);

    // 1. Fetch Featured Bento Apps (first 3)
    if ($hasTrans) {
        $featured = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                COALESCE(t.name, p.name) as display_name,
                COALESCE(t.short_description, p.description) as display_short_desc
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id 
            LEFT JOIN app_product_translations t ON p.id = t.product_id AND t.locale = ?
            WHERE p.is_active = 1 AND p.is_public = 1
            ORDER BY p.created_at DESC LIMIT 3");
        $featured->execute([$locale]);
    } else {
        $featured = $db->query("SELECT p.*, c.name as category_name, c.color as category_color,
                p.name as display_name, p.description as display_short_desc
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 AND p.is_public = 1
            ORDER BY p.created_at DESC LIMIT 3");
    }
    $featured = $featured->fetchAll();

    foreach ($featured as &$f) {
        $f['name'] = $f['display_name'];
        $f['short_description'] = $f['display_short_desc'] ?? $f['short_description'] ?? '';
    }
    unset($f);

    // 2. Fetch Dynamic Sections with their products
    $sections = $db->query("SELECT * FROM app_sections WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
    
    // Fetch all products just in case there are unassigned or no sections
    if ($hasTrans) {
        $allProductsStmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                COALESCE(t.name, p.name) as display_name,
                COALESCE(t.short_description, p.description) as display_short_desc
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id
            LEFT JOIN app_product_translations t ON p.id = t.product_id AND t.locale = ?
            WHERE p.is_active = 1 AND p.is_public = 1
            ORDER BY p.created_at DESC");
        $allProductsStmt->execute([$locale]);
    } else {
        $allProductsStmt = $db->query("SELECT p.*, c.name as category_name, c.color as category_color,
                p.name as display_name, p.description as display_short_desc
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_public = 1
            ORDER BY p.created_at DESC");
    }
    $allProducts = $allProductsStmt->fetchAll();

    foreach ($allProducts as &$p) {
        $p['name'] = $p['display_name'];
        $p['short_description'] = $p['display_short_desc'] ?? $p['short_description'] ?? '';
    }
    unset($p);
        
    if (empty($sections)) {
        $sections = [
            [
                'id' => 0,
                'title' => getCurrentLocale() === 'ar' ? 'جميع التطبيقات' : 'All Applications',
                'products' => $allProducts
            ]
        ];
    } else {
        foreach ($sections as &$sec) {
            if ($hasTrans) {
                $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                        COALESCE(t.name, p.name) as display_name,
                        COALESCE(t.short_description, p.description) as display_short_desc
                    FROM app_products p 
                    JOIN app_section_products sp ON p.id = sp.product_id 
                    JOIN app_categories c ON p.category_id = c.id
                    LEFT JOIN app_product_translations t ON p.id = t.product_id AND t.locale = ?
                    WHERE sp.section_id = ? AND p.is_active = 1 AND p.is_public = 1
                    ORDER BY sp.sort_order ASC");
                $stmt->execute([$locale, $sec['id']]);
            } else {
                $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                        p.name as display_name, p.description as display_short_desc
                    FROM app_products p 
                    JOIN app_section_products sp ON p.id = sp.product_id 
                    JOIN app_categories c ON p.category_id = c.id
                    WHERE sp.section_id = ? AND p.is_active = 1 AND p.is_public = 1
                    ORDER BY sp.sort_order ASC");
                $stmt->execute([$sec['id']]);
            }
            $sec['products'] = $stmt->fetchAll();

            foreach ($sec['products'] as &$sp) {
                $sp['name'] = $sp['display_name'];
                $sp['short_description'] = $sp['display_short_desc'] ?? $sp['short_description'] ?? '';
            }
            unset($sp);
        }
        
        // Find orphan products that aren't in any section
        $assignedProductIds = [];
        foreach ($sections as $sec) {
            foreach ($sec['products'] as $p) {
                $assignedProductIds[] = $p['id'];
            }
        }
        
        $unassignedProducts = [];
        foreach ($allProducts as $p) {
            if (!in_array($p['id'], $assignedProductIds)) {
                $unassignedProducts[] = $p;
            }
        }
        
        // Append an "Other Applications" section if there are unassigned products
        if (!empty($unassignedProducts)) {
            $sections[] = [
                'id' => -1,
                'title' => 'New & Notable Discoveries',
                'products' => $unassignedProducts
            ];
        }
    }

    require __DIR__ . '/../views/software.php';
}

function publicSoftwareDetails(string $slug): void
{
    $db = getDB();
    $locale = getCurrentLocale();
    $hasTrans = _translationsTableExists($db);

    // Fetch specific product
    if ($hasTrans) {
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                COALESCE(t.name, p.name) as display_name,
                COALESCE(t.short_description, p.description) as display_short_desc,
                COALESCE(t.description, p.description) as display_desc,
                COALESCE(t.features, p.features) as display_features
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id 
            LEFT JOIN app_product_translations t ON p.id = t.product_id AND t.locale = ?
            WHERE p.slug = ? AND p.is_active = 1 LIMIT 1");
        $stmt->execute([$locale, $slug]);
    } else {
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color,
                p.name as display_name, p.description as display_short_desc,
                p.description as display_desc, p.features as display_features
            FROM app_products p 
            JOIN app_categories c ON p.category_id = c.id 
            WHERE p.slug = ? AND p.is_active = 1 LIMIT 1");
        $stmt->execute([$slug]);
    }
    $product = $stmt->fetch();

    if ($product) {
        $product['name'] = $product['display_name'];
        $product['short_description'] = $product['display_short_desc'] ?? $product['short_description'] ?? '';
        $product['description'] = $product['display_desc'] ?? $product['description'] ?? '';
        $product['features'] = $product['display_features'] ?? $product['features'] ?? '';
    }

    if (!$product) {
        require __DIR__ . '/../views/user/404.php';
        exit;
    }

    // Fetch gallery
    $gallery = $db->query("SELECT * FROM app_product_images WHERE product_id = " . intval($product['id']) . " ORDER BY sort_order")->fetchAll();

    // Fetch approved reviews
    $stmt = $db->prepare("SELECT * FROM app_reviews WHERE product_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $stmt->execute([$product['id']]);
    $reviews = $stmt->fetchAll();

    require __DIR__ . '/../views/software_details.php';
}

function handleReviewSubmit(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
    
    $productId = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? 'Anonymous');
    $rating = intval($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');

    if ($productId > 0 && !empty($comment)) {
        $db = getDB();
        $db->prepare("INSERT INTO app_reviews (product_id, name, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')")
           ->execute([$productId, $name, $rating, $comment]);
        
        setFlash('Review submitted! It will appear after approval.', 'success');
    }

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? baseUrl('software')));
    exit;
}
