<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="mb-4">Panel de Administración</h1>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3><i class="fas fa-users"></i> Usuarios</h3>
                    <p class="mb-0">Gestión completa de usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3><i class="fas fa-graduation-cap"></i> Alumnos</h3>
                    <p class="mb-0">Matrículas y datos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3><i class="fas fa-book"></i> Cursos</h3>
                    <p class="mb-0">Gestión de materias</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3><i class="fas fa-chalkboard-user"></i> Docentes</h3>
                    <p class="mb-0">Asignaciones</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
