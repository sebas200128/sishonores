<?php
require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'PadreFamilia') {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT a.id_alumno, a.apellidos, a.nombres 
          FROM alumnos a
          JOIN alumno_padre ap ON a.id_alumno = ap.id_alumno
          JOIN padres_familia pf ON ap.id_padre = pf.id_padre
          JOIN usuarios u ON pf.id_usuario = u.id_usuario
          WHERE u.id_usuario = :user_id AND a.activo = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$hijos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-comments"></i> Comentarios y Observaciones</h1>
        <div class="row">
            <?php foreach($hijos as $hijo): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-dark text-white"><?php echo $hijo['apellidos'] . ' ' . $hijo['nombres']; ?></div>
                    <div class="card-body">
                        <div id="comentarios-<?php echo $hijo['id_alumno']; ?>">Cargando...</div>
                    </div>
                </div>
            </div>
            <script> cargarComentarios(<?php echo $hijo['id_alumno']; ?>); </script>
            <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarComentarios(id) { $.ajax({url:'views/padre/comentarios_ajax.php',method:'POST',data:{id_alumno:id},success:function(data){$('#comentarios-'+id).html(data);}}); }
    </script>
</body>
</html>
