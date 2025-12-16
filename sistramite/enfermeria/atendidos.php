<?php
session_start();
include("../modelo/conexion.php");

// Roles permitidos
$roles_permitidos = ['JUA', 'COORD ENFERMERIA', 'COORD TOPO', 'COORD INFORMATICA'];
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
    header("Location: ../index.php");
    exit();
}

// Área según rol
switch ($_SESSION['rol']) {
    case 'JUA': $nombre_area='JUA'; break;
    case 'COORD ENFERMERIA': $nombre_area='Coordinación de Enfermería Técnica'; break;
    case 'COORD TOPO': $nombre_area='Coordinación de topografia'; break;
    case 'COORD INFORMATICA': $nombre_area='Coordinación de Computación e Informática'; break;
    default: $nombre_area=''; break;
}
$area = $conexion->query("SELECT id FROM areas WHERE nombre_area='$nombre_area'")->fetch_assoc();
$id_area = $area['id'] ?? 0;

// Documentos finalizados
$sql = "SELECT m.id as id_movimiento, d.codigo, d.tipo_documento, d.asunto, d.fecha_ingreso, m.estado
        FROM movimientos m
        INNER JOIN documentos d ON m.id_documento = d.id
        WHERE m.id_area_destino = $id_area AND m.estado='FINALIZADO'
        ORDER BY m.fecha_envio DESC";
$query = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Atendidos</title>
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
    <h3>Documentos Atendidos</h3>
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Tipo Documento</th>
                <th>Asunto</th>
                <th>Fecha Ingreso</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if($query->num_rows>0): $i=1; ?>
                <?php while($row=$query->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['codigo']); ?></td>
                        <td><?= htmlspecialchars($row['tipo_documento']); ?></td>
                        <td><?= htmlspecialchars($row['asunto']); ?></td>
                        <td><?= $row['fecha_ingreso']; ?></td>
                        <td><span class="badge bg-success"><?= $row['estado'] ?></span></td>
                        <td><a href="detalle_documento.php?id=<?= $row['id_movimiento'] ?>" class="btn btn-sm btn-primary">Ver</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No hay documentos atendidos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
