<?php
session_start();
include("../modelo/conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'JUA') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>JUA - Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f1f5f9; }
        .sidebar {
            width: 240px; height: 100vh; position: fixed;
            background: #0d6efd; color: white; padding-top: 20px;
        }
        .sidebar a { color: white; display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; }
        .card:hover { cursor: pointer; transform: scale(1.05); transition: 0.2s; }
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
    <h3>Bienvenido, <?php echo $_SESSION['nombres']; ?></h3>

    <div class="row g-4 mt-3">

        <div class="col-md-4">
            <div class="card text-center shadow-sm" onclick="location.href='recibidos.php'">
                <div class="card-body">
                    <i class="bi bi-inbox-fill fs-1 text-primary"></i>
                    <h5 class="mt-2">Documentos Recibidos</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm" onclick="location.href='atendidos.php'">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                    <h5 class="mt-2">Atendidos</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm" onclick="location.href='movimientos.php'">
                <div class="card-body">
                    <i class="bi bi-arrow-left-right fs-1 text-warning"></i>
                    <h5 class="mt-2">Movimientos</h5>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
