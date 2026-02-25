<?php
/**
 * API endpoint for sending campaign emails one-by-one with progress tracking.
 * Called via AJAX from the admin marketing page.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/smtp.php';

// Only admin
if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST required']);
    exit;
}

$db = getDB();
$action = $_POST['action'] ?? '';

// ── Step 1: Create campaign and return list of emails ──
if ($action === 'create_campaign') {
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $sendType = $_POST['send_type'] ?? 'single';

    if (empty($subject) || empty($body)) {
        echo json_encode(['error' => 'Subject and body are required.']);
        exit;
    }

    $emails = [];
    if ($sendType === 'single') {
        $raw = $_POST['recipients'] ?? '';
        $parts = explode(',', $raw);
        foreach ($parts as $p) {
            $email = trim($p);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }
    } else {
        // CSV upload
        if (!empty($_FILES['email_list']['tmp_name'])) {
            if (($handle = fopen($_FILES['email_list']['tmp_name'], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $email = trim($data[0] ?? '');
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $email;
                    }
                }
                fclose($handle);
            }
        }
    }

    $emails = array_unique($emails);

    if (empty($emails)) {
        echo json_encode(['error' => 'No valid email addresses found.']);
        exit;
    }

    // Create campaign in DB
    $db->prepare('INSERT INTO marketing_campaigns (subject, body, total_emails, status) VALUES (?, ?, ?, ?)')
       ->execute([$subject, $body, count($emails), 'sending']);
    $campaignId = $db->lastInsertId();

    // Pre-create all recipient rows as "pending"
    $stmt = $db->prepare('INSERT INTO marketing_recipients (campaign_id, email, status) VALUES (?, ?, ?)');
    foreach ($emails as $email) {
        $stmt->execute([$campaignId, $email, 'pending']);
    }

    echo json_encode([
        'success' => true,
        'campaign_id' => $campaignId,
        'total' => count($emails),
        'emails' => $emails
    ]);
    exit;
}

// ── Step 2: Send a single email ──
if ($action === 'send_one') {
    $campaignId = intval($_POST['campaign_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');

    if (!$campaignId || !$email) {
        echo json_encode(['error' => 'Missing campaign_id or email']);
        exit;
    }

    // Get settings
    $settings = $db->query('SELECT * FROM email_settings LIMIT 1')->fetch();
    if (!$settings || empty($settings['smtp_host'])) {
        echo json_encode(['error' => 'SMTP not configured']);
        exit;
    }

    // Get campaign data
    $campStmt = $db->prepare('SELECT * FROM marketing_campaigns WHERE id = ?');
    $campStmt->execute([$campaignId]);
    $campaign = $campStmt->fetch();
    if (!$campaign) {
        echo json_encode(['error' => 'Campaign not found']);
        exit;
    }

    // Send the email
    $mailer = new MicoSMTP(
        $settings['smtp_host'],
        $settings['smtp_port'],
        $settings['smtp_user'],
        $settings['smtp_pass'],
        $settings['smtp_encryption']
    );

    $result = $mailer->send(
        $email,
        $settings['from_email'],
        $settings['from_name'],
        $campaign['subject'],
        $campaign['body'],
        $settings['signature_html']
    );

    $status = $result ? 'sent' : 'failed';

    // Update recipient row
    $db->prepare('UPDATE marketing_recipients SET status = ?, sent_at = NOW() WHERE campaign_id = ? AND email = ?')
       ->execute([$status, $campaignId, $email]);

    // Update campaign counts
    if ($result) {
        $db->prepare('UPDATE marketing_campaigns SET sent_count = sent_count + 1 WHERE id = ?')->execute([$campaignId]);
    } else {
        $db->prepare('UPDATE marketing_campaigns SET failed_count = failed_count + 1 WHERE id = ?')->execute([$campaignId]);
    }

    echo json_encode([
        'success' => true,
        'email' => $email,
        'status' => $status,
        'debug' => $mailer->getDebug()
    ]);
    exit;
}

// ── Step 3: Finalize campaign ──
if ($action === 'finalize') {
    $campaignId = intval($_POST['campaign_id'] ?? 0);
    if ($campaignId) {
        $db->prepare('UPDATE marketing_campaigns SET status = ? WHERE id = ?')
           ->execute(['completed', $campaignId]);
    }
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
