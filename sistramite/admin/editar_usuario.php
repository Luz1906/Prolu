<?php
session_start();
include("../modelo/conexion.php");

// Solo ADMIN puede editar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}

// Obtener ID del usuario
if (!isset($_GET['id'])) {
    header("Location: lista_usuarios.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener datos del usuario
$query = mysqli_query($conn, "SELECT * FROM usuarios WHERE id='$id'");
if (mysqli_num_rows($query) == 0) {
    header("Location: lista_usuarios.php");
    exit();
}
$user = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f2f4f7; }
        .form-container { max-width: 600px; margin: 60px auto; }
    </style>
</head>
<body>

<div class="container form-container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark text-center">
            <h4><i class="bi bi-pencil-square"></i> Editar Usuario</h4>
        </div>
        <div class="card-body">
            <form action="actualizar_usuario.php" method="POST">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Usuario:</label>
                    <input type="text" name="usuario" class="form-control" value="<?= $user['usuario'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Clave: <small>(dejar vacío para no cambiar)</small></label>
                    <input type="password" name="clave" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombres:</label>
                    <input type="text" name="nombres" class="form-control" value="<?= $user['nombres'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellidos:</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= $user['apellidos'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo:</label>
                    <input type="text" name="cargo" class="form-control" value="<?= $user['cargo'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol:</label>
                    <select name="rol" class="form-select" required>
                        <?php
                        $roles = ['ADMIN','MESA_PARTES','DIRECTOR','SECRETARIA ACADEMICA','JUA','COORD INFORMATICA','COORD TOPO','COORD ENFERMERIA'];
                        foreach ($roles as $r) {
                            $selected = $user['rol']==$r?'selected':'';
                            echo "<option value='$r' $selected>$r</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
