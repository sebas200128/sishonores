<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Secretaria') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Alumno - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-search"></i> Buscar Alumno por DNI</h1>
        <div class="card">
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="dniBuscar" placeholder="Ingrese DNI del alumno">
                    <button class="btn btn-primary" onclick="buscarAlumno()"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <div id="resultados"></div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function buscarAlumno() {
            let dni = $('#dniBuscar').val();
            if(dni.length>=6) $.ajax({url:'buscar_alumno_ajax.php', method:'POST', data:{dni:dni}, success:function(data){$('#resultados').html(data);}});
        }
    </script>
</body>
</html>
