<?php
session_start();
include("../modelo/conexion.php"); // Asegúrate de tener tu conexión a la base de datos

// Solo usuarios de Mesa de Partes
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'MESA_PARTES') {
    header("Location: ../index.php");
    exit();
}

// Función para generar un código único de documento
function generarCodigo() {
    return "DOC-" . date("YmdHis");
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_documento = $_POST['tipo_documento'];
    $asunto = $_POST['asunto'];
    $descripcion = $_POST['descripcion'];
    $id_remitente_est = $_POST['id_remitente_est'] != "" ? $_POST['id_remitente_est'] : NULL;
    $remitente_externo = $_POST['remitente_externo'] != "" ? $_POST['remitente_externo'] : NULL;
    $dni_externo = $_POST['dni_externo'] != "" ? $_POST['dni_externo'] : NULL;

    $codigo = generarCodigo();

    $stmt = $conn->prepare("INSERT INTO documentos (codigo, tipo_documento, asunto, descripcion, id_remitente_est, remitente_externo, dni_externo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $codigo, $tipo_documento, $asunto, $descripcion, $id_remitente_est, $remitente_externo, $dni_externo);

    if ($stmt->execute()) {
        $mensaje = "Documento registrado correctamente con código: $codigo";
    } else {
        $mensaje = "Error al registrar el documento: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Documento - Mesa de Partes</title>
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
    <h3>Registrar Documento</h3>

    <?php if(isset($mensaje)): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Tipo de Documento</label>
            <select name="tipo_documento" class="form-select" required>
                <option value="SOLICITUD">SOLICITUD</option>
                <option value="CARTA">CARTA</option>
                <option value="OFICIO">OFICIO</option>
                <option value="MEMORANDO">MEMORANDO</option>
                <option value="INFORME">INFORME</option>
                <option value="CONSTANCIA">CONSTANCIA</option>
                <option value="OTRO">OTRO</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Asunto</label>
            <input type="text" name="asunto" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Remitente (Estudiante)</label>
            <select name="id_remitente_est" class="form-select">
                <option value="">-- Ninguno --</option>
                <?php
                $res = $conn->query("SELECT id, nombres, apellidos, dni FROM estudiantes");
                while($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombres']} {$row['apellidos']} - {$row['dni']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Remitente Externo</label>
            <input type="text" name="remitente_externo" class="form-control">
        </div>

        <div class="mb-3">
            <label>DNI Externo</label>
            <input type="text" name="dni_externo" class="form-control" maxlength="8">
        </div>

        <button type="submit" class="btn btn-success">Registrar Documento</button>
    </form>
</div>

</body>
</html>
