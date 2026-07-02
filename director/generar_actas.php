<?php
require_once '../util/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../vista/login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$query_grados = "SELECT id_grado, nombre_grado FROM grados ORDER BY orden";
$stmt_grados = $db->prepare($query_grados);
$stmt_grados->execute();
$grados = $stmt_grados->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Actas - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-file-alt"></i> Generar Actas de Notas</h1>
        <div class="card">
            <div class="card-header bg-dark text-white"><i class="fas fa-download"></i> Generar Acta por Grado</div>
            <div class="card-body">
                <form method="GET" action="actas_pdf.php" target="_blank" class="row g-3">
                    <div class="col-md-4"><label>Grado</label><select name="id_grado" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($grados as $grado): ?>
                            <option value="<?php echo $grado['id_grado']; ?>"><?php echo $grado['nombre_grado']; ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                                <div class="col-md-4"><label>Bimestre</label><select name="bimestre" class="form-select" required>
                                    <option value="1">1° Bimestre</option><option value="2">2° Bimestre</option>
                                    <option value="3">3° Bimestre</option><option value="4">4° Bimestre</option>
                                </select></div>
                                <div class="col-md-4"><label>&nbsp;</label><button type="submit" class="btn btn-primary d-block"><i class="fas fa-file-pdf"></i> Generar Acta</button></div>
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
