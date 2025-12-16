<?php
session_start();
include("modelo/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND estado = 'ACTIVO' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verificar clave encriptada
        if (password_verify($clave, $user['clave'])) {

            $_SESSION['id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombres'] = $user['nombres'];

            // Redirección por rol
            switch ($user['rol']) {
                case "ADMIN": header("Location: admin/dashboardadmin.php"); break;
                case "MESA_PARTES": header("Location: mesa_partes/inicio.php"); break;
                case "DIRECTOR": header("Location: director/inicio.php"); break;
                case "SECRETARIA ACADEMICA": header("Location: secretaria/inicio.php"); break;
                case "JUA": header("Location: jua/inicio.php"); break;
                case "COORD INFORMATICA": header("Location: informatica/inicio.php"); break;
                case "COORD TOPO": header("Location: topografia/inicio.php"); break;
                case "COORD ENFERMERIA": header("Location: enfermeria/inicio.php"); break;
            }

        } else {
            header("Location: index.php?error=Contraseña incorrecta");
        }

    } else {
        header("Location: index.php?error=Usuario no encontrado");
    }
}
?>
