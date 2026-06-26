<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id_grado = $_GET['id_grado'] ?? 0;
$bimestre = $_GET['bimestre'] ?? 1;

if ($id_grado > 0) {
    $query_alumnos = "SELECT a.id_alumno, a.apellidos, a.nombres, a.dni, s.nombre_seccion,
                      (SELECT nota FROM notas n 
                       JOIN competencias c ON n.id_competencia = c.id_competencia 
                       WHERE n.id_alumno = a.id_alumno AND c.id_curso = (SELECT MIN(id_curso) FROM cursos WHERE id_nivel = (SELECT id_nivel FROM grados WHERE id_grado = :grado))
                       AND n.bimestre = :bimestre LIMIT 1) as nota
                      FROM alumnos a
                      JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
                      JOIN grados g ON aa.id_grado = g.id_grado
                      JOIN secciones s ON aa.id_seccion = s.id_seccion
                      WHERE g.id_grado = :grado AND a.activo = 1
                      ORDER BY s.nombre_seccion, a.apellidos";
    $stmt_alumnos = $db->prepare($query_alumnos);
    $stmt_alumnos->bindParam(':grado', $id_grado);
    $stmt_alumnos->bindParam(':bimestre', $bimestre);
    $stmt_alumnos->execute();
    $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
    
    $query_grado = "SELECT nombre_grado FROM grados WHERE id_grado = :grado";
    $stmt_grado = $db->prepare($query_grado);
    $stmt_grado->bindParam(':grado', $id_grado);
    $stmt_grado->execute();
    $grado = $stmt_grado->fetch(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Acta de Notas - <?php echo $grado['nombre_grado']; ?></title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #1a56db; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 8px; text-align: center; }
            th { background: #2c3e50; color: white; }
            .firmas { margin-top: 50px; display: flex; justify-content: space-around; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>COLEGIO MATEMÁTICO HONORES</h1>
            <h3>ACTA DE NOTAS - <?php echo $bimestre; ?>° BIMESTRE</h3>
            <p>Grado: <?php echo $grado['nombre_grado']; ?> - Año Lectivo 2026</p>
        </div>
        <table>
            <thead><tr><th>#</th><th>Apellidos y Nombres</th><th>DNI</th><th>Sección</th><th>Nota</th><th>Letra</th></tr></thead>
            <tbody>
            <?php foreach($alumnos as $index => $alumno): 
                $nota = $alumno['nota'] ?? 0;
                $letra = $nota >= 18 ? 'AD' : ($nota >= 14 ? 'A' : ($nota >= 11 ? 'B' : 'C'));
                $color = $nota >= 18 ? '#27ae60' : ($nota >= 14 ? '#f39c12' : ($nota >= 11 ? '#e67e22' : '#e74c3c'));
            ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td style="text-align:left"><?php echo $alumno['apellidos'] . ' ' . $alumno['nombres']; ?></td>
                    <td><?php echo $alumno['dni']; ?></td>
                    <td><?php echo $alumno['nombre_seccion']; ?></td>
                    <td style="color:<?php echo $color; ?>"><?php echo $nota ? number_format($nota, 1) : '-'; ?></td>
                    <td style="color:<?php echo $color; ?>"><?php echo $nota ? $letra : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="firmas">
            <div><div style="border-top:1px solid #000; width:200px; margin-top:40px"></div><p>DIRECTOR(A)</p></div>
            <div><div style="border-top:1px solid #000; width:200px; margin-top:40px"></div><p>COORDINADOR ACADÉMICO</p></div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
