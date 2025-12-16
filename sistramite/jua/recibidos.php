<?php
session_start();
include("../modelo/conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'JUA') {
    header("Location: ../index.php");
    exit();
}

// Atender documento
if(isset($_POST['atender_id'])){
    $id_documento = intval($_POST['atender_id']);
    mysqli_query($conn, "UPDATE documentos SET estado='ATENDIDO' WHERE id=$id_documento");
    header("Location: recibidos.php");
    exit();
}

// Documentos asignados a JUA
$sql = "SELECT d.id, d.codigo, d.tipo_documento, d.asunto, d.fecha_ingreso, d.estado,
        IFNULL(e.nombres, d.remitente_externo) AS remitente
        FROM documentos d
        LEFT JOIN estudiantes e ON d.id_remitente_est = e.id
        JOIN movimientos m ON m.id_documento=d.id
        JOIN areas a ON m.id_area_destino=a.id
        WHERE a.nombre_area='JUA' AND d.estado IN ('REGISTRADO','EN PROCESO','DERIVADO')
        ORDER BY d.fecha_ingreso DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Documentos Recibidos - JUA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f1f5f9; }
.sidebar { width: 240px; height: 100vh; position: fixed; background: #0d6efd; color: white; padding-top: 20px; }
.sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
.sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
.content { margin-left: 250px; padding: 30px; }
</style>
</head>
<body>
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-briefcase-fill"></i> JUA</h4>
    <hr>
    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="recibidos.php"><i class="bi bi-inbox"></i> Recibidos</a>
    <a href="atendidos.php"><i class="bi bi-check2"></i> Atendidos</a>
    <a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
<h3>Documentos Recibidos / Pendientes</h3>
<div class="card shadow-sm mt-3">
<div class="card-body">
<table class="table table-striped table-bordered align-middle">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Código</th>
<th>Tipo</th>
<th>Asunto</th>
<th>Remitente</th>
<th>Fecha de Ingreso</th>
<th>Estado</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($result) > 0):
$i=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $i++; ?></td>
<td><?= htmlspecialchars($row['codigo']); ?></td>
<td><?= htmlspecialchars($row['tipo_documento']); ?></td>
<td><?= htmlspecialchars($row['asunto']); ?></td>
<td><?= htmlspecialchars($row['remitente']); ?></td>
<td><?= date("d-m-Y H:i", strtotime($row['fecha_ingreso'])); ?></td>
<td>
<?php
$estado=$row['estado']; $badge='secondary';
switch($estado){
case 'REGISTRADO': $badge='secondary'; break;
case 'DERIVADO': $badge='info'; break;
case 'EN PROCESO': $badge='warning'; break;
}
echo "<span class='badge bg-$badge'>$estado</span>";
?>
</td>
<td>
<div class="d-flex gap-1">
<a href="ver_documento.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">
<i class="bi bi-eye"></i>
</a>
<form method="POST" style="display:inline;">
<input type="hidden" name="atender_id" value="<?= $row['id']; ?>">
<button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Desea marcar este documento como atendido?');">
<i class="bi bi-check-circle"></i>
</button>
</form>
</div>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="8" class="text-center">No hay documentos pendientes.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
