<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/gestionar_matricula.php -->
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

// Procesar el formulario de creación de matrícula
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_matricula"])) {
    $estudiante_id = $_POST["estudiante_id"];
    $anio_escolar_id = $_POST["anio_escolar_id"];
    $fecha_matricula = $_POST["fecha_matricula"];
    $grado = $_POST["grado"];
    $seccion = $_POST["seccion"];

    // Validar los datos (puedes añadir más validaciones)
    if (empty($estudiante_id) || empty($anio_escolar_id) || empty($fecha_matricula) || empty($grado) || empty($seccion)) {
        $error = "Por favor, rellena todos los campos para crear la matrícula.";
    } else {
        // Preparar la consulta SQL para insertar la matrícula
        $sql = "INSERT INTO matricula (estudiante_id, anio_escolar_id, fecha_matricula, grado, seccion) VALUES ('$estudiante_id', '$anio_escolar_id', '$fecha_matricula', '$grado', '$seccion')";

        // Ejecutar la consulta
        if ($conn->query($sql) === TRUE) {
            $mensaje = "Matrícula creada correctamente.";
        } else {
            $error = "Error al crear la matrícula: " . $conn->error;
        }
    }
}

// Procesar el formulario de eliminación de matrícula
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_matricula"])) {
    $id_matricula_eliminar = $_POST["id_matricula"];

    // Validar que se haya proporcionado un ID de matrícula para eliminar
    if (empty($id_matricula_eliminar)) {
        $error = "ID de matrícula no válido.";
    } else {
        // Preparar la consulta SQL para eliminar la matrícula
        $sql_eliminar = "DELETE FROM matricula WHERE id = $id_matricula_eliminar";

        // Ejecutar la consulta
        if ($conn->query($sql_eliminar) === TRUE) {
            $mensaje = "Matrícula eliminada correctamente.";
        } else {
            $error = "Error al eliminar la matrícula: " . $conn->error;
        }
    }
}

// Consulta para obtener la lista de matrículas
$sql_matriculas = "SELECT m.id, e.nombres AS nombre_estudiante, e.apellidos AS apellido_estudiante, a.nombre AS nombre_anio_escolar, m.fecha_matricula, m.grado, m.seccion
                    FROM matricula m
                    INNER JOIN estudiantes e ON m.estudiante_id = e.id
                    INNER JOIN anios_escolares a ON m.anio_escolar_id = a.id";
$result_matriculas = $conn->query($sql_matriculas);

// Consulta para obtener la lista de estudiantes
$sql_estudiantes = "SELECT id, nombres, apellidos FROM estudiantes";
$result_estudiantes = $conn->query($sql_estudiantes);

// Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT id, nombre FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// Obtener el rol del usuario
$user_role = $_SESSION["role"];

?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gestionar Matrículas - Gestión Escolar</title>
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
                    <h3 class="text-dark mb-4">Gestionar Matrículas</h3>
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
                            <p class="text-primary m-0 fw-bold">Lista de Matrículas</p>
                        </div>
                        <div class="card-body">
                            <!-- Formulario para crear matrícula -->
                            <h4>Crear Matrícula</h4>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="estudiante_id"><strong>Estudiante</strong></label>
                                            <select class="form-control" id="estudiante_id" name="estudiante_id">
                                                <option value="">Selecciona un estudiante</option>
                                                <?php
                                                if ($result_estudiantes->num_rows > 0) {
                                                    while ($row = $result_estudiantes->fetch_assoc()) {
                                                        echo "<option value='" . $row["id"] . "'>" . $row["nombres"] . " " . $row["apellidos"] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="fecha_matricula"><strong>Fecha de Matrícula</strong></label>
                                            <input class="form-control" type="date" id="fecha_matricula" name="fecha_matricula">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="grado"><strong>Grado</strong></label>
                                            <input class="form-control" type="text" id="grado" name="grado">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="seccion"><strong>Sección</strong></label>
                                    <input class="form-control" type="text" id="seccion" name="seccion">
                                </div>
                                <button class="btn btn-primary" type="submit" name="crear_matricula">Crear Matrícula</button>
                            </form>

                            <hr>

                            <!-- Lista de matrículas -->
                            <h4>Lista de Matrículas</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Estudiante</th>
                                            <th>Año Escolar</th>
                                            <th>Fecha de Matrícula</th>
                                            <th>Grado</th>
                                            <th>Sección</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_matriculas->num_rows > 0) {
                                            while ($row = $result_matriculas->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["id"] . "</td>";
                                                echo "<td>" . $row["nombre_estudiante"] . " " . $row["apellido_estudiante"] . "</td>";
                                                echo "<td>" . $row["nombre_anio_escolar"] . "</td>";
                                                echo "<td>" . $row["fecha_matricula"] . "</td>";
                                                echo "<td>" . $row["grado"] . "</td>";
                                                echo "<td>" . $row["seccion"] . "</td>";
                                                echo "<td>
                                                        <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                                                            <input type='hidden' name='id_matricula' value='" . $row["id"] . "'>
                                                            <button type='submit' name='eliminar_matricula' class='btn btn-danger btn-sm'>Eliminar</button>
                                                        </form>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No hay matrículas registradas.</td></tr>";
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