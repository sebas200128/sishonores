<?php
// index.php - Front Controller
session_start();
require_once 'core/Database.php';

// Obtener el controlador y acción solicitados
$controller = $_GET['controller'] ?? $_POST['controller'] ?? 'login';
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

// Mapeo de controladores
$controllerMap = [
    'login' => 'LoginController.php',
    'logout' => 'LogoutController.php',
    'alumno' => 'AlumnoController.php',
    'asignacion' => 'AsignacionDocenteController.php',
    'aula' => 'AulaController.php',
    'curso' => 'CursoController.php',
    'docente' => 'DocenteController.php',
    'nota' => 'NotaController.php',
    'usuario' => 'UsuarioController.php'
];

// Si es un controlador conocido, se ejecuta su lógica
if (array_key_exists($controller, $controllerMap)) {
    $controllerFile = 'controllers/' . $controllerMap[$controller];
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        // Si el controlador tiene lógica de backend, se ejecutará y llamará a exit en ella.
        // Si no (o si la acción es de vista, por ejemplo 'index'), el propio controlador incluirá la vista al final.
    } else {
        die("Error: El archivo del controlador $controller no existe.");
    }
} else if ($controller === 'dashboard') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=login');
        exit;
    }
    include 'views/dashboard.php';
    exit;
} else if ($controller === 'reporte') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=login');
        exit;
    }
    
    $reportMap = [
        'reporte_alumnos_pdf' => 'views/super_admin/reporte_alumnos_pdf.php',
        'reporte_asignaciones_pdf' => 'views/super_admin/reporte_asignaciones_pdf.php',
        'reporte_aulas_pdf' => 'views/super_admin/reporte_aulas_pdf.php',
        'reporte_cursos_pdf' => 'views/super_admin/reporte_cursos_pdf.php',
        'exportar_excel' => 'views/super_admin/exportar_excel.php'
    ];
    
    if (array_key_exists($action, $reportMap)) {
        include $reportMap[$action];
        exit;
    }
} else if ($controller === 'padre') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
        header('Location: index.php?controller=login');
        exit;
    }
    
    $padreViews = [
        'ver_notas' => 'views/padre/ver_notas.php',
        'mis_hijos' => 'views/padre/mis_hijos.php',
        'comentarios' => 'views/padre/comentarios.php',
        'perfil' => 'views/padre/perfil.php'
    ];
    
    if (array_key_exists($action, $padreViews)) {
        include $padreViews[$action];
        exit;
    }
} else if ($controller === 'secretaria') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Secretaria') {
        header('Location: index.php?controller=login');
        exit;
    }
    
    $secretariaViews = [
        'buscar_alumno' => 'views/secretaria/buscar_alumno.php',
        'ver_notas' => 'views/secretaria/ver_notas.php',
        'ver_notas_detalle' => 'views/secretaria/ver_notas_detalle.php'
    ];
    
    if (array_key_exists($action, $secretariaViews)) {
        include $secretariaViews[$action];
        exit;
    }
} else if ($controller === 'director') {
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'Director' && $_SESSION['user_role'] != 'SuperUsuario')) {
        header('Location: index.php?controller=login');
        exit;
    }
    
    $directorViews = [
        'generar_actas' => 'views/director/generar_actas.php',
        'actas_pdf' => 'views/director/actas_pdf.php',
        'boletas_pdf' => 'views/director/boletas_pdf.php',
        'reportes' => 'views/director/reportes.php',
        'generar_boletas' => 'views/super_admin/generar_boletas.php',
        'generar_boletas_masivas' => 'views/super_admin/generar_boletas_masivas.php'
    ];
    
    if (array_key_exists($action, $directorViews)) {
        include $directorViews[$action];
        exit;
    }
} else {
    header('Location: index.php?controller=login');
    exit;
}