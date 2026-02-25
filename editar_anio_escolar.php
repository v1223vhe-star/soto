<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/editar_anio_escolar.php -->
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

// Verificar si se ha proporcionado un ID de año escolar para editar
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de año escolar no válido.";
    // Puedes redirigir a la página de gestión de años escolares si lo deseas
    // header("Location: gestionar_anios_escolares.php");
    // exit();
} else {
    $id_anio_escolar = $_GET["id"];

    // Consulta para obtener la información del año escolar
    $sql_anio_escolar = "SELECT * FROM anios_escolares WHERE id = $id_anio_escolar";
    $result_anio_escolar = $conn->query($sql_anio_escolar);

    if ($result_anio_escolar->num_rows == 0) {
        $error = "Año escolar no encontrado.";
        // Puedes redirigir a la página de gestión de años escolares si lo deseas
        // header("Location: gestionar_anios_escolares.php");
        // exit();
    } else {
        $anio_escolar = $result_anio_escolar->fetch_assoc();

        // Procesar el formulario de edición de año escolar
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_anio_escolar"])) {
            $nombre = $_POST["nombre"];
            $fecha_inicio = $_POST["fecha_inicio"];
            $fecha_fin = $_POST["fecha_fin"];

            // Validar los datos (puedes añadir más validaciones)
            if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                $error = "Por favor, rellena todos los campos para editar el año escolar.";
            } else {
                // Preparar la consulta SQL para actualizar el año escolar
                $sql = "UPDATE anios_escolares SET nombre = '$nombre', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin' WHERE id = $id_anio_escolar";

                // Ejecutar la consulta
                if ($conn->query($sql) === TRUE) {
                    $mensaje = "Año escolar actualizado correctamente.";
                    // Actualizar la información del año escolar con los nuevos datos
                    $anio_escolar["nombre"] = $nombre;
                    $anio_escolar["fecha_inicio"] = $fecha_inicio;
                    $anio_escolar["fecha_fin"] = $fecha_fin;
                } else {
                    $error = "Error al actualizar el año escolar: " . $conn->error;
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Editar Año Escolar - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
</head>

<body id="page-top">
    <div id="wrapper">
    <?php include 'nav.php'; ?>
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
                    <h3 class="text-dark mb-4">Editar Año Escolar</h3>
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
                            <p class="text-primary m-0 fw-bold">Editar Año Escolar</p>
                        </div>
                        <div class="card-body">
                            <?php if ($anio_escolar) : ?>
                                <!-- Formulario para editar año escolar -->
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_anio_escolar); ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="nombre"><strong>Nombre</strong></label>
                                                <input class="form-control" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($anio_escolar["nombre"]); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fecha_inicio"><strong>Fecha de Inicio</strong></label>
                                                <input class="form-control" type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($anio_escolar["fecha_inicio"]); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fecha_fin"><strong>Fecha de Fin</strong></label>
                                                <input class="form-control" type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($anio_escolar["fecha_fin"]); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit" name="editar_anio_escolar">Actualizar Año Escolar</button>
                                    <a href="gestionar_anios_escolares.php" class="btn btn-secondary">Cancelar</a>
                                </form>
                            <?php else : ?>
                                <p><?php echo $error; ?></p>
                            <?php endif; ?>
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