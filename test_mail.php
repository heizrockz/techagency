<?php
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

echo "Testing SMTP send...\n";
$res = $smtp->send(
    'test@micosage.com', // To
    $settings['from_email'], // From
    $settings['from_name'], // From Name
    'Test Email',
    '<h1>Hello!</h1><p>Testing email delivery.</p>'
);

if ($res) {
    echo "Sent successfully!\n";
} else {
    echo "Failed to send.\n";
}

echo "Debug Log:\n";
print_r($smtp->getDebug());
