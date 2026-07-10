<?php
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/Alumno.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$alumnoModel = new Alumno($db);

$alumnos = $alumnoModel->listarTodos();

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
    <title>Gestión de Alumnos - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-graduation-cap"></i> Gestión de Alumnos</h1>
        <a href="matricula.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Nuevo Alumno
        </a>
        <a href="reporte_alumnos_pdf.php" target="_blank" class="btn btn-danger mb-3 ms-2">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>Código</th><th>Nombres</th><th>DNI</th><th>Grado</th><th>Sección</th><th>Contraseña Padre</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($alumnos as $alumno): ?>
                    <tr>
                                    <td><?php echo $alumno['codigo_estudiante']; ?></td>
                                    <td><?php echo $alumno['apellidos'] . ' ' . $alumno['nombres']; ?></td>
                                    <td><?php echo $alumno['dni']; ?></td>
                                    <td><?php echo $alumno['nombre_grado']; ?></td>
                                    <td><?php echo $alumno['nombre_seccion']; ?></td>
                                    <td><code><?php echo htmlspecialchars($alumno['contraseña_padre']); ?></code></td>
                                    <td><?php echo $alumno['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editarAlumno(<?php echo $alumno['id_alumno']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarAlumno(<?php echo $alumno['id_alumno']; ?>)"><i class="fas fa-trash"></i></button>
                                        <button class="btn btn-sm btn-info" onclick="verAlumno(<?php echo $alumno['id_alumno']; ?>)"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAlumno" tabindex="-1" aria-labelledby="modalAlumnoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalAlumnoLabel">Datos del alumno</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formDetalleAlumno">
                        <input type="hidden" name="alumno_id" id="alumno_id">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" name="codigo_estudiante" id="codigo_estudiante" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">DNI</label>
                                <input type="text" class="form-control" name="dni" id="dni" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha de nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="nombres" id="nombres" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="apellidos" id="apellidos" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aula</label>
                                <select class="form-select" name="id_aula" id="id_aula" disabled>
                                    <option value="">Seleccionar aula</option>
                                    <?php foreach ($aulas as $aula): ?>
                                        <option value="<?php echo $aula['id_aula']; ?>"><?php echo htmlspecialchars($aula['nombre_grado'] . ' - Sección ' . $aula['nombre_seccion']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary">Datos del padre / apoderado</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre del apoderado</label>
                                <input type="text" class="form-control" name="nombre_apoderado" id="nombre_apoderado" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono_apoderado" id="telefono_apoderado" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email_apoderado" id="email_apoderado" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarAlumno" onclick="guardarAlumnoModal()" style="display:none;">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalAlumno(id, editable) {
            $.ajax({url: 'index.php?controller=alumno&action=get&id='+id, method: 'GET', dataType: 'json', success: function(alumno){
                $('#alumno_id').val(alumno.id_alumno || '');
                $('#codigo_estudiante').val(alumno.codigo_estudiante || '');
                $('#nombres').val(alumno.nombres || '');
                $('#apellidos').val(alumno.apellidos || '');
                $('#dni').val(alumno.dni || '');
                $('#fecha_nacimiento').val(alumno.fecha_nacimiento || '');
                $('#telefono').val(alumno.telefono || '');
                $('#nombre_apoderado').val(alumno.nombre_apoderado || '');
                $('#telefono_apoderado').val(alumno.telefono_apoderado || '');
                $('#email_apoderado').val(alumno.email_apoderado || '');
                $('#id_aula').val(alumno.id_aula || '');

                const fields = ['#codigo_estudiante', '#nombres', '#apellidos', '#dni', '#fecha_nacimiento', '#telefono', '#nombre_apoderado', '#telefono_apoderado', '#email_apoderado', '#id_aula'];
                fields.forEach(function(selector){
                    $(selector).prop('readonly', !editable).prop('disabled', !editable && selector === '#id_aula');
                });

                $('#modalAlumnoLabel').text(editable ? 'Editar alumno' : 'Datos del alumno');
                $('#btnGuardarAlumno').toggle(editable);
                $('#modalAlumno').modal('show');
            }});
        }

        function editarAlumno(id) {
            abrirModalAlumno(id, true);
        }

        function eliminarAlumno(id) {
            if(confirm('¿Eliminar este alumno? Marcarlo como inactivo')) $.ajax({url: 'index.php?controller=alumno&action=delete&id='+id, method: 'GET', success: function(){location.reload();}});
        }

        function verAlumno(id) {
            abrirModalAlumno(id, false);
        }

        function guardarAlumnoModal() {
            $.ajax({
                url: 'index.php?controller=alumno',
                method: 'POST',
                dataType: 'json',
                data: $('#formDetalleAlumno').serialize() + '&action=guardar',
                success: function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.message || 'No se pudieron guardar los cambios.');
                    }
                },
                error: function() {
                    alert('Error al guardar los cambios.');
                }
            });
        }
    </script>
</body>
</html>
