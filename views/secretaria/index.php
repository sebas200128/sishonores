<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Secretaria') {
    header('Location: index.php?controller=login');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="container-fluid">
    <h1 class="mb-4">Panel de Secretaría</h1>
    <div class="row">
        <div class="col-md-6 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h3><i class="fas fa-search"></i> Buscar Alumno</h3><p>Consulta rápida por DNI</p></div></div></div>
        <div class="col-md-6 mb-3"><div class="card bg-success text-white"><div class="card-body"><h3><i class="fas fa-eye"></i> Ver Notas</h3><p>Detalle de calificaciones</p></div></div></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
