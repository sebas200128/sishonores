<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT dca.id_docente_curso_aula, c.nombre_curso, g.nombre_grado, s.nombre_seccion 
          FROM docente_curso_aula dca
          JOIN cursos c ON dca.id_curso = c.id_curso
          JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
          JOIN grados g ON aa.id_grado = g.id_grado
          JOIN secciones s ON aa.id_seccion = s.id_seccion
          WHERE dca.id_usuario = :user_id";
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
    <title>Exportar Excel - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-file-excel"></i> Exportar Notas a Excel</h1>
        <div class="card">
            <div class="card-header bg-dark text-white"><i class="fas fa-download"></i> Seleccionar Curso</div>
            <div class="card-body">
                <form method="GET" action="exportar_excel_generar.php" class="row g-3">
                                <div class="col-md-6"><label>Curso</label><select name="id_curso" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id_docente_curso_aula']; ?>"><?php echo $curso['nombre_curso'] . ' - ' . $curso['nombre_grado'] . ' "' . $curso['nombre_seccion'] . '"'; ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                                <div class="col-md-3"><label>Bimestre</label><select name="bimestre" class="form-select" required>
                                    <option value="1">1° Bimestre</option><option value="2">2° Bimestre</option>
                                    <option value="3">3° Bimestre</option><option value="4">4° Bimestre</option>
                                </select></div>
                                <div class="col-md-3"><label>&nbsp;</label><button type="submit" class="btn btn-success d-block"><i class="fas fa-download"></i> Exportar Excel</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
