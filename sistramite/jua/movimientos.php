<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";      // Cambia si tu usuario es otro
$pass = "";          // Cambia si tienes contraseña
$db   = "tramite_iest";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Solo usuarios JUA
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'JUA') {
    header("Location: ../index.php");
    exit();
}

// Procesar acciones de movimiento
if(isset($_GET['accion'], $_GET['id']) && is_numeric($_GET['id'])){
    $id_mov = $_GET['id'];
    if($_GET['accion'] === 'recepcionar'){
        $conexion->query("UPDATE movimientos SET estado='RECEPCIONADO', fecha_recepcion=NOW() WHERE id=$id_mov");
    } elseif($_GET['accion'] === 'finalizar'){
        $conexion->query("UPDATE movimientos SET estado='FINALIZADO', fecha_recepcion=NOW() WHERE id=$id_mov");
        $id_doc = $conexion->query("SELECT id_documento FROM movimientos WHERE id=$id_mov")->fetch_assoc()['id_documento'];
        $conexion->query("UPDATE documentos SET estado='ATENDIDO' WHERE id=$id_doc");
    }
    header("Location: movimientos.php");
    exit();
}

// Obtener áreas para el formulario de derivación
$areas = $conexion->query("SELECT * FROM areas WHERE estado='ACTIVO'");

// Registrar nuevo movimiento
if(isset($_POST['registrar_movimiento'])){
    $id_documento = $_POST['id_documento'];
    $id_area_destino = $_POST['id_area_destino'];
    $observacion = $_POST['observacion'];
    
    $area_jua = $conexion->query("SELECT id FROM areas WHERE nombre_area='JUA'")->fetch_assoc()['id'];
    
    $stmt = $conexion->prepare("INSERT INTO movimientos (id_documento, id_area_origen, id_area_destino, observacion) VALUES (?,?,?,?)");
    $stmt->bind_param("iiis", $id_documento, $area_jua, $id_area_destino, $observacion);
    $stmt->execute();
    $stmt->close();

    $conexion->query("UPDATE documentos SET estado='EN PROCESO' WHERE id=$id_documento");

    header("Location: movimientos.php");
    exit();
}

// Consultar movimientos
$sql = "SELECT m.id, d.codigo, d.tipo_documento, d.asunto,
        a_origen.nombre_area AS area_origen,
        a_destino.nombre_area AS area_destino,
        m.observacion, m.fecha_envio, m.fecha_recepcion, m.estado
        FROM movimientos m
        INNER JOIN documentos d ON m.id_documento = d.id
        LEFT JOIN areas a_origen ON m.id_area_origen = a_origen.id
        INNER JOIN areas a_destino ON m.id_area_destino = a_destino.id
        ORDER BY m.fecha_envio DESC";

$query = $conexion->query($sql);

// Documentos disponibles para derivar
$docs = $conexion->query("SELECT * FROM documentos WHERE estado IN ('REGISTRADO','EN PROCESO')");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos - JUA</title>
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
    <h4 class="text-center"><i class="bi bi-briefcase-fill"></i> JUA</h4>
    <hr>
    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="recibidos.php"><i class="bi bi-inbox"></i> Recibidos</a>
    <a href="atendidos.php"><i class="bi bi-check2"></i> Atendidos</a>
    <a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="content">
    <h3>Movimientos de Documentos</h3>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#nuevoMovimientoModal">
        <i class="bi bi-plus-circle"></i> Nuevo Movimiento
    </button>

    <!-- Modal para registrar movimiento -->
    <div class="modal fade" id="nuevoMovimientoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nuevo Movimiento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Documento</label>
                            <select name="id_documento" class="form-select" required>
                                <option value="">Seleccionar documento</option>
                                <?php while($doc = $docs->fetch_assoc()): ?>
                                    <option value="<?= $doc['id'] ?>"><?= $doc['codigo'] ?> - <?= $doc['asunto'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Área Destino</label>
                            <select name="id_area_destino" class="form-select" required>
                                <option value="">Seleccionar área</option>
                                <?php while($area = $areas->fetch_assoc()): ?>
                                    <option value="<?= $area['id'] ?>"><?= $area['nombre_area'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Observación</label>
                            <textarea name="observacion" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="registrar_movimiento" class="btn btn-success">Registrar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered mt-3">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Tipo Documento</th>
                <th>Asunto</th>
                <th>Área Origen</th>
                <th>Área Destino</th>
                <th>Observación</th>
                <th>Fecha Envío</th>
                <th>Fecha Recepción</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if($query->num_rows > 0): ?>
                <?php $i = 1; while($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['codigo']); ?></td>
                    <td><?= htmlspecialchars($row['tipo_documento']); ?></td>
                    <td><?= htmlspecialchars($row['asunto']); ?></td>
                    <td><?= htmlspecialchars($row['area_origen'] ?? '---'); ?></td>
                    <td><?= htmlspecialchars($row['area_destino']); ?></td>
                    <td><?= htmlspecialchars($row['observacion']); ?></td>
                    <td><?= $row['fecha_envio']; ?></td>
                    <td><?= $row['fecha_recepcion'] ?? '---'; ?></td>
                    <td>
                        <?php
                            $estado = $row['estado'];
                            $badge = match($estado) {
                                'ENVIADO' => 'bg-warning',
                                'RECEPCIONADO' => 'bg-info',
                                'FINALIZADO' => 'bg-success',
                                default => 'bg-secondary'
                            };
                        ?>
                        <span class="badge <?= $badge ?>"><?= $estado ?></span>
                    </td>
                    <td>
                        <?php if($row['estado'] === 'ENVIADO'): ?>
                            <a href="?accion=recepcionar&id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Recepcionar</a>
                        <?php elseif($row['estado'] === 'RECEPCIONADO'): ?>
                            <a href="?accion=finalizar&id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Finalizar</a>
                        <?php else: ?>
                            ---
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="11" class="text-center">No hay movimientos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
