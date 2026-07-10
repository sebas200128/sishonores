require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Acciones exclusivas de Docentes
$docenteActions = ['mis_cursos', 'competencias', 'ingresar_notas', 'exportar_excel', 'exportar_excel_generar', 'comentarios', 'reporte_notas_pdf'];

if (in_array($action, $docenteActions, true)) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Docente') {
        header('Location: index.php?controller=login');
        exit;
    }
} else {
    // Acciones administrativas de Docentes (SuperUsuario/Director)
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'SuperUsuario' && $_SESSION['user_role'] !== 'Director')) {
        if ($action === 'index') {
            header('Location: index.php?controller=login');
            exit;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }
}

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

// Enrutamiento de vistas
if ($action === 'index') {
    include __DIR__ . '/../views/super_admin/docentes.php';
    exit;
}
if ($action === 'mis_cursos') {
    include __DIR__ . '/../views/docente/mis_cursos.php';
    exit;
}
if ($action === 'competencias') {
    include __DIR__ . '/../views/docente/competencias.php';
    exit;
}
if ($action === 'ingresar_notas') {
    include __DIR__ . '/../views/docente/ingresar_notas.php';
    exit;
}
if ($action === 'exportar_excel') {
    include __DIR__ . '/../views/docente/exportar_excel.php';
    exit;
}
if ($action === 'exportar_excel_generar') {
    include __DIR__ . '/../views/docente/exportar_excel_generar.php';
    exit;
}
if ($action === 'comentarios') {
    include __DIR__ . '/../views/docente/comentarios.php';
    exit;
}
if ($action === 'reporte_notas_pdf') {
    include __DIR__ . '/../views/docente/reporte_notas_pdf.php';
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID de docente inválido']);
            exit;
        }

        $usuario = $usuarioModel->getById($id);
        if ($usuario) {
            // Obtener los niveles asignados al docente
            $niveles = $usuarioModel->getDocenteNiveles($id);
            
            echo json_encode([
                'id_usuario' => $usuario->getIdUsuario(),
                'nombres' => $usuario->getNombres(),
                'apellidos' => $usuario->getApellidos(),
                'email' => $usuario->getEmail(),
                'dni' => $usuario->getDni(),
                'telefono' => $usuario->getTelefono(),
                'id_rol' => $usuario->getIdRol(),
                'niveles' => $niveles
            ]);
        } else {
            echo json_encode(['error' => 'Docente no encontrado']);
        }
        exit;
    }

    if ($action === 'guardar') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $id = (int)($_POST['usuario_id'] ?? 0);
        $nombres = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';
        $niveles = $_POST['niveles'] ?? []; // Array de IDs de niveles

        // Validaciones básicas
        if (empty($nombres) || empty($apellidos) || empty($dni) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            echo json_encode(['success' => false, 'message' => 'El DNI debe tener exactamente 8 dígitos numéricos.']);
            exit;
        }

        if (empty($niveles)) {
            echo json_encode(['success' => false, 'message' => 'Debe asignar al menos un nivel educativo.']);
            exit;
        }

        // Verificar duplicados de DNI
        if ($usuarioModel->checkDniDuplicado($dni, $id)) {
            echo json_encode(['success' => false, 'message' => 'El DNI ya se encuentra registrado por otro usuario.']);
            exit;
        }

        // Verificar duplicados de Email
        if ($usuarioModel->checkEmailDuplicado($email, $id)) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya se encuentra registrado por otro usuario.']);
            exit;
        }

        // Obtener el ID del rol Docente
        $id_rol_docente = (int)$usuarioModel->getRolIdByNombre('Docente');
        if ($id_rol_docente <= 0) {
            echo json_encode(['success' => false, 'message' => 'El rol Docente no está configurado en el sistema.']);
            exit;
        }

        $usuario = new Usuario($db);
        $usuario->setIdUsuario($id);
        $usuario->setNombres($nombres);
        $usuario->setApellidos($apellidos);
        $usuario->setEmail($email);
        $usuario->setDni($dni);
        $usuario->setTelefono($telefono);
        $usuario->setIdRol($id_rol_docente);

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $usuario->setPasswordHash($hash);
        } else if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'La contraseña es obligatoria para nuevos docentes.']);
            exit;
        }

        $success = $usuario->guardarDocenteConNiveles($niveles);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de docente inválido.']);
            exit;
        }

        $success = $usuarioModel->eliminarDocenteConAsociaciones($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
