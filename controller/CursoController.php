<?php
// controller/CursoController.php

require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/../dao/CursoDAO.php';
require_once __DIR__ . '/../bean/CursoBean.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar permisos de administrador
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$cursoDAO = new CursoDAO($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        $curso = $cursoDAO->getById($id);
        if ($curso) {
            echo json_encode([
                'id_curso' => $curso->getIdCurso(),
                'nombre_curso' => $curso->getNombreCurso(),
                'codigo_curso' => $curso->getCodigoCurso(),
                'horas_semanales' => $curso->getHorasSemanales(),
                'id_nivel' => $curso->getIdNivel()
            ]);
        } else {
            echo json_encode(['error' => 'Curso no encontrado']);
        }
        exit;
    }

    if ($action === 'guardar') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $id = (int)($_POST['curso_id'] ?? 0);
        $nombre_curso = trim($_POST['nombre_curso'] ?? '');
        $codigo_curso = trim($_POST['codigo_curso'] ?? '');
        $horas_semanales = (int)($_POST['horas_semanales'] ?? 0);
        $id_nivel = (int)($_POST['id_nivel'] ?? 0);

        if (empty($nombre_curso) || empty($codigo_curso) || $horas_semanales <= 0 || $id_nivel <= 0) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos requeridos.']);
            exit;
        }

        // Validar si el código ya está duplicado
        if ($cursoDAO->checkCodigoDuplicado($codigo_curso, $id)) {
            echo json_encode(['success' => false, 'message' => 'El código de curso ya se encuentra registrado.']);
            exit;
        }

        $curso = new CursoBean();
        $curso->setIdCurso($id);
        $curso->setNombreCurso($nombre_curso);
        $curso->setCodigoCurso($codigo_curso);
        $curso->setHorasSemanales($horas_semanales);
        $curso->setIdNivel($id_nivel);

        $success = $cursoDAO->guardar($curso);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de curso inválido.']);
            exit;
        }

        $success = $cursoDAO->eliminar($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
