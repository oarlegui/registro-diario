<?php
/**
 * Save Record Handler
 */

require_once __DIR__ . '/Auth.php';
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

// Validate required fields
$fieldLabels = [
    'date' => 'Fecha',
    'name' => 'Nombre',
    'rut' => 'RUT',
    'classification' => 'Clasificación',
    'visit_reason' => 'Motivo de visita'
];

$requiredFields = ['date', 'name', 'rut', 'classification', 'visit_reason'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $label = $fieldLabels[$field] ?? $field;
        echo json_encode(['success' => false, 'message' => "El campo '$label' es requerido"]);
        exit;
    }
}

// Validate RUT
$rut = trim($_POST['rut']);
if (!Record::validateRUT($rut)) {
    echo json_encode(['success' => false, 'message' => 'RUT inválido']);
    exit;
}

// Prepare data
$data = [
    'date' => $_POST['date'],
    'name' => trim($_POST['name']),
    'rut' => $rut,
    'phone_number' => trim($_POST['phone_number'] ?? ''),
    'classification' => $_POST['classification'],
    'company_name' => trim($_POST['company_name'] ?? ''),
    'transport_company_name' => trim($_POST['transport_company_name'] ?? ''),
    'visit_reason' => trim($_POST['visit_reason']),
    'entry_time' => date('H:i:s'), // Auto-generate entry time
    'license_plate' => trim($_POST['license_plate'] ?? ''),
    'invoice_or_guide_number' => trim($_POST['invoice_or_guide_number'] ?? '')
];

// Save record
$recordId = Record::create($data);

if ($recordId) {
    echo json_encode([
        'success' => true,
        'message' => 'Registro guardado exitosamente',
        'record_id' => $recordId
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el registro'
    ]);
}
