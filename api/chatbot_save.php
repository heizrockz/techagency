<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Get the raw POST body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['transcript']) || !is_array($data['transcript']) || empty($data['transcript'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or empty transcript']);
    exit;
}

$transcript = $data['transcript'];
$db = getDB();

try {
    $db->beginTransaction();

    // Determine the user's IP (simplified)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    // Look for email or phone in the transcript
    $userEmail = '';
    $userPhone = '';

    foreach ($transcript as $msg) {
        if ($msg['sender'] === 'user') {
            $text = $msg['message'];
            
            // Extract Email
            if (empty($userEmail) && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
                $userEmail = $matches[0];
            }
            // Extract Phone (Simple regex for 10-14 digits, optional plus)
            if (empty($userPhone) && preg_match('/(\+?[0-9]{10,14})/', str_replace([' ', '-', '(', ')'], '', $text), $matches)) {
                $userPhone = $matches[0];
            }
        }
    }

    // Try to find an existing active session for this IP (within the last 2 hours) to append to
    $stmt = $db->prepare("SELECT id FROM chatbot_sessions WHERE user_ip = ? AND status = 'Open' AND updated_at > NOW() - INTERVAL 2 HOUR ORDER BY updated_at DESC LIMIT 1");
    $stmt->execute([$ip]);
    $existingSession = $stmt->fetch();

    if ($existingSession) {
        $sessionId = $existingSession['id'];
        
        // Update session tracking data if we newly found an email or phone
        $updateSql = [];
        $updateParams = [];
        if (!empty($userEmail)) {
            $updateSql[] = "user_email = ?";
            $updateParams[] = $userEmail;
        }
        if (!empty($userPhone)) {
            $updateSql[] = "user_phone = ?";
            $updateParams[] = $userPhone;
        }
        
        if (!empty($updateSql)) {
            $updateParams[] = $sessionId;
            $db->prepare("UPDATE chatbot_sessions SET updated_at = CURRENT_TIMESTAMP, " . implode(', ', $updateSql) . " WHERE id = ?")->execute($updateParams);
        } else {
            $db->prepare("UPDATE chatbot_sessions SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$sessionId]);
        }
        
    } else {
        // Create a new session
        $stmt = $db->prepare('INSERT INTO chatbot_sessions (user_email, user_phone, user_ip, user_agent) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userEmail, $userPhone, $ip, $userAgent]);
        $sessionId = $db->lastInsertId();
    }

    // Delete existing messages for this session to prevent duplicates from the array
    $db->prepare('DELETE FROM chatbot_messages WHERE session_id = ?')->execute([$sessionId]);

    // Insert the whole transcript afresh
    $insertStmt = $db->prepare('INSERT INTO chatbot_messages (session_id, sender, message) VALUES (?, ?, ?)');
    foreach ($transcript as $msg) {
        $insertStmt->execute([
            $sessionId,
            $msg['sender'] === 'bot' ? 'bot' : 'user',
            trim($msg['message'])
        ]);
    }

    $db->commit();
    echo json_encode(['success' => true, 'session_id' => $sessionId]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Chatbot Save Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
