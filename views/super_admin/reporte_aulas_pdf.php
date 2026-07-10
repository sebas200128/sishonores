<?php
// vista/super_admin/reporte_aulas_pdf.php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/Aula.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'SuperUsuario' && $_SESSION['user_role'] != 'Director')) {
    header('Location: index.php?controller=login');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$aulaModel = new Aula($db);

// Obtener datos de aulas del Modelo
$aulas = $aulaModel->listarTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Aulas Asignadas - SisHonores</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 30px;
            color: #333;
            font-size: 13px;
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
            font-size: 12px;
        }
        .report-meta {
            text-align: right;
            font-size: 11px;
            color: #6c757d;
        }
        .report-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-center {
            text-align: center;
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
            <strong>Año Lectivo:</strong> 2026
        </div>
    </div>

    <div class="report-title">
        Reporte de Aulas Habilitadas
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">#</th>
                <th style="width: 25%;">Nivel Educativo</th>
                <th style="width: 25%;">Grado</th>
                <th style="width: 15%;">Sección</th>
                <th class="text-center" style="width: 15%;">Año Lectivo</th>
                <th class="text-center" style="width: 12%;">Vacantes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aulas)): ?>
                <tr>
                    <td colspan="6" class="text-center">No hay aulas registradas en el sistema.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($aulas as $idx => $aula): ?>
                    <tr>
                        <td class="text-center"><?php echo $idx + 1; ?></td>
                        <td><?php echo htmlspecialchars($aula['nombre_nivel']); ?></td>
                        <td><strong><?php echo htmlspecialchars($aula['nombre_grado']); ?></strong></td>
                        <td><?php echo htmlspecialchars($aula['nombre_seccion']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($aula['anio']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($aula['vacantes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

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
