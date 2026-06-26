<?php
require_once '../../util/Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT dca.*, c.nombre_curso, g.nombre_grado, s.nombre_seccion 
          FROM docente_curso_aula dca
          JOIN cursos c ON dca.id_curso = c.id_curso
          JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
          JOIN grados g ON aa.id_grado = g.id_grado
          JOIN secciones s ON aa.id_seccion = s.id_seccion
          WHERE dca.id_usuario = :user_id AND dca.anio = YEAR(NOW())";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$curso_seleccionado = $_GET['curso'] ?? 0;
$bimestre = $_GET['bimestre'] ?? 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar Notas - SisHonores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_url('css/style.css')); ?>">
    <style>
        .nota-input { width: 70px; text-align: center; border: 1px solid #ddd; border-radius: 5px; padding: 5px; }
        .nota-input:focus { border-color: #1a56db; outline: none; }
        .nota-ad { background-color: #d4edda; color: #155724; }
        .nota-a { background-color: #fff3cd; color: #856404; }
        .nota-b { background-color: #ffe6cc; color: #e67e22; }
        .nota-c { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="container-fluid">
        <h1><i class="fas fa-edit"></i> Ingresar Notas</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label>Curso / Aula</label>
                        <select name="curso" class="form-select" onchange="this.form.submit()">
                            <option value="0">Seleccionar curso...</option>
                            <?php foreach($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id_docente_curso_aula']; ?>" <?php echo $curso_seleccionado == $curso['id_docente_curso_aula'] ? 'selected' : ''; ?>>
                                            <?php echo $curso['nombre_curso'] . ' - ' . $curso['nombre_grado'] . ' "' . $curso['nombre_seccion'] . '"'; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Bimestre</label>
                                    <select name="bimestre" class="form-select" onchange="this.form.submit()">
                                        <?php for($i=1;$i<=4;$i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $bimestre==$i?'selected':''; ?>><?php echo $i; ?>° Bimestre</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4"><label>&nbsp;</label><button type="button" class="btn btn-primary d-block" onclick="cargarCompetencias()"><i class="fas fa-tasks"></i> Gestionar Competencias</button></div>
                            </form>
                        </div>
                    </div>
                    
                    <?php if($curso_seleccionado > 0): ?>
                    <div class="card">
                        <div class="card-header bg-dark text-white"><i class="fas fa-table"></i> Registro de Notas</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr><th>#</th><th>Alumno</th><th id="comp1_titulo">Competencia 1</th><th id="comp2_titulo">Competencia 2</th><th id="comp3_titulo">Competencia 3</th><th>Promedio</th><th>Observaciones</th></tr>
                                    </thead>
                                    <tbody id="listaAlumnos"><tr><td colspan="7" class="text-center">Cargando...</td></tr></tbody>
                                    <tfoot><tr><td colspan="7"><button class="btn btn-success" onclick="guardarTodasNotas()"><i class="fas fa-save"></i> Guardar Todas</button></td></tr></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalCompetencias" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5 class="modal-title">Gestionar Competencias</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div id="listaCompetencias"></div><div class="mt-3"><input type="text" class="form-control" id="nuevaCompetencia" placeholder="Nueva competencia"><button class="btn btn-primary mt-2" onclick="agregarCompetencia()">Agregar</button></div></div></div></div></div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cursoId = <?php echo $curso_seleccionado; ?>;
        let bimestre = <?php echo $bimestre; ?>;
        function cargarCompetencias() { $.ajax({url: '../../controller/NotaController.php', method: 'POST', data: {action:'list_competencias', id_curso:cursoId}, success:function(data){$('#listaCompetencias').html(data);$('#modalCompetencias').modal('show');}}); }
        function agregarCompetencia() { let nombre=$('#nuevaCompetencia').val(); if(nombre.trim()=='') return; $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'add_competencia',id_curso:cursoId,nombre:nombre},success:function(r){if(r.success){$('#nuevaCompetencia').val('');cargarCompetencias();cargarAlumnos();}}}); }
        function eliminarCompetencia(id) { if(confirm('¿Eliminar?')) $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'delete_competencia',id_competencia:id},success:function(){cargarCompetencias();cargarAlumnos();}}); }
        function cargarAlumnos() { $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'alumnos_notas',curso:cursoId,bimestre:bimestre},dataType:'json',success:function(data){
            if(data.competencias){ $('#comp1_titulo').text(data.competencias[0]||'C1'); $('#comp2_titulo').text(data.competencias[1]||'C2'); $('#comp3_titulo').text(data.competencias[2]||'C3'); }
            let html=''; data.alumnos.forEach(function(a,i){ let color=''; let p=a.promedio||0; if(p>=18) color='nota-ad'; else if(p>=14) color='nota-a'; else if(p>=11) color='nota-b'; else if(p>0) color='nota-c';
            html+='<tr class="nota-row" data-id="'+a.id_alumno+'"><td>'+(i+1)+'</td><td>'+a.apellidos+' '+a.nombres+'</td>';
            for(let j=0;j<3;j++) html+='<td><input type="number" class="nota-input" value="'+(a.notas[j]||'')+'" min="0" max="20" step="0.5" onchange="calcularPromedio(this)"></td>';
            html+='<td class="promedio-cell '+color+'">'+(p||'-')+'</td><td><input type="text" class="form-control form-control-sm observacion" value="'+(a.observacion||'')+'"></td></tr>';
            }); $('#listaAlumnos').html(html);
        }}); }
      function calcularPromedio(input) { 
            // Obtenemos el valor ingresado
            let val = parseFloat($(input).val());

            // Validación: Si es un número y está fuera del rango [0, 20]
            // Usamos !isNaN(val) para asegurar que se ingresó un número y no está vacío
            if (!isNaN(val) && (val < 0 || val > 20)) {
                alert("La nota ingresada es incorrecta. El valor debe estar entre 0 y 20.");
                $(input).val(''); // Limpiamos el campo
                return; // Detenemos la ejecución de la función para no calcular el promedio incorrecto
            }

            let row=$(input).closest('tr'); 
            let sum=0,count=0; 
            row.find('.nota-input').each(function(){ let v=parseFloat($(this).val()); if(!isNaN(v)&&v>0){sum+=v;count++;} }); 
            let p=count>0?(sum/count).toFixed(1):0; 
            let cell=row.find('.promedio-cell'); 
            cell.text(p);
            cell.removeClass('nota-ad nota-a nota-b nota-c'); 
            if(p>=18) cell.addClass('nota-ad'); 
            else if(p>=14) cell.addClass('nota-a'); 
            else if(p>=11) cell.addClass('nota-b'); 
            else if(p>0) cell.addClass('nota-c'); 
        }

        function guardarTodasNotas() { let datos=[]; $('.nota-row').each(function(){ let row=$(this); let templates_notas=[]; row.find('.nota-input').each(function(){templates_notas.push($(this).val()||0);}); datos.push({id_alumno:row.data('id'),notas:templates_notas,observacion:row.find('.observacion').val()}); });
            $.ajax({url:'../../controller/NotaController.php',method:'POST',data:{action:'guardar_notas',id_docente_curso_aula:cursoId,bimestre:bimestre,datos:JSON.stringify(datos)},success:function(r){if(r.success){alert('Notas guardadas');cargarAlumnos();}else{alert('Error');}}}); }
        $(document).ready(function(){if(cursoId>0)cargarAlumnos();});
    </script>
</body>
</html>