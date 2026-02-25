<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Procesar el formulario de creación de año escolar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_anio_escolar"])) {
    $nombre = $_POST["nombre"];
    $fecha_inicio = $_POST["fecha_inicio"];
    $fecha_fin = $_POST["fecha_fin"];

    // Validar los datos (puedes añadir más validaciones)
    if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
        $error = "Por favor, rellena todos los campos para crear el año escolar.";
    } else {
        // Preparar la consulta SQL para insertar el nuevo año escolar
        $sql = "INSERT INTO anios_escolares (nombre, fecha_inicio, fecha_fin) VALUES ('$nombre', '$fecha_inicio', '$fecha_fin')";

        // Ejecutar la consulta
        if ($conn->query($sql) === TRUE) {
            $mensaje = "Año escolar creado correctamente.";
        } else {
            $error = "Error al crear el año escolar: " . $conn->error;
        }
    }
}

// Procesar la activación de año escolar
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["activar_anio_escolar"])) {
    $id_activar = $_GET["activar_anio_escolar"];

    // Desactivar todos los años escolares
    $sql_desactivar_todos = "UPDATE anios_escolares SET activo = FALSE";
    if ($conn->query($sql_desactivar_todos) === TRUE) {
        // Activar el año escolar seleccionado
        $sql_activar = "UPDATE anios_escolares SET activo = TRUE WHERE id = $id_activar";
        if ($conn->query($sql_activar) === TRUE) {
            $mensaje = "Año escolar activado correctamente.";
        } else {
            $error = "Error al activar el año escolar: " . $conn->error;
        }
    } else {
        $error = "Error al desactivar los demás años escolares: " . $conn->error;
    }
}

// Procesar la edición del año escolar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_anio_escolar"])) {
    $id = $_POST["id"];
    $nuevo_nombre = $_POST["nuevo_nombre"];
    $nueva_fecha_inicio = $_POST["nueva_fecha_inicio"];
    $nueva_fecha_fin = $_POST["nueva_fecha_fin"];

    if (empty($nuevo_nombre) || empty($nueva_fecha_inicio) || empty($nueva_fecha_fin)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $sql = "UPDATE anios_escolares SET 
                nombre = '$nuevo_nombre', 
                fecha_inicio = '$nueva_fecha_inicio', 
                fecha_fin = '$nueva_fecha_fin' 
                WHERE id = $id";
                
        if ($conn->query($sql) === TRUE) {
            $mensaje = "Año escolar actualizado correctamente.";
        } else {
            $error = "Error al actualizar el año escolar: " . $conn->error;
        }
    }
}

// Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT * FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gestionar Año Escolar - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Barra de navegación superior -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow">
                                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                                        <span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $_SESSION["username"]; ?></span>
                                        <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Cerrar sesión</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Gestionar Años Escolares</h3>
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Lista de Años Escolares</p>
                        </div>
                        <div class="card-body">
                        
                            <!-- Tabla de años escolares -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Fecha de Inicio</th>
                                            <th>Fecha de Fin</th>
                                            <th>Activo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_anios_escolares->num_rows > 0) {
                                            while ($row = $result_anios_escolares->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["id"] . "</td>";
                                                echo "<td>
                                                        <form method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."' style='display:inline;'>
                                                            <input type='hidden' name='id' value='".$row["id"]."'>
                                                            <input type='text' name='nuevo_nombre' value='".htmlspecialchars($row["nombre"])."' class='form-control form-control-sm' required>
                                                        </td>";
                                                echo "<td>
                                                        <input type='date' name='nueva_fecha_inicio' value='".$row["fecha_inicio"]."' class='form-control form-control-sm' required>
                                                      </td>";
                                                echo "<td>
                                                        <input type='date' name='nueva_fecha_fin' value='".$row["fecha_fin"]."' class='form-control form-control-sm' required>
                                                      </td>";
                                                echo "<td>" . ($row["activo"] ? "Sí" : "No") . "</td>";
                                                echo "<td>
                                                        <button type='submit' name='editar_anio_escolar' class='btn btn-primary btn-sm'>Guardar</button>
                                                        </form>
                                                        <a href='gestionar_anios_escolares.php?activar_anio_escolar=" . $row["id"] . "' class='btn btn-success btn-sm'>Activar</a>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No hay años escolares registrados.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Gestión Escolar 2025</span></div>
                </div>
            </footer>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>

<?php
$conn->close();
?>