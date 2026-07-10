<?php
require_once __DIR__ . '/../../core/Database.php';
$database = new Database();
$db = $database->getConnection();

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

$id_alumno = $_GET['id'] ?? 0;
if($id_alumno){
    $stmt = $db->prepare("SELECT a.*, g.nombre_grado, s.nombre_seccion FROM alumnos a JOIN aulas_asignadas aa ON a.id_aula=aa.id_aula JOIN grados g ON aa.id_grado=g.id_grado JOIN secciones s ON aa.id_seccion=s.id_seccion WHERE a.id_alumno=:id");
    $stmt->bindParam(':id',$id_alumno); 
    $stmt->execute(); 
    $alumno=$stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener todas las notas del alumno
    $stmt_notas = $db->prepare("SELECT c.id_curso, c.nombre_curso, comp.id_competencia, comp.nombre_competencia, n.bimestre, n.nota, n.observacion
                                FROM notas n
                                JOIN competencias comp ON n.id_competencia=comp.id_competencia
                                JOIN cursos c ON comp.id_curso=c.id_curso
                                WHERE n.id_alumno=:alumno
                                ORDER BY c.id_curso, comp.id_competencia, n.bimestre");
    $stmt_notas->bindParam(':alumno',$id_alumno); 
    $stmt_notas->execute(); 
    $notas_raw=$stmt_notas->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar datos por curso y competencia
    $datos_cursos = [];
    foreach ($notas_raw as $fila) {
        $curso_id = $fila['id_curso'];
        $curso_nombre = $fila['nombre_curso'];
        $comp_id = $fila['id_competencia'];
        $comp_nombre = $fila['nombre_competencia'];
        
        if (!isset($datos_cursos[$curso_id])) {
            $datos_cursos[$curso_id] = [
                'nombre' => $curso_nombre,
                'competencias' => []
            ];
        }
        
        if (!isset($datos_cursos[$curso_id]['competencias'][$comp_id])) {
            $datos_cursos[$curso_id]['competencias'][$comp_id] = [
                'nombre' => $comp_nombre,
                'bim1' => null,
                'bim2' => null,
                'bim3' => null,
                'bim4' => null,
                'observacion' => ''
            ];
        }
        
        $bimestre = 'bim' . $fila['bimestre'];
        $datos_cursos[$curso_id]['competencias'][$comp_id][$bimestre] = $fila['nota'];
        
        // Guardar la observación (última observación de la competencia)
        if (!empty($fila['observacion'])) {
            $datos_cursos[$curso_id]['competencias'][$comp_id]['observacion'] = $fila['observacion'];
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Detalle de Notas</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .fila-curso { background-color: #e8f4f8; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container mt-4">
            <h2>Notas de <?php echo $alumno['apellidos'].' '.$alumno['nombres']; ?></h2>
            <p>Grado: <?php echo $alumno['nombre_grado'].' - Sección '.$alumno['nombre_seccion']; ?></p>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Curso</th><th>Competencia</th><th>Bim1</th><th>Bim2</th><th>Bim3</th><th>Bim4</th><th>Promedio</th><th>Observaciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($datos_cursos as $curso_id => $curso_data): ?>
                        <?php 
                        $suma_competencias = 0;
                        $contador_competencias = 0;
                        $es_primera_competencia = true;
                        $competencias = $curso_data['competencias'];
                        ?>
                        <?php foreach($competencias as $comp_id => $competencia): ?>
                            <?php 
                            $bim1 = floatval($competencia['bim1'] ?? 0);
                            $bim2 = floatval($competencia['bim2'] ?? 0);
                            $bim3 = floatval($competencia['bim3'] ?? 0);
                            $bim4 = floatval($competencia['bim4'] ?? 0);
                            
                            $notas_ingresadas = array_filter([$bim1, $bim2, $bim3, $bim4], function($n) { return $n > 0; });
                            $promedio_comp = count($notas_ingresadas) > 0 ? array_sum($notas_ingresadas) / count($notas_ingresadas) : 0;
                            
                            $letra = getLetraNota($promedio_comp);
                            $color = getColorNota($promedio_comp);
                            
                            $suma_competencias += $promedio_comp;
                            $contador_competencias++;
                            ?>
                            <tr>
                                <td style="text-align:left;">
                                    <?php if($es_primera_competencia): ?>
                                        <strong><?php echo $curso_data['nombre']; ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:left;"><?php echo $competencia['nombre']; ?></td>
                                <td style="color:<?php echo getColorNota($bim1); ?>"><?php echo $bim1 > 0 ? number_format($bim1, 1) : '-'; ?></td>
                                <td style="color:<?php echo getColorNota($bim2); ?>"><?php echo $bim2 > 0 ? number_format($bim2, 1) : '-'; ?></td>
                                <td style="color:<?php echo getColorNota($bim3); ?>"><?php echo $bim3 > 0 ? number_format($bim3, 1) : '-'; ?></td>
                                <td style="color:<?php echo getColorNota($bim4); ?>"><?php echo $bim4 > 0 ? number_format($bim4, 1) : '-'; ?></td>
                                <td style="color:<?php echo $color; ?>; font-weight: bold;">
                                    <?php echo $promedio_comp > 0 ? number_format($promedio_comp, 1) . ' (' . $letra . ')' : '-'; ?>
                                </td>
                                <td><?php echo $competencia['observacion']; ?></td>
                            </tr>
                            <?php $es_primera_competencia = false; ?>
                        <?php endforeach; ?>
                        <!-- Fila de promedio del curso -->
                        <?php 
                        $promedio_curso = $contador_competencias > 0 ? $suma_competencias / $contador_competencias : 0;
                        $letra_curso = getLetraNota($promedio_curso);
                        $color_curso = getColorNota($promedio_curso);
                        ?>
                        <tr class="fila-curso">
                            <td colspan="5" style="text-align:right;">Promedio de <?php echo $curso_data['nombre']; ?>:</td>
                            <td style="color:<?php echo $color_curso; ?>"><?php echo $promedio_curso > 0 ? number_format($promedio_curso, 1) . ' (' . $letra_curso . ')' : '-'; ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="index.php?controller=secretaria&action=ver_notas" class="btn btn-secondary">Volver</a>
        </div>
    </body>
    </html>
    <?php
}
?>
