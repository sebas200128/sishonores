<?php
require_once '../../util/Database.php';
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
        echo "<div class='alert alert-danger'>Acceso denegado. No tiene permisos para ver los comentarios de este estudiante.</div>";
        exit;
    }
    $stmt = $db->prepare("SELECT c.nombre_curso, comp.nombre_competencia, n.observacion, n.bimestre 
                          FROM notas n
                          JOIN competencias comp ON n.id_competencia = comp.id_competencia
                          JOIN cursos c ON comp.id_curso = c.id_curso
                          WHERE n.id_alumno = :alumno AND n.observacion IS NOT NULL AND n.observacion != ''
                          ORDER BY n.bimestre, c.nombre_curso");
    $stmt->bindParam(':alumno',$id_alumno); $stmt->execute(); $comentarios=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($comentarios)>0){
        $html = '<div class="list-group">';
        foreach($comentarios as $c) $html .= '<div class="list-group-item"><strong>'.$c['nombre_curso'].'</strong> - '.$c['nombre_competencia'].'<br><span class="badge bg-secondary">'.$c['bimestre'].'° Bimestre</span><p class="mt-2">'.$c['observacion'].'</p></div>';
        $html .= '</div>';
        echo $html;
    } else echo '<div class="alert alert-info">No hay comentarios registrados para este alumno</div>';
}
?>