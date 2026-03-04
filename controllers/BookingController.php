<?php
/**
 * Mico Sage — Booking Controller
 */
require_once __DIR__ . '/../includes/db.php';

function handleBookingSubmit(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . baseUrl('/#booking'));
        exit;
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['booking_error'] = t('booking_error');
        header('Location: ' . baseUrl('/#booking'));
        exit;
    }

    $fields = $_POST['fields'] ?? [];
    
    // Standard fields present in the schema
    $standardFields = ['name', 'email', 'phone', 'service', 'preferred_date', 'message'];
    $data = [];
    $extraFields = [];

    foreach ($fields as $key => $value) {
        if (in_array($key, $standardFields)) {
            $data[$key] = trim($value);
        } else {
            $extraFields[$key] = trim($value);
        }
    }

    // Basic validation on required standard fields if they exist in schema
    $name = $data['name'] ?? 'Unknown';
    $email = $data['email'] ?? 'Unknown';
    $phone = $data['phone'] ?? '';
    $service = $data['service'] ?? '';
    $date = $data['preferred_date'] ?? null;
    $message = $data['message'] ?? '';

    if (empty($name) || empty($email)) {
        $_SESSION['booking_error'] = t('booking_error');
        header('Location: ' . baseUrl('/#booking'));
        exit;
    }

    try {
        $db = getDB();
        $stmt = $db->prepare('
            INSERT INTO bookings (name, email, phone, service, message, preferred_date, extra_fields)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $name,
            $email,
            $phone,
            $service,
            $message,
            $date ? $date : null,
            empty($extraFields) ? null : json_encode($extraFields)
        ]);

        require_once __DIR__ . '/../includes/helpers.php';
        addAdminNotification('booking', 'New Booking Created', "You have a new booking from {$name} for {$service}.", 'admin/bookings');

        header('Location: ' . baseUrl('/booking/success'));
        exit;
    } catch (Exception $e) {
        $_SESSION['booking_error'] = t('booking_error');
        header('Location: ' . baseUrl('/#booking'));
        exit;
    }
}

function showBookingSuccess(): void {
    require __DIR__ . '/../views/user/booking-success.php';
}
