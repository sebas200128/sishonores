// controllers/LoginController.php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $dni = $_POST['dni'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($dni) || empty($password)) {
        header('Location: index.php?controller=login&error=' . urlencode("Todos los campos son obligatorios"));
        exit;
    }
    
    $usuario = new Usuario($db);
    $user = $usuario->getByDni($dni);
    
    if (!$user) {
        // Fallback: Si no se encuentra el usuario directamente, buscar si es el DNI de un alumno
        // y obtener el usuario apoderado asociado
        $query_fallback = "SELECT u.*, r.nombre_rol 
                           FROM usuarios u 
                           JOIN roles r ON u.id_rol = r.id_rol
                           JOIN padres_familia pf ON u.id_usuario = pf.id_usuario
                           JOIN alumno_padre ap ON pf.id_padre = ap.id_padre
                           JOIN alumnos a ON ap.id_alumno = a.id_alumno
                           WHERE a.dni = :dni AND a.activo = 1 AND u.activo = 1 
                           LIMIT 1";
        $stmt_fallback = $db->prepare($query_fallback);
        $stmt_fallback->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt_fallback->execute();
        $user = $stmt_fallback->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($user) {
        $loginExitoso = false;
        
        // Verificar si es padre (contraseña generada)
        if ($user['nombre_rol'] == 'PadreFamilia') {
            $query_padre = "SELECT password_generada FROM padres_familia WHERE id_usuario = :id";
            $stmt_padre = $db->prepare($query_padre);
            $stmt_padre->bindValue(':id', $user['id_usuario'], PDO::PARAM_INT);
            $stmt_padre->execute();
            $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);
            
            if ($padre && $password == $padre['password_generada']) {
                $loginExitoso = true;
            }
        } else {
            // Verificar contraseña normal (encriptada por hash)
            if (password_verify($password, $user['password_hash'])) {
                $loginExitoso = true;
            }
        }
        
        if ($loginExitoso) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombres'] . ' ' . $user['apellidos'];
            $_SESSION['user_role'] = $user['nombre_rol'];
            $_SESSION['user_dni'] = $user['dni'];
            
            header('Location: index.php?controller=dashboard');
            exit;
        }
    }
    
    header('Location: index.php?controller=login&error=' . urlencode("DNI o contraseña incorrectos"));
    exit;
} else {
    // Si ya está logueado, redirigir al dashboard
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=dashboard');
        exit;
    }
    include __DIR__ . '/../views/login.php';
    exit;
}
?>
