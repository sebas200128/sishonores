<?php
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/Aula.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$aulaModel = new Aula($db);

// Usar el Modelo en lugar de consulta directa o DAO
$aulas = $aulaModel->listarTodas();

$query_grados = "SELECT id_grado, nombre_grado, id_nivel FROM grados ORDER BY orden";
$stmt_grados = $db->prepare($query_grados);
$stmt_grados->execute();
$grados = $stmt_grados->fetchAll(PDO::FETCH_ASSOC);

$query_secciones = "SELECT id_seccion, nombre_seccion FROM secciones";
$stmt_secciones = $db->prepare($query_secciones);
$stmt_secciones->execute();
$secciones = $stmt_secciones->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aulas - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-door-open"></i> Gestión de Aulas</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAula">
            <i class="fas fa-plus"></i> Nueva Aula
        </button>
        <a href="reporte_aulas_pdf.php" target="_blank" class="btn btn-danger mb-3 ms-2">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>Grado</th><th>Sección</th><th>Nivel</th><th>Año Lectivo</th><th>Vacantes</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($aulas as $aula): ?>
                    <tr>
                                    <td><?php echo $aula['nombre_grado']; ?></td>
                                    <td><?php echo $aula['nombre_seccion']; ?></td>
                                    <td><?php echo $aula['nombre_nivel']; ?></td>
                                    <td><?php echo $aula['anio']; ?></td>
                                    <td><?php echo $aula['vacantes']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editarAula(<?php echo $aula['id_aula']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarAula(<?php echo $aula['id_aula']; ?>)"><i class="fas fa-trash"></i></button>
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
    <div class="modal fade" id="modalAula" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Nueva Aula</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAula">
                        <input type="hidden" id="aula_id" name="aula_id">
                        <div class="mb-3"><label>Grado</label><select class="form-control" name="id_grado" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($grados as $grado): ?>
                                <option value="<?php echo $grado['id_grado']; ?>"><?php echo $grado['nombre_grado']; ?></option>
                            <?php endforeach; ?>
                        </select></div>
                        <div class="mb-3"><label>Sección</label><select class="form-control" name="id_seccion" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($secciones as $seccion): ?>
                                <option value="<?php echo $seccion['id_seccion']; ?>"><?php echo $seccion['nombre_seccion']; ?></option>
                            <?php endforeach; ?>
                        </select></div>
                        <div class="mb-3"><label>Vacantes</label><input type="number" class="form-control" name="vacantes" value="30"></div>
                        <div class="mb-3"><label>Año Lectivo</label><input type="number" class="form-control" name="anio" value="<?php echo date('Y'); ?>"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarAula()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function guardarAula() {
            $.ajax({url: 'index.php?controller=aula', method: 'POST', data: $('#formAula').serialize() + '&action=guardar', success: function(r){location.reload();}});
        }
        function editarAula(id) {
            $.ajax({url: 'index.php?controller=aula&action=get&id='+id, method: 'GET', success: function(aula){
                $('#aula_id').val(aula.id_aula);
                $('select[name="id_grado"]').val(aula.id_grado);
                $('select[name="id_seccion"]').val(aula.id_seccion);
                $('input[name="vacantes"]').val(aula.vacantes);
                $('input[name="anio"]').val(aula.anio);
                $('#modalAula').modal('show');
            }});
        }
        function eliminarAula(id) {
            if(confirm('¿Eliminar esta aula?')) $.ajax({url: 'index.php?controller=aula&action=delete&id='+id, method: 'GET', success: function(){location.reload();}});
        }
    </script>
</body>
</html>
