<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT u.*, pf.password_generada FROM usuarios u JOIN padres_familia pf ON u.id_usuario = pf.id_usuario WHERE u.id_usuario = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $stmt = $db->prepare("UPDATE usuarios SET telefono=:tel, email=:email WHERE id_usuario=:id");
    $stmt->bindParam(':tel', $telefono);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    if($stmt->execute()) $success = "Datos actualizados correctamente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-user"></i> Mi Perfil</h1>
        <?php if(isset($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <div class="card">
            <div class="card-header bg-dark text-white">Datos Personales</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                                    <div class="col-md-6 mb-3"><label>Nombres</label><input type="text" class="form-control" value="<?php echo $usuario['nombres']; ?>" disabled></div>
                                    <div class="col-md-6 mb-3"><label>Apellidos</label><input type="text" class="form-control" value="<?php echo $usuario['apellidos']; ?>" disabled></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label>DNI</label><input type="text" class="form-control" value="<?php echo $usuario['dni']; ?>" disabled></div>
                                    <div class="col-md-6 mb-3"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo $usuario['email']; ?>"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label>Teléfono</label><input type="text" class="form-control" name="telefono" value="<?php echo $usuario['telefono']; ?>"></div>
                                    <div class="col-md-6 mb-3"><label>Contraseña de Acceso</label><input type="text" class="form-control" value="<?php echo $usuario['password_generada']; ?>" disabled><small class="text-muted">Contraseña generada automáticamente</small></div>
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Datos</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
