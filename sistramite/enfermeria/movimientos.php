<?php
session_start();
include("../modelo/conexion.php");

// Roles permitidos
$roles_permitidos = ['JUA', 'COORD ENFERMERIA', 'COORD TOPO', 'COORD INFORMATICA'];
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
    header("Location: ../index.php");
    exit();
}

// Obtener todas las áreas para filtros
$areas = $conexion->query("SELECT * FROM areas WHERE estado='ACTIVO' ORDER BY nombre_area ASC");

// Filtros
$filtro_origen = $_GET['origen'] ?? '';
$filtro_destino = $_GET['destino'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construir consulta
$sql = "SELECT m.id as id_movimiento, d.codigo, d.tipo_documento, d.asunto, m.fecha_envio, m.fecha_recepcion,
               m.estado, a1.nombre_area as area_origen, a2.nombre_area as area_destino
        FROM movimientos m
        INNER JOIN documentos d ON m.id_documento=d.id
        LEFT JOIN areas a1 ON m.id_area_origen=a1.id
        LEFT JOIN areas a2 ON m.id_area_destino=a2.id
        WHERE 1=1";

if ($filtro_origen != '') $sql .= " AND m.id_area_origen='$filtro_origen'";
if ($filtro_destino != '') $sql .= " AND m.id_area_destino='$filtro_destino'";
if ($filtro_estado != '') $sql .= " AND m.estado='$filtro_estado'";

$sql .= " ORDER BY m.fecha_envio DESC";

$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos de Documentos</title>
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
    <h3>Movimientos de Documentos</h3>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label>Área Origen</label>
            <select name="origen" class="form-select">
                <option value="">Todos</option>
                <?php while($a = $areas->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>" <?= ($filtro_origen==$a['id'])?'selected':'' ?>><?= $a['nombre_area'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Área Destino</label>
            <select name="destino" class="form-select">
                <option value="">Todos</option>
                <?php 
                $areas2 = $conexion->query("SELECT * FROM areas WHERE estado='ACTIVO' ORDER BY nombre_area ASC");
                while($a = $areas2->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>" <?= ($filtro_destino==$a['id'])?'selected':'' ?>><?= $a['nombre_area'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="ENVIADO" <?= ($filtro_estado=='ENVIADO')?'selected':'' ?>>ENVIADO</option>
                <option value="RECEPCIONADO" <?= ($filtro_estado=='RECEPCIONADO')?'selected':'' ?>>RECEPCIONADO</option>
                <option value="FINALIZADO" <?= ($filtro_estado=='FINALIZADO')?'selected':'' ?>>FINALIZADO</option>
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-warning">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Tipo Documento</th>
                <th>Asunto</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha Envío</th>
                <th>Fecha Recepción</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows>0): $i=1; ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['codigo']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_documento']) ?></td>
                        <td><?= htmlspecialchars($row['asunto']) ?></td>
                        <td><?= $row['area_origen'] ?? '---' ?></td>
                        <td><?= $row['area_destino'] ?? '---' ?></td>
                        <td><?= $row['fecha_envio'] ?></td>
                        <td><?= $row['fecha_recepcion'] ?? '---' ?></td>
                        <td><?= $row['estado'] ?></td>
                        <td><a href="detalle_documento.php?id=<?= $row['id_movimiento'] ?>" class="btn btn-sm btn-primary">Ver</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center">No hay movimientos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
