<?php
// controller/AlumnoController.php

require_once __DIR__ . '/../models/Alumno.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['SuperUsuario', 'Director', 'Secretaria'])) {
    if ($action === 'index' || $action === 'matricula') {
        header('Location: index.php?controller=login');
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$alumnoModel = new Alumno($db);

if ($action === 'index') {
    include __DIR__ . '/../views/super_admin/alumnos.php';
    exit;
}
if ($action === 'matricula') {
    include __DIR__ . '/../views/super_admin/matricula.php';
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID de alumno inválido']);
            exit;
        }

        $alumno = $alumnoModel->getById($id);
        if ($alumno) {
            echo json_encode([
                'id_alumno' => $alumno->getIdAlumno(),
                'codigo_estudiante' => $alumno->getCodigoEstudiante(),
                'nombres' => $alumno->getNombres(),
                'apellidos' => $alumno->getApellidos(),
                'dni' => $alumno->getDni(),
                'fecha_nacimiento' => $alumno->getFechaNacimiento(),
                'telefono' => $alumno->getTelefono(),
                'nombre_apoderado' => $alumno->getNombreApoderado(),
                'telefono_apoderado' => $alumno->getTelefonoApoderado(),
                'email_apoderado' => $alumno->getEmailApoderado(),
                'id_aula' => $alumno->getIdAula()
            ]);
        } else {
            echo json_encode(['error' => 'Alumno no encontrado']);
        }
        exit;
    }

    if ($action === 'check_dni') {
        $dni = trim($_POST['dni'] ?? '');
        $id = (int)($_POST['id'] ?? 0);

        if ($dni === '') {
            echo json_encode(['available' => true, 'message' => '']);
            exit;
        }

        $duplicado = $alumnoModel->checkDniDuplicado($dni, $id);
        echo json_encode([
            'available' => !$duplicado,
            'message' => $duplicado ? 'El DNI ya se encuentra registrado.' : ''
        ]);
        exit;
    }

    if ($action === 'guardar') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $id = (int)($_POST['alumno_id'] ?? 0);
        $codigo_estudiante = trim($_POST['codigo_estudiante'] ?? '');
        $nombres = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $nombre_apoderado = trim($_POST['nombre_apoderado'] ?? '');
        $telefono_apoderado = trim($_POST['telefono_apoderado'] ?? '');
        $email_apoderado = trim($_POST['email_apoderado'] ?? '');
        $id_aula = trim($_POST['id_aula'] ?? '');

        if (empty($codigo_estudiante) || empty($nombres) || empty($apellidos) || empty($dni) || empty($fecha_nacimiento) || empty($nombre_apoderado) || empty($email_apoderado)) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        // Validar DNI duplicado
        if ($alumnoModel->checkDniDuplicado($dni, $id)) {
            echo json_encode(['success' => false, 'message' => 'El DNI de este alumno ya se encuentra registrado.']);
            exit;
        }

        // Validar Código duplicado
        if ($alumnoModel->checkCodigoDuplicado($codigo_estudiante, $id)) {
            echo json_encode(['success' => false, 'message' => 'El código de estudiante ya se encuentra registrado.']);
            exit;
        }

        $alumno = new Alumno($db);
        $alumno->setIdAlumno($id);
        $alumno->setCodigoEstudiante($codigo_estudiante);
        $alumno->setNombres($nombres);
        $alumno->setApellidos($apellidos);
        $alumno->setDni($dni);
        $alumno->setFechaNacimiento($fecha_nacimiento);
        $alumno->setTelefono($telefono);
        $alumno->setNombreApoderado($nombre_apoderado);
        $alumno->setTelefonoApoderado($telefono_apoderado);
        $alumno->setEmailApoderado($email_apoderado);
        $alumno->setIdAula($id_aula);

        $resultado = $alumno->guardar();
        echo json_encode($resultado);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        $success = $alumnoModel->eliminar($id);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
