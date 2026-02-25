<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/smtp.php';

$db = getDB();
$settings = $db->query("SELECT * FROM email_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$smtp = new MicoSMTP(
    $settings['smtp_host'],
    $settings['smtp_port'],
    $settings['smtp_user'],
    $settings['smtp_pass'],
    $settings['smtp_encryption']
);

echo "Testing SMTP Connection method...\n";
$res = $smtp->testConnection();

if ($res === true) {
    echo "Connection TEST: SUCCESS!\n";
} else {
    echo "Connection TEST: FAILED - " . $res . "\n";
}

echo "Debug Log:\n";
print_r($smtp->getDebug());
