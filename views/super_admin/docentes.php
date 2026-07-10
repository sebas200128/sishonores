<?php
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

// Usar el Modelo para listar docentes
$docentes = $usuarioModel->listarDocentes();
?>
<?php
// Obtener los niveles disponibles para el formulario
$query_niveles = "SELECT * FROM niveles ORDER BY id_nivel ASC";
$stmt_niveles = $db->prepare($query_niveles);
$stmt_niveles->execute();
$niveles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Docentes - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
    <style>
        .badge-nivel {
            margin-right: 5px;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-chalkboard-teacher"></i> Gestión de Docentes</h1>
            <button class="btn btn-primary" onclick="nuevoDocente()">
                <i class="fas fa-plus"></i> Nuevo Docente
            </button>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Docentes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombres y Apellidos</th>
                                <th>DNI</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Niveles Asociados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($docentes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay docentes registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($docentes as $docente): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($docente['apellidos'] . ', ' . $docente['nombres']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($docente['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['email']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['telefono'] ?: '-'); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($docente['nombres_niveles'])) {
                                            $arr_niveles = explode(', ', $docente['nombres_niveles']);
                                            foreach($arr_niveles as $nv) {
                                                $color = 'bg-secondary';
                                                if ($nv == 'Inicial') $color = 'bg-info text-dark';
                                                elseif ($nv == 'Primaria') $color = 'bg-success';
                                                elseif ($nv == 'Secundaria') $color = 'bg-primary';
                                                echo '<span class="badge ' . $color . ' badge-nivel">' . htmlspecialchars($nv) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Sin nivel asignado</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editarDocente(<?php echo $docente['id_usuario']; ?>)" title="Editar Docente">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarDocente(<?php echo $docente['id_usuario']; ?>)" title="Eliminar Docente">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Docente -->
    <div class="modal fade" id="modalDocente" tabindex="-1" aria-labelledby="modalDocenteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalDocenteLabel">Nuevo Docente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formDocente">
                    <div class="modal-body">
                        <input type="hidden" id="usuario_id" name="usuario_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" id="docente_nombres" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" id="docente_apellidos" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">DNI (Servirá como usuario)</label>
                            <input type="text" class="form-control" name="dni" id="docente_dni" maxlength="8" required pattern="\d{8}" title="El DNI debe tener 8 dígitos numéricos">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="docente_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="docente_telefono">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-block">Niveles de Enseñanza</label>
                            <div class="border rounded p-3 bg-light">
                                <?php foreach($niveles as $nivel): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input check-nivel" type="checkbox" name="niveles[]" 
                                           id="nivel_<?php echo $nivel['id_nivel']; ?>" 
                                           value="<?php echo $nivel['id_nivel']; ?>">
                                    <label class="form-check-label" for="nivel_<?php echo $nivel['id_nivel']; ?>">
                                        <?php echo htmlspecialchars($nivel['nombre_nivel']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Debe seleccionar al menos un nivel educativo.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" id="labelPassword">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="docente_password">
                            <small class="text-muted" id="helpPassword">Para nuevos docentes es obligatoria. Para existentes, dejar en blanco si no desea cambiarla.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Docente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalEl = document.getElementById('modalDocente');
        const modalDocente = new bootstrap.Modal(modalEl);

        function nuevoDocente() {
            $('#formDocente')[0].reset();
            $('#usuario_id').val('');
            $('#modalDocenteLabel').text('Nuevo Docente');
            $('#docente_password').prop('required', true);
            $('#labelPassword').text('Contraseña');
            $('#helpPassword').text('La contraseña es obligatoria para nuevos docentes.');
            $('.check-nivel').prop('checked', false);
            modalDocente.show();
        }

        function editarDocente(id) {
            $('#formDocente')[0].reset();
            $('.check-nivel').prop('checked', false);
            
            $.ajax({
                url: 'index.php?controller=docente&action=get&id=' + id,
                method: 'GET',
                dataType: 'json',
                success: function(docente) {
                    if (docente.error) {
                        alert(docente.error);
                        return;
                    }
                    $('#usuario_id').val(docente.id_usuario);
                    $('#docente_nombres').val(docente.nombres);
                    $('#docente_apellidos').val(docente.apellidos);
                    $('#docente_dni').val(docente.dni);
                    $('#docente_email').val(docente.email);
                    $('#docente_telefono').val(docente.telefono);
                    
                    // Marcar los niveles asignados
                    if (docente.niveles && Array.isArray(docente.niveles)) {
                        docente.niveles.forEach(function(nivelId) {
                            $('#nivel_' + nivelId).prop('checked', true);
                        });
                    }

                    $('#modalDocenteLabel').text('Editar Docente');
                    $('#docente_password').prop('required', false);
                    $('#labelPassword').text('Nueva Contraseña (Opcional)');
                    $('#helpPassword').text('Deje en blanco si no desea cambiar la contraseña.');
                    modalDocente.show();
                },
                error: function() {
                    alert('Error al obtener la información del docente.');
                }
            });
        }

        function eliminarDocente(id) {
            if (confirm('¿Está seguro de que desea eliminar a este docente? Esta acción también eliminará todas sus asociaciones de nivel y curso.')) {
                $.ajax({
                    url: 'index.php?controller=docente&action=delete&id=' + id,
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            location.reload();
                        } else {
                            alert(res.message || 'Error al eliminar docente');
                        }
                    },
                    error: function() {
                        alert('Error en la comunicación con el servidor.');
                    }
                });
            }
        }

        $('#formDocente').on('submit', function(e) {
            e.preventDefault();
            
            // Validar que se haya seleccionado al menos un nivel
            if ($('.check-nivel:checked').length === 0) {
                alert('Debe seleccionar al menos un nivel de enseñanza para el docente.');
                return;
            }

            $.ajax({
                url: 'index.php?controller=docente&action=guardar',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        modalDocente.hide();
                        location.reload();
                    } else {
                        alert(res.message || 'Error al guardar el docente.');
                    }
                },
                error: function() {
                    alert('Error al guardar los datos en el servidor.');
                }
            });
        });
    </script>
</body>
</html>
