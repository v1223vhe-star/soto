<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/asignar_materias_anio.php -->
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

// Procesar el formulario de asignación de materias
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["asignar_materias"])) {
    $anio_escolar_id = $_POST["anio_escolar_id"];
    $materias = isset($_POST["materias"]) ? $_POST["materias"] : []; // Obtener las materias seleccionadas

    // Validar que se haya seleccionado un año escolar
    if (empty($anio_escolar_id)) {
        $error = "Por favor, selecciona un año escolar.";
    } else {
        // Eliminar las asignaciones de materias existentes para este año escolar
        $sql_eliminar_asignaciones = "DELETE FROM materias_anio_escolar WHERE anio_escolar_id = $anio_escolar_id";
        if ($conn->query($sql_eliminar_asignaciones) === TRUE) {

            // Insertar las nuevas asignaciones de materias
            if (!empty($materias)) {
                $sql_insertar_asignaciones = "INSERT INTO materias_anio_escolar (anio_escolar_id, materia_id) VALUES ";
                $valores = [];
                foreach ($materias as $materia_id) {
                    $valores[] = "($anio_escolar_id, $materia_id)";
                }
                $sql_insertar_asignaciones .= implode(",", $valores);

                if ($conn->query($sql_insertar_asignaciones) === TRUE) {
                    $mensaje = "Materias asignadas correctamente al año escolar.";
                } else {
                    $error = "Error al asignar las materias: " . $conn->error;
                }
            } else {
                $mensaje = "Se han eliminado todas las materias asignadas a este año escolar.";
            }
        } else {
            $error = "Error al eliminar las asignaciones de materias existentes: " . $conn->error;
        }
    }
}

// Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT id, nombre FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// Consulta para obtener la lista de materias
$sql_materias = "SELECT id, nombre FROM materias";
$result_materias = $conn->query($sql_materias);

?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Asignar Materias a Año Escolar - Gestión Escolar</title>
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
                    <h3 class="text-dark mb-4">Asignar Materias a Año Escolar</h3>
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
                            <p class="text-primary m-0 fw-bold">Asignar Materias a Año Escolar</p>
                        </div>
                        <div class="card-body">
                            <!-- Formulario para asignar materias -->
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="mb-3">
                                    <label class="form-label" for="anio_escolar_id"><strong>Año Escolar</strong></label>
                                    <select class="form-control" id="anio_escolar_id" name="anio_escolar_id">
                                        <option value="">Selecciona un año escolar</option>
                                        <?php
                                        if ($result_anios_escolares->num_rows > 0) {
                                            while ($row = $result_anios_escolares->fetch_assoc()) {
                                                echo "<option value='" . $row["id"] . "'>" . $row["nombre"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Materias</strong></label>
                                    <?php
                                    if ($result_materias->num_rows > 0) {
                                        while ($row = $result_materias->fetch_assoc()) {
                                            echo "<div class='form-check'>";
                                            echo "<input class='form-check-input' type='checkbox' name='materias[]' value='" . $row["id"] . "' id='materia_" . $row["id"] . "'>";
                                            echo "<label class='form-check-label' for='materia_" . $row["id"] . "'>" . $row["nombre"] . "</label>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p>No hay materias registradas.</p>";
                                    }
                                    ?>
                                </div>
                                <button class="btn btn-primary" type="submit" name="asignar_materias">Asignar Materias</button>
                            </form>
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