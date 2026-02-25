<?php
require_once __DIR__ . '/includes/db.php';
$db = getDB();

echo "--- chatbot_sessions ---\n";
try {
    $res = $db->query("DESCRIBE chatbot_sessions")->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

echo "\n--- chatbot_messages ---\n";
try {
    $res = $db->query("DESCRIBE chatbot_messages")->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

echo "\n--- Sample Messages for last session ---\n";
try {
    $last = $db->query("SELECT id FROM chatbot_sessions ORDER BY id DESC LIMIT 1")->fetch();
    if ($last) {
        $msgs = $db->prepare("SELECT * FROM chatbot_messages WHERE session_id = ?");
        $msgs->execute([$last['id']]);
        print_r($msgs->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) { echo $e->getMessage() . "\n"; }
