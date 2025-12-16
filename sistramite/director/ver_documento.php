<?php
session_start();
include("../modelo/conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'DIRECTOR') {
    header("Location: ../index.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: documentos_pendientes.php");
    exit();
}

$id_documento = intval($_GET['id']);

// Atender documento
if(isset($_POST['atender'])){
    mysqli_query($conn, "UPDATE documentos SET estado='ATENDIDO' WHERE id=$id_documento");
    header("Location: ver_documento.php?id=$id_documento");
    exit();
}

// Consulta del documento
$sql_doc = "SELECT d.*, IFNULL(CONCAT(e.nombres,' ',e.apellidos),d.remitente_externo) AS remitente_completo
            FROM documentos d
            LEFT JOIN estudiantes e ON d.id_remitente_est = e.id
            WHERE d.id = $id_documento";
$documento = mysqli_fetch_assoc(mysqli_query($conn, $sql_doc));
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Documento - Director</title>
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
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <p><strong>Código:</strong> <?= htmlspecialchars($documento['codigo']); ?></p>
            <p><strong>Tipo:</strong> <?= htmlspecialchars($documento['tipo_documento']); ?></p>
            <p><strong>Asunto:</strong> <?= htmlspecialchars($documento['asunto']); ?></p>
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

            <?php if($documento['estado'] != 'ATENDIDO'): ?>
            <form method="POST" class="mt-3">
                <button type="submit" name="atender" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Marcar como Atendido
                </button>
            </form>
            <?php else: ?>
            <p class="text-success fw-bold mt-2"><i class="bi bi-check-circle"></i> Documento ya atendido</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
