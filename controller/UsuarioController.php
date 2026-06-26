<?php
// controller/UsuarioController.php

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
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }
        $usuario = $usuarioDAO->getById($id);
        if ($usuario) {
            echo json_encode([
                'id_usuario' => $usuario->getIdUsuario(),
                'nombres' => $usuario->getNombres(),
                'apellidos' => $usuario->getApellidos(),
                'email' => $usuario->getEmail(),
                'dni' => $usuario->getDni(),
                'telefono' => $usuario->getTelefono(),
                'id_rol' => $usuario->getIdRol(),
                'activo' => $usuario->getActivo()
            ]);
        } else {
            echo json_encode(['error' => 'Usuario no encontrado']);
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
        $email = trim($_POST['email'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $id_rol = (int)($_POST['id_rol'] ?? 0);
        $password = $_POST['password'] ?? '';

        if (empty($nombres) || empty($apellidos) || empty($email) || empty($dni) || $id_rol <= 0) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        // Validar DNI duplicado
        if ($usuarioDAO->checkDniDuplicado($dni, $id)) {
            echo json_encode(['success' => false, 'message' => 'El DNI ya se encuentra registrado por otro usuario.']);
            exit;
        }

        // Validar Email duplicado
        if ($usuarioDAO->checkEmailDuplicado($email, $id)) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya se encuentra registrado por otro usuario.']);
            exit;
        }

        $usuario = new UsuarioBean();
        $usuario->setIdUsuario($id);
        $usuario->setNombres($nombres);
        $usuario->setApellidos($apellidos);
        $usuario->setEmail($email);
        $usuario->setDni($dni);
        $usuario->setTelefono($telefono);
        $usuario->setIdRol($id_rol);

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $usuario->setPasswordHash($hash);
        }

        $success = $usuarioDAO->guardar($usuario);

        // Si es un nuevo usuario PadreFamilia, crear registro en padres_familia
        if ($success && $id === 0) {
            $rol_padre_id = (int)$usuarioDAO->getRolIdByNombre('PadreFamilia');
            if ($id_rol === $rol_padre_id) {
                $usuario_id = $usuario->getIdUsuario();
                $query_padre = "INSERT INTO padres_familia (id_usuario, password_generada) VALUES (:id_usuario, :password)";
                $stmt_padre = $db->prepare($query_padre);
                $stmt_padre->bindValue(':id_usuario', $usuario_id, PDO::PARAM_INT);
                $stmt_padre->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt_padre->execute();
            }
        }

        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        $success = $usuarioDAO->eliminar($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
