<?php
require_once '../config/database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$id_alumno = $_GET['id_alumno'] ?? 0;
$bimestre = $_GET['bimestre'] ?? 0;

// Si es padre de familia, validar que sea su hijo
if ($_SESSION['user_role'] == 'PadreFamilia') {
    $query_validar = "SELECT COUNT(*) as total FROM alumnos a
                      JOIN alumno_padre ap ON a.id_alumno = ap.id_alumno
                      JOIN padres_familia pf ON ap.id_padre = pf.id_padre
                      WHERE pf.id_usuario = :user_id AND a.id_alumno = :id_alumno";
    $stmt_validar = $db->prepare($query_validar);
    $stmt_validar->bindParam(':user_id', $_SESSION['user_id']);
    $stmt_validar->bindParam(':id_alumno', $id_alumno);
    $stmt_validar->execute();
    $result = $stmt_validar->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] == 0) {
        echo "Acceso denegado. No tiene permiso para ver esta boleta.";
        exit;
    }
}

function getLetraNota($nota) {
    if ($nota >= 18) return 'AD';
    if ($nota >= 14) return 'A';
    if ($nota >= 11) return 'B';
    return 'C';
}

function getColorNota($nota) {
    if ($nota >= 18) return '#27ae60';
    if ($nota >= 14) return '#f39c12';
    if ($nota >= 11) return '#e67e22';
    return '#e74c3c';
}

