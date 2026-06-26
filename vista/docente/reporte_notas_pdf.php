<?php
// vista/docente/reporte_notas_pdf.php

require_once '../../util/Database.php';
require_once '../../dao/NotaDAO.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Docente') {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$curso_id = isset($_GET['curso']) ? (int)$_GET['curso'] : 0;
$bimestre = isset($_GET['bimestre']) ? (int)$_GET['bimestre'] : 1;

if ($curso_id <= 0 || $bimestre < 1 || $bimestre > 4) {
    die("Parámetros de reporte no válidos.");
}

// 1. Obtener detalles del curso/aula y validar que pertenezca al docente
$query_curso = "SELECT dca.*, c.nombre_curso, g.nombre_grado, s.nombre_seccion 
                FROM docente_curso_aula dca
                JOIN cursos c ON dca.id_curso = c.id_curso
                JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
                JOIN grados g ON aa.id_grado = g.id_grado
                JOIN secciones s ON aa.id_seccion = s.id_seccion
                WHERE dca.id_docente_curso_aula = :curso AND dca.id_usuario = :user_id";
$stmt_curso = $db->prepare($query_curso);
$stmt_curso->bindValue(':curso', $curso_id, PDO::PARAM_INT);
$stmt_curso->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt_curso->execute();
$curso_info = $stmt_curso->fetch(PDO::FETCH_ASSOC);

if (!$curso_info) {
    die("Acceso denegado o curso no encontrado.");
}

// 2. Obtener alumnos y notas
$notaDAO = new NotaDAO($db);
$resultado = $notaDAO->obtenerAlumnosYNotasPorCursoBimestre($curso_id, $bimestre);
$competencias = $resultado['competencias'];
$alumnos = $resultado['alumnos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Notas - <?php echo htmlspecialchars($curso_info['nombre_curso']); ?> - SisHonores</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 30px;
            color: #333;
            font-size: 12px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1a56db;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-img {
            width: 65px;
            height: auto;
        }
        .logo-text h1 {
            color: #1a56db;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .logo-text p {
            margin: 2px 0 0;
            color: #6c757d;
            font-size: 11px;
        }
        .report-meta {
            text-align: right;
            font-size: 11px;
            color: #6c757d;
            line-height: 1.4;
        }
        .report-title {
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .info-item {
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        /* Nota color styling matching the web interface */
        .nota-ad {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }
        .nota-a {
            background-color: #fff3cd !important;
            color: #856404;
            font-weight: bold;
        }
        .nota-b {
            background-color: #ffe6cc !important;
            color: #e67e22;
            font-weight: bold;
        }
        .nota-c {
            background-color: #f8d7da !important;
            color: #721c24;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 8px;
        }
        .signature-box p {
            margin: 2px 0;
            font-size: 11px;
        }
        .no-print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            font-size: 14px;
            transition: all 0.3s;
            z-index: 1000;
        }
        .no-print-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                margin: 15px;
            }
            .info-grid {
                background-color: #fff;
                border: 1px solid #ccc;
            }
            th {
                background-color: #2c3e50 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .nota-ad {
                background-color: #d4edda !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .nota-a {
                background-color: #fff3cd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .nota-b {
                background-color: #ffe6cc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .nota-c {
                background-color: #f8d7da !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-section">
            <img src="<?php echo app_url('assets/escudo trasparente.png'); ?>" class="logo-img" alt="Logo Escudo">
            <div class="logo-text">
                <h1>COLEGIO MATEMÁTICO HONORES</h1>
                <p>Formando Líderes con Valores</p>
            </div>
        </div>
        <div class="report-meta">
            <strong>Fecha de Reporte:</strong> <?php echo date('d/m/Y h:i A'); ?><br>
            <strong>Generado por:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?><br>
            <strong>Año Lectivo:</strong> <?php echo htmlspecialchars($curso_info['anio']); ?>
        </div>
    </div>

    <div class="report-title">
        Registro Oficial de Evaluación de Notas
    </div>

    <div class="info-grid">
        <div class="info-item">
            <strong>Curso:</strong> <?php echo htmlspecialchars($curso_info['nombre_curso']); ?>
        </div>
        <div class="info-item">
            <strong>Bimestre:</strong> <?php echo $bimestre; ?>° Bimestre
        </div>
        <div class="info-item">
            <strong>Grado y Sección:</strong> <?php echo htmlspecialchars($curso_info['nombre_grado'] . ' "' . $curso_info['nombre_seccion'] . '"'); ?>
        </div>
        <div class="info-item">
            <strong>Docente:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">#</th>
                <th style="width: 35%;">Apellidos y Nombres</th>
                <th class="text-center" style="width: 12%;"><?php echo htmlspecialchars($competencias[0] ?? 'Competencia 1'); ?></th>
                <th class="text-center" style="width: 12%;"><?php echo htmlspecialchars($competencias[1] ?? 'Competencia 2'); ?></th>
                <th class="text-center" style="width: 12%;"><?php echo htmlspecialchars($competencias[2] ?? 'Competencia 3'); ?></th>
                <th class="text-center" style="width: 10%;">Promedio</th>
                <th style="width: 14%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($alumnos)): ?>
                <tr>
                    <td colspan="7" class="text-center">No hay alumnos registrados en esta sección.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($alumnos as $idx => $alumno): ?>
                    <?php 
                    $prom = $alumno['promedio'];
                    $prom_style = '';
                    if ($prom >= 18) {
                        $prom_style = 'nota-ad';
                    } elseif ($prom >= 14) {
                        $prom_style = 'nota-a';
                    } elseif ($prom >= 11) {
                        $prom_style = 'nota-b';
                    } elseif ($prom > 0) {
                        $prom_style = 'nota-c';
                    }
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $idx + 1; ?></td>
                        <td><?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombres']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($alumno['notas'][0] ?? '-'); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($alumno['notas'][1] ?? '-'); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($alumno['notas'][2] ?? '-'); ?></td>
                        <td class="text-center text-bold <?php echo $prom_style; ?>"><?php echo $prom > 0 ? htmlspecialchars($prom) : '-'; ?></td>
                        <td><span style="font-size: 11px;"><?php echo htmlspecialchars($alumno['observacion']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="text-bold">Prof. <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p>Docente de Aula</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="text-bold">Dirección Académica</p>
            <p>Colegio Matemático Honores</p>
        </div>
    </div>

    <button class="no-print-btn" onclick="window.print()"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
