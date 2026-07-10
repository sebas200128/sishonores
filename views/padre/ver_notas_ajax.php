<?php
require_once __DIR__ . '/../../core/Database.php';
$database = new Database();
$db = $database->getConnection();
$id_alumno = (int)($_POST['id_alumno'] ?? 0);
if($id_alumno){
    // Iniciar sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Validar sesión y rol de PadreFamilia
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
        echo "<div class='alert alert-danger'>Acceso denegado. No tiene permisos para realizar esta consulta.</div>";
        exit;
    }

    // Validar que el alumno pertenezca al padre logueado
    $query_validar = "SELECT COUNT(*) as total FROM alumnos a
                      JOIN alumno_padre ap ON a.id_alumno = ap.id_alumno
                      JOIN padres_familia pf ON ap.id_padre = pf.id_padre
                      WHERE pf.id_usuario = :user_id AND a.id_alumno = :id_alumno AND a.activo = 1";
    $stmt_validar = $db->prepare($query_validar);
    $stmt_validar->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt_validar->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_validar->execute();
    $result_val = $stmt_validar->fetch(PDO::FETCH_ASSOC);
    
    if (!$result_val || $result_val['total'] == 0) {
        echo "<div class='alert alert-danger'>Acceso denegado. No tiene permisos para ver las notas de este estudiante.</div>";
        exit;
    }
    $stmt_alumno = $db->prepare("SELECT apellidos, nombres FROM alumnos WHERE id_alumno=:id");
    $stmt_alumno->bindParam(':id',$id_alumno); $stmt_alumno->execute(); $alumno=$stmt_alumno->fetch(PDO::FETCH_ASSOC);
    $html = '<h4>'.$alumno['apellidos'].' '.$alumno['nombres'].'</h4><table class="table table-bordered"><thead class="table-dark"><th>Curso</th><th>Competencia</th><th>Bim1</th><th>Bim2</th><th>Bim3</th><th>Bim4</th><th>Promedio</th></thead><tbody>';
    $stmt_cursos = $db->prepare("SELECT DISTINCT c.id_curso, c.nombre_curso FROM notas n JOIN competencias comp ON n.id_competencia=comp.id_competencia JOIN cursos c ON comp.id_curso=c.id_curso WHERE n.id_alumno=:alumno");
    $stmt_cursos->bindParam(':alumno',$id_alumno); $stmt_cursos->execute(); $cursos=$stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    foreach($cursos as $curso){
        $stmt_comp = $db->prepare("SELECT id_competencia, nombre_competencia FROM competencias WHERE id_curso=:curso");
        $stmt_comp->bindParam(':curso',$curso['id_curso']); $stmt_comp->execute(); $competencias=$stmt_comp->fetchAll(PDO::FETCH_ASSOC);
        foreach($competencias as $comp){
            $suma=0; $count=0;
            $html .= '<tr><td>'.$curso['nombre_curso'].'</td><td>'.$comp['nombre_competencia'].'</td>';
            for($b=1;$b<=4;$b++){
                $stmt_nota = $db->prepare("SELECT nota FROM notas WHERE id_alumno=:alumno AND id_competencia=:comp AND bimestre=:bim");
                $stmt_nota->bindParam(':alumno',$id_alumno); $stmt_nota->bindParam(':comp',$comp['id_competencia']); $stmt_nota->bindParam(':bim',$b); $stmt_nota->execute(); $nota=$stmt_nota->fetch(PDO::FETCH_ASSOC);
                $val=$nota['nota']??0; if($val>0){ $suma+=$val; $count++; }
                $color=$val>=18?'#27ae60':($val>=14?'#f39c12':($val>=11?'#e67e22':'#e74c3c'));
                $html .= '<td style="color:'.$color.'">'.($val?number_format($val,1):'-').'</td>';
            }
            $promedio=$count>0?$suma/$count:0; $letra=$promedio>=18?'AD':($promedio>=14?'A':($promedio>=11?'B':'C')); $colorProm=$promedio>=18?'#27ae60':($promedio>=14?'#f39c12':($promedio>=11?'#e67e22':'#e74c3c'));
            $html .= '<td style="color:'.$colorProm.'; font-weight:bold">'.number_format($promedio,1).' ('.$letra.')</td></tr>';
        }
    }
    $html .= '</tbody></table>'; echo $html;
}
?>