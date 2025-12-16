<?php
session_start();
include("../modelo/conexion.php");

// Solo ADMIN puede acceder
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
    
    <style>
        body { background: #f2f4f7; }
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #0d6efd;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 12px;
            display: block;
            text-decoration: none;
            margin-bottom: 4px;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .content { margin-left: 250px; padding: 30px; }
        .header {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0px 2px 5px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center"><i class="bi bi-speedometer2"></i> ADMIN</h4>
    <hr>
    <a href="dashboardadmin.php"><i class="bi bi-house"></i> Inicio</a>
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
        <h3>Gestión de Usuarios</h3>
        <span class="badge bg-primary fs-6"><?= $_SESSION['rol'] ?></span>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><i class="bi bi-people"></i> Lista de Usuarios</strong>
        </div>
        <div class="card-body">
            <table id="usuarios" class="table table-hover table-bordered align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM usuarios ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='text-center'>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['nombres']."</td>";
                        echo "<td>".$row['apellidos']."</td>";
                        echo "<td>".$row['usuario']."</td>";
                        echo "<td><span class='badge bg-success'>".$row['rol']."</span></td>";
                        echo "<td>
                                <a href='editar_usuario.php?id=".$row['id']."' class='btn btn-warning btn-sm'>
                                    <i class='bi bi-pencil-square'></i>
                                </a>
                                <a href='eliminar_usuario.php?id=".$row['id']."' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Eliminar este usuario?\")'>
                                    <i class='bi bi-trash'></i>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#usuarios').DataTable({
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            pageLength: 10,
            responsive: true
        });
    });
</script>

</body>
</html>
