<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . app_url('login.php'));
    exit;
}

$role = $_SESSION['user_role'];

$adminItems = [
    ['url' => 'super_admin/docentes.php', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Docentes'],
    ['url' => 'super_admin/cursos.php', 'icon' => 'fa-book', 'label' => 'Cursos'],
    ['url' => 'super_admin/alumnos.php', 'icon' => 'fa-graduation-cap', 'label' => 'Alumnos'],
    ['url' => 'super_admin/matricula.php', 'icon' => 'fa-file-signature', 'label' => 'Matricular Alumno'],
    ['url' => 'super_admin/aulas.php', 'icon' => 'fa-door-open', 'label' => 'Aulas'],
    ['url' => 'super_admin/asignar_docentes.php', 'icon' => 'fa-chalkboard-user', 'label' => 'Asignar Docentes'],
    ['url' => 'super_admin/generar_boletas.php', 'icon' => 'fa-file-pdf', 'label' => 'Generar Boletas'],
    ['url' => 'super_admin/exportar_excel.php', 'icon' => 'fa-file-excel', 'label' => 'Exportar Excel']
];

if ($role == 'SuperUsuario') {
    array_unshift($adminItems, ['url' => 'super_admin/usuarios.php', 'icon' => 'fa-users', 'label' => 'Usuarios']);
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?php echo htmlspecialchars(app_url('assets/escudo%20trasparente.png')); ?>" alt="Logo SisHonores" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <div class="logo">SisHonores 1.0</div>
                <p>Colegio Matemático Honores</p>
            </div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="<?php echo htmlspecialchars(app_url('dashboard.php')); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        
        <?php if(in_array($role, ['SuperUsuario', 'Director'])): ?>
        <li class="menu-title">Administración</li>
        <?php foreach ($adminItems as $item): ?>
        <li><a href="<?php echo htmlspecialchars(app_url($item['url'])); ?>"><i class="fas <?php echo htmlspecialchars($item['icon']); ?>"></i> <?php echo htmlspecialchars($item['label']); ?></a></li>
        <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if($role == 'Docente'): ?>
        <li class="menu-title">Gestión Académica</li>
        <li><a href="<?php echo htmlspecialchars(app_url('docente/mis_cursos.php')); ?>"><i class="fas fa-book-open"></i> Mis Cursos</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('docente/competencias.php')); ?>"><i class="fas fa-tasks"></i> Competencias</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('docente/ingresar_notas.php')); ?>"><i class="fas fa-edit"></i> Ingresar Notas</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('docente/exportar_excel.php')); ?>"><i class="fas fa-file-excel"></i> Exportar Excel</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('docente/comentarios.php')); ?>"><i class="fas fa-comments"></i> Comentarios</a></li>
        <?php endif; ?>
        
        <?php if($role == 'Secretaria'): ?>
        <li class="menu-title">Consultas</li>
        <li><a href="<?php echo htmlspecialchars(app_url('secretaria/buscar_alumno.php')); ?>"><i class="fas fa-search"></i> Buscar Alumno</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('secretaria/ver_notas.php')); ?>"><i class="fas fa-eye"></i> Ver Notas</a></li>
        <?php endif; ?>
        
        <?php if($role == 'PadreFamilia'): ?>
        <li class="menu-title">Mis Hijos</li>
        <li><a href="<?php echo htmlspecialchars(app_url('padre/ver_notas.php')); ?>"><i class="fas fa-chart-line"></i> Ver Notas</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('padre/comentarios.php')); ?>"><i class="fas fa-comments"></i> Comentarios</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('padre/perfil.php')); ?>"><i class="fas fa-user"></i> Mi Perfil</a></li>
        <?php endif; ?>
        
        <li class="menu-title">Sistema</li>
        <li><a href="<?php echo htmlspecialchars(app_url('logout.php')); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
    </ul>
</div>
<div class="main-content">
    <div class="navbar-container d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-dark d-md-none" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="user-info">
            <span class="me-3"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <span class="badge bg-primary"><?php echo htmlspecialchars($role); ?></span>
        </div>
    </div>
    <div id="alertContainer"></div>
