<?php
// controller/AsignacionDocenteController.php

require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/../dao/AsignacionDocenteDAO.php';
require_once __DIR__ . '/../bean/AsignacionDocenteBean.php';

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
$asignacionDAO = new AsignacionDocenteDAO($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'get_cursos') {
        $id_aula = (int)($_POST['id_aula'] ?? 0);
        if ($id_aula <= 0) {
            json_response(['success' => false, 'message' => 'Aula inválida'], 422);
        }

        $cursos = $asignacionDAO->getCursosPorAula($id_aula);
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

        if (!$asignacionDAO->checkDocenteActivo($id_usuario)) {
            json_response(['success' => false, 'message' => 'El usuario seleccionado no es un docente activo'], 422);
        }

        if (!$asignacionDAO->checkCursoNivelAula($id_curso, $id_aula)) {
            json_response(['success' => false, 'message' => 'El curso no corresponde al nivel del aula seleccionada'], 422);
        }

        if ($asignacionDAO->checkAsignacionExiste($id_usuario, $id_curso, $id_aula, $anio)) {
            json_response(['success' => false, 'message' => 'Esta asignación ya existe'], 409);
        }

        $asignacion = new AsignacionDocenteBean();
        $asignacion->setIdUsuario($id_usuario);
        $asignacion->setIdCurso($id_curso);
        $asignacion->setIdAula($id_aula);
        $asignacion->setAnio($anio);

        $success = $asignacionDAO->guardar($asignacion);
        json_response(['success' => $success]);
    }

    if ($action === 'listar') {
        $rows = $asignacionDAO->listarAsignacionesAnioActual();

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

        $success = $asignacionDAO->eliminar($id);
        json_response(['success' => $success]);
    }

    json_response(['success' => false, 'message' => 'Acción no válida'], 400);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
}
?>
