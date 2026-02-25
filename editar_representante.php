<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/editar_representante.php -->
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

// Verificar si se ha proporcionado un ID de representante para editar
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de representante no válido.";
    // Puedes redirigir a la página de gestión de representantes si lo deseas
    header("Location: gestionar_representantes.php");
    exit();
} else {
    $id_representante = $_GET["id"];

    // Consulta para obtener la información del representante
    $sql_representante = "SELECT * FROM representantes WHERE id = $id_representante";
    $result_representante = $conn->query($sql_representante);

    if ($result_representante->num_rows == 0) {
        $error = "Representante no encontrado.";
        // Puedes redirigir a la página de gestión de representantes si lo deseas
        header("Location: gestionar_representantes.php");
        exit();
    } else {
        $representante = $result_representante->fetch_assoc();

        // Procesar el formulario de edición de representante
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_representante"])) {
            $cedula = $_POST["cedula"];
            $nombres = $_POST["nombres"];
            $apellidos = $_POST["apellidos"];
            $telefono = $_POST["telefono"];
            $direccion = $_POST["direccion"];
            $correo_electronico = $_POST["correo_electronico"];
            $parentesco = $_POST["parentesco"];

            // Validar los datos (puedes añadir más validaciones)
            if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($telefono)) {
                $error = "Por favor, rellena todos los campos obligatorios para editar el representante.";
            } else {
                // Preparar la consulta SQL para actualizar el representante
                $sql = "UPDATE representantes SET cedula = '$cedula', nombres = '$nombres', apellidos = '$apellidos', telefono = '$telefono', direccion = '$direccion', correo_electronico = '$correo_electronico', parentesco = '$parentesco' WHERE id = $id_representante";

                // Ejecutar la consulta
                if ($conn->query($sql) === TRUE) {
                    $mensaje = "Representante actualizado correctamente.";
                    // Actualizar la información del representante con los nuevos datos
                    $representante["cedula"] = $cedula;
                    $representante["nombres"] = $nombres;
                    $representante["apellidos"] = $apellidos;
                    $representante["telefono"] = $telefono;
                    $representante["direccion"] = $direccion;
                    $representante["correo_electronico"] = $correo_electronico;
                    $representante["parentesco"] = $parentesco;
                } else {
                    $error = "Error al actualizar el representante: " . $conn->error;
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
    <title>Editar Representante - Gestión Escolar</title>
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
                    <h3 class="text-dark mb-4">Editar Representante</h3>
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
                            <p class="text-primary m-0 fw-bold">Editar Representante</p>
                        </div>
                        <div class="card-body">
                            <?php if ($representante) : ?>
                                <!-- Formulario para editar representante -->
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_representante); ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="cedula"><strong>Cédula</strong></label>
                                                <input class="form-control" type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($representante["cedula"]); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="nombres"><strong>Nombres</strong></label>
                                                <input class="form-control" type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($representante["nombres"]); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="apellidos"><strong>Apellidos</strong></label>
                                                <input class="form-control" type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($representante["apellidos"]); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="telefono"><strong>Teléfono</strong></label>
                                                <input class="form-control" type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($representante["telefono"]); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="direccion"><strong>Dirección</strong></label>
                                        <input class="form-control" type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($representante["direccion"]); ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="correo_electronico"><strong>correo_electronico</strong></label>
                                                <input class="form-control" type="correo_electronico" id="correo_electronico" name="correo_electronico" value="<?php echo htmlspecialchars($representante["correo_electronico"]); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="parentesco"><strong>Parentesco</strong></label>
                                                <input class="form-control" type="text" id="parentesco" name="parentesco" value="<?php echo htmlspecialchars($representante["parentesco"]); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit" name="editar_representante">Actualizar Representante</button>
                                    <a href="gestionar_representantes.php" class="btn btn-secondary">Cancelar</a>
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