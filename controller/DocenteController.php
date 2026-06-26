<?php
// controller/DocenteController.php

require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';
require_once __DIR__ . '/../bean/UsuarioBean.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar permisos de administrador
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$usuarioDAO = new UsuarioDAO($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID de docente inválido']);
            exit;
        }

        $usuario = $usuarioDAO->getById($id);
        if ($usuario) {
            // Obtener los niveles asignados al docente
            $niveles = $usuarioDAO->getDocenteNiveles($id);
            
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
        if ($usuarioDAO->checkDniDuplicado($dni, $id)) {
            echo json_encode(['success' => false, 'message' => 'El DNI ya se encuentra registrado por otro usuario.']);
            exit;
        }

        // Verificar duplicados de Email
        if ($usuarioDAO->checkEmailDuplicado($email, $id)) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya se encuentra registrado por otro usuario.']);
            exit;
        }

        // Obtener el ID del rol Docente
        $id_rol_docente = (int)$usuarioDAO->getRolIdByNombre('Docente');
        if ($id_rol_docente <= 0) {
            echo json_encode(['success' => false, 'message' => 'El rol Docente no está configurado en el sistema.']);
            exit;
        }

        $usuario = new UsuarioBean();
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

        $success = $usuarioDAO->guardarDocenteConNiveles($usuario, $niveles);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de docente inválido.']);
            exit;
        }

        $success = $usuarioDAO->eliminarDocenteConAsociaciones($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
