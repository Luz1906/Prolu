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

// Procesar acciones
if(isset($_POST['atender_id'])){
    $id = intval($_POST['atender_id']);
    mysqli_query($conn, "UPDATE documentos SET estado='ATENDIDO' WHERE id=$id");
    header("Location: documentos_pendientes.php");
    exit();
}

if(isset($_POST['derivar_id'])){
    $id = intval($_POST['derivar_id']);
    $id_area_destino = intval($_POST['area_destino']);
    $observacion = mysqli_real_escape_string($conn, $_POST['observacion']);
    mysqli_query($conn, "INSERT INTO movimientos (id_documento, id_area_origen, id_area_destino, observacion, estado) 
        VALUES ($id, (SELECT id FROM areas WHERE nombre_area='$area_usuario'), $id_area_destino, '$observacion','ENVIADO')");
    mysqli_query($conn, "UPDATE documentos SET estado='DERIVADO' WHERE id=$id");
    header("Location: documentos_pendientes.php");
    exit();
}

if(isset($_POST['finalizar_id'])){
    $id = intval($_POST['finalizar_id']);
    mysqli_query($conn, "UPDATE documentos SET estado='ARCHIVADO' WHERE id=$id");
    header("Location: documentos_pendientes.php");
    exit();
}

// Consultar documentos pendientes para el área
$sql = "SELECT d.id, d.codigo, d.tipo_documento, d.asunto, d.fecha_ingreso, d.estado,
        IFNULL(e.nombres, d.remitente_externo) AS remitente
        FROM documentos d
        LEFT JOIN estudiantes e ON d.id_remitente_est = e.id
        LEFT JOIN movimientos m ON d.id = m.id_documento
        LEFT JOIN areas a ON m.id_area_destino = a.id
        WHERE (d.estado IN ('REGISTRADO','EN PROCESO','DERIVADO') AND (a.nombre_area='$area_usuario' OR a.nombre_area IS NULL))
        ORDER BY d.fecha_ingreso ASC";
$result = mysqli_query($conn, $sql);

// Obtener áreas disponibles para derivar (excepto área propia)
$areas = mysqli_query($conn, "SELECT * FROM areas WHERE nombre_area != '$area_usuario'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Documentos Pendientes - <?= htmlspecialchars($rol); ?></title>
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
<h4 class="text-center"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($rol); ?></h4>
<hr>
<a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
<a href="documentos_pendientes.php"><i class="bi bi-hourglass-split"></i> Pendientes</a>
<a href="documentos_atendidos.php"><i class="bi bi-check2-circle"></i> Atendidos</a>
<a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
<a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
<h3>Documentos Pendientes / En Proceso</h3>
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
<th>Fecha</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($result) > 0): $i=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $i++; ?></td>
<td><?= htmlspecialchars($row['codigo']); ?></td>
<td><?= htmlspecialchars($row['tipo_documento']); ?></td>
<td><?= htmlspecialchars($row['asunto']); ?></td>
<td><?= htmlspecialchars($row['remitente']); ?></td>
<td><?= date("d-m-Y H:i", strtotime($row['fecha_ingreso'])); ?></td>
<td>
<?php
$estado = $row['estado']; $badge='secondary';
switch($estado){
    case 'REGISTRADO': $badge='secondary'; break;
    case 'EN PROCESO': $badge='warning'; break;
    case 'DERIVADO': $badge='info'; break;
}
echo "<span class='badge bg-$badge'>$estado</span>";
?>
</td>
<td>
<!-- Atender -->
<form method="POST" style="display:inline;">
<input type="hidden" name="atender_id" value="<?= $row['id']; ?>">
<button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-circle"></i> Atender</button>
</form>

<!-- Derivar -->
<button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDerivar<?= $row['id']; ?>"><i class="bi bi-arrow-right-circle"></i> Derivar</button>
<div class="modal fade" id="modalDerivar<?= $row['id']; ?>" tabindex="-1">
<div class="modal-dialog"><div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5 class="modal-title">Derivar Documento</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="derivar_id" value="<?= $row['id']; ?>">
<div class="mb-3">
<label>Área Destino:</label>
<select class="form-select" name="area_destino" required>
<option value="">-- Seleccione Área --</option>
<?php mysqli_data_seek($areas,0); while($area=mysqli_fetch_assoc($areas)): ?>
<option value="<?= $area['id']; ?>"><?= htmlspecialchars($area['nombre_area']); ?></option>
<?php endwhile; ?>
</select>
</div>
<div class="mb-3">
<label>Observación:</label>
<textarea class="form-control" name="observacion" required></textarea>
</div>
</div>
<div class="modal-footer">
<button type="submit" class="btn btn-info">Derivar</button>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
</div>
</form>
</div></div></div>

<!-- Finalizar -->
<form method="POST" style="display:inline;">
<input type="hidden" name="finalizar_id" value="<?= $row['id']; ?>">
<button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('¿Desea finalizar este documento?');">
<i class="bi bi-file-earmark-check"></i> Finalizar
</button>
</form>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
