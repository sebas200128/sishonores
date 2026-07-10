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

$usuarios = $usuarioModel->listarTodos();
$usuarios = array_filter($usuarios, function($user) {
    return ($user['nombre_rol'] ?? '') !== 'PadreFamilia';
});

$query_roles = "SELECT * FROM roles ORDER BY nivel_acceso DESC";
$stmt_roles = $db->prepare($query_roles);
$stmt_roles->execute();
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>Nombres</th><th>Email</th><th>DNI</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $user): ?>
                    <tr>
                        <td><?php echo $user['nombres'] . ' ' . $user['apellidos']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['dni']; ?></td>
                        <td><?php echo $user['nombre_rol']; ?></td>
                        <td><?php echo $user['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarUsuario(<?php echo $user['id_usuario']; ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?php echo $user['id_usuario']; ?>)"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario">
                        <input type="hidden" id="usuario_id" name="usuario_id">
                        <div class="mb-3"><label>Nombres</label><input type="text" class="form-control" name="nombres" required></div>
                        <div class="mb-3"><label>Apellidos</label><input type="text" class="form-control" name="apellidos" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="email" required></div>
                        <div class="mb-3">
                            <label>DNI</label>
                            <input type="text" class="form-control" name="dni" id="dniUsuario" maxlength="8" pattern="\d{8}" required>
                            <div id="dniFeedbackUsuario" class="alert alert-danger py-2 mt-2 d-none"></div>
                        </div>
                        <div class="mb-3"><label>Teléfono</label><input type="text" class="form-control" name="telefono"></div>
                        <div class="mb-3"><label>Rol</label><select class="form-control" name="id_rol" required>
                            <?php foreach($roles as $rol): ?>
                                <?php if ($rol['nombre_rol'] !== 'PadreFamilia'): ?>
                                    <option value="<?php echo $rol['id_rol']; ?>"><?php echo $rol['nombre_rol']; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select></div>
                        <div class="mb-3"><label>Contraseña</label><input type="password" class="form-control" name="password"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validarDniUsuario(mostrarAlerta = false) {
            const input = $('#dniUsuario');
            const feedback = $('#dniFeedbackUsuario');
            const dni = input.val().trim();
            const id = $('#usuario_id').val() || 0;

            input.removeClass('is-invalid');
            feedback.addClass('d-none').text('');

            if (!dni) {
                return true;
            }

            $.ajax({
                url: 'index.php?controller=usuario',
                method: 'POST',
                dataType: 'json',
                data: { action: 'check_dni', dni: dni, id: id },
                success: function(res) {
                    if (!res.available) {
                        input.addClass('is-invalid');
                        feedback.removeClass('d-none').text(res.message || 'El DNI ya se encuentra registrado.');
                        if (mostrarAlerta) {
                            alert(res.message || 'El DNI ya se encuentra registrado.');
                        }
                    }
                }
            });

            return true;
        }

        function guardarUsuario() {
            const dniInput = $('#dniUsuario');
            const dni = dniInput.val().trim();

            if (!dni) {
                alert('Ingrese un DNI');
                return;
            }

            $.ajax({
                url: 'index.php?controller=usuario',
                method: 'POST',
                dataType: 'json',
                data: $('#formUsuario').serialize() + '&action=guardar',
                success: function(r) {
                    if (!r.success) {
                        if (r.message && r.message.toLowerCase().includes('dni')) {
                            dniInput.addClass('is-invalid');
                            $('#dniFeedbackUsuario').removeClass('d-none').text(r.message);
                            alert(r.message);
                        } else {
                            alert(r.message || 'No se pudo guardar el usuario.');
                        }
                        return;
                    }
                    location.reload();
                }
            });
        }

        function editarUsuario(id) {
            $.ajax({url: 'index.php?controller=usuario&action=get&id='+id, method: 'GET', success: function(user){
                $('#usuario_id').val(user.id_usuario);
                $('input[name="nombres"]').val(user.nombres);
                $('input[name="apellidos"]').val(user.apellidos);
                $('input[name="email"]').val(user.email);
                $('input[name="dni"]').val(user.dni);
                $('input[name="telefono"]').val(user.telefono);
                $('select[name="id_rol"]').val(user.id_rol);
                $('#dniUsuario').removeClass('is-invalid');
                $('#dniFeedbackUsuario').addClass('d-none').text('');
                $('#modalUsuario').modal('show');
            }});
        }
        function eliminarUsuario(id) {
            if(confirm('¿Eliminar?')) $.ajax({url: 'index.php?controller=usuario&action=delete&id='+id, method: 'GET', success: function(){location.reload();}});
        }

        $(document).ready(function() {
            $('#dniUsuario').on('input blur', function() {
                validarDniUsuario(false);
            });
        });
    </script>
</body>
</html>
