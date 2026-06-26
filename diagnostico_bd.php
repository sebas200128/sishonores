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
    
    echo "<h2>Diagnóstico de Tabla padres_familia</h2>";
    echo "<hr>";
    
    // Verificar si existe la tabla
    $stmt = $conn->prepare("SHOW TABLES LIKE 'padres_familia'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color:green;'><strong>✓ Tabla padres_familia existe</strong></p>";
        
        // Mostrar estructura
        echo "<h3>Estructura:</h3>";
        $stmt = $conn->prepare("DESCRIBE padres_familia");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<pre>";
        print_r($columns);
        echo "</pre>";
        
        // Mostrar registros
        echo "<h3>Registros:</h3>";
        $stmt = $conn->prepare("SELECT * FROM padres_familia");
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<pre>";
        print_r($records);
        echo "</pre>";
        echo "Total: " . count($records) . " registros";
    } else {
        echo "<p style='color:red;'><strong>✗ Tabla padres_familia NO existe</strong></p>";
        echo "<p>Se necesita crear esta tabla.</p>";
    }
    
    echo "<hr>";
    echo "<h3>Verificar tabla alumno_padre:</h3>";
    $stmt = $conn->prepare("SHOW TABLES LIKE 'alumno_padre'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color:green;'><strong>✓ Tabla alumno_padre existe</strong></p>";
        $stmt = $conn->prepare("SELECT * FROM alumno_padre");
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total: " . count($records) . " registros<br>";
        if ($records) {
            echo "<pre>";
            print_r($records);
            echo "</pre>";
        }
    } else {
        echo "<p style='color:red;'><strong>✗ Tabla alumno_padre NO existe</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
}
?>
