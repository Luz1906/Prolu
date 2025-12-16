<?php
session_start(); 
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}

include("../modelo/conexion.php"); // Ajusta la ruta según tu estructura

// Contar Usuarios
$result_usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$usuarios = $result_usuarios->fetch_assoc()['total'];

// Contar Documentos
$result_documentos = $conn->query("SELECT COUNT(*) AS total FROM documentos");
$documentos = $result_documentos->fetch_assoc()['total'];

// Contar Movimientos
$result_movimientos = $conn->query("SELECT COUNT(*) AS total FROM movimientos");
$movimientos = $result_movimientos->fetch_assoc()['total'];

// Contar Áreas
$result_areas = $conn->query("SELECT COUNT(*) AS total FROM areas");
$areas = $result_areas->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f2f4f7; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0d6efd; color: white; padding-top: 20px; transition: all 0.3s; }
        .sidebar a { color: white; padding: 12px; display: block; text-decoration: none; margin-bottom: 4px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); border-radius: 5px; }
        .content { margin-left: 250px; padding: 30px; transition: all 0.3s; }
        .header { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0px 2px 5px rgba(0,0,0,.1); }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-speedometer2"></i> ADMIN</h4>
    <hr>
    <a href="dashboardadmin.php" class="active"><i class="bi bi-house"></i> Inicio</a>
    <a href="registrar_usuario.php"><i class="bi bi-person-plus"></i> Registrar Usuario</a>
    <a href="lista_usuarios.php"><i class="bi bi-people"></i> Gestionar Usuarios</a>
    <a href="#"><i class="bi bi-folder"></i> Documentos</a>
    <a href="#"><i class="bi bi-send"></i> Movimientos</a>
    <a href="#"><i class="bi bi-building"></i> Áreas</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<!-- CONTENIDO -->
<div class="content">
    <div class="header d-flex justify-content-between align-items-center">
        <h3>Bienvenido, <?php echo $_SESSION['nombres']; ?></h3>
        <span class="badge bg-primary fs-6"><?php echo $_SESSION['rol']; ?></span>
    </div>

    <!-- TARJETAS RESUMEN -->
    <div class="row g-4">
        <!-- Usuarios -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h5 class="mt-2">Usuarios</h5>
                    <p class="text-muted">Gestión del personal</p>
                    <h3><?php echo $usuarios; ?></h3>
                    <a href="lista_usuarios.php" class="btn btn-primary btn-sm">Ver más</a>
                </div>
            </div>
        </div>
        <!-- Documentos -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-folder2-open fs-1 text-warning"></i>
                    <h5 class="mt-2">Documentos</h5>
                    <p class="text-muted">Ingresos y trámites</p>
                    <h3><?php echo $documentos; ?></h3>
                    <a href="#" class="btn btn-warning btn-sm">Gestionar</a>
                </div>
            </div>
        </div>
        <!-- Movimientos -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-send-check fs-1 text-success"></i>
                    <h5 class="mt-2">Movimientos</h5>
                    <p class="text-muted">Derivaciones entre áreas</p>
                    <h3><?php echo $movimientos; ?></h3>
                    <a href="#" class="btn btn-success btn-sm">Revisar</a>
                </div>
            </div>
        </div>
        <!-- Áreas -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-building fs-1 text-danger"></i>
                    <h5 class="mt-2">Áreas</h5>
                    <p class="text-muted">Estructura institucional</p>
                    <h3><?php echo $areas; ?></h3>
                    <a href="#" class="btn btn-danger btn-sm">Ver áreas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
