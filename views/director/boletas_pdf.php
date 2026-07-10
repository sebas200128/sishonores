<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=login');
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
                               comp.id_competencia,
                               comp.nombre_competencia,
                               n.id_alumno,
                               n.bimestre,
                               n.nota
                        FROM cursos c
                        JOIN docente_curso_aula dca ON c.id_curso = dca.id_curso
                        JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
                        JOIN competencias comp ON comp.id_curso = c.id_curso
                        LEFT JOIN notas n ON comp.id_competencia = n.id_competencia 
                                          AND n.id_alumno = :id
                                          AND n.id_docente_curso_aula = dca.id_docente_curso_aula
                        WHERE aa.id_aula = :aula
                        ORDER BY c.id_curso, comp.id_competencia, n.bimestre";
        $stmt_notas = $db->prepare($query_notas);
        $stmt_notas->bindParam(':id', $id_alumno);
        $stmt_notas->bindParam(':aula', $alumno['id_aula']);
        $stmt_notas->execute();
        $notas_raw = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);
        
        // Organizar datos por curso y competencia
        $cursos = [];
        $competencias_por_curso = [];
        
        foreach ($notas_raw as $fila) {
            $id_curso = $fila['nombre_curso'];
            if (!isset($cursos[$id_curso])) {
                $cursos[$id_curso] = [];
                $competencias_por_curso[$id_curso] = [];
            }
            
            $id_comp = $fila['id_competencia'];
            if (!isset($cursos[$id_curso][$id_comp])) {
                $cursos[$id_curso][$id_comp] = [
                    'nombre_competencia' => $fila['nombre_competencia'],
                    'bim1' => null,
                    'bim2' => null,
                    'bim3' => null,
                    'bim4' => null
                ];
                $competencias_por_curso[$id_curso][] = $id_comp;
            }
            
            if ($fila['nota']) {
                $bimestre = 'bim' . $fila['bimestre'];
                if (!$cursos[$id_curso][$id_comp][$bimestre]) {
                    $cursos[$id_curso][$id_comp][$bimestre] = $fila['nota'];
                }
            }
        }
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
            <div style="text-align: center; margin-bottom: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #1a56db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;"><i class="fas fa-print"></i> Imprimir Boleta</button>
            </div>
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
                <thead><tr><th>ÁREAS / CURSOS</th><th>COMPETENCIAS</th><th>BIM1</th><th>BIM2</th><th>BIM3</th><th>BIM4</th><th>NOTA FINAL</th></tr></thead>
                <tbody>
                <?php 
                $total_promedios_general = 0;
                $contador_cursos = 0;
                foreach($cursos as $nombre_curso => $competencias_data):
                    $suma_competencias = 0;
                    $contador_competencias_curso = 0;
                    $es_primera_competencia = true;
                    
                    foreach($competencias_data as $id_comp => $competencia):
                        $bim1 = floatval($competencia['bim1'] ?? 0);
                        $bim2 = floatval($competencia['bim2'] ?? 0);
                        $bim3 = floatval($competencia['bim3'] ?? 0);
                        $bim4 = floatval($competencia['bim4'] ?? 0);
                        
                        // Calcular promedio de la competencia
                        $notas_ingresadas = array_filter([$bim1, $bim2, $bim3, $bim4], function($n) { return $n > 0; });
                        $promedio_competencia = count($notas_ingresadas) > 0 ? array_sum($notas_ingresadas) / count($notas_ingresadas) : 0;
                        
                        $letra = getLetraNota($promedio_competencia);
                        $color = getColorNota($promedio_competencia);
                        
                        $suma_competencias += $promedio_competencia;
                        $contador_competencias_curso++;
                ?>
                    <tr>
                        <td style="text-align:left;">
                            <?php if($es_primera_competencia): ?>
                                <strong><?php echo $nombre_curso; ?></strong>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:left;"><?php echo $competencia['nombre_competencia']; ?></td>
                        <td style="color:<?php echo getColorNota($bim1); ?>"><?php echo $bim1 > 0 ? number_format($bim1, 1) : '-'; ?></td>
                        <td style="color:<?php echo getColorNota($bim2); ?>"><?php echo $bim2 > 0 ? number_format($bim2, 1) : '-'; ?></td>
                        <td style="color:<?php echo getColorNota($bim3); ?>"><?php echo $bim3 > 0 ? number_format($bim3, 1) : '-'; ?></td>
                        <td style="color:<?php echo getColorNota($bim4); ?>"><?php echo $bim4 > 0 ? number_format($bim4, 1) : '-'; ?></td>
                        <td class="promedio" style="color:<?php echo $color; ?>">
                            <?php echo $promedio_competencia > 0 ? number_format($promedio_competencia, 1) . ' (' . $letra . ')' : '-'; ?>
                        </td>
                    </tr>
                    <?php $es_primera_competencia = false; ?>
                <?php endforeach; ?>
                    <!-- Fila de promedio del curso -->
                    <?php 
                    $promedio_curso = $contador_competencias_curso > 0 ? $suma_competencias / $contador_competencias_curso : 0;
                    $letra_curso = getLetraNota($promedio_curso);
                    $color_curso = getColorNota($promedio_curso);
                    $total_promedios_general += $promedio_curso;
                    $contador_cursos++;
                    ?>
                    <tr style="background:#e8f4f8; font-weight:bold;">
                        <td colspan="6" style="text-align:right;">Promedio de <?php echo $nombre_curso; ?>:</td>
                        <td style="color:<?php echo $color_curso; ?>"><?php echo $promedio_curso > 0 ? number_format($promedio_curso, 1) . ' (' . $letra_curso . ')' : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Fila de promedio global -->
                <?php $promedio_global = $contador_cursos > 0 ? $total_promedios_general / $contador_cursos : 0; ?>
                <tr style="background:#f5f7fa; font-weight:bold; font-size: 13px;">
                    <td colspan="6" style="text-align:right;">PROMEDIO GENERAL:</td>
                    <td style="color:<?php echo getColorNota($promedio_global); ?>"><?php echo $promedio_global > 0 ? number_format($promedio_global, 2) . ' (' . getLetraNota($promedio_global) . ')' : '-'; ?></td>
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
