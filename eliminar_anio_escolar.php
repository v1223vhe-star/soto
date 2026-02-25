<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/eliminar_anio_escolar.php -->
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

// Verificar si se ha proporcionado un ID de año escolar para eliminar
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de año escolar no válido.";
    // Puedes redirigir a la página de gestión de años escolares si lo deseas
    header("Location: gestionar_anios_escolares.php");
    exit();
} else {
    $id_anio_escolar = $_GET["id"];

    // Consulta para verificar si el año escolar existe
    $sql_verificar_anio_escolar = "SELECT id FROM anios_escolares WHERE id = $id_anio_escolar";
    $result_verificar_anio_escolar = $conn->query($sql_verificar_anio_escolar);

    if ($result_verificar_anio_escolar->num_rows == 0) {
        $error = "Año escolar no encontrado.";
        // Puedes redirigir a la página de gestión de años escolares si lo deseas
        header("Location: gestionar_anios_escolares.php");
        exit();
    } else {
        // Procesar la eliminación del año escolar
        // Primero, eliminar las referencias en otras tablas (materias_anio_escolar, materias_dictadas)
        $sql_eliminar_materias_anio = "DELETE FROM materias_anio_escolar WHERE anio_escolar_id = $id_anio_escolar";
        $sql_eliminar_materias_dictadas = "DELETE FROM materias_dictadas WHERE anio_escolar_id = $id_anio_escolar";

        if ($conn->query($sql_eliminar_materias_anio) === TRUE &&
            $conn->query($sql_eliminar_materias_dictadas) === TRUE) {

            // Luego, eliminar el año escolar de la tabla anios_escolares
            $sql_eliminar_anio_escolar = "DELETE FROM anios_escolares WHERE id = $id_anio_escolar";

            if ($conn->query($sql_eliminar_anio_escolar) === TRUE) {
                $mensaje = "Año escolar eliminado correctamente.";
                // Redirigir a la página de gestión de años escolares
                header("Location: gestionar_anios_escolares.php");
                exit();
            } else {
                $error = "Error al eliminar el año escolar: " . $conn->error;
            }
        } else {
            $error = "Error al eliminar las referencias del año escolar en otras tablas: " . $conn->error;
        }
    }
}

// Si hay un error, mostrar el mensaje y un enlace para volver a la página de gestión
if ($error) {
    echo "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
    echo "<a href='gestionar_anios_escolares.php' class='btn btn-secondary'>Volver a Gestionar Años Escolares</a>";
}

$conn->close();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Eliminar Año Escolar - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Barra lateral de navegación -->
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    Menú Inicio
                    <div class="sidebar-brand-text mx-3"><span>Gestión Escolar</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i><span>Menú Inicio</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="gestionar_estudiantes.php"><i class="fas fa-graduation-cap"></i><span>Gestionar Estudiantes</span></a></li>
                    <!-- Agrega los demás enlaces de navegación -->
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Barra de navegación superior -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <!-- Agrega la información del usuario y el botón de logout -->
                        </ul>
                    </div>
                </nav>
                <!-- Contenido principal -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Eliminar Año Escolar</h3>
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