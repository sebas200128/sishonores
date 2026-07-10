<?php
// core/Database.php

class Database {
    private $host = "localhost";
    private $db_name = "honoresbd";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                   $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

function app_base_path() {
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    // Como core/Database.php está una carpeta adentro, el root del proyecto es core/..
    $projectRoot = realpath(__DIR__ . '/..');

    if ($documentRoot && $projectRoot && stripos($projectRoot, $documentRoot) === 0) {
        $relativePath = substr($projectRoot, strlen($documentRoot));
        $relativePath = str_replace('\\', '/', $relativePath);
        $basePath = '/' . trim($relativePath, '/');
        $basePath = $basePath === '/' ? '' : $basePath;
        return $basePath;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = strpos($scriptName, '/sishonores/') === 0 ? '/sishonores' : '';
    return $basePath;
}

function app_url($path = '') {
    $cleanPath = ltrim($path, '/');
    if ($cleanPath === '') {
        return app_base_path() . '/index.php';
    }

    // Si es un recurso estático (assets, css, js), retornarlo directamente
    $parts = explode('/', $cleanPath);
    $first_part = $parts[0];
    $staticDirs = ['assets', 'css', 'js', 'sql'];
    if (in_array($first_part, $staticDirs)) {
        return app_base_path() . '/' . $cleanPath;
    }

    // Quitar la extensión .php si existe
    $cleanPathNoExt = preg_replace('/\.php$/', '', $cleanPath);

    // Mapear rutas heredadas al nuevo Front Controller
    $routeMap = [
        'login' => 'index.php?controller=login',
        'logout' => 'index.php?controller=login&action=logout',
        'dashboard' => 'index.php?controller=dashboard',
        
        'super_admin/usuarios' => 'index.php?controller=usuario&action=index',
        'super_admin/alumnos' => 'index.php?controller=alumno&action=index',
        'super_admin/matricula' => 'index.php?controller=alumno&action=matricula',
        'super_admin/docentes' => 'index.php?controller=docente&action=index',
        'super_admin/cursos' => 'index.php?controller=curso&action=index',
        'super_admin/aulas' => 'index.php?controller=aula&action=index',
        'super_admin/asignar_docentes' => 'index.php?controller=asignacion&action=index',
        'super_admin/generar_boletas' => 'index.php?controller=director&action=generar_boletas',
        'super_admin/generar_boletas_masivas' => 'index.php?controller=director&action=generar_boletas_masivas',
        'super_admin/exportar_excel' => 'index.php?controller=reporte&action=exportar_excel',
        
        'super_admin/reporte_alumnos_pdf' => 'index.php?controller=reporte&action=reporte_alumnos_pdf',
        'super_admin/reporte_asignaciones_pdf' => 'index.php?controller=reporte&action=reporte_asignaciones_pdf',
        'super_admin/reporte_aulas_pdf' => 'index.php?controller=reporte&action=reporte_aulas_pdf',
        'super_admin/reporte_cursos_pdf' => 'index.php?controller=reporte&action=reporte_cursos_pdf',

        'docente/mis_cursos' => 'index.php?controller=docente&action=mis_cursos',
        'docente/competencias' => 'index.php?controller=docente&action=competencias',
        'docente/ingresar_notas' => 'index.php?controller=docente&action=ingresar_notas',
        'docente/exportar_excel' => 'index.php?controller=docente&action=exportar_excel',
        'docente/comentarios' => 'index.php?controller=docente&action=comentarios',
        'docente/reporte_notas_pdf' => 'index.php?controller=docente&action=reporte_notas_pdf',
        
        'secretaria/buscar_alumno' => 'index.php?controller=secretaria&action=buscar_alumno',
        'secretaria/ver_notas' => 'index.php?controller=secretaria&action=ver_notas',
        
        'padre/ver_notas' => 'index.php?controller=padre&action=ver_notas',
        'padre/mis_hijos' => 'index.php?controller=padre&action=mis_hijos',
        'padre/comentarios' => 'index.php?controller=padre&action=comentarios',
        'padre/perfil' => 'index.php?controller=padre&action=perfil',

        'director/generar_actas' => 'index.php?controller=director&action=generar_actas',
        'director/actas_pdf' => 'index.php?controller=director&action=actas_pdf',
        'director/boletas_pdf' => 'index.php?controller=director&action=boletas_pdf',
        'director/reportes' => 'index.php?controller=director&action=reportes'
    ];

    if (array_key_exists($cleanPathNoExt, $routeMap)) {
        return app_base_path() . '/' . $routeMap[$cleanPathNoExt];
    }

    return app_base_path() . '/' . $cleanPath;
}

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
