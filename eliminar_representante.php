<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/eliminar_representante.php -->
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

// Verificar si se ha proporcionado un ID de representante para eliminar
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de representante no válido.";
    // Puedes redirigir a la página de gestión de representantes si lo deseas
    header("Location: gestionar_representantes.php");
    exit();
} else {
    $id_representante = $_GET["id"];

    // Consulta para verificar si el representante existe
    $sql_verificar_representante = "SELECT id FROM representantes WHERE id = $id_representante";
    $result_verificar_representante = $conn->query($sql_verificar_representante);

    if ($result_verificar_representante->num_rows == 0) {
        $error = "Representante no encontrado.";
        // Puedes redirigir a la página de gestión de representantes si lo deseas
        header("Location: gestionar_representantes.php");
        exit();
    } else {
        // Procesar la eliminación del representante

        // Primero, verificar si el representante tiene estudiantes asociados
        $sql_verificar_estudiantes = "SELECT id FROM estudiantes WHERE representante_id = $id_representante";
        $result_verificar_estudiantes = $conn->query($sql_verificar_estudiantes);

        if ($result_verificar_estudiantes->num_rows > 0) {
            $error = "No se puede eliminar el representante porque tiene estudiantes asociados.  Debe modificar los estudiantes asociados a este representante antes de eliminarlo.";
        } else {
            // Si no tiene estudiantes asociados, se puede eliminar el representante
            $sql_eliminar_representante = "DELETE FROM representantes WHERE id = $id_representante";

            if ($conn->query($sql_eliminar_representante) === TRUE) {
                $mensaje = "Representante eliminado correctamente.";
                // Redirigir a la página de gestión de representantes
                header("Location: gestionar_representantes.php");
                exit();
            } else {
                $error = "Error al eliminar el representante: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Eliminar Representante - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Barra lateral de navegación -->
        <?php include 'nav.php'; ?>
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
                                        <img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- Contenido principal -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Eliminar Representante</h3>
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