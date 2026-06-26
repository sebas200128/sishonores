<?php
require_once '../util/Database.php';

// Si ya inició sesión, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SisHonores 1.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a56db 0%, #2c3e50 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-logo img {
            width: 80px;
            height: auto;
            filter: drop-shadow(0 2px 8px rgba(26, 86, 219, 0.2));
            transition: transform 0.3s ease;
            margin-bottom: 15px;
        }
        .login-logo img:hover {
            transform: scale(1.08);
        }
        .login-logo h2 {
            color: #1a56db;
            font-weight: bold;
            margin: 15px 0 5px;
        }
        .login-logo p {
            color: #6c757d;
            font-size: 14px;
        }
        .btn-login {
            background: linear-gradient(135deg, #1a56db 0%, #0e3a8a 100%);
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0e3a8a 0%, #082150 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 86, 219, 0.3);
        }
        .form-control {
            border-color: #ddd;
        }
        .form-control:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 0.2rem rgba(26, 86, 219, 0.25);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="<?php echo app_url('assets/escudo trasparente.png'); ?>" alt="Logo SisHonores">
            <h2>SisHonores 1.0</h2>
            <p>Colegio Matemático Honores</p>
        </div>
        <h4 class="text-center mb-4">Iniciar Sesión</h4>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="POST" action="../controller/LoginController.php">
            <div class="mb-3">
                <label class="form-label">DNI</label>
                <input type="text" class="form-control" name="dni" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">Ingresar</button>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Sistema de Gestión Académica v1.0</small>
        </div>
    </div>
</body>
</html>