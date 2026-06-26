<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
include '../includes/sidebar.php';

$database = new Database();
$db = $database->getConnection();

// Obtener los hijos del padre actual
$query = "SELECT a.* 
          FROM alumnos a
          JOIN alumno_padre ap ON a.id_alumno = ap.id_alumno
          JOIN padres_familia pf ON ap.id_padre = pf.id_padre
          WHERE pf.id_usuario = :user_id AND a.activo = 1
          ORDER BY a.apellidos, a.nombres";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$hijos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="fas fa-child"></i> Mis Hijos</h1>
            
            <?php if (empty($hijos)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay hijos registrados en el sistema.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($hijos as $hijo): 
                        // Obtener información del aula
                        $query_aula = "SELECT aa.*, g.nombre_grado, s.nombre_seccion, n.nombre_nivel
                                      FROM aulas_asignadas aa
                                      JOIN grados g ON aa.id_grado = g.id_grado
                                      JOIN secciones s ON aa.id_seccion = s.id_seccion
                                      JOIN niveles n ON g.id_nivel = n.id_nivel
                                      WHERE aa.id_aula = :aula";
                        $stmt_aula = $db->prepare($query_aula);
                        $stmt_aula->bindParam(':aula', $hijo['id_aula']);
                        $stmt_aula->execute();
                        $aula = $stmt_aula->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user-graduate text-primary"></i>
                                    <?php echo htmlspecialchars($hijo['apellidos'] . ' ' . $hijo['nombres']); ?>
                                </h5>
                                <p class="card-text">
                                    <small><strong>DNI:</strong> <?php echo htmlspecialchars($hijo['dni']); ?></small><br>
                                    <small><strong>Código:</strong> <?php echo htmlspecialchars($hijo['codigo_estudiante']); ?></small><br>
                                    <small><strong>Grado:</strong> <?php echo htmlspecialchars($aula['nombre_grado'] . ' - ' . $aula['nombre_seccion']); ?></small><br>
                                    <small><strong>Nivel:</strong> <?php echo htmlspecialchars($aula['nombre_nivel']); ?></small>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top">
                                <a href="../../director/boletas_pdf.php?id_alumno=<?php echo $hijo['id_alumno']; ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-file-pdf"></i> Ver Boleta de Notas
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
