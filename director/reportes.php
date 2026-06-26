<?php
require_once '../util/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query_total = "SELECT COUNT(*) as total FROM alumnos WHERE activo = 1";
$stmt_total = $db->prepare($query_total);
$stmt_total->execute();
$total_alumnos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

$query_docentes = "SELECT COUNT(*) as total
                   FROM usuarios u
                   JOIN roles r ON u.id_rol = r.id_rol
                   WHERE r.nombre_rol = 'Docente' AND u.activo = 1";
$stmt_docentes = $db->prepare($query_docentes);
$stmt_docentes->execute();
$total_docentes = $stmt_docentes->fetch(PDO::FETCH_ASSOC)['total'];

$query_aprobados = "SELECT COUNT(*) as total FROM alumnos a WHERE a.activo = 1 AND (
    SELECT AVG(nota) FROM notas WHERE id_alumno = a.id_alumno) >= 11";
$stmt_aprobados = $db->prepare($query_aprobados);
$stmt_aprobados->execute();
$total_aprobados = $stmt_aprobados->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-chart-bar"></i> Reportes Estadísticos</h1>
        <div class="row">
            <div class="col-md-4 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h3><?php echo $total_alumnos; ?></h3><p>Total Alumnos</p></div></div></div>
            <div class="col-md-4 mb-3"><div class="card bg-success text-white"><div class="card-body"><h3><?php echo $total_docentes; ?></h3><p>Total Docentes</p></div></div></div>
            <div class="col-md-4 mb-3"><div class="card bg-info text-white"><div class="card-body"><h3><?php echo $total_aprobados; ?></h3><p>Alumnos Aprobados</p></div></div></div>
        </div>
        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-body"><canvas id="chartNiveles"></canvas></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body"><canvas id="chartAprobados"></canvas></div></div></div>
        </div>
    </div>
    <script>
        new Chart(document.getElementById('chartNiveles'), {type: 'bar', data: {labels: ['Inicial', 'Primaria', 'Secundaria'], datasets: [{label: 'Alumnos por Nivel', data: [0, 0, 0], backgroundColor: ['#1a56db', '#27ae60', '#f39c12']}]}});
        new Chart(document.getElementById('chartAprobados'), {type: 'pie', data: {labels: ['Aprobados', 'Desaprobados'], datasets: [{data: [<?php echo $total_aprobados; ?>, <?php echo $total_alumnos - $total_aprobados; ?>], backgroundColor: ['#27ae60', '#e74c3c']}]}});
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
