<?php
/**
 * Update Exit Time Handler
 */

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/CSRF.php';
require_once __DIR__ . '/Record.php';

header('Content-Type: application/json');

// Require authentication
if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validate CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!CSRF::validateToken($csrfToken)) {
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
    exit;
}

// Validate required fields
$recordId = $_POST['record_id'] ?? '';
$exitTime = $_POST['exit_time'] ?? '';

if (empty($recordId) || empty($exitTime)) {
    echo json_encode(['success' => false, 'message' => 'ID de registro y hora de salida son requeridos']);
    exit;
}

// Update exit time
$result = Record::updateExitTime($recordId, $exitTime);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Hora de salida actualizada exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar la hora de salida'
    ]);
}
