<?php
// vista/secretaria/ver_notas.php

require_once '../../util/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Secretaria') {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Obtener todas las aulas asignadas activas en el año actual (que solo tienen sección "A")
$query_aulas = "SELECT aa.id_aula, g.nombre_grado, s.nombre_seccion, n.nombre_nivel 
                FROM aulas_asignadas aa 
                JOIN grados g ON aa.id_grado = g.id_grado 
                JOIN secciones s ON aa.id_seccion = s.id_seccion 
                JOIN niveles n ON g.id_nivel = n.id_nivel 
                WHERE aa.anio = YEAR(NOW()) 
                ORDER BY g.orden";
$stmt_aulas = $db->prepare($query_aulas);
$stmt_aulas->execute();
$aulas = $stmt_aulas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Notas - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-eye text-primary"></i> Ver Notas de Alumnos</h1>
        
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-3" onsubmit="event.preventDefault(); buscarAlumnos();">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Grado / Aula</label>
                        <select class="form-select" id="aulaSelect" onchange="buscarAlumnos()">
                            <option value="">Seleccionar grado...</option>
                            <?php foreach ($aulas as $a): ?>
                                <option value="<?php echo $a['id_aula']; ?>">
                                    <?php echo htmlspecialchars($a['nombre_grado'] . ' - Sección "' . $a['nombre_seccion'] . '" (' . $a['nombre_nivel'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Buscar por DNI</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="dniBuscar" placeholder="Ingrese DNI (8 dígitos)" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-outline-secondary d-block w-100" type="button" onclick="limpiarFiltros()"><i class="fas fa-undo"></i> Limpiar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold">
                <i class="fas fa-list"></i> Resultados
            </div>
            <div class="card-body">
                <div id="resultados">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> Seleccione un grado o ingrese un DNI para buscar alumnos.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function buscarAlumnos() {
            let aulaId = $('#aulaSelect').val();
            let dni = $('#dniBuscar').val();
            
            if (!aulaId && !dni) {
                $('#resultados').html('<div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Seleccione un grado o ingrese un DNI para buscar alumnos.</div>');
                return;
            }
            
            $('#resultados').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 text-muted">Buscando alumnos...</p></div>');
            
            $.ajax({
                url: 'buscar_alumno_ajax.php',
                method: 'POST',
                data: { id_aula: aulaId, dni: dni },
                success: function(data) {
                    $('#resultados').html(data);
                },
                error: function() {
                    $('#resultados').html('<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle"></i> Error al conectar con el servidor.</div>');
                }
            });
        }
        
        function limpiarFiltros() {
            $('#aulaSelect').val('');
            $('#dniBuscar').val('');
            $('#resultados').html('<div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Seleccione un grado o ingrese un DNI para buscar alumnos.</div>');
        }
    </script>
</body>
</html>
