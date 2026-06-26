<?php
require_once '../../util/Database.php';
$database = new Database();
$db = $database->getConnection();
$dni = $_POST['dni'] ?? '';
if($dni){
    $stmt = $db->prepare("SELECT a.*, g.nombre_grado, s.nombre_seccion, (SELECT AVG(nota) FROM notas WHERE id_alumno=a.id_alumno) as promedio FROM alumnos a JOIN aulas_asignadas aa ON a.id_aula=aa.id_aula JOIN grados g ON aa.id_grado=g.id_grado JOIN secciones s ON aa.id_seccion=s.id_seccion WHERE a.dni LIKE :dni AND a.activo=1");
    $search="%$dni%"; $stmt->bindParam(':dni',$search); $stmt->execute(); $alumnos=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($alumnos)>0){
        echo '<table class="table table-bordered"><thead class="table-dark"><tr><th>Alumno</th><th>DNI</th><th>Grado</th><th>Sección</th><th>Promedio</th><th>Acciones</th></tr></thead><tbody>';
        foreach($alumnos as $a){
            $p=$a['promedio']??0; $letra=$p>=18?'AD':($p>=14?'A':($p>=11?'B':'C')); $color=$p>=18?'success':($p>=14?'warning':($p>=11?'info':'danger'));
            echo '<tr><td>'.$a['apellidos'].' '.$a['nombres'].'</td><td>'.$a['dni'].'</td><td>'.$a['nombre_grado'].'</td><td>'.$a['nombre_seccion'].'</td><td>'.number_format($p,1).' <span class="badge bg-'.$color.'">'.$letra.'</span></td>';
            echo '<td><a href="ver_notas_detalle.php?id='.$a['id_alumno'].'" class="btn btn-sm btn-primary">Ver Notas</a> <a href="../director/boletas_pdf.php?id_alumno='.$a['id_alumno'].'&bimestre=0" target="_blank" class="btn btn-sm btn-danger">Boleta</a></td></tr>';
        }
        echo '</tbody></table>';
    } else echo '<div class="alert alert-warning">No se encontraron alumnos</div>';
}
?>?>