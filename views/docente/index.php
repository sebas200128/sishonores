<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: index.php?controller=login');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="container-fluid">
    <h1 class="mb-4">Panel del Docente</h1>
    <p class="lead">Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
    <div class="row">
        <div class="col-md-4 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h3><i class="fas fa-book-open"></i> Mis Cursos</h3><p>Cursos asignados</p></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-success text-white"><div class="card-body"><h3><i class="fas fa-edit"></i> Ingresar Notas</h3><p>Registro de calificaciones</p></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-info text-white"><div class="card-body"><h3><i class="fas fa-file-excel"></i> Exportar Excel</h3><p>Reportes de notas</p></div></div></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
