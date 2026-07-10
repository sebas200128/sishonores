require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT dca.id_docente_curso_aula, c.nombre_curso, c.id_curso, g.nombre_grado, s.nombre_seccion 
          FROM docente_curso_aula dca
          JOIN cursos c ON dca.id_curso = c.id_curso
          JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
          JOIN grados g ON aa.id_grado = g.id_grado
          JOIN secciones s ON aa.id_seccion = s.id_seccion
          WHERE dca.id_usuario = :user_id
          ORDER BY c.nombre_curso, g.nombre_grado, s.nombre_seccion";
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
    <style>
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none; }
        .card-header { font-weight: bold; font-size: 14px; }
        .competencias-list { min-height: 100px; }
        .loading-spinner { text-align: center; padding: 20px; }
        .main-content { margin-left: 280px; }
        .container-fluid { padding: 20px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid p-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1><i class="fas fa-tasks"></i> Gestionar Competencias</h1>
                <p class="text-muted">Administra las competencias de los cursos que imparte</p>
            </div>
        </div>
        
        <?php if(empty($cursos)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No tiene cursos asignados.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($cursos as $curso): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0">
                                            <?php echo htmlspecialchars($curso['nombre_curso'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h6>
                                        <small><?php echo htmlspecialchars($curso['nombre_grado'] . ' - Sección ' . $curso['nombre_seccion'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Listado de competencias -->
                                <div id="comp-<?php echo $curso['id_docente_curso_aula']; ?>" class="competencias-list mb-3">
                                    <div class="loading-spinner">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <small class="d-block mt-2">Cargando competencias...</small>
                                    </div>
                                </div>
                                
                                <!-- Formulario para agregar competencia -->
                                <div class="border-top pt-3">
                                    <h6 class="mb-2"><i class="fas fa-plus-circle"></i> Agregar Competencia</h6>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="newcomp-<?php echo $curso['id_docente_curso_aula']; ?>" 
                                               placeholder="Nombre de la nueva competencia"
                                               onkeypress="if(event.key==='Enter') agregarCompetencia(<?php echo $curso['id_docente_curso_aula']; ?>)">
                                        <button class="btn btn-success" 
                                                type="button"
                                                onclick="agregarCompetencia(<?php echo $curso['id_docente_curso_aula']; ?>)">
                                            <i class="fas fa-check"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal para editar competencia -->
    <div class="modal fade" id="modalEditarCompetencia" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Competencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editIdCompetencia">
                    <input type="hidden" id="editIdCurso">
                    <div class="mb-3">
                        <label for="editNombreCompetencia" class="form-label">Nombre de la Competencia</label>
                        <input type="text" class="form-control" id="editNombreCompetencia" placeholder="Nombre de la competencia">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicionCompetencia()">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funciones AJAX para gestionar competencias
        function cargarCompetencias(id) { 
            $.ajax({
                url:'index.php?controller=nota',
                method:'POST',
                data:{action:'list_competencias',id_curso:id},
                success:function(data){
                    $('#comp-'+id).html(data);
                },
                error:function(){
                    $('#comp-'+id).html('<div class="alert alert-danger">Error al cargar competencias</div>');
                }
            }); 
        }
        
        function agregarCompetencia(id) { 
            let nombre=$('#newcomp-'+id).val().trim(); 
            if(nombre=='') {
                alert('Por favor ingrese el nombre de la competencia');
                return; 
            }
            $.ajax({
                url:'index.php?controller=nota',
                method:'POST',
                data:{action:'add_competencia',id_curso:id,nombre:nombre},
                success:function(r){
                    if(r.success){
                        $('#newcomp-'+id).val('');
                        cargarCompetencias(id);
                    } else {
                        alert('Error al agregar competencia');
                    }
                },
                error:function(){
                    alert('Error en la solicitud');
                }
            }); 
        }
        
        function eliminarCompetencia(idComp,idCurso) { 
            if(confirm('¿Está seguro que desea eliminar esta competencia?')) {
                $.ajax({
                    url:'index.php?controller=nota',
                    method:'POST',
                    data:{action:'delete_competencia',id_competencia:idComp},
                    success:function(){
                        cargarCompetencias(idCurso);
                    },
                    error:function(){
                        alert('Error al eliminar competencia');
                    }
                }); 
            }
        }
        
        function editarCompetencia(idComp, nombre, idCurso) { 
            $('#editIdCompetencia').val(idComp); 
            $('#editIdCurso').val(idCurso); 
            $('#editNombreCompetencia').val(nombre); 
            new bootstrap.Modal(document.getElementById('modalEditarCompetencia')).show(); 
        }
        
        function guardarEdicionCompetencia() { 
            let idComp=$('#editIdCompetencia').val(); 
            let nombre=$('#editNombreCompetencia').val().trim(); 
            let idCurso=$('#editIdCurso').val(); 
            
            if(nombre=='') { 
                alert('El nombre no puede estar vacío'); 
                return; 
            } 
            
            $.ajax({
                url:'index.php?controller=nota',
                method:'POST',
                data:{action:'update_competencia',id_competencia:idComp,nombre:nombre,id_curso:idCurso},
                success:function(r){
                    if(r.success){
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarCompetencia')).hide();
                        cargarCompetencias(idCurso);
                    }else{
                        alert('Error al actualizar');
                    }
                },
                error:function(){
                    alert('Error en la solicitud');
                }
            });
        }
        
        // Cargar todas las competencias cuando carga la página
        $(document).ready(function() {
            <?php foreach($cursos as $curso): ?>
                cargarCompetencias(<?php echo $curso['id_docente_curso_aula']; ?>);
            <?php endforeach; ?>
        });
    </script>
</body>
</html>
