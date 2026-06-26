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
    <title>Gestionar Competencias - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-tasks"></i> Gestionar Competencias</h1>
        <div class="row">
            <?php foreach($cursos as $curso): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header bg-dark text-white"><?php echo $curso['nombre_curso'] . ' - ' . $curso['nombre_grado'] . ' "' . $curso['nombre_seccion'] . '"'; ?></div>
                                <div class="card-body">
                                    <div id="comp-<?php echo $curso['id_docente_curso_aula']; ?>"></div>
                                    <div class="mt-3"><input type="text" class="form-control" id="newcomp-<?php echo $curso['id_docente_curso_aula']; ?>" placeholder="Nueva competencia"><button class="btn btn-primary mt-2" onclick="agregarCompetencia(<?php echo $curso['id_docente_curso_aula']; ?>)">Agregar</button></div>
                                </div>
                            </div>
                        </div>
                        <script> cargarCompetencias(<?php echo $curso['id_docente_curso_aula']; ?>); </script>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarCompetencias(id) { $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'list_competencias',id_curso:id},success:function(data){$('#comp-'+id).html(data);}}); }
        function agregarCompetencia(id) { let nombre=$('#newcomp-'+id).val(); if(nombre.trim()=='') return; $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'add_competencia',id_curso:id,nombre:nombre},success:function(r){if(r.success){$('#newcomp-'+id).val('');cargarCompetencias(id);}}}); }
        function eliminarCompetencia(idComp,idCurso) { if(confirm('¿Eliminar?')) $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'delete_competencia',id_competencia:idComp},success:function(){cargarCompetencias(idCurso);}}); }
    </script>
</body>
</html>
