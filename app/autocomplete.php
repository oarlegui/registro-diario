<?php
/**
 * Autocomplete Handler
 */

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Record.php';

header('Content-Type: application/json');

// Require authentication
if (!Auth::isLoggedIn()) {
    echo json_encode([]);
    exit;
}

$type = $_GET['type'] ?? '';
$query = $_GET['q'] ?? '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$suggestions = [];

switch ($type) {
    case 'name':
        $suggestions = Record::getNameSuggestions($query);
        break;
    case 'company':
        $suggestions = Record::getCompanySuggestions($query);
        break;
}

echo json_encode($suggestions);
