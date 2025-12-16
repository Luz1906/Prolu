<?php
session_start();
include("../modelo/conexion.php");

// Roles permitidos
$roles_permitidos = ['JUA', 'COORD ENFERMERIA', 'COORD TOPO', 'COORD INFORMATICA'];
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
    header("Location: ../index.php");
    exit();
}

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Documento no especificado."; exit();
}
$id_mov = (int)$_GET['id'];

// Obtener documento y movimiento
$sql = "SELECT m.id AS id_mov, m.estado AS estado_mov, m.fecha_envio, m.fecha_recepcion,
               d.codigo, d.tipo_documento, d.asunto, d.descripcion, d.fecha_ingreso
        FROM movimientos m
        INNER JOIN documentos d ON m.id_documento=d.id
        WHERE m.id=$id_mov LIMIT 1";
$result = $conexion->query($sql);
if ($result->num_rows==0){ echo "Documento no encontrado."; exit(); }
$doc = $result->fetch_assoc();

// Movimientos del documento
$sql_mov="SELECT m.*, a1.nombre_area AS origen, a2.nombre_area AS destino
          FROM movimientos m
          LEFT JOIN areas a1 ON m.id_area_origen=a1.id
          LEFT JOIN areas a2 ON m.id_area_destino=a2.id
          WHERE m.id_documento=(SELECT id_documento FROM movimientos WHERE id=$id_mov)
          ORDER BY m.fecha_envio ASC";
$movimientos=$conexion->query($sql_mov);

// Archivos adjuntos
$sql_adj="SELECT * FROM adjuntos WHERE id_documento=(SELECT id_documento FROM movimientos WHERE id=$id_mov)";
$adjuntos=$conexion->query($sql_adj);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0d6efd; color: white; padding-top: 20px; }
        .sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; }
        table { background: white; }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-briefcase-fill"></i> <?= $_SESSION['rol'] ?></h4>
    <hr>
    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="recibidos.php"><i class="bi bi-inbox"></i> Recibidos</a>
    <a href="atendidos.php"><i class="bi bi-check2"></i> Atendidos</a>
    <a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
    <h3>Detalle del Documento: <?= htmlspecialchars($doc['codigo']); ?></h3>

    <p><strong>Tipo:</strong> <?= htmlspecialchars($doc['tipo_documento']); ?></p>
    <p><strong>Asunto:</strong> <?= htmlspecialchars($doc['asunto']); ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($doc['descripcion']); ?></p>
    <p><strong>Fecha Ingreso:</strong> <?= $doc['fecha_ingreso']; ?></p>
    <p><strong>Estado:</strong> <?= $doc['estado_mov']; ?></p>

    <!-- Botones de acción -->
    <?php if($doc['estado_mov']=='ENVIADO'): ?>
        <a href="recibidos.php?accion=recepcionar&id=<?= $doc['id_mov'] ?>" class="btn btn-info mb-3">Recepcionar Documento</a>
    <?php elseif($doc['estado_mov']=='RECEPCIONADO'): ?>
        <a href="recibidos.php?accion=finalizar&id=<?= $doc['id_mov'] ?>" class="btn btn-success mb-3">Finalizar Documento</a>
    <?php else: ?>
        <span class="badge bg-success mb-3">Documento Finalizado</span>
    <?php endif; ?>

    <h5>Historial de Movimientos</h5>
    <table class="table table-bordered mt-2">
        <thead class="table-primary">
            <tr><th>Origen</th><th>Destino</th><th>Fecha Envío</th><th>Fecha Recepción</th><th>Estado</th></tr>
        </thead>
        <tbody>
            <?php while($m=$movimientos->fetch_assoc()): ?>
                <tr>
                    <td><?= $m['origen'] ?? '---' ?></td>
                    <td><?= $m['destino'] ?? '---' ?></td>
                    <td><?= $m['fecha_envio'] ?></td>
                    <td><?= $m['fecha_recepcion'] ?? '---' ?></td>
                    <td><?= $m['estado'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h5>Archivos Adjuntos</h5>
    <ul>
        <?php while($a=$adjuntos->fetch_assoc()): ?>
            <li><a href="<?= $a['ruta'] ?>" target="_blank"><?= htmlspecialchars($a['nombre_archivo']) ?></a></li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>
