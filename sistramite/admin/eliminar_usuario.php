<?php
session_start();
include("../modelo/conexion.php");

// Solo ADMIN puede eliminar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}

// Verificar ID
if (!isset($_GET['id'])) {
    header("Location: lista_usuarios.php");
    exit();
}

$id = intval($_GET['id']);

// Evitar que ADMIN se elimine a sí mismo
if ($_SESSION['id'] == $id) {
    header("Location: lista_usuarios.php?error=No puedes eliminar tu propio usuario");
    exit();
}

// Eliminar usuario
$delete = mysqli_query($conn, "DELETE FROM usuarios WHERE id='$id'");

if ($delete) {
    header("Location: lista_usuarios.php?success=Usuario eliminado correctamente");
} else {
    header("Location: lista_usuarios.php?error=Error al eliminar usuario");
}
?>
