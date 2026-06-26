<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'honoresbd';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Prueba de Creación de Padre de Familia</h2>";
    echo "<hr>";
    
    // Obtener el rol PadreFamilia
    $stmt = $conn->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'PadreFamilia'");
    $stmt->execute();
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Rol PadreFamilia ID: " . ($rol ? $rol['id_rol'] : 'NO ENCONTRADO') . "<br>";
    
    if (!$rol) {
        echo "<p style='color:red;'><strong>ERROR: Rol PadreFamilia no existe</strong></p>";
        echo "<p>Disponibles:</p>";
        $stmt = $conn->prepare("SELECT * FROM roles");
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($roles);
        echo "</pre>";
        die();
    }
    
    // Intentar crear un usuario de prueba
    echo "<h3>Intentando crear usuario padre...</h3>";
    $dni_padre = 'TEST' . time();
    $password = 'TestPass123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, email, dni, telefono, id_rol, password_hash, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $result = $stmt->execute(['Test Padre', 'Test', 'test@test.com', $dni_padre, '123456', $rol['id_rol'], $hash]);
    echo "Resultado INSERT usuario: " . ($result ? 'SUCCESS' : 'FAIL') . "<br>";
    
    $usuario_id = $conn->lastInsertId();
    echo "Usuario ID creado: " . $usuario_id . "<br>";
    
    if ($usuario_id > 0) {
        // Intentar crear padres_familia
        echo "<h3>Intentando crear padres_familia...</h3>";
        $stmt2 = $conn->prepare("INSERT INTO padres_familia (id_usuario, password_generada) VALUES (?, ?)");
        $result2 = $stmt2->execute([$usuario_id, 'TestPass123']);
        echo "Resultado INSERT padres_familia: " . ($result2 ? 'SUCCESS' : 'FAIL') . "<br>";
        
        $padre_id = $conn->lastInsertId();
        echo "Padre ID creado: " . $padre_id . "<br>";
        
        if ($padre_id > 0) {
            // Crear alumno_padre con ID 4 (que existe)
            echo "<h3>Intentando crear alumno_padre...</h3>";
            $stmt3 = $conn->prepare("INSERT INTO alumno_padre (id_alumno, id_padre) VALUES (?, ?)");
            $result3 = $stmt3->execute([4, $padre_id]);
            echo "Resultado INSERT alumno_padre: " . ($result3 ? 'SUCCESS' : 'FAIL') . "<br>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Verificar si se crearon:</h3>";
    $stmt_check = $conn->prepare("SELECT u.id_usuario, u.nombres, pf.id_padre, pf.password_generada FROM usuarios u JOIN padres_familia pf ON u.id_usuario = pf.id_usuario WHERE u.dni LIKE 'TEST%' ORDER BY u.id_usuario DESC LIMIT 5");
    $stmt_check->execute();
    $results = $stmt_check->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($results);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
    echo "<p>" . $e->getFile() . " - Línea: " . $e->getLine() . "</p>";
}
?>
