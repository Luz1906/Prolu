<?php
session_start();
include("../modelo/conexion.php");

// Solo JUA
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'JUA') {
    header("Location: ../index.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: recibidos.php");
    exit();
}

$id_documento = intval($_GET['id']);

// Consulta del documento asignado a JUA
$sql_doc = "SELECT d.*, IFNULL(CONCAT(e.nombres,' ',e.apellidos), d.remitente_externo) AS remitente_completo
            FROM documentos d
            LEFT JOIN estudiantes e ON d.id_remitente_est = e.id
            JOIN movimientos m ON m.id_documento = d.id
            JOIN areas a ON m.id_area_destino = a.id
            WHERE d.id = $id_documento AND a.nombre_area='JUA'";
$documento = mysqli_fetch_assoc(mysqli_query($conn, $sql_doc));

// Obtener archivos adjuntos
$adjuntos = mysqli_query($conn, "SELECT * FROM adjuntos WHERE id_documento=$id_documento");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Documento - JUA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f1f5f9; }
.content { margin-left: 20px; padding: 30px; }
</style>
</head>
<body>

<div class="content">
<h3>Detalle del Documento</h3>

<?php if($documento): ?>
<div class="card shadow-sm mt-3">
<div class="card-body">
<p><strong>Código:</strong> <?= htmlspecialchars($documento['codigo']); ?></p>
<p><strong>Tipo:</strong> <?= htmlspecialchars($documento['tipo_documento']); ?></p>
<p><strong>Asunto:</strong> <?= htmlspecialchars($documento['asunto']); ?></p>
<p><strong>Descripción:</strong> <?= htmlspecialchars($documento['descripcion']); ?></p>
<p><strong>Remitente:</strong> <?= htmlspecialchars($documento['remitente_completo']); ?></p>
<p><strong>Estado:</strong> 
<?php
$estado=$documento['estado']; $badge='secondary';
switch($estado){
    case 'REGISTRADO': $badge='secondary'; break;
    case 'DERIVADO': $badge='info'; break;
    case 'EN PROCESO': $badge='warning'; break;
    case 'ATENDIDO': $badge='success'; break;
}
echo "<span class='badge bg-$badge'>$estado</span>";
?>
</p>

<?php if(mysqli_num_rows($adjuntos) > 0): ?>
<p><strong>Archivos Adjuntos:</strong></p>
<ul>
<?php while($archivo = mysqli_fetch_assoc($adjuntos)): ?>
<li><a href="../<?= htmlspecialchars($archivo['ruta']); ?>" target="_blank"><?= htmlspecialchars($archivo['nombre_archivo']); ?></a></li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p><strong>Archivos Adjuntos:</strong> No hay archivos.</p>
<?php endif; ?>

<a href="recibidos.php" class="btn btn-secondary mt-2"><i class="bi bi-arrow-left"></i> Volver</a>

</div>
</div>

<?php else: ?>
<div class="alert alert-warning mt-3">
No se encontró el documento o no tiene permisos para verlo.
</div>
<a href="recibidos.php" class="btn btn-secondary mt-2"><i class="bi bi-arrow-left"></i> Volver</a>
<?php endif; ?>

</div>
</body>
</html>
