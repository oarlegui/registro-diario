<?php
/**
 * Login Page
 */

require_once __DIR__ . '/../app/Auth.php';

// If already logged in, redirect to dashboard
if (Auth::isLoggedIn()) {
    header('Location: /public/index.php');
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, ingrese su email y contraseña.';
    } elseif (Auth::login($email, $password)) {
        header('Location: /public/index.php');
        exit;
    } else {
        $error = 'Credenciales inválidas. Por favor, intente nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Registro Diario</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">Registro Diario</h1>
            <p class="login-subtitle">Inicie sesión para continuar</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        autofocus
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px;">
                    Iniciar Sesión
                </button>
            </form>
            
            <?php if ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false): ?>
            <div class="text-center mt-2" style="color: var(--text-secondary); font-size: 12px;">
                <p><strong>Modo Desarrollo</strong></p>
                <p>Usuario por defecto: admin@registro-diario.local</p>
                <p>Contraseña: admin123</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
