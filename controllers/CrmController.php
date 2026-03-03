<?php
/**
 * CRM Controller (Odoo Clone features)
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

function adminCrmPipeline(): void {
    requireAdmin();
    $db = getDB();

    // Handle new POST actions for dynamic stages
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'update_stage') {
            $id = (int)($_POST['id'] ?? 0);
            $stage = trim($_POST['stage'] ?? '');
            if ($id && $stage) {
                $stmt = $db->prepare("UPDATE crm_opportunities SET stage = ? WHERE id = ?");
                $stmt->execute([$stage, $id]);
                
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['success' => true]);
                    exit;
                }
                setFlash("Stage updated to $stage.");
                redirect('admin/crm_opportunity?id=' . $id);
            } else {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['error' => 'Invalid data']);
                    exit;
                }
                redirect('admin/crm_pipeline');
            }
            exit;
        }

        if ($action === 'quick_add') {
            $title = trim($_POST['title'] ?? '');
            $stage = trim($_POST['stage'] ?? 'New Lead');
            $priority = (int)($_POST['priority'] ?? 0);
            $color = trim($_POST['color_code'] ?? '');
            
            if ($title) {
                $stmt = $db->prepare("INSERT INTO crm_opportunities (title, stage, priority, color_code) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $stage, $priority, $color]);
            }
            redirect('admin/crm_pipeline');
        }

        if ($action === 'add_stage') {
            $name = trim($_POST['name'] ?? '');
            if ($name) {
                // Determine max sort order
                $maxSort = (int)$db->query("SELECT MAX(sort_order) FROM crm_stages")->fetchColumn();
                $stmt = $db->prepare("INSERT INTO crm_stages (name, sort_order) VALUES (?, ?)");
                $stmt->execute([$name, $maxSort + 1]);
                setFlash('Stage added.');
            }
            redirect('admin/crm_pipeline');
        }

        if ($action === 'delete_stage') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                // Get the stage name to update linked opportunities
                $stageNameObj = $db->query("SELECT name FROM crm_stages WHERE id = $id")->fetch();
                if ($stageNameObj) {
                    $stmtUpdate = $db->prepare("UPDATE crm_opportunities SET stage = 'New Lead' WHERE stage = ?");
                    $stmtUpdate->execute([$stageNameObj['name']]);
                }
                
                $stmt = $db->prepare("DELETE FROM crm_stages WHERE id = ?");
                $stmt->execute([$id]);
                setFlash('Stage deleted.');
            }
            redirect('admin/crm_pipeline');
        }

        if ($action === 'toggle_collapse') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                // Toggle the boolean value
                $db->query("UPDATE crm_stages SET is_collapsed = NOT is_collapsed WHERE id = $id");
                setFlash('Stage toggled.');
            }
            redirect('admin/crm_pipeline');
        }

        if ($action === 'reorder_stages') {
            $order = $_POST['order'] ?? [];
            if (is_array($order)) {
                $stmt = $db->prepare("UPDATE crm_stages SET sort_order = ? WHERE id = ?");
                foreach ($order as $index => $stageId) {
                    $stmt->execute([$index, (int)$stageId]);
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Invalid data']);
            }
            exit;
        }

        if ($action === 'update_color') {
            $id = (int)($_POST['id'] ?? 0);
            $color = trim($_POST['color'] ?? '');
            if ($id) {
                $stmt = $db->prepare("UPDATE crm_opportunities SET color_code = ? WHERE id = ?");
                $stmt->execute([$color === 'none' ? '' : $color, $id]);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid data']);
            }
            exit;
        }

        if ($action === 'ajax_create_product') {
            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            header('Content-Type: application/json');
            if ($name) {
                $stmt = $db->prepare("INSERT INTO crm_items (name, price, category, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $category, $description]);
                $newId = $db->lastInsertId();
                echo json_encode(['success' => true, 'product' => [
                    'id' => $newId, 'name' => $name, 'price' => $price,
                    'category' => $category, 'description' => $description
                ]]);
            } else {
                echo json_encode(['error' => 'Product name is required']);
            }
            exit;
        }

        if ($action === 'create_invoice') {
            // ... (keep existing)
            $oppId = (int)($_POST['id'] ?? 0);
            $splitType = $_POST['split_type'] ?? '100'; 
            
            $stmt = $db->prepare("SELECT * FROM crm_opportunities WHERE id = ?");
            $stmt->execute([$oppId]);
            $opp = $stmt->fetch();
            
            if ($opp) {
                $totalAmount = (float)$opp['expected_revenue'];
                $splits = [];
                if ($splitType === '50-50') $splits = [0.5, 0.5];
                elseif ($splitType === '30-70') $splits = [0.3, 0.7];
                elseif ($splitType === '30-40-30') $splits = [0.3, 0.4, 0.3];
                else $splits = [1.0];

                foreach ($splits as $i => $percent) {
                    $amount = $totalAmount * $percent;
                    $invNum = 'INV-' . $oppId . '-' . ($i + 1) . '-' . rand(100, 999);
                    
                    $stmt = $db->prepare("
                        INSERT INTO invoices (opportunity_id, contact_id, client_name, client_email, client_phone, invoice_number, status, invoice_currency, payment_terms) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $oppId, $opp['contact_id'], $opp['title'], $opp['email'], $opp['phone'], 
                        $invNum, 'draft', 'AED', ($percent * 100) . '% Payment'
                    ]);
                    $invId = $db->lastInsertId();
                    
                    // Add one item for the amount
                    $stmt = $db->prepare("INSERT INTO invoice_items (invoice_id, service_name, qty, unit_price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$invId, "Payment for " . $opp['title'], 1, $amount]);
                }
                setFlash("Split invoices generated.");
                redirect('admin/crm_opportunity?id=' . $oppId);
            }
            redirect('admin/crm_pipeline');
            exit;
        }

        if ($action === 'add_opportunity_item') {
            $oppId = (int)($_POST['id'] ?? 0);
            $itemId = (int)($_POST['item_id'] ?? 0);
            $qty = (int)($_POST['qty'] ?? 1);
            
            if ($oppId && $itemId) {
                // Fetch product price
                $stmt = $db->prepare("SELECT price FROM crm_items WHERE id = ?");
                $stmt->execute([$itemId]);
                $price = (float)$stmt->fetchColumn();
                $subtotal = $price * $qty;
                
                $stmt = $db->prepare("INSERT INTO crm_opportunity_items (opportunity_id, item_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$oppId, $itemId, $qty, $price, $subtotal]);
                
                // Update expected revenue
                $db->query("UPDATE crm_opportunities SET expected_revenue = (SELECT SUM(subtotal) FROM crm_opportunity_items WHERE opportunity_id = $oppId) WHERE id = $oppId");
                
                setFlash('Product added to opportunity.');
            }
            redirect('admin/crm_opportunity?id=' . $oppId);
            exit;
        }

        if ($action === 'remove_opportunity_item') {
            $id = (int)($_POST['line_id'] ?? 0);
            $oppId = (int)($_POST['id'] ?? 0);
            if ($id) {
                $db->prepare("DELETE FROM crm_opportunity_items WHERE id = ?")->execute([$id]);
                // Update expected revenue
                $rev = (float)$db->query("SELECT SUM(subtotal) FROM crm_opportunity_items WHERE opportunity_id = $oppId")->fetchColumn();
                $db->prepare("UPDATE crm_opportunities SET expected_revenue = ? WHERE id = ?")->execute([$rev, $oppId]);
                setFlash('Product removed.');
            }
            redirect('admin/crm_opportunity?id=' . $oppId);
            exit;
        }

        if ($action === 'delete_opportunity') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                // Delete items, attachments, notes first
                $db->prepare("DELETE FROM crm_opportunity_items WHERE opportunity_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM crm_log_notes WHERE opportunity_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM crm_opportunities WHERE id = ?")->execute([$id]);
                setFlash('Opportunity deleted.');
            }
            redirect('admin/crm_pipeline');
            exit;
        }

        if ($action === 'delete_log_note') {
            $id = (int)($_POST['note_id'] ?? 0);
            $oppId = (int)($_POST['id'] ?? 0);
            if ($id) {
                // Delete attachments first
                $db->prepare("DELETE FROM crm_attachments WHERE linked_type = 'log_note' AND linked_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM crm_log_notes WHERE id = ?")->execute([$id]);
                setFlash('Log note deleted.');
            }
            redirect('admin/crm_opportunity?id=' . $oppId);
            exit;
        }
    }

    // Handle Search & View
    $search = trim($_GET['search'] ?? '');
    $view = trim($_GET['view'] ?? 'kanban'); // 'kanban' or 'list'

    // Fetch all opportunities with optional search
    $sql = "SELECT o.*, c.name as contact_name FROM crm_opportunities o LEFT JOIN contacts c ON o.contact_id = c.id";
    $params = [];
    
    if ($search) {
        if ($search === 'Won') {
            $sql .= " WHERE o.stage = 'Won'";
        } elseif ($search === 'Lost') {
            $sql .= " WHERE o.stage = 'Lost'";
        } elseif ($search === 'New') {
            $sql .= " WHERE o.stage = 'New Lead'";
        } else {
            $sql .= " WHERE o.title LIKE ? OR o.email LIKE ? OR o.phone LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
    }
    
    $sql .= " ORDER BY o.priority DESC, o.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $opportunities = $stmt->fetchAll();

    // Fetch dynamic stages
    $stagesRows = $db->query("SELECT * FROM crm_stages ORDER BY sort_order ASC")->fetchAll();
    
    // Create an organized mapping for the view
    $pipeline = [];
    foreach ($stagesRows as $s) {
        $pipeline[$s['name']] = [
            'info' => $s,
            'opportunities' => []
        ];
    }
    
    // Group opportunities by stage name
    foreach ($opportunities as $opp) {
        $stageName = $opp['stage'];
        if (isset($pipeline[$stageName])) {
            $pipeline[$stageName]['opportunities'][] = $opp;
        }
    }

    $stages = array_column($stagesRows, 'name'); // for the quick-add dropdown

    require __DIR__ . '/../views/admin/crm_pipeline.php';
}

function adminCrmOpportunity($id = null): void {
    requireAdmin();
    $db = getDB();

    $opportunity = null;
    $log_notes = [];
    $attachments = [];
    $linkedInvoices = [];
    $opportunityItems = [];
    $allProducts = [];

    if ($id) {
        $stmt = $db->prepare("SELECT * FROM crm_opportunities WHERE id = ?");
        $stmt->execute([$id]);
        $opportunity = $stmt->fetch();

        if (!$opportunity) {
            redirect('admin/crm_pipeline');
        }

        // Fetch linked invoices
        $stmt = $db->prepare("SELECT * FROM invoices WHERE opportunity_id = ? ORDER BY created_at ASC");
        $stmt->execute([$id]);
        $linkedInvoices = $stmt->fetchAll();

        // Fetch Opportunity Items
        $stmt = $db->prepare("
            SELECT oi.*, i.name as product_name, i.category 
            FROM crm_opportunity_items oi 
            JOIN crm_items i ON oi.item_id = i.id 
            WHERE oi.opportunity_id = ? 
            ORDER BY oi.created_at ASC
        ");
        $stmt->execute([$id]);
        $opportunityItems = $stmt->fetchAll();

        // Fetch Log Notes and Attachments
        $stmt = $db->prepare("
            SELECT l.*, a.full_name, a.avatar_emoji, a.username 
            FROM crm_log_notes l 
            LEFT JOIN admins a ON l.admin_id = a.id 
            WHERE l.opportunity_id = ? 
            ORDER BY l.created_at DESC
        ");
        $stmt->execute([$id]);
        $log_notes = $stmt->fetchAll();

        // Fetch all attachments related to this opportunity or its log notes
        $stmt = $db->prepare("
            SELECT * FROM crm_attachments 
            WHERE (linked_type = 'opportunity' AND linked_id = ?)
               OR (linked_type = 'log_note' AND linked_id IN (SELECT id FROM crm_log_notes WHERE opportunity_id = ?))
            ORDER BY created_at DESC
        ");
        $stmt->execute([$id, $id]);
        $attachments = $stmt->fetchAll();
    }

    // Fetch all available products for select modal
    $allProducts = $db->query("SELECT * FROM crm_items ORDER BY name ASC")->fetchAll();

    // Handle saving/creating
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_opportunity') {
        // Build data array
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'expected_revenue' => (float)($_POST['expected_revenue'] ?? 0),
            'probability' => (float)($_POST['probability'] ?? 0),
            'stage' => trim($_POST['stage'] ?? 'New Lead'),
            'color_code' => trim($_POST['color_code'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'contact_id' => !empty($_POST['contact_id']) ? (int)$_POST['contact_id'] : null,
        ];

        if ($id) {
            $stmt = $db->prepare("
                UPDATE crm_opportunities 
                SET title = ?, email = ?, phone = ?, expected_revenue = ?, probability = ?, stage = ?, color_code = ?, notes = ?, contact_id = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $data['title'], $data['email'], $data['phone'], $data['expected_revenue'], 
                $data['probability'], $data['stage'], $data['color_code'], $data['notes'], $data['contact_id'], 
                $id
            ]);
            setFlash('Opportunity updated.');
            redirect('admin/crm_opportunity?id=' . $id);
        } else {
            $stmt = $db->prepare("
                INSERT INTO crm_opportunities (title, email, phone, expected_revenue, probability, stage, color_code, notes, contact_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['title'], $data['email'], $data['phone'], $data['expected_revenue'], 
                $data['probability'], $data['stage'], $data['color_code'], $data['notes'], $data['contact_id']
            ]);
            $newId = $db->lastInsertId();
            setFlash('Opportunity created.');
            redirect('admin/crm_opportunity?id=' . $newId);
        }
    }
    
    // Handle adding log note
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_log_note' && $id) {
        $content = trim($_POST['content'] ?? '');
        $noteType = trim($_POST['note_type'] ?? 'note');
        
        if ($content) {
            $stmt = $db->prepare("INSERT INTO crm_log_notes (opportunity_id, admin_id, note_type, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $_SESSION['admin_id'], $noteType, $content]);
            $noteId = $db->lastInsertId();

            // Handle file upload if present
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/crm/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                $targetFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $fileUrl = '/assets/uploads/crm/' . $fileName;
                    $stmtAttach = $db->prepare("INSERT INTO crm_attachments (linked_type, linked_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmtAttach->execute(['log_note', $noteId, $_FILES['attachment']['name'], $fileUrl, $_FILES['attachment']['type'], $_FILES['attachment']['size']]);
                }
            }

            setFlash('Log note added.');
        }
        redirect('admin/crm_opportunity?id=' . $id);
    }

    // Handle deleting log note (soft delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_log_note') {
        $noteId = (int)($_POST['note_id'] ?? 0);
        if ($noteId) {
            // Remove attachments
            $db->prepare("DELETE FROM crm_attachments WHERE linked_type = 'log_note' AND linked_id = ?")->execute([$noteId]);
            // Soft delete: mark as deleted, clear content
            $db->prepare("UPDATE crm_log_notes SET is_deleted = 1, content = '' WHERE id = ?")->execute([$noteId]);
            
            // If AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'deleted_id' => $noteId]);
                exit;
            }
            
            setFlash('Log note deleted.');
        }
        redirect('admin/crm_opportunity?id=' . $id);
    }

    // Fetch contacts for dropdown
    $contacts = $db->query("SELECT id, name FROM contacts ORDER BY name ASC")->fetchAll();

    require __DIR__ . '/../views/admin/crm_opportunity.php';
}

function adminCrmProducts(): void {
    requireAdmin();
    $db = getDB();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'save_item') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id) {
                 $stmt = $db->prepare("UPDATE crm_items SET name=?, price=?, category=?, description=? WHERE id=?");
                 $stmt->execute([$name, $price, $category, $description, $id]);
                 setFlash('Product updated.');
            } else {
                 $stmt = $db->prepare("INSERT INTO crm_items (name, price, category, description) VALUES (?, ?, ?, ?)");
                 $stmt->execute([$name, $price, $category, $description]);
                 setFlash('Product added.');
            }
        } elseif ($_POST['action'] === 'delete_item') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM crm_items WHERE id=?");
            $stmt->execute([$id]);
            setFlash('Product deleted.');
        }
        redirect('admin/crm_products');
    }

    $stmt = $db->query("SELECT * FROM crm_items ORDER BY name ASC");
    $items = $stmt->fetchAll();

    require __DIR__ . '/../views/admin/crm_products.php';
}

/**
 * Fetch Link Preview Data (OG Tags)
 */
function fetchLinkPreview($url) {
    $data = [
        'title' => '',
        'description' => '',
        'image' => '',
        'url' => $url
    ];

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $html = curl_exec($ch);
        curl_close($ch);

        if ($html) {
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $nodes = $doc->getElementsByTagName('title');
            if ($nodes->length > 0) $data['title'] = $nodes->item(0)->nodeValue;

            $metas = $doc->getElementsByTagName('meta');
            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if ($meta->getAttribute('property') === 'og:title') $data['title'] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') === 'og:description') $data['description'] = $meta->getAttribute('content');
                if ($meta->getAttribute('name') === 'description' && !$data['description']) $data['description'] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') === 'og:image') $data['image'] = $meta->getAttribute('content');
            }
        }
    } catch (Exception $e) { }

    return $data;
}
