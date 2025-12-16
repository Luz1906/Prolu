<?php
session_start();
include("../modelo/conexion.php");

// Verificar sesión
if(!isset($_SESSION['rol'])){
    header("Location: ../index.php");
    exit();
}

// Mapear rol a área
$rol = $_SESSION['rol'];
$area_usuario = '';
switch($rol){
    case 'DIRECTOR': $area_usuario='Dirección General'; break;
    case 'SECRETARIA ACADEMICA': $area_usuario='Secretaría Académica'; break;
    case 'JUA': $area_usuario='JUA'; break;
    case 'COORD INFORMATICA': $area_usuario='Coordinación de Computación e Informática'; break;
    case 'COORD ENFERMERIA': $area_usuario='Coordinación de Enfermería Técnica'; break;
    case 'COORD TOPO': $area_usuario='Coordinación de topografia'; break;
    case 'MESA_PARTES': $area_usuario='Mesa de Partes'; break;
    default: die("Rol sin área asignada."); 
}

// Marcar recepción de documento
if(isset($_POST['recepcion_id'])){
    $id_mov = intval($_POST['recepcion_id']);
    mysqli_query($conn, "UPDATE movimientos SET fecha_recepcion=NOW(), estado='RECEPCIONADO' WHERE id=$id_mov");
    mysqli_query($conn, "UPDATE documentos SET estado='EN PROCESO' WHERE id=(SELECT id_documento FROM movimientos WHERE id=$id_mov)");
    header("Location: movimientos.php");
    exit();
}

// Consultar movimientos relacionados con el área del usuario
$sql = "SELECT m.id, d.codigo, d.tipo_documento, d.asunto, 
        a1.nombre_area AS area_origen, a2.nombre_area AS area_destino,
        m.fecha_envio, m.fecha_recepcion, m.estado, m.observacion
        FROM movimientos m
        INNER JOIN documentos d ON m.id_documento=d.id
        LEFT JOIN areas a1 ON m.id_area_origen=a1.id
        LEFT JOIN areas a2 ON m.id_area_destino=a2.id
        WHERE a1.nombre_area='$area_usuario' OR a2.nombre_area='$area_usuario'
        ORDER BY m.fecha_envio DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Movimientos - <?= htmlspecialchars($rol); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f1f5f9; }
.sidebar { width: 240px; height: 100vh; position: fixed; background: #6f42c1; color: white; padding-top: 20px; }
.sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
.sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
.content { margin-left: 250px; padding: 30px; }
</style>
</head>
<body>

<div class="sidebar">
<h4 class="text-center"><i class="bi bi-arrow-left-right"></i> <?= htmlspecialchars($rol); ?></h4>
<hr>
<a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
<a href="documentos_pendientes.php"><i class="bi bi-hourglass-split"></i> Pendientes</a>
<a href="documentos_atendidos.php"><i class="bi bi-check2-circle"></i> Atendidos</a>
<a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
<a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
<h3>Movimientos de Documentos</h3>
<div class="card shadow-sm mt-3">
<div class="card-body">
<table class="table table-striped table-bordered align-middle">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Código</th>
<th>Tipo</th>
<th>Asunto</th>
<th>Área Origen</th>
<th>Área Destino</th>
<th>Fecha Envío</th>
<th>Fecha Recepción</th>
<th>Estado</th>
<th>Observación</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($result)>0): $i=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $i++; ?></td>
<td><?= htmlspecialchars($row['codigo']); ?></td>
<td><?= htmlspecialchars($row['tipo_documento']); ?></td>
<td><?= htmlspecialchars($row['asunto']); ?></td>
<td><?= htmlspecialchars($row['area_origen']); ?></td>
<td><?= htmlspecialchars($row['area_destino']); ?></td>
<td><?= date("d-m-Y H:i", strtotime($row['fecha_envio'])); ?></td>
<td><?= $row['fecha_recepcion'] ? date("d-m-Y H:i", strtotime($row['fecha_recepcion'])) : '-'; ?></td>
<td>
<?php
$estado = $row['estado']; $badge='secondary';
switch($estado){
    case 'ENVIADO': $badge='warning'; break;
    case 'RECEPCIONADO': $badge='success'; break;
    case 'FINALIZADO': $badge='dark'; break;
}
echo "<span class='badge bg-$badge'>$estado</span>";
?>
</td>
<td><?= htmlspecialchars($row['observacion']); ?></td>
<td>
<?php if($row['estado']=='ENVIADO' && $row['area_destino']==$area_usuario): ?>
<form method="POST">
<input type="hidden" name="recepcion_id" value="<?= $row['id']; ?>">
<button type="submit" class="btn btn-sm btn-success"><i class="bi bi-box-arrow-in-down"></i> Recepcionar</button>
</form>
<?php else: ?>
-
<?php endif; ?>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="11" class="text-center">No hay movimientos registrados.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
