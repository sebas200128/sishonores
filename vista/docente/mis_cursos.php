<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT dca.*, c.nombre_curso, g.nombre_grado, s.nombre_seccion 
          FROM docente_curso_aula dca
          JOIN cursos c ON dca.id_curso = c.id_curso
          JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
          JOIN grados g ON aa.id_grado = g.id_grado
          JOIN secciones s ON aa.id_seccion = s.id_seccion
          WHERE dca.id_usuario = :user_id AND dca.anio = YEAR(NOW())";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-book-open"></i> Mis Cursos Asignados</h1>
        <div class="row mt-4">
                        <?php foreach($cursos as $curso): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header bg-dark text-white"><?php echo $curso['nombre_curso']; ?></div>
                                <div class="card-body">
                                    <p><strong>Grado:</strong> <?php echo $curso['nombre_grado']; ?></p>
                                    <p><strong>Sección:</strong> <?php echo $curso['nombre_seccion']; ?></p>
                                    <a href="ingresar_notas.php?curso=<?php echo $curso['id_docente_curso_aula']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Ingresar Notas</a>
                                    <a href="competencias.php?curso=<?php echo $curso['id_docente_curso_aula']; ?>" class="btn btn-info"><i class="fas fa-tasks"></i> Competencias</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
