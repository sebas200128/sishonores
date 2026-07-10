require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: index.php?controller=login');
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
    <title>Comentarios - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-comments"></i> Comentarios y Observaciones</h1>
        <div class="card">
                        <div class="card-header bg-dark text-white"><i class="fas fa-filter"></i> Seleccionar Curso</div>
                        <div class="card-body">
                            <select class="form-select" id="selectCurso" onchange="cargarAlumnos()">
                                <option value="">Seleccionar curso...</option>
                                <?php foreach($cursos as $curso): ?>
                                    <option value="<?php echo $curso['id_docente_curso_aula']; ?>"><?php echo $curso['nombre_curso'] . ' - ' . $curso['nombre_grado'] . ' "' . $curso['nombre_seccion'] . '"'; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="alumnosList" class="mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarAlumnos() {
            let id_curso = $('#selectCurso').val();
            if(id_curso) {
                $.ajax({url: 'index.php?controller=nota', method: 'POST', data: {action:'comentarios_alumnos', id_curso:id_curso}, success: function(data){ $('#alumnosList').html(data); }});
            }
        }
        function guardarComentario(id_alumno) {
            let comentario = $('#comentario_'+id_alumno).val();
            let id_curso = $('#selectCurso').val();
            $.ajax({url: 'index.php?controller=nota', method: 'POST', dataType: 'json', data: {action:'guardar_comentario', id_alumno:id_alumno, id_curso:id_curso, comentario:comentario}, success: function(r){ if(r.success) alert('Comentario guardado'); else alert(r.message || 'No se pudo guardar'); } });
        }
    </script>
</body>
</html>
