<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query_docentes = "SELECT u.id_usuario, u.nombres, u.apellidos
                   FROM usuarios u
                   JOIN roles r ON u.id_rol = r.id_rol
                   WHERE r.nombre_rol = 'Docente' AND u.activo = 1
                   ORDER BY u.apellidos, u.nombres";
$stmt_docentes = $db->prepare($query_docentes);
$stmt_docentes->execute();
$docentes = $stmt_docentes->fetchAll(PDO::FETCH_ASSOC);

$query_aulas = "SELECT aa.id_aula, g.nombre_grado, s.nombre_seccion 
                FROM aulas_asignadas aa
                JOIN grados g ON aa.id_grado = g.id_grado
                JOIN secciones s ON aa.id_seccion = s.id_seccion
                WHERE aa.anio = YEAR(NOW())";
$stmt_aulas = $db->prepare($query_aulas);
$stmt_aulas->execute();
$aulas = $stmt_aulas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Docentes - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-chalkboard-user"></i> Asignar Docentes a Cursos</h1>
        <a href="reporte_asignaciones_pdf.php" target="_blank" class="btn btn-danger mb-4">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                                    <i class="fas fa-plus"></i> Nueva Asignación
                                </div>
                                <div class="card-body">
                                    <form id="formAsignacion">
                                        <div class="mb-3">
                                            <label>Docente</label>
                                            <select name="id_usuario" class="form-select" required>
                                                <option value="">Seleccionar...</option>
                                                <?php foreach($docentes as $docente): ?>
                                                <option value="<?php echo $docente['id_usuario']; ?>"><?php echo $docente['nombres'] . ' ' . $docente['apellidos']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Aula (Grado y Sección)</label>
                                            <select name="id_aula" class="form-select" id="selectAula" required>
                                                <option value="">Seleccionar...</option>
                                                <?php foreach($aulas as $aula): ?>
                                                <option value="<?php echo $aula['id_aula']; ?>"><?php echo $aula['nombre_grado'] . ' - Sección "' . $aula['nombre_seccion'] . '"'; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Curso</label>
                                            <select name="id_curso" class="form-select" id="selectCurso" required>
                                                <option value="">Primero seleccione un aula</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="asignarDocente()"><i class="fas fa-save"></i> Asignar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <i class="fas fa-list"></i> Asignaciones Actuales
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark"><tr><th>Docente</th><th>Curso</th><th>Aula</th><th>Acciones</th></tr></thead>
                                            <tbody id="listaAsignaciones"><tr><td colspan="4" class="text-center">Cargando...</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarAsignaciones() {
            $.ajax({url: '../../controller/AsignacionDocenteController.php?action=listar', method: 'GET', success: function(data) {$('#listaAsignaciones').html(data);}});
        }
        $('#selectAula').change(function() {
            let id_aula = $(this).val();
            if(id_aula) {
                $.ajax({url: '../../controller/AsignacionDocenteController.php', method: 'POST', data: {action: 'get_cursos', id_aula: id_aula}, dataType: 'json', success: function(data) {
                    let options = '<option value="">Seleccionar curso...</option>';
                    if (data.length === 0) {
                        options = '<option value="">No hay cursos para este nivel</option>';
                    } else {
                        data.forEach(function(curso) { options += '<option value="' + curso.id_curso + '">' + curso.nombre_curso + '</option>'; });
                    }
                    $('#selectCurso').html(options);
                }, error: function(){ alert('No se pudieron cargar los cursos del aula'); }});
            } else {
                $('#selectCurso').html('<option value="">Primero seleccione un aula</option>');
            }
        });
        function asignarDocente() {
            let data = $('#formAsignacion').serialize() + '&action=asignar';
            $.ajax({url: '../../controller/AsignacionDocenteController.php', method: 'POST', data: data, dataType: 'json', success: function(r){
                if(r.success){
                    alert('Asignación realizada');
                    $('#formAsignacion')[0].reset();
                    $('#selectCurso').html('<option value="">Primero seleccione un aula</option>');
                    cargarAsignaciones();
                } else {
                    alert(r.message || 'No se pudo realizar la asignación');
                }
            }, error: function(){ alert('Error al guardar la asignación'); }});
        }
        function eliminarAsignacion(id) { if(confirm('¿Eliminar?')) $.ajax({url: '../../controller/AsignacionDocenteController.php?action=eliminar&id='+id, method: 'GET', success: function(){cargarAsignaciones();}}); }
        $(document).ready(function(){cargarAsignaciones();});
    </script>

</body>
</html>
