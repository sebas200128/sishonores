require_once __DIR__ . '/../core/Database.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

// Definir rutas según rol
$rol = $_SESSION['user_role'];
$links = array();

if ($rol == 'SuperUsuario') {
    $links = [
        ['url' => app_url('super_admin/usuarios.php'), 'icon' => 'fa-users', 'titulo' => 'Gestionar Usuarios', 'desc' => 'Administración de usuarios', 'color' => 'danger'],
        ['url' => app_url('super_admin/docentes.php'), 'icon' => 'fa-chalkboard-teacher', 'titulo' => 'Registrar Docente', 'desc' => 'Gestión de docentes', 'color' => 'primary'],
        ['url' => app_url('super_admin/generar_boletas.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Reportes en Línea', 'desc' => 'Generación de boletas', 'color' => 'success'],
        ['url' => app_url('super_admin/alumnos.php'), 'icon' => 'fa-graduation-cap', 'titulo' => 'Gestión de Alumnos', 'desc' => 'Administración de estudiantes', 'color' => 'info'],
        ['url' => app_url('super_admin/cursos.php'), 'icon' => 'fa-book', 'titulo' => 'Gestionar Cursos', 'desc' => 'Administración de cursos', 'color' => 'warning'],
        ['url' => app_url('super_admin/aulas.php'), 'icon' => 'fa-door-open', 'titulo' => 'Gestionar Aulas', 'desc' => 'Administración de aulas', 'color' => 'secondary']
    ];
} elseif ($rol == 'Director') {
    $links = [
        ['url' => app_url('super_admin/docentes.php'), 'icon' => 'fa-chalkboard-teacher', 'titulo' => 'Registrar Docente', 'desc' => 'Gestión de docentes', 'color' => 'primary'],
        ['url' => app_url('super_admin/generar_boletas.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Reportes en Línea', 'desc' => 'Generación de boletas', 'color' => 'success'],
        ['url' => app_url('super_admin/alumnos.php'), 'icon' => 'fa-graduation-cap', 'titulo' => 'Gestión de Alumnos', 'desc' => 'Administración de estudiantes', 'color' => 'info'],
        ['url' => app_url('super_admin/cursos.php'), 'icon' => 'fa-book', 'titulo' => 'Gestionar Cursos', 'desc' => 'Administración de cursos', 'color' => 'warning'],
        ['url' => app_url('super_admin/aulas.php'), 'icon' => 'fa-door-open', 'titulo' => 'Gestionar Aulas', 'desc' => 'Administración de aulas', 'color' => 'secondary']
    ];
} elseif ($rol == 'Docente') {
    $links = [
        ['url' => app_url('docente/ingresar_notas.php'), 'icon' => 'fa-chart-line', 'titulo' => 'Ingresar Notas', 'desc' => 'Registro de calificaciones', 'color' => 'primary'],
        ['url' => app_url('docente/exportar_excel.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Exportar Reportes', 'desc' => 'Generar reportes en Excel', 'color' => 'success'],
        ['url' => app_url('docente/comentarios.php'), 'icon' => 'fa-comments', 'titulo' => 'Comentarios', 'desc' => 'Observaciones a estudiantes', 'color' => 'info']
    ];
} elseif ($rol == 'Secretaria') {
    $links = [
        ['url' => app_url('secretaria/buscar_alumno.php'), 'icon' => 'fa-search', 'titulo' => 'Buscar Alumno', 'desc' => 'Consulta de registros', 'color' => 'primary'],
        ['url' => app_url('secretaria/ver_notas.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Ver Notas', 'desc' => 'Consulta de calificaciones', 'color' => 'success'],
        ['url' => app_url('dashboard.php'), 'icon' => 'fa-comments', 'titulo' => 'Reportes', 'desc' => 'Generación de reportes', 'color' => 'info']
    ];
} elseif ($rol == 'PadreFamilia') {
    $links = [
        ['url' => app_url('padre/ver_notas.php'), 'icon' => 'fa-chart-line', 'titulo' => 'Ver Notas', 'desc' => 'Calificaciones de mis hijos', 'color' => 'primary'],
        ['url' => app_url('padre/mis_hijos.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Mis Hijos', 'desc' => 'Información de estudiantes (boletas)', 'color' => 'success'],
        ['url' => app_url('padre/comentarios.php'), 'icon' => 'fa-comments', 'titulo' => 'Mensajes', 'desc' => 'Comunicación con docentes', 'color' => 'info']
    ];
} else {
    $links = [
        ['url' => app_url('dashboard.php'), 'icon' => 'fa-chart-line', 'titulo' => 'Sistema de Notas', 'desc' => 'Gestión académica integrada', 'color' => 'primary'],
        ['url' => app_url('dashboard.php'), 'icon' => 'fa-file-pdf', 'titulo' => 'Reportes en Línea', 'desc' => 'Generación de boletas y actas', 'color' => 'success'],
        ['url' => app_url('dashboard.php'), 'icon' => 'fa-comments', 'titulo' => 'Comunicación Directa', 'desc' => 'Padres - Docentes', 'color' => 'info']
    ];
}
?>

<div class="container-fluid">
    <h1 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
    <p class="lead">Panel de Control - SisHonores 1.0</p>
    
    <div class="row mt-4">
        <?php foreach($links as $link): ?>
        <div class="col-md-4 mb-3">
            <a href="<?php echo htmlspecialchars($link['url']); ?>" class="card-clickable">
                <div class="card text-white bg-<?php echo $link['color']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas <?php echo $link['icon']; ?>"></i> <?php echo htmlspecialchars($link['titulo']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($link['desc']); ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>