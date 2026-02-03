<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_error.log');

/**
 * Main Dashboard - Registro Diario
 */

require_once __DIR__ . '/../app/Auth.php';

// Configurar la zona horaria
date_default_timezone_set('America/Santiago');

// Obtener la fecha actual
$fechaHoy = date("Y-m-d");
$horaActual = date("H:i:s");

// Requerir autenticación
Auth::requireLogin();

$userEmail = Auth::getUserEmail();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Diario</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">📋 Registro Diario</div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></span>
                <a href="/app/logout.php" class="btn btn-secondary">Cerrar Sesión</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="tabs">
            <button class="tab active" data-tab="register">Nuevo Registro</button>
            <button class="tab" data-tab="pending">Salidas Pendientes</button>
        </div>
        
        <!-- New Registration Tab -->
        <div id="register" class="tab-content active">
            <div class="card">
                <h2 class="card-title">Registro de Visita</h2>
                
                <form id="record-form" action="/app/save_record.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date">Fecha *</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        
                        <div class="form-group autocomplete-wrapper">
                            <label for="name">Nombre *</label>
                            <input type="text" id="name" name="name" required autocomplete="off">
                        </div>
                        
                        <div class="form-group">
                            <label for="rut">RUT *</label>
                            <input type="text" id="rut" name="rut" placeholder="12.345.678-9" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone_number">Teléfono</label>
                            <input type="tel" id="phone_number" name="phone_number" placeholder="+56 9 1234 5678">
                        </div>
                        
                        <div class="form-group">
                            <label for="classification">Clasificación *</label>
                            <select id="classification" name="classification" required>
                                <option value="">Seleccione...</option>
                                <option value="Cliente">Cliente</option>
                                <option value="Proveedor">Proveedor</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="form-group autocomplete-wrapper">
                            <label for="company_name">Empresa</label>
                            <input type="text" id="company_name" name="company_name" autocomplete="off">
                        </div>
                        
                        <div class="form-group">
                            <label for="transport_company_name">Empresa de Transporte</label>
                            <input type="text" id="transport_company_name" name="transport_company_name">
                        </div>
                        
                        <div class="form-group">
                            <label for="license_plate">Patente</label>
                            <input type="text" id="license_plate" name="license_plate" placeholder="AB-CD-12">
                        </div>
                        
                        <div class="form-group">
                            <label for="invoice_or_guide_number">N° Factura/Guía</label>
                            <input type="text" id="invoice_or_guide_number" name="invoice_or_guide_number">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="visit_reason">Motivo de Visita *</label>
                            <textarea id="visit_reason" name="visit_reason" required></textarea>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Nota:</strong> La hora de entrada se registrará automáticamente al guardar el formulario.
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">Guardar Registro</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Formulario</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Pending Departures Tab -->
        <div id="pending" class="tab-content">
            <div class="card">
                <h2 class="card-title">Salidas Pendientes</h2>
                
                <div style="overflow-x: auto;">
                    <table id="pending-records-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>RUT</th>
                                <th>Empresa</th>
                                <th>Clasificación</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                                <th>Patente</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/public/js/app.js"></script>
</body>
</html>
