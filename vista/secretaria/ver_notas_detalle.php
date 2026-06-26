<?php
require_once '../../util/Database.php';
$database = new Database();
$db = $database->getConnection();
$id_alumno = $_GET['id'] ?? 0;
if($id_alumno){
    $stmt = $db->prepare("SELECT a.*, g.nombre_grado, s.nombre_seccion FROM alumnos a JOIN aulas_asignadas aa ON a.id_aula=aa.id_aula JOIN grados g ON aa.id_grado=g.id_grado JOIN secciones s ON aa.id_seccion=s.id_seccion WHERE a.id_alumno=:id");
    $stmt->bindParam(':id',$id_alumno); $stmt->execute(); $alumno=$stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Detalle de Notas</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
    <body><div class="container mt-4"><h2>Notas de <?php echo $alumno['apellidos'].' '.$alumno['nombres']; ?></h2><p>Grado: <?php echo $alumno['nombre_grado'].' - Sección '.$alumno['nombre_seccion']; ?></p>
    <table class="table table-bordered"><thead class="table-dark"><th>Curso</th><th>Competencia</th><th>Bim1</th><th>Bim2</th><th>Bim3</th><th>Bim4</th><th>Observaciones</th></thead><tbody>
    <?php
    $stmt_cursos = $db->prepare("SELECT DISTINCT c.id_curso, c.nombre_curso FROM notas n JOIN competencias comp ON n.id_competencia=comp.id_competencia JOIN cursos c ON comp.id_curso=c.id_curso WHERE n.id_alumno=:alumno");
    $stmt_cursos->bindParam(':alumno',$id_alumno); $stmt_cursos->execute(); $cursos=$stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    foreach($cursos as $curso){
        $stmt_comp = $db->prepare("SELECT id_competencia, nombre_competencia FROM competencias WHERE id_curso=:curso");
        $stmt_comp->bindParam(':curso',$curso['id_curso']); $stmt_comp->execute(); $competencias=$stmt_comp->fetchAll(PDO::FETCH_ASSOC);
        foreach($competencias as $comp){
            echo '<tr>;<td>'.$curso['nombre_curso'].'</td><td>'.$comp['nombre_competencia'].'</td>';
            for($b=1;$b<=4;$b++){
                $stmt_nota = $db->prepare("SELECT nota, observacion FROM notas WHERE id_alumno=:alumno AND id_competencia=:comp AND bimestre=:bim");
                $stmt_nota->bindParam(':alumno',$id_alumno); $stmt_nota->bindParam(':comp',$comp['id_competencia']); $stmt_nota->bindParam(':bim',$b); $stmt_nota->execute(); $nota=$stmt_nota->fetch(PDO::FETCH_ASSOC);
                echo '<td>'.($nota['nota']??'-').'</td>';
            }
            echo '<td>'.($nota['observacion']??'').'</td></tr>';
        }
    }
    ?>
    </tbody></table><a href="ver_notas.php" class="btn btn-secondary">Volver</a></div></body></html>
    <?php
}
?>
