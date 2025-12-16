<?php
session_start();

// Solo ADMIN puede registrar usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f2f4f7;
        }
        .form-container {
            max-width: 600px;
            margin: 60px auto;
        }
    </style>
</head>
<body>

<div class="container form-container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="bi bi-person-plus"></i> Registrar Nuevo Usuario</h4>
        </div>

        <div class="card-body">

            <!-- MENSAJES -->
            <?php if (isset($_GET["success"])) { ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> <?= $_GET["success"] ?>
                </div>
            <?php } ?>

            <?php if (isset($_GET["error"])) { ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= $_GET["error"] ?>
                </div>
            <?php } ?>

            <form action="guardar_usuario.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Usuario:</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Clave:</label>
                    <input type="password" name="clave" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombres:</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellidos:</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo:</label>
                    <input type="text" name="cargo" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol:</label>
                    <select name="rol" class="form-select" required>
                        <option value="ADMIN">ADMIN</option>
                        <option value="MESA_PARTES">MESA DE PARTES</option>
                        <option value="DIRECTOR">DIRECTOR</option>
                        <option value="SECRETARIA ACADEMICA">SECRETARÍA ACADÉMICA</option>
                        <option value="JUA">JUA</option>
                        <option value="COORD INFORMATICA">COORD. INFORMÁTICA</option>
                        <option value="COORD TOPO">COORD. TOPOGRAFÍA</option>
                        <option value="COORD ENFERMERIA">COORD. ENFERMERÍA</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Registrar Usuario
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
