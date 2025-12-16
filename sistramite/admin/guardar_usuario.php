<?php
session_start();
include("../modelo/conexion.php");
// Solo admin registra usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario  = $_POST['usuario'];
    $clave    = $_POST['clave'];
    $nombres  = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $cargo    = $_POST['cargo'];
    $rol      = $_POST['rol'];

    // Verificar duplicado
    $check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: registrar_usuario.php?error=El usuario ya existe");
        exit();
    }

    // Encriptar clave
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql = "INSERT INTO usuarios (usuario, clave, nombres, apellidos, cargo, rol)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss",
        $usuario,
        $clave_hash,
        $nombres,
        $apellidos,
        $cargo,
        $rol
    );

    if ($stmt->execute()) {
        header("Location: registrar_usuario.php?success=Usuario registrado correctamente");
    } else {
        header("Location: registrar_usuario.php?error=Error al registrar usuario");
    }
}
?>
