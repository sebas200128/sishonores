<?php
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="container-fluid">
    <h1 class="mb-4">Panel del Director</h1>
    <div class="row">
        <div class="col-md-3 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h3><i class="fas fa-users"></i> Usuarios</h3><p>Gestión completa</p></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-success text-white"><div class="card-body"><h3><i class="fas fa-graduation-cap"></i> Alumnos</h3><p>Matrículas</p></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-warning text-white"><div class="card-body"><h3><i class="fas fa-file-pdf"></i> Boletas</h3><p>Generar PDF</p></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-info text-white"><div class="card-body"><h3><i class="fas fa-chart-bar"></i> Reportes</h3><p>Estadísticas</p></div></div></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
