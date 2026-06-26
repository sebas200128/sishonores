<?php
require_once '../../util/Database.php';
require_once '../../dao/CursoDAO.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$cursoDAO = new CursoDAO($db);

// Usar el DAO para listar cursos
$cursos = $cursoDAO->listarTodos();

$query_niveles = "SELECT * FROM niveles";
$stmt_niveles = $db->prepare($query_niveles);
$stmt_niveles->execute();
$niveles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-book"></i> Gestión de Cursos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCurso">
            <i class="fas fa-plus"></i> Nuevo Curso
        </button>
        <a href="reporte_cursos_pdf.php" target="_blank" class="btn btn-danger mb-3 ms-2">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>Nombre</th><th>Código</th><th>Horas/Sem</th><th>Nivel</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($cursos as $curso): ?>
                    <tr>
                        <td><?php echo $curso['nombre_curso']; ?></td>
                        <td><?php echo $curso['codigo_curso']; ?></td>
                        <td><?php echo $curso['horas_semanales']; ?></td>
                        <td><?php echo $curso['nombre_nivel']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarCurso(<?php echo $curso['id_curso']; ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarCurso(<?php echo $curso['id_curso']; ?>)"><i class="fas fa-trash"></i></button>
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
    <div class="modal fade" id="modalCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Nuevo Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCurso">
                        <input type="hidden" id="curso_id" name="curso_id">
                        <div class="mb-3"><label>Nombre del Curso</label><input type="text" class="form-control" name="nombre_curso" required></div>
                        <div class="mb-3"><label>Código</label><input type="text" class="form-control" name="codigo_curso"></div>
                        <div class="mb-3"><label>Horas Semanales</label><input type="number" class="form-control" name="horas_semanales" value="4"></div>
                        <div class="mb-3"><label>Nivel</label><select class="form-control" name="id_nivel" required>
                            <?php foreach($niveles as $nivel): ?>
                                <option value="<?php echo $nivel['id_nivel']; ?>"><?php echo $nivel['nombre_nivel']; ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCurso()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function guardarCurso() {
            $.ajax({url: '../../controller/CursoController.php', method: 'POST', data: $('#formCurso').serialize() + '&action=guardar', success: function(r){location.reload();}});
        }
        function editarCurso(id) {
            $.ajax({url: '../../controller/CursoController.php?action=get&id='+id, method: 'GET', success: function(curso){
                $('#curso_id').val(curso.id_curso);
                $('input[name="nombre_curso"]').val(curso.nombre_curso);
                $('input[name="codigo_curso"]').val(curso.codigo_curso);
                $('input[name="horas_semanales"]').val(curso.horas_semanales);
                $('select[name="id_nivel"]').val(curso.id_nivel);
                $('#modalCurso').modal('show');
            }});
        }
        function eliminarCurso(id) {
            if(confirm('¿Eliminar este curso?')) $.ajax({url: '../../controller/CursoController.php?action=delete&id='+id, method: 'GET', success: function(){location.reload();}});
        }
    </script>
</body>
</html>
