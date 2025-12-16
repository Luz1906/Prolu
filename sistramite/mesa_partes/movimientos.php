<?php
session_start();
include("../modelo/conexion.php");

// Solo usuarios de Mesa de Partes
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'MESA_PARTES') {
    header("Location: ../index.php");
    exit();
}

// Procesar envío de documento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_documento = $_POST['id_documento'];
    $id_area_destino = $_POST['id_area_destino'];
    $observacion = $_POST['observacion'];

    // Obtener área de origen (Mesa de Partes)
    $sql_origen = $conn->query("SELECT id_area_origen FROM movimientos WHERE id_documento = $id_documento ORDER BY fecha_envio DESC LIMIT 1");
    $area_origen = $sql_origen->num_rows > 0 ? $sql_origen->fetch_assoc()['id_area_origen'] : NULL;

    // Insertar movimiento
    $stmt = $conn->prepare("INSERT INTO movimientos (id_documento, id_area_origen, id_area_destino, observacion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_documento, $area_origen, $id_area_destino, $observacion);

    if ($stmt->execute()) {
        // Actualizar estado del documento
        $conn->query("UPDATE documentos SET estado='DERIVADO' WHERE id=$id_documento");
        $mensaje = "Documento derivado correctamente.";
    } else {
        $mensaje = "Error al derivar documento: " . $stmt->error;
    }
}

// Consultar documentos registrados que estén en estado REGISTRADO o DERIVADO
$docs = $conn->query("SELECT id, codigo, tipo_documento, asunto, estado FROM documentos WHERE estado IN ('REGISTRADO','DERIVADO') ORDER BY fecha_ingreso DESC");

// Consultar áreas activas
$areas = $conn->query("SELECT id, nombre_area FROM areas WHERE estado='ACTIVO' ORDER BY nombre_area ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Derivar Documentos - Mesa de Partes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #198754; color: white; padding-top: 20px; }
        .sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; }
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
    <h3>Derivar Documentos</h3>

    <?php if(isset($mensaje)): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Documento</label>
                <select name="id_documento" class="form-select" required>
                    <option value="">-- Seleccionar Documento --</option>
                    <?php while($doc = $docs->fetch_assoc()): ?>
                        <option value="<?= $doc['id'] ?>"><?= $doc['codigo'] ?> - <?= $doc['asunto'] ?> (<?= $doc['estado'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Área Destino</label>
                <select name="id_area_destino" class="form-select" required>
                    <option value="">-- Seleccionar Área --</option>
                    <?php while($area = $areas->fetch_assoc()): ?>
                        <option value="<?= $area['id'] ?>"><?= $area['nombre_area'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Observación</label>
                <input type="text" name="observacion" class="form-control" placeholder="Comentario opcional">
            </div>
        </div>

        <button type="submit" class="btn btn-warning mt-3"><i class="bi bi-send-fill"></i> Derivar Documento</button>
    </form>

    <h5>Documentos Recientes</h5>
    <table class="table table-striped table-hover">
        <thead class="table-success">
            <tr>
                <th>Código</th>
                <th>Asunto</th>
                <th>Tipo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $docs = $conn->query("SELECT id, codigo, tipo_documento, asunto, estado FROM documentos ORDER BY fecha_ingreso DESC LIMIT 10");
            while($doc = $docs->fetch_assoc()): ?>
                <tr>
                    <td><?= $doc['codigo'] ?></td>
                    <td><?= $doc['asunto'] ?></td>
                    <td><?= $doc['tipo_documento'] ?></td>
                    <td><?= $doc['estado'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
