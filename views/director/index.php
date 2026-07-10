require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="container-fluid">
    <h1 class="mb-4">Panel del Director</h1>
    <div class="row">
        <div class="col-md-3 mb-3"><a href="index.php?controller=alumno&action=index" style="text-decoration: none;"><div class="card bg-success text-white"><div class="card-body"><h3><i class="fas fa-graduation-cap"></i> Alumnos</h3><p>Gestionar Alumnos</p></div></div></a></div>
        <div class="col-md-3 mb-3"><a href="index.php?controller=alumno&action=matricula" style="text-decoration: none;"><div class="card bg-primary text-white"><div class="card-body"><h3><i class="fas fa-file-signature"></i> Nuevo Alumno</h3><p>Ficha de Matrícula</p></div></div></a></div>
        <div class="col-md-3 mb-3"><a href="index.php?controller=director&action=generar_boletas" style="text-decoration: none;"><div class="card bg-warning text-white"><div class="card-body"><h3><i class="fas fa-file-pdf"></i> Boletas</h3><p>Generar PDF</p></div></div></a></div>
        <div class="col-md-3 mb-3"><a href="index.php?controller=director&action=reportes" style="text-decoration: none;"><div class="card bg-info text-white"><div class="card-body"><h3><i class="fas fa-chart-bar"></i> Reportes</h3><p>Estadísticas</p></div></div></a></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
