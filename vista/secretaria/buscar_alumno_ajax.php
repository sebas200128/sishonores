<?php
// vista/secretaria/buscar_alumno_ajax.php

require_once '../../util/Database.php';

$database = new Database();
$db = $database->getConnection();

$id_aula = isset($_POST['id_aula']) ? (int)$_POST['id_aula'] : 0;
$dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';

if ($id_aula > 0 || $dni !== '') {
    $sql = "SELECT a.*, g.nombre_grado, s.nombre_seccion, 
                   (SELECT AVG(nota) FROM notas WHERE id_alumno = a.id_alumno) as promedio 
            FROM alumnos a 
            JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula 
            JOIN grados g ON aa.id_grado = g.id_grado 
            JOIN secciones s ON aa.id_seccion = s.id_seccion 
            WHERE a.activo = 1";
    
    $params = [];
    if ($id_aula > 0) {
        $sql .= " AND a.id_aula = :id_aula";
        $params[':id_aula'] = $id_aula;
    }
    if ($dni !== '') {
        $sql .= " AND a.dni LIKE :dni";
        $params[':dni'] = "%$dni%";
    }
    
    $sql .= " ORDER BY a.apellidos, a.nombres";
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($alumnos) > 0) {
        echo '<div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                  <thead class="table-dark">
                      <tr>
                          <th class="text-center" style="width: 5%;">#</th>
                          <th>Alumno</th>
                          <th style="width: 15%;">DNI</th>
                          <th style="width: 25%;">Grado y Sección</th>
                          <th class="text-center" style="width: 15%;">Promedio</th>
                          <th class="text-center" style="width: 20%;">Acciones</th>
                      </tr>
                  </thead>
                  <tbody>';
        foreach ($alumnos as $idx => $a) {
            $p = $a['promedio'] ?? 0;
            $letra = $p >= 18 ? 'AD' : ($p >= 14 ? 'A' : ($p >= 11 ? 'B' : 'C'));
            $color = $p >= 18 ? 'success' : ($p >= 14 ? 'warning' : ($p >= 11 ? 'info' : 'danger'));
            $promedio_display = $p > 0 ? number_format($p, 1) . ' <span class="badge bg-' . $color . '">' . $letra . '</span>' : '<span class="text-muted">-</span>';
            
            echo '<tr>
                    <td class="text-center">' . ($idx + 1) . '</td>
                    <td><strong>' . htmlspecialchars($a['apellidos'] . ', ' . $a['nombres']) . '</strong></td>
                    <td>' . htmlspecialchars($a['dni']) . '</td>
                    <td>' . htmlspecialchars($a['nombre_grado'] . ' "' . $a['nombre_seccion'] . '"') . '</td>
                    <td class="text-center fw-bold">' . $promedio_display . '</td>
                    <td class="text-center">
                        <a href="ver_notas_detalle.php?id=' . $a['id_alumno'] . '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Notas</a> 
                        <a href="../../director/boletas_pdf.php?id_alumno=' . $a['id_alumno'] . '&bimestre=0" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Boleta</a>
                    </td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-circle"></i> No se encontraron alumnos con los criterios de búsqueda especificados.</div>';
    }
} else {
    echo '<div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Seleccione un grado o ingrese un DNI para buscar alumnos.</div>';
}
?>