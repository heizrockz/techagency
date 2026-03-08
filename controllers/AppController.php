<?php
/**
 * Public App Ecosystem Controller
 */

function publicSoftwareStore(): void
{
    $db = getDB();
    
    // 1. Fetch Featured Bento Apps (first 3)
    $featured = $db->query("SELECT p.*, c.name as category_name, c.color as category_color 
        FROM app_products p 
        JOIN app_categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 AND p.is_public = 1
        ORDER BY p.created_at DESC LIMIT 3")->fetchAll();

    // 2. Fetch Dynamic Sections with their products
    $sections = $db->query("SELECT * FROM app_sections WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
    
    // Check if we have sections defined, if not or we just want everything, we'll append an "All Apps" section
    $allProducts = $db->query("SELECT p.*, c.name as category_name, c.color as category_color 
        FROM app_products p 
        JOIN app_categories c ON p.category_id = c.id
        WHERE p.is_active = 1 AND p.is_public = 1
        ORDER BY p.created_at DESC")->fetchAll();
        
    if (empty($sections)) {
        $sections = [
            [
                'id' => 0,
                'title' => 'All Applications',
                'products' => $allProducts
            ]
        ];
    } else {
        foreach ($sections as &$sec) {
            $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color 
                FROM app_products p 
                JOIN app_section_products sp ON p.id = sp.product_id 
                JOIN app_categories c ON p.category_id = c.id
                WHERE sp.section_id = ? AND p.is_active = 1 AND p.is_public = 1
                ORDER BY sp.sort_order ASC");
            $stmt->execute([$sec['id']]);
            $sec['products'] = $stmt->fetchAll();
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
    
    // Fetch specific product
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.color as category_color 
        FROM app_products p 
        JOIN app_categories c ON p.category_id = c.id 
        WHERE p.slug = ? AND p.is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();

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
