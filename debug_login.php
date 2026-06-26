<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Verificar si existe el usuario
echo "Buscando usuario con DNI: 31482828\n";
$stmt = $db->prepare('SELECT * FROM usuarios WHERE dni = ?');
$stmt->execute(['31482828']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "✓ Usuario encontrado:\n";
    echo "  ID: " . $user['id_usuario'] . "\n";
    echo "  Nombres: " . $user['nombres'] . "\n";
    echo "  Rol ID: " . $user['id_rol'] . "\n";
    
    // Obtener nombre del rol
    $stmt_rol = $db->prepare('SELECT nombre_rol FROM roles WHERE id_rol = ?');
    $stmt_rol->execute([$user['id_rol']]);
    $rol = $stmt_rol->fetch(PDO::FETCH_ASSOC);
    echo "  Rol: " . ($rol ? $rol['nombre_rol'] : 'NO ENCONTRADO') . "\n\n";
    
    // Verificar si existe en padres_familia
    echo "Buscando en padres_familia...\n";
    $stmt2 = $db->prepare('SELECT * FROM padres_familia WHERE id_usuario = ?');
    $stmt2->execute([$user['id_usuario']]);
    $padre = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    if ($padre) {
        echo "✓ Registro encontrado en padres_familia:\n";
        echo "  Password guardada: " . $padre['password_generada'] . "\n";
    } else {
        echo "✗ NO hay registro en padres_familia - ESTE ES EL PROBLEMA\n";
    }
} else {
    echo "✗ Usuario NO encontrado\n";
}
?>
