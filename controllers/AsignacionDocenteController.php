<?php
// controller/AsignacionDocenteController.php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/AsignacionDocente.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

if ($action === 'index') {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['SuperUsuario', 'Director'], true)) {
        header('Location: index.php?controller=login');
        exit;
    }
    include __DIR__ . '/../views/super_admin/asignar_docentes.php';
    exit;
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function is_admin_user() {
    return isset($_SESSION['user_id']) && in_array($_SESSION['user_role'] ?? '', ['SuperUsuario', 'Director'], true);
}

if (!is_admin_user()) {
    json_response(['success' => false, 'message' => 'Sesión no autorizada'], 401);
}

$database = new Database();
$db = $database->getConnection();
$asignacionModel = new AsignacionDocente($db);

try {
    if ($action === 'get_cursos') {
        $id_aula = (int)($_POST['id_aula'] ?? 0);
        if ($id_aula <= 0) {
            json_response(['success' => false, 'message' => 'Aula inválida'], 422);
        }

        $cursos = $asignacionModel->getCursosPorAula($id_aula);
        json_response($cursos);
    }

    if ($action === 'asignar') {
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        $id_curso = (int)($_POST['id_curso'] ?? 0);
        $id_aula = (int)($_POST['id_aula'] ?? 0);
        $anio = (int)date('Y');

        if ($id_usuario <= 0 || $id_curso <= 0 || $id_aula <= 0) {
            json_response(['success' => false, 'message' => 'Seleccione docente, aula y curso'], 422);
        }

        if (!$asignacionModel->checkDocenteActivo($id_usuario)) {
            json_response(['success' => false, 'message' => 'El usuario seleccionado no es un docente activo'], 422);
        }

        if (!$asignacionModel->checkCursoNivelAula($id_curso, $id_aula)) {
            json_response(['success' => false, 'message' => 'El curso no corresponde al nivel del aula seleccionada'], 422);
        }

        if ($asignacionModel->checkAsignacionExiste($id_usuario, $id_curso, $id_aula, $anio)) {
            json_response(['success' => false, 'message' => 'Esta asignación ya existe'], 409);
        }

        $asignacion = new AsignacionDocente($db);
        $asignacion->setIdUsuario($id_usuario);
        $asignacion->setIdCurso($id_curso);
        $asignacion->setIdAula($id_aula);
        $asignacion->setAnio($anio);

        $success = $asignacion->guardar();
        json_response(['success' => $success]);
    }

    if ($action === 'listar') {
        $rows = $asignacionModel->listarAsignacionesAnioActual();

        if (empty($rows)) {
            echo '<tr><td colspan="4" class="text-center">No hay asignaciones registradas</td></tr>';
            exit;
        }

        foreach ($rows as $asig) {
            $docente = htmlspecialchars($asig['nombres'] . ' ' . $asig['apellidos'], ENT_QUOTES, 'UTF-8');
            $curso = htmlspecialchars($asig['nombre_curso'], ENT_QUOTES, 'UTF-8');
            $aula = htmlspecialchars($asig['nombre_grado'] . ' "' . $asig['nombre_seccion'] . '"', ENT_QUOTES, 'UTF-8');
            $id = (int)$asig['id_docente_curso_aula'];

            echo '<tr><td>' . $docente . '</td><td>' . $curso . '</td><td>' . $aula . '</td><td><button class="btn btn-sm btn-danger" onclick="eliminarAsignacion(' . $id . ')"><i class="fas fa-trash"></i></button></td></tr>';
        }
        exit;
    }

    if ($action === 'eliminar') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'ID inválido'], 422);
        }

        $success = $asignacionModel->eliminar($id);
        json_response(['success' => $success]);
    }

    json_response(['success' => false, 'message' => 'Acción no válida'], 400);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
}
?>
