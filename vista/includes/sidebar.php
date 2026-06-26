<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . app_url('login.php'));
    exit;
}

$role = $_SESSION['user_role'];
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo htmlspecialchars(app_url('assets/escudo%20trasparente.png')); ?>" alt="Logo SisHonores" class="sidebar-logo">
        <div class="logo">SisHonores 1.0</div>
        <p>Colegio Matemático Honores</p>
    </div>
    <ul class="sidebar-menu">
        <li><a href="<?php echo htmlspecialchars(app_url('dashboard.php')); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        
        <?php if($role == 'SuperUsuario' || $role == 'Director'): ?>
        <li class="menu-title">Administración</li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/usuarios.php')); ?>"><i class="fas fa-users"></i> Usuarios</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/docentes.php')); ?>"><i class="fas fa-chalkboard-teacher"></i> Docentes</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/cursos.php')); ?>"><i class="fas fa-book"></i> Cursos</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/alumnos.php')); ?>"><i class="fas fa-graduation-cap"></i> Alumnos</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/matricula.php')); ?>"><i class="fas fa-file-signature"></i> Matricular Alumno</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/aulas.php')); ?>"><i class="fas fa-door-open"></i> Aulas</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/asignar_docentes.php')); ?>"><i class="fas fa-chalkboard-user"></i> Asignar Docentes</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/generar_boletas.php')); ?>"><i class="fas fa-file-pdf"></i> Generar Boletas</a></li>
        <li><a href="<?php echo htmlspecialchars(app_url('super_admin/exportar_excel.php')); ?>"><i class="fas fa-file-excel"></i> Exportar Excel</a></li>
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
