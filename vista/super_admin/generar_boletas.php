<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query_alumnos = "SELECT a.id_alumno, a.apellidos, a.nombres, a.dni, g.nombre_grado, s.nombre_seccion 
                  FROM alumnos a
                  JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
                  JOIN grados g ON aa.id_grado = g.id_grado
                  JOIN secciones s ON aa.id_seccion = s.id_seccion
                  WHERE a.activo = 1
                  ORDER BY g.orden, s.nombre_seccion, a.apellidos";
$stmt_alumnos = $db->prepare($query_alumnos);
$stmt_alumnos->execute();
$alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Boletas - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-file-pdf"></i> Generar Boletas de Notas</h1>
        
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-search"></i> Seleccionar Alumno
            </div>
            <div class="card-body">
                <form method="GET" action="../../director/boletas_pdf.php" target="_blank" class="row g-3">
                    <div class="col-md-6">
                        <label>Alumno</label>
                        <select name="id_alumno" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($alumnos as $alumno): ?>
                                            <option value="<?php echo $alumno['id_alumno']; ?>"><?php echo $alumno['apellidos'] . ' ' . $alumno['nombres'] . ' - ' . $alumno['nombre_grado'] . ' "' . $alumno['nombre_seccion'] . '"'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Bimestre</label>
                                    <select name="bimestre" class="form-select">
                                        <option value="0">Boleta Anual</option>
                                        <option value="1">1° Bimestre</option>
                                        <option value="2">2° Bimestre</option>
                                        <option value="3">3° Bimestre</option>
                                        <option value="4">4° Bimestre</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block"><i class="fas fa-file-pdf"></i> Generar Boleta</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-dark text-white">
                            <i class="fas fa-users"></i> Generar Boletas Masivas
                        </div>
                        <div class="card-body">
                            <form method="POST" action="generar_boletas_masivas.php" class="row g-3">
                                <div class="col-md-4">
                                    <label>Nivel</label>
                                    <select name="id_nivel" class="form-select" id="selectNivel" required>
                                        <option value="">Seleccionar...</option>
                                        <?php
                                        $query_niveles = "SELECT * FROM niveles";
                                        $stmt_niveles = $db->prepare($query_niveles);
                                        $stmt_niveles->execute();
                                        while($nivel = $stmt_niveles->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $nivel['id_nivel']; ?>"><?php echo $nivel['nombre_nivel']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Grado</label>
                                    <select name="id_grado" class="form-select" id="selectGrado">
                                        <option value="">Todos los grados</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-success d-block"><i class="fas fa-file-archive"></i> Generar Boletas (ZIP)</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#selectNivel').change(function() {
            let id_nivel = $(this).val();
            if(id_nivel) {
                $.ajax({url: 'generar_boletas_masivas.php?action=get_grados&id_nivel='+id_nivel, method: 'GET', dataType: 'json', success: function(data){
                    let options = '<option value="">Todos los grados</option>';
                    data.forEach(function(grado){ options += '<option value="'+grado.id_grado+'">'+grado.nombre_grado+'</option>'; });
                    $('#selectGrado').html(options);
                }});
            }
        });
    </script>
</body>
</html>
