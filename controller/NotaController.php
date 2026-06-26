<?php
// controller/NotaController.php

require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/../dao/NotaDAO.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar permisos de docente
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$notaDAO = new NotaDAO($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'alumnos_notas') {
        $curso = (int)($_POST['curso'] ?? 0);
        $bimestre = (int)($_POST['bimestre'] ?? 1);

        if ($curso <= 0) {
            echo json_encode(['competencias' => [], 'alumnos' => []]);
            exit;
        }

        $resultado = $notaDAO->obtenerAlumnosYNotasPorCursoBimestre($curso, $bimestre);
        echo json_encode($resultado);
        exit;
    }

    if ($action === 'guardar_notas') {
        $id_curso = (int)($_POST['id_docente_curso_aula'] ?? 0);
        $bimestre = (int)($_POST['bimestre'] ?? 1);
        $datos = json_decode($_POST['datos'] ?? '', true);

        if ($id_curso <= 0 || empty($datos)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        $success = $notaDAO->guardarNotasDeAlumnos($id_curso, $bimestre, $datos);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'list_competencias') {
        $id_curso = (int)($_POST['id_curso'] ?? 0);
        if ($id_curso <= 0) {
            echo json_encode(['success' => false, 'message' => 'Curso inválido']);
            exit;
        }

        $competencias = $notaDAO->listarCompetenciasPorDocenteCursoAula($id_curso);
        
        $html = '<ul class="list-group">';
        foreach ($competencias as $c) {
            $html .= '<li class="list-group-item d-flex justify-content-between">' 
                     . htmlspecialchars($c['nombre_competencia'], ENT_QUOTES, 'UTF-8') 
                     . '<button class="btn btn-sm btn-danger" onclick="eliminarCompetencia(' 
                     . (int)$c['id_competencia'] . ',' . $id_curso 
                     . ')"><i class="fas fa-trash"></i></button></li>';
        }
        $html .= '</ul>';
        
        // Devolvemos HTML directamente como hacía la lógica previa, o JSON. 
        // Para mantener compatibilidad exacta sin modificar demasiado el Javascript de la vista, devolvemos el HTML text.
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    if ($action === 'add_competencia') {
        $id_curso = (int)($_POST['id_curso'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($id_curso <= 0 || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Datos insuficientes']);
            exit;
        }

        $success = $notaDAO->agregarCompetencia($id_curso, $nombre);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete_competencia') {
        $id = (int)($_POST['id_competencia'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        $success = $notaDAO->eliminarCompetencia($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'comentarios_alumnos') {
        $id_curso = (int)($_POST['id_curso'] ?? 0);
        if ($id_curso <= 0) {
            echo '<div class="alert alert-danger">Asignación no válida</div>';
            exit;
        }

        // Obtener alumnos de la asignación
        $query_alumnos = "SELECT a.id_alumno, a.apellidos, a.nombres 
                          FROM alumnos a 
                          JOIN docente_curso_aula dca ON dca.id_aula = a.id_aula 
                          WHERE dca.id_docente_curso_aula = :curso 
                            AND a.activo = 1 
                          ORDER BY a.apellidos, a.nombres";
        $stmt_alumnos = $db->prepare($query_alumnos);
        $stmt_alumnos->bindValue(':curso', $id_curso, PDO::PARAM_INT);
        $stmt_alumnos->execute();
        $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);

        $html = '<div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Alumno</th><th>Comentario</th><th>Acción</th></tr></thead><tbody>';
        foreach ($alumnos as $a) {
            $id_alumno = (int)$a['id_alumno'];
            $comentario = $notaDAO->obtenerComentarioPorAlumnoYCurso($id_alumno, $id_curso, 1);
            
            $nombre_completo = htmlspecialchars($a['apellidos'] . ' ' . $a['nombres'], ENT_QUOTES, 'UTF-8');
            $html .= '<tr><td>' . $nombre_completo . '</td><td><textarea class="form-control" id="comentario_' . $id_alumno . '" rows="2">' . htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8') . '</textarea></td><td><button class="btn btn-primary" onclick="guardarComentario(' . $id_alumno . ')">Guardar</button></td></tr>';
        }
        $html .= '</tbody></table></div>';

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    if ($action === 'guardar_comentario') {
        $id_alumno = (int)($_POST['id_alumno'] ?? 0);
        $id_curso = (int)($_POST['id_curso'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');
        $bimestre = 1;

        if ($id_alumno <= 0 || $id_curso <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $success = $notaDAO->guardarComentarioPorAlumnoYCurso($id_alumno, $id_curso, $bimestre, $comentario);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
