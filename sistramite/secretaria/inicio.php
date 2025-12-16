<?php
session_start();
include("../modelo/conexion.php");

// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'SECRETARIA ACADEMICA') {
    header("Location: ../index.php");
    exit();
}

// ID del área de Secretaría Académica
$sqlArea = mysqli_query($conn, "SELECT id FROM areas WHERE nombre_area='Secretaría Académica' LIMIT 1");
$area = mysqli_fetch_assoc($sqlArea);
$id_area_secretaria = $area['id'];

// Contador: Documentos recibidos
$rec = mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM movimientos
    WHERE id_area_destino='$id_area_secretaria'
");
$recibidos = mysqli_fetch_assoc($rec)['total'];

// Contador: Documentos atendidos
$aten = mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM documentos
    WHERE estado='ATENDIDO'
    AND id IN (SELECT id_documento FROM movimientos WHERE id_area_destino='$id_area_secretaria')
");
$atendidos = mysqli_fetch_assoc($aten)['total'];

// Contador: movimientos internos
$mov = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM movimientos
    WHERE id_area_origen='$id_area_secretaria'
");
$movimientos = mysqli_fetch_assoc($mov)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Secretaría Académica - Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f1f5f9; }
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #6f42c1;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px;
            margin-bottom: 5px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-journal-bookmark"></i> Secretaría Académica</h4>
    <hr>

    <a href="inicio.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="documentos_recibidos.php"><i class="bi bi-inbox"></i> Recibidos</a>
    <a href="documentos_atendidos.php"><i class="bi bi-check2-circle"></i> Atendidos</a>
    <a href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="content">

    <h3>Bienvenida, <?php echo $_SESSION['nombres']; ?> 👋</h3>
    <p class="text-muted">Panel principal de Secretaría Académica</p>

    <div class="row g-4 mt-2">

        <!-- Recibidos -->
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-inbox fs-1 text-primary"></i>
                    <h5>Documentos Recibidos</h5>
                    <h2 class="mt-2"><?php echo $recibidos; ?></h2>
                    <a href="documentos_recibidos.php" class="btn btn-primary btn-sm mt-2">Ver más</a>
                </div>
            </div>
        </div>

        <!-- Atendidos -->
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <h5>Documentos Atendidos</h5>
                    <h2 class="mt-2"><?php echo $atendidos; ?></h2>
                    <a href="documentos_atendidos.php" class="btn btn-success btn-sm mt-2">Revisar</a>
                </div>
            </div>
        </div>

        <!-- Movimientos -->
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-arrow-left-right fs-1 text-warning"></i>
                    <h5>Movimientos Internos</h5>
                    <h2 class="mt-2"><?php echo $movimientos; ?></h2>
                    <a href="movimientos.php" class="btn btn-warning btn-sm mt-2">Ver detalles</a>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>
