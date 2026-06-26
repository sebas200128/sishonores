<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="container-fluid">
    <h1 class="mb-4">Panel del Padre de Familia</h1>
    <p class="lead">Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
    <div class="row">
        <div class="col-md-6 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h3><i class="fas fa-child"></i> Mis Hijos</h3><p>Ver información de tus hijos</p></div></div></div>
        <div class="col-md-6 mb-3"><div class="card bg-success text-white"><div class="card-body"><h3><i class="fas fa-chart-line"></i> Ver Notas</h3><p>Consultar calificaciones</p></div></div></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