if ($id_alumno > 0) {
    $query = "SELECT a.*, g.nombre_grado, s.nombre_seccion, n.nombre_nivel
              FROM alumnos a
              JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
              JOIN grados g ON aa.id_grado = g.id_grado
              JOIN secciones s ON aa.id_seccion = s.id_seccion
              JOIN niveles n ON g.id_nivel = n.id_nivel
              WHERE a.id_alumno = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_alumno);
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($alumno) {
        $query_notas = "SELECT c.nombre_curso, 
                               n1.nota as bim1, n2.nota as bim2, n3.nota as bim3, n4.nota as bim4
                        FROM cursos c
                        JOIN docente_curso_aula dca ON c.id_curso = dca.id_curso
                        JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
                        LEFT JOIN (SELECT id_competencia, nota FROM notas WHERE id_alumno = :id AND bimestre = 1 LIMIT 1) n1 ON n1.id_competencia = (SELECT MIN(id_competencia) FROM competencias WHERE id_curso = c.id_curso)
                        LEFT JOIN (SELECT id_competencia, nota FROM notas WHERE id_alumno = :id AND bimestre = 2 LIMIT 1) n2 ON n2.id_competencia = (SELECT MIN(id_competencia) FROM competencias WHERE id_curso = c.id_curso)
                        LEFT JOIN (SELECT id_competencia, nota FROM notas WHERE id_alumno = :id AND bimestre = 3 LIMIT 1) n3 ON n3.id_competencia = (SELECT MIN(id_competencia) FROM competencias WHERE id_curso = c.id_curso)
                        LEFT JOIN (SELECT id_competencia, nota FROM notas WHERE id_alumno = :id AND bimestre = 4 LIMIT 1) n4 ON n4.id_competencia = (SELECT MIN(id_competencia) FROM competencias WHERE id_curso = c.id_curso)
                        WHERE aa.id_aula = :aula
                        GROUP BY c.id_curso";
        $stmt_notas = $db->prepare($query_notas);
        $stmt_notas->bindParam(':id', $id_alumno);
        $stmt_notas->bindParam(':aula', $alumno['id_aula']);
        $stmt_notas->execute();
        $cursos = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Boleta de Notas - <?php echo $alumno['apellidos'] . ' ' . $alumno['nombres']; ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
                .header { text-align: center; border-bottom: 2px solid #1a56db; padding-bottom: 10px; margin-bottom: 20px; }
                .header h1 { color: #1a56db; margin: 0; font-size: 24px; }
                .info-alumno { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; background: #f5f7fa; }
                .escala { margin-bottom: 20px; font-size: 11px; border: 1px solid #ddd; padding: 8px; }
                .escala span { display: inline-block; margin-right: 15px; padding: 2px 8px; border-radius: 3px; }
                .escala-ad { background: #d4edda; color: #155724; }
                .escala-a { background: #fff3cd; color: #856404; }
                .escala-b { background: #ffe6cc; color: #e67e22; }
                .escala-c { background: #f8d7da; color: #721c24; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                th { background: #2c3e50; color: white; }
                .promedio { font-weight: bold; }
                .firmas { display: flex; justify-content: space-around; margin-top: 40px; }
                .firma { text-align: center; width: 200px; }
                .firma-linea { border-top: 1px solid #000; margin-top: 40px; margin-bottom: 5px; }
                .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; text-align: center; font-size: 11px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>COLEGIO MATEMÁTICO HONORES</h1>
                <p>Los Olivos - Lima</p>
                <h3>BOLETA DE NOTAS <?php echo $bimestre > 0 ? $bimestre . '° BIMESTRE' : 'ANUAL'; ?></h3>
                <p>Año Lectivo 2026</p>
            </div>
            
            <div class="info-alumno">
                <table style="border:none">
                    <tr><td style="border:none"><strong>Alumno:</strong> <?php echo strtoupper($alumno['apellidos'] . ' ' . $alumno['nombres']); ?></td>
                        <td style="border:none"><strong>Grado:</strong> <?php echo $alumno['nombre_grado']; ?></td></tr>
                    <tr><td style="border:none"><strong>Nivel:</strong> <?php echo $alumno['nombre_nivel']; ?></td>
                        <td style="border:none"><strong>Sección:</strong> <?php echo $alumno['nombre_seccion']; ?></td></tr>
                </table>
            </div>
            
            <div class="escala">
                <strong>ESCALA DE CALIFICACIÓN:</strong><br>
                <span class="escala-ad">AD (18-20) Logro Destacado</span>
                <span class="escala-a">A (14-17) Logro Esperado</span>
                <span class="escala-b">B (11-13) En Proceso</span>
                <span class="escala-c">C (10 a menos) En Inicio</span>
            </div>
            
            <table>
                <thead><tr><th>ÁREAS / CURSOS</th><th>BIM1</th><th>BIM2</th><th>BIM3</th><th>BIM4</th><th>PROM. FINAL</th><th>LETRA</th></tr></thead>
                <tbody>
                <?php 
                $total_promedios = 0;
                $contador = 0;
                foreach($cursos as $curso):
                    $promedio = ($curso['bim1'] + $curso['bim2'] + $curso['bim3'] + $curso['bim4']) / 4;
                    $letra = getLetraNota($promedio);
                    $color = getColorNota($promedio);
                    $total_promedios += $promedio;
                    $contador++;
                ?>
                    <tr>
                        <td style="text-align:left"><strong><?php echo $curso['nombre_curso']; ?></strong></td>
                        <td style="color:<?php echo getColorNota($curso['bim1']); ?>"><?php echo $curso['bim1'] ?: '-'; ?></td>
                        <td style="color:<?php echo getColorNota($curso['bim2']); ?>"><?php echo $curso['bim2'] ?: '-'; ?></td>
                        <td style="color:<?php echo getColorNota($curso['bim3']); ?>"><?php echo $curso['bim3'] ?: '-'; ?></td>
                        <td style="color:<?php echo getColorNota($curso['bim4']); ?>"><?php echo $curso['bim4'] ?: '-'; ?></td>
                        <td class="promedio" style="color:<?php echo $color; ?>"><?php echo number_format($promedio, 1); ?></td>
                        <td style="color:<?php echo $color; ?>"><?php echo $letra; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php $promedio_global = $contador > 0 ? $total_promedios / $contador : 0; ?>
                <tr style="background:#f5f7fa">
                    <td><strong>PROMEDIO GLOBAL</strong></td>
                    <td colspan="5" class="promedio" style="color:<?php echo getColorNota($promedio_global); ?>"><?php echo number_format($promedio_global, 2); ?></td>
                    <td style="color:<?php echo getColorNota($promedio_global); ?>"><?php echo getLetraNota($promedio_global); ?></td>
                </tr>
                </tbody>
            </table>
            
            <div class="firmas">
                <div class="firma"><div class="firma-linea"></div><p>DIRECTOR(A)</p></div>
                <div class="firma"><div class="firma-linea"></div><p>COORDINADOR ACADÉMICO</p></div>
                <div class="firma"><div class="firma-linea"></div><p>TUTOR(A)</p></div>
            </div>
            
            <div class="footer">
                <p>Los Olivos, <?php echo date('d/m/Y'); ?></p>
                <p>SisHonores 1.0 - Sistema de Gestión Académica</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
