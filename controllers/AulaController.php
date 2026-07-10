require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Aula.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    if ($action === 'index') {
        header('Location: index.php?controller=login');
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$aulaModel = new Aula($db);

if ($action === 'index') {
    include __DIR__ . '/../views/super_admin/aulas.php';
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        $aula = $aulaModel->getById($id);
        if ($aula) {
            echo json_encode([
                'id_aula' => $aula->getIdAula(),
                'id_grado' => $aula->getIdGrado(),
                'id_seccion' => $aula->getIdSeccion(),
                'vacantes' => $aula->getVacantes(),
                'anio' => $aula->getAnio()
            ]);
        } else {
            echo json_encode(['error' => 'Aula no encontrada']);
        }
        exit;
    }

    if ($action === 'guardar') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $id = (int)($_POST['aula_id'] ?? 0);
        $id_grado = (int)($_POST['id_grado'] ?? 0);
        $id_seccion = (int)($_POST['id_seccion'] ?? 0);
        $vacantes = (int)($_POST['vacantes'] ?? 30);
        $anio = (int)($_POST['anio'] ?? date('Y'));

        if ($id_grado <= 0 || $id_seccion <= 0 || $vacantes <= 0 || $anio <= 0) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos requeridos correctamente.']);
            exit;
        }

        // Validar si el aula ya está duplicada para el grado, sección y año
        if ($aulaModel->checkAulaDuplicada($id_grado, $id_seccion, $anio, $id)) {
            echo json_encode(['success' => false, 'message' => 'Esta aula ya se encuentra registrada para el grado, sección y año indicados.']);
            exit;
        }

        $aula = new Aula($db);
        $aula->setIdAula($id);
        $aula->setIdGrado($id_grado);
        $aula->setIdSeccion($id_seccion);
        $aula->setVacantes($vacantes);
        $aula->setAnio($anio);

        $success = $aula->guardar();
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de aula inválido.']);
            exit;
        }

        $success = $aulaModel->eliminar($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
