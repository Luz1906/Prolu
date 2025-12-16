<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Trámite Documentario</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f2f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="card shadow-lg login-card">
    <div class="card-header bg-primary text-white text-center">
        <h4><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</h4>
    </div>
    <div class="card-body">

        <?php if (isset($_GET["error"])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $_GET["error"] ?>
            </div>
        <?php endif; ?>

        <form action="validarusuario.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario:</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Clave:</label>
                <input type="password" name="clave" class="form-control" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Ingresar
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

