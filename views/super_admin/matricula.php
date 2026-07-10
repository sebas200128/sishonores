require_once __DIR__ . '/../../core/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Obtener todas las aulas ordenadas por nivel, grado y sección
$query_aulas = "SELECT aa.id_aula, g.nombre_grado, s.nombre_seccion, n.nombre_nivel 
                FROM aulas_asignadas aa
                JOIN grados g ON aa.id_grado = g.id_grado
                JOIN secciones s ON aa.id_seccion = s.id_seccion
                JOIN niveles n ON g.id_nivel = n.id_nivel
                WHERE aa.anio = YEAR(NOW())
                ORDER BY n.id_nivel, g.orden, s.nombre_seccion";
$stmt_aulas = $db->prepare($query_aulas);
$stmt_aulas->execute();
$aulas = $stmt_aulas->fetchAll(PDO::FETCH_ASSOC);

// Agrupar aulas por nivel para organizarlas en el select
$aulas_por_nivel = [];
foreach ($aulas as $aula) {
    $aulas_por_nivel[$aula['nombre_nivel']][] = $aula;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrícula de Alumno - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
    <style>
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a56db;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .card-matricula {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: none;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-file-signature"></i> Ficha de Matrícula</h1>
            <a href="alumnos.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Alumnos
            </a>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-matricula">
                    <div class="card-header bg-dark text-white p-3">
                        <h5 class="mb-0"><i class="fas fa-user-plus"></i> Registrar Matrícula de Nuevo Estudiante</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="formMatricula">
                            <input type="hidden" name="alumno_id" value="">
                            
                            <!-- Sección 1: Datos del Estudiante -->
                            <div class="form-section-title">
                                <i class="fas fa-user-graduate"></i> Datos Generales del Estudiante
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Código Estudiante</label>
                                    <input type="text" class="form-control" name="codigo_estudiante" id="codigo_estudiante" placeholder="Ej: EST20260001" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">DNI del Alumno</label>
                                    <input type="text" class="form-control" name="dni" id="dniAlumno" maxlength="8" pattern="\d{8}" title="El DNI debe tener 8 dígitos numéricos" placeholder="8 dígitos" required>
                                    <div id="dniFeedbackAlumno" class="alert alert-danger py-2 mt-2 d-none"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombres</label>
                                    <input type="text" class="form-control" name="nombres" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Aula de Destino (Grado - Sección)</label>
                                    <select class="form-select" name="id_aula" required>
                                        <option value="">Seleccionar Aula...</option>
                                        <?php foreach ($aulas_por_nivel as $nivel => $aulas_lista): ?>
                                            <optgroup label="<?php echo htmlspecialchars($nivel); ?>">
                                                <?php foreach ($aulas_lista as $a): ?>
                                                    <option value="<?php echo $a['id_aula']; ?>">
                                                        <?php echo htmlspecialchars($a['nombre_grado'] . ' - Sección "' . $a['nombre_seccion'] . '"'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Sección 2: Datos del Apoderado -->
                            <div class="form-section-title mt-2">
                                <i class="fas fa-user-friends"></i> Información del Apoderado (Padre / Madre / Tutor)
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombres y Apellidos del Apoderado</label>
                                    <input type="text" class="form-control" name="nombre_apoderado" placeholder="Nombres completos" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Teléfono del Apoderado</label>
                                    <input type="text" class="form-control" name="telefono_apoderado" placeholder="Ej: 987654321" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Email del Apoderado</label>
                                    <input type="email" class="form-control" name="email_apoderado" placeholder="correo@ejemplo.com" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-undo"></i> Limpiar Formulario
                                </button>
                                <button type="submit" class="btn btn-success px-4" id="btnMatricular">
                                    <i class="fas fa-save"></i> Registrar Matrícula
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Credenciales Autogeneradas del Padre -->
    <div class="modal fade" id="modalCredenciales" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalCredencialesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalCredencialesLabel">
                        <i class="fas fa-check-circle"></i> ¡Matrícula Exitosa!
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted" id="cred_mensaje">Se ha registrado al alumno y se ha generado la cuenta de acceso para el Padre de Familia automáticamente con los siguientes datos:</p>
                    
                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="mb-2">
                            <strong>Apoderado:</strong> <span id="cred_apoderado"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Usuario (DNI Alumno):</strong> <code id="cred_usuario"></code>
                            <button class="btn btn-sm btn-link p-0 ms-2 text-decoration-none" onclick="copiarTexto('cred_usuario')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                        <div class="mb-0">
                            <strong>Contraseña Generada:</strong> <code id="cred_password"></code>
                            <button class="btn btn-sm btn-link p-0 ms-2 text-decoration-none" onclick="copiarTexto('cred_password')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Importante:</strong> Por favor, guarde o comparta estas credenciales con el padre de familia, ya que la contraseña solo se mostrará esta vez.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="registrarOtroHijo()">
                        <i class="fas fa-plus"></i> Registrar Otro Hijo
                    </button>
                    <button type="button" class="btn btn-primary" onclick="redirigirAlumnos()">
                        Ir a Listado de Alumnos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales para guardar datos del apoderado
        let datosApoderado = {
            nombre_apoderado: '',
            telefono_apoderado: '',
            email_apoderado: ''
        };
        let contadorHijos = 1;

        // Autogenerar código de estudiante sugerido al cargar la página
        $(document).ready(function() {
            const randomSuffix = Math.floor(1000 + Math.random() * 9000);
            $('#codigo_estudiante').val('EST2026' + randomSuffix);
        });

        const modalCred = new bootstrap.Modal(document.getElementById('modalCredenciales'));

        function validarDniAlumno(mostrarAlerta = false) {
            const input = $('#dniAlumno');
            const feedback = $('#dniFeedbackAlumno');
            const dni = input.val().trim();
            const id = $('input[name="alumno_id"]').val() || 0;

            input.removeClass('is-invalid');
            feedback.addClass('d-none').text('');

            if (!dni) {
                return true;
            }

            $.ajax({
                url: 'index.php?controller=alumno',
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

        $('#formMatricula').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const dniInput = $('#dniAlumno');
            const dni = dniInput.val().trim();
            if (!dni) {
                alert('Ingrese el DNI del alumno');
                return;
            }

            $.ajax({
                url: 'index.php?controller=alumno',
                method: 'POST',
                dataType: 'json',
                data: { action: 'check_dni', dni: dni, id: $('input[name="alumno_id"]').val() || 0 },
                success: function(res) {
                    if (!res.available) {
                        dniInput.addClass('is-invalid');
                        $('#dniFeedbackAlumno').removeClass('d-none').text(res.message || 'El DNI ya se encuentra registrado.');
                        alert(res.message || 'El DNI ya se encuentra registrado.');
                        return;
                    }

                    $('#btnMatricular').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');

                    // Guardar datos del apoderado para reutilizar
                    datosApoderado.nombre_apoderado = $('input[name="nombre_apoderado"]').val();
                    datosApoderado.telefono_apoderado = $('input[name="telefono_apoderado"]').val();
                    datosApoderado.email_apoderado = $('input[name="email_apoderado"]').val();

                    $.ajax({
                        url: 'index.php?controller=alumno',
                        method: 'POST',
                        data: form.serialize() + '&action=guardar',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                // Guardar datos para siguiente registro
                                datosApoderado.nombre_apoderado = $('input[name="nombre_apoderado"]').val();
                                datosApoderado.telefono_apoderado = $('input[name="telefono_apoderado"]').val();
                                datosApoderado.email_apoderado = $('input[name="email_apoderado"]').val();

                                // Mostrar el modal con credenciales
                                if (res.mensaje) {
                                    $('#cred_mensaje').text(res.mensaje + ' con los siguientes datos:');
                                }
                                $('#cred_apoderado').text(res.apoderado || res.nombre_apoderado || '');
                                $('#cred_usuario').text(res.dni_padre || res.dni || '');
                                $('#cred_password').text(res.password || '');
                                contadorHijos = 2;
                                modalCred.show();
                            } else {
                                alert(res.message || 'Error al procesar la matrícula.');
                                $('#btnMatricular').prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Matrícula');
                            }
                        },
                        error: function() {
                            alert('Error en la comunicación con el servidor.');
                            $('#btnMatricular').prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Matrícula');
                        }
                    });
                },
                error: function() {
                    alert('Error en la comunicación con el servidor.');
                    $('#btnMatricular').prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Matrícula');
                }
            });
        });

        function copiarTexto(elementId) {
            const texto = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(texto).then(() => {
                alert('Copiado al portapapeles: ' + texto);
            }).catch(err => {
                console.error('Error al copiar: ', err);
            });
        }

        $(document).ready(function() {
            $('#dniAlumno').on('input blur', function() {
                validarDniAlumno(false);
            });
        });

        function registrarOtroHijo() {
            // Cerrar el modal de credenciales
            modalCred.hide();
            
            // Limpiar solo los datos del estudiante
            $('input[name="alumno_id"]').val('');
            $('input[name="codigo_estudiante"]').val('EST2026' + Math.floor(1000 + Math.random() * 9000));
            $('input[name="dni"]').val('');
            $('input[name="nombres"]').val('');
            $('input[name="apellidos"]').val('');
            $('input[name="fecha_nacimiento"]').val('');
            $('select[name="id_aula"]').val('');
            
            // Restaurar los datos del apoderado desde variables globales
            $('input[name="nombre_apoderado"]').val(datosApoderado.nombre_apoderado);
            $('input[name="telefono_apoderado"]').val(datosApoderado.telefono_apoderado);
            $('input[name="email_apoderado"]').val(datosApoderado.email_apoderado);
            
            // Scroll al formulario
            $('html, body').animate({scrollTop: 0}, 'smooth');
            
            // Focus en el DNI del alumno
            setTimeout(function() {
                $('input[name="dni"]').focus();
            }, 500);
            
            // Reset del botón
            $('#btnMatricular').prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Matrícula - Hijo ' + contadorHijos);
            
            contadorHijos++;
        }

        function redirigirAlumnos() {
            window.location.href = 'index.php?controller=alumno';
        }
    </script>
</body>
</html>
