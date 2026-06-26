<?php
require_once '../../util/Database.php';
$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

if ($action == 'notas') {
    $id_alumno = $_GET['id'] ?? 0;
    
    $query = "SELECT a.*, g.nombre_grado, s.nombre_seccion 
              FROM alumnos a
              JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
              JOIN grados g ON aa.id_grado = g.id_grado
              JOIN secciones s ON aa.id_seccion = s.id_seccion
              WHERE a.id_alumno = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_alumno);
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $query_notas = "SELECT c.nombre_curso, n.nota, n.bimestre, n.observacion, comp.nombre_competencia
                    FROM notas n
                    JOIN competencias comp ON n.id_competencia = comp.id_competencia
                    JOIN cursos c ON comp.id_curso = c.id_curso
                    WHERE n.id_alumno = :id_alumno
                    ORDER BY c.nombre_curso, n.bimestre";
    $stmt_notas = $db->prepare($query_notas);
    $stmt_notas->bindParam(':id_alumno', $id_alumno);
    $stmt_notas->execute();
    $notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Notas del Alumno</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Notas de <?php echo $alumno['apellidos'] . ' ' . $alumno['nombres']; ?></h2>
            <p>Grado: <?php echo $alumno['nombre_grado'] . ' - Sección ' . $alumno['nombre_seccion']; ?></p>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Curso</th><th>Competencia</th><th>Bimestre</th><th>Nota</th><th>Observación</th></tr>
                </thead>
                <tbody>
                    <?php foreach($notas as $nota): ?>
                    <tr>
                        <td><?php echo $nota['nombre_curso']; ?></td>
                        <td><?php echo $nota['nombre_competencia']; ?></td>
                        <td><?php echo $nota['bimestre']; ?>°</td>
                        <td><?php echo $nota['nota']; ?></td>
                        <td><?php echo $nota['observacion']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
    </html>
    <?php
}
?>