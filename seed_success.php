<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/db.php';

$db = getDB();

$seeds = [
    ['success_page_title', 'en', 'Success!'],
    ['success_page_title', 'ar', 'تم بنجاح!'],
    ['success_page_message', 'en', 'Thank you! Your booking request has been submitted. Our team will contact you shortly.'],
    ['success_page_message', 'ar', 'شكراً لك! تم استلام الطلب وسنتواصل معك قريباً.'],
    ['success_page_button', 'en', 'Return to Home'],
    ['success_page_button', 'ar', 'العودة للرئيسية']
];

$stmt = $db->prepare('INSERT INTO contents (section_key, locale, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = value');

foreach ($seeds as $seed) {
    $stmt->execute($seed);
}

echo "Seeded successfully!";
