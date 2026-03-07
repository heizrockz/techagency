<?php
require 'config.php';
require 'includes/db.php';
$db = getDB();
$stmt = $db->query('DESCRIBE app_licenses');
echo json_encode($stmt->fetchAll());
