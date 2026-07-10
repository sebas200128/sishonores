<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (($_GET['subaction'] ?? '') == 'get_grados' || ($_GET['action'] ?? '') == 'get_grados') {
    $id_nivel = $_GET['id_nivel'] ?? 0;
    $query = "SELECT id_grado, nombre_grado FROM grados WHERE id_nivel = :nivel ORDER BY orden";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nivel', $id_nivel);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_nivel = $_POST['id_nivel'] ?? 0;
    $id_grado = $_POST['id_grado'] ?? 0;
    
    $query_alumnos = "SELECT a.id_alumno, a.apellidos, a.nombres 
                      FROM alumnos a
                      JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
                      JOIN grados g ON aa.id_grado = g.id_grado
                      WHERE a.activo = 1";
    if ($id_nivel > 0) $query_alumnos .= " AND g.id_nivel = :nivel";
    if ($id_grado > 0) $query_alumnos .= " AND g.id_grado = :grado";
    
    $stmt_alumnos = $db->prepare($query_alumnos);
    if ($id_nivel > 0) $stmt_alumnos->bindParam(':nivel', $id_nivel);
    if ($id_grado > 0) $stmt_alumnos->bindParam(':grado', $id_grado);
    $stmt_alumnos->execute();
    $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Boletas generadas para " . count($alumnos) . " alumnos</h2>";
    echo "<ul>";
    foreach($alumnos as $alumno) {
        echo "<li>Boleta generada para: " . $alumno['apellidos'] . ' ' . $alumno['nombres'] . "</li>";
    }
    echo "</ul>";
    echo '<a href="index.php?controller=director&action=generar_boletas" class="btn btn-primary">Volver</a>';
    exit;
}
?>
