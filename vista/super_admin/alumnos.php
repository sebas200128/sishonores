<?php
require_once '../../util/Database.php';
require_once '../../dao/AlumnoDAO.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$alumnoDAO = new AlumnoDAO($db);

// Usar el DAO para obtener todos los alumnos
$alumnos = $alumnoDAO->listarTodos();

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
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-graduation-cap"></i> Gestión de Alumnos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAlumno">
            <i class="fas fa-plus"></i> Nuevo Alumno
        </button>
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
                                        <button class="btn btn-sm btn-info" onclick="verNotas(<?php echo $alumno['id_alumno']; ?>)"><i class="fas fa-eye"></i></button>
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

    <!-- Modal -->
    <div class="modal fade" id="modalAlumno" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Nuevo Alumno</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAlumno">
                        <input type="hidden" id="alumno_id" name="alumno_id">
                        <div class="row">
                            <div class="col-md-6"><div class="mb-3"><label>Código Estudiante</label><input type="text" class="form-control" name="codigo_estudiante" required></div></div>
                            <div class="col-md-6"><div class="mb-3"><label>DNI</label><input type="text" class="form-control" name="dni" required></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><div class="mb-3"><label>Nombres</label><input type="text" class="form-control" name="nombres" required></div></div>
                            <div class="col-md-6"><div class="mb-3"><label>Apellidos</label><input type="text" class="form-control" name="apellidos" required></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><div class="mb-3"><label>Fecha Nacimiento</label><input type="date" class="form-control" name="fecha_nacimiento"></div></div>
                            <div class="col-md-6"><div class="mb-3"><label>Teléfono</label><input type="text" class="form-control" name="telefono"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><div class="mb-3"><label>Nombre Apoderado</label><input type="text" class="form-control" name="nombre_apoderado"></div></div>
                            <div class="col-md-6"><div class="mb-3"><label>Teléfono Apoderado</label><input type="text" class="form-control" name="telefono_apoderado"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><div class="mb-3"><label>Email Apoderado</label><input type="email" class="form-control" name="email_apoderado"></div></div>
                            <div class="col-md-6"><div class="mb-3"><label>Aula (Grado-Sección)</label><select class="form-control" name="id_aula" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach($aulas as $aula): ?>
                                    <option value="<?php echo $aula['id_aula']; ?>"><?php echo $aula['nombre_grado'] . ' - Sección ' . $aula['nombre_seccion']; ?></option>
                                <?php endforeach; ?>
                            </select></div></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarAlumno()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function guardarAlumno() {
            $.ajax({
                url: '../../controller/AlumnoController.php', 
                method: 'POST', 
                data: $('#formAlumno').serialize() + '&action=guardar',
                dataType: 'json',
                success: function(r){
                    if (r.padre_creado) {
                        alert('✓ Alumno creado exitosamente\n\n' +
                              'Acceso Padre de Familia:\n' +
                              'Usuario: ' + r.dni_padre + '\n' +
                              'Contraseña: ' + r.password + '\n' +
                              'Apoderado: ' + r.apoderado);
                    }
                    location.reload();
                }
            });
        }
        function editarAlumno(id) {
            $.ajax({url: '../../controller/AlumnoController.php?action=get&id='+id, method: 'GET', dataType: 'json', success: function(alumno){
                $('#alumno_id').val(alumno.id_alumno);
                $('input[name="codigo_estudiante"]').val(alumno.codigo_estudiante);
                $('input[name="nombres"]').val(alumno.nombres);
                $('input[name="apellidos"]').val(alumno.apellidos);
                $('input[name="dni"]').val(alumno.dni);
                $('input[name="fecha_nacimiento"]').val(alumno.fecha_nacimiento);
                $('input[name="telefono"]').val(alumno.telefono);
                $('input[name="nombre_apoderado"]').val(alumno.nombre_apoderado);
                $('input[name="telefono_apoderado"]').val(alumno.telefono_apoderado);
                $('input[name="email_apoderado"]').val(alumno.email_apoderado);
                $('select[name="id_aula"]').val(alumno.id_aula);
                $('#modalAlumno').modal('show');
            }});
        }
        function eliminarAlumno(id) {
            if(confirm('¿Eliminar este alumno? Marcarlo como inactivo')) $.ajax({url: '../../controller/AlumnoController.php?action=delete&id='+id, method: 'GET', success: function(){location.reload();}});
        }
        function verNotas(id) {
            window.open('alumnos_ajax.php?action=notas&id='+id, '_blank', 'width=800,height=600');
        }
    </script>
</body>
</html>
