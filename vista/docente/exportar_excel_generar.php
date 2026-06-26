<?php
require_once '../../util/Database.php';
$database = new Database();
$db = $database->getConnection();
$id_curso = $_GET['id_curso'] ?? 0;
$bimestre = $_GET['bimestre'] ?? 1;
if($id_curso>0){
    $stmt_curso = $db->prepare("SELECT c.nombre_curso, g.nombre_grado, s.nombre_seccion FROM docente_curso_aula dca JOIN cursos c ON dca.id_curso=c.id_curso JOIN aulas_asignadas aa ON dca.id_aula=aa.id_aula JOIN grados g ON aa.id_grado=g.id_grado JOIN secciones s ON aa.id_seccion=s.id_seccion WHERE dca.id_docente_curso_aula=:curso");
    $stmt_curso->bindParam(':curso',$id_curso); $stmt_curso->execute(); $curso=$stmt_curso->fetch(PDO::FETCH_ASSOC);
    $stmt_alumnos = $db->prepare("SELECT a.id_alumno, a.apellidos, a.nombres, a.dni FROM alumnos a JOIN docente_curso_aula dca ON dca.id_aula=a.id_aula WHERE dca.id_docente_curso_aula=:curso AND a.activo=1 ORDER BY a.apellidos");
    $stmt_alumnos->bindParam(':curso',$id_curso); $stmt_alumnos->execute(); $alumnos=$stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
    $stmt_comp = $db->prepare("SELECT id_competencia, nombre_competencia FROM competencias WHERE id_curso=(SELECT id_curso FROM docente_curso_aula WHERE id_docente_curso_aula=:curso)");
    $stmt_comp->bindParam(':curso',$id_curso); $stmt_comp->execute(); $competencias=$stmt_comp->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/vnd.ms-excel'); header('Content-Disposition: attachment; filename="notas_'.$curso['nombre_curso'].'_'.$bimestre.'Bim.xls"');
    echo '<table border="1"><thead><tr><th>#</th><th>Apellidos</th><th>Nombres</th><th>DNI</th>';
    foreach($competencias as $c) echo '<th>'.$c['nombre_competencia'].'</th>';
    echo '<th>Promedio</th><th>Letra</th></tr></thead><tbody>';
    $idx=1;
    foreach($alumnos as $a){
        $suma=0; $count=0; $promedio=0;
        echo '<tr><td>'.$idx.'</td><td>'.$a['apellidos'].'</td><td>'.$a['nombres'].'</td><td>'.$a['dni'].'</td>';
        foreach($competencias as $c){
            $stmt_nota = $db->prepare("SELECT nota FROM notas WHERE id_alumno=:alumno AND id_competencia=:comp AND id_docente_curso_aula=:curso AND bimestre=:bim");
            $stmt_nota->bindParam(':alumno',$a['id_alumno']); $stmt_nota->bindParam(':comp',$c['id_competencia']); $stmt_nota->bindParam(':curso',$id_curso); $stmt_nota->bindParam(':bim',$bimestre); $stmt_nota->execute(); $nota=$stmt_nota->fetch(PDO::FETCH_ASSOC);
            $val=$nota['nota']??''; if($val>0){ $suma+=$val; $count++; } echo '<td>'.$val.'</td>';
        }
        $promedio=$count>0?$suma/$count:0; $letra=$promedio>=18?'AD':($promedio>=14?'A':($promedio>=11?'B':'C'));
        echo '<td>'.number_format($promedio,1).'</td><td>'.$letra.'</td></tr>';
        $idx++;
    }
    echo '</tbody></table>';
}
?>
