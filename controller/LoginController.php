<?php
// controller/LoginController.php

require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $dni = $_POST['dni'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($dni) || empty($password)) {
        header('Location: ../vista/login.php?error=' . urlencode("Todos los campos son obligatorios"));
        exit;
    }
    
    $usuarioDAO = new UsuarioDAO($db);
    $user = $usuarioDAO->getByDni($dni);
    
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
            
            header('Location: ../vista/dashboard.php');
            exit;
        }
    }
    
    header('Location: ../vista/login.php?error=' . urlencode("DNI o contraseña incorrectos"));
    exit;
} else {
    header('Location: ../vista/login.php');
    exit;
}
?>
