<?php
session_start();
include("../modelo/conexion.php");

// Solo usuarios de Mesa de Partes
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'MESA_PARTES') {
    header("Location: ../index.php");
    exit();
}

// Consultar documentos
$sql = "SELECT d.id, d.codigo, d.tipo_documento, d.asunto, d.fecha_ingreso, d.estado,
        e.nombres AS est_nombres, e.apellidos AS est_apellidos, e.dni AS est_dni,
        d.id_remitente_est, d.remitente_externo, d.dni_externo
        FROM documentos d
        LEFT JOIN estudiantes e ON d.id_remitente_est = e.id
        ORDER BY d.fecha_ingreso DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Ingresados - Mesa de Partes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #198754; color: white; padding-top: 20px; }
        .sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; }
        table td, table th { vertical-align: middle; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-inboxes"></i> Mesa Partes</h4>
    <hr>
    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="registrar_doc.php"><i class="bi bi-file-earmark-plus"></i> Registrar Documento</a>
    <a href="lista_documentos.php"><i class="bi bi-folder"></i> Documentos Ingresados</a>
    <a href="movimientos.php"><i class="bi bi-send"></i> Enviar a Áreas</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
    <h3>Documentos Ingresados</h3>

    <table class="table table-striped table-hover">
        <thead class="table-success">
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Asunto</th>
                <th>Remitente</th>
                <th>Fecha Ingreso</th>
                <th>Estado</th>
                <th>Archivos</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['codigo'] ?></td>
                    <td><?= $row['tipo_documento'] ?></td>
                    <td><?= $row['asunto'] ?></td>
                    <td>
                        <?php 
                        if (!empty($row['id_remitente_est'])) {
                            echo ($row['est_nombres'] ?? '') . " " . ($row['est_apellidos'] ?? '') . " - " . ($row['est_dni'] ?? '');
                        } else {
                            echo ($row['remitente_externo'] ?? '') . " - " . ($row['dni_externo'] ?? '');
                        }
                        ?>
                    </td>
                    <td><?= date("d/m/Y H:i", strtotime($row['fecha_ingreso'])) ?></td>
                    <td><?= $row['estado'] ?></td>
                    <td>
                        <?php
                        // Consultar archivos adjuntos
                        $doc_id = $row['id'];
                        $adjuntos = $conn->query("SELECT * FROM adjuntos WHERE id_documento = $doc_id");
                        if ($adjuntos->num_rows > 0) {
                            while($file = $adjuntos->fetch_assoc()) {
                                echo "<a href='{$file['ruta']}' target='_blank' class='btn btn-sm btn-outline-primary mb-1'>{$file['nombre_archivo']}</a><br>";
                            }
                        } else {
                            echo "Sin archivos";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
