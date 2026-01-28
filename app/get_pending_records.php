<?php
/**
 * Get Pending Records Handler
 */

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Record.php';

header('Content-Type: application/json');

// Require authentication
if (!Auth::isLoggedIn()) {
    echo json_encode([]);
    exit;
}

// Get pending records
$date = $_GET['date'] ?? null;
$records = Record::getPending($date);

echo json_encode($records);
