<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT a.apellidos, a.nombres, a.dni, g.nombre_grado, s.nombre_seccion,
          (SELECT AVG(nota) FROM notas WHERE id_alumno = a.id_alumno) as promedio
          FROM alumnos a
          JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
          JOIN grados g ON aa.id_grado = g.id_grado
          JOIN secciones s ON aa.id_seccion = s.id_seccion
          WHERE a.activo = 1
          ORDER BY g.orden, s.nombre_seccion, a.apellidos";
$stmt = $db->prepare($query);
$stmt->execute();
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="reporte_alumnos.xls"');
?>
<table border="1">
    <thead>
        <tr><th>Apellidos</th><th>Nombres</th><th>DNI</th><th>Grado</th><th>Sección</th><th>Promedio General</th></tr>
    </thead>
    <tbody>
        <?php foreach($alumnos as $alumno): ?>
        <tr>
            <td><?php echo $alumno['apellidos']; ?></td>
            <td><?php echo $alumno['nombres']; ?></td>
            <td><?php echo $alumno['dni']; ?></td>
            <td><?php echo $alumno['nombre_grado']; ?></td>
            <td><?php echo $alumno['nombre_seccion']; ?></td>
            <td><?php echo number_format($alumno['promedio'] ?? 0, 1); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
