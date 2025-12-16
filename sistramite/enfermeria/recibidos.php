<?php
session_start();

// Incluir conexión a la base de datos
include("../modelo/conexion.php");

// Verificar conexión
if (!isset($conexion)) {
    die("Error: no se pudo conectar a la base de datos.");
}

// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'COORD ENFERMERIA') {
    header("Location: ../index.php");
    exit();
}

// ID de la coordinación de Enfermería (ajústalo según tu base de datos)
$id_area_enfermeria = 6;

// Consulta: documentos que han sido enviados a esta área
$sql = "SELECT d.id, d.codigo, d.tipo_documento, d.asunto, d.fecha_ingreso, m.estado AS estado_movimiento
        FROM documentos d
        INNER JOIN movimientos m ON d.id = m.id_documento
        WHERE m.id_area_destino = '$id_area_enfermeria'
          AND m.estado = 'ENVIADO'
        ORDER BY d.fecha_ingreso DESC";

$result = $conexion->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Recibidos - Enfermería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .sidebar {
            width: 240px; height: 100vh; position: fixed;
            background: #dc3545; color: white; padding-top: 20px;
        }
        .sidebar a {
            color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none;
        }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; }
        table { background: white; }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-heart-pulse"></i> Enfermería</h4>
    <hr>
    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="recibidos.php"><i class="bi bi-inbox"></i> Recibidos</a>
    <a href="atendidos.php"><i class="bi bi-check-circle"></i> Atendidos</a>
    <a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
    <h3>Bienvenida, <?php echo $_SESSION['nombres']; ?></h3>

    <h5 class="mt-4">Documentos Recibidos</h5>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-primary">
            <tr>
                <th>Código</th>
                <th>Tipo Documento</th>
                <th>Asunto</th>
                <th>Fecha Ingreso</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['codigo']); ?></td>
                    <td><?= htmlspecialchars($row['tipo_documento']); ?></td>
                    <td><?= htmlspecialchars($row['asunto']); ?></td>
                    <td><?= $row['fecha_ingreso']; ?></td>
                    <td><?= $row['estado_movimiento']; ?></td>
                    <td>
                        <a href="detalle_documento.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">
                            Ver Detalle
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay documentos recibidos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
