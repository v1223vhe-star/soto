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

// Procesar el formulario de creación de grado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_grado"])) {
    $nombre_grado = trim($_POST["nombre_grado"]);

    // Validar los datos
    if (empty($nombre_grado)) {
        $error = "Por favor, rellena el nombre del grado.";
    } else {
        // Preparar la consulta SQL para insertar el grado
        $sql = "INSERT INTO grados (nombre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre_grado);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $mensaje = "Grado creado correctamente.";
        } else {
            $error = "Error al crear el grado: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: gestionar_anios_grados.php");
    exit();
}

// Procesar el formulario de eliminación de grado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_grado"])) {
    $grado_id = trim($_POST["grado_id"]);

    // Validar los datos
    if (empty($grado_id)) {
        $error = "ID de grado no válido.";
    } else {
        // Verificar si el grado está asignado a algún año escolar
        $sql_verificar_anios = "SELECT id FROM anios_grados WHERE grado_id = ?";
        $stmt_verificar_anios = $conn->prepare($sql_verificar_anios);
        $stmt_verificar_anios->bind_param("i", $grado_id);
        $stmt_verificar_anios->execute();
        $result_verificar_anios = $stmt_verificar_anios->get_result();
        
        // Verificar si hay estudiantes en este grado
        $sql_verificar_estudiantes = "SELECT id FROM datos_academicos WHERE anio_grado_id IN (SELECT id FROM anios_grados WHERE grado_id = ?)";
        $stmt_verificar_estudiantes = $conn->prepare($sql_verificar_estudiantes);
        $stmt_verificar_estudiantes->bind_param("i", $grado_id);
        $stmt_verificar_estudiantes->execute();
        $result_verificar_estudiantes = $stmt_verificar_estudiantes->get_result();

        if ($result_verificar_anios->num_rows > 0) {
            if ($result_verificar_estudiantes->num_rows > 0) {
                $error = "No se puede eliminar el grado porque tiene estudiantes matriculados.";
            } else {
                $error = "No se puede eliminar el grado porque está asignado a uno o más años escolares.";
            }
        } else {
            // Preparar la consulta SQL para eliminar el grado
            $sql = "DELETE FROM grados WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grado_id);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $mensaje = "Grado eliminado correctamente.";
            } else {
                $error = "Error al eliminar el grado: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_verificar_anios->close();
        $stmt_verificar_estudiantes->close();
    }
    header("Location: gestionar_anios_grados.php");
    exit();
}

// Procesar el formulario de asignación de grado a año escolar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["asignar_grado"])) {
    $anio_escolar_id = trim($_POST["anio_escolar_id"]);
    $grado_id = trim($_POST["grado_id"]);
    $secciones = trim($_POST["secciones"]);

    // Validar los datos
    if (empty($anio_escolar_id) || empty($grado_id)) {
        $error = "Por favor, selecciona un año escolar y un grado.";
    } else {
        // Verificar si la asignación ya existe
        $sql_verificar = "SELECT id FROM anios_grados WHERE anio_escolar_id = ? AND grado_id = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("ii", $anio_escolar_id, $grado_id);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {
            $error = "Este grado ya está asignado a este año escolar.";
        } else {
            // Preparar la consulta SQL para insertar la asignación
            $sql = "INSERT INTO anios_grados (anio_escolar_id, grado_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $anio_escolar_id, $grado_id);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $anio_grado_id = $conn->insert_id;

                // Insertar las secciones
                $secciones_array = explode("\n", $secciones);
                foreach ($secciones_array as $seccion) {
                    $seccion = trim($seccion);
                    if (!empty($seccion)) {
                        $sql_seccion = "INSERT INTO secciones_anio_grado (anio_grado_id, seccion) VALUES (?, ?)";
                        $stmt_seccion = $conn->prepare($sql_seccion);
                        $stmt_seccion->bind_param("is", $anio_grado_id, $seccion);
                        $stmt_seccion->execute();
                        $stmt_seccion->close();
                    }
                }

                $mensaje = "Grado asignado al año escolar correctamente.";
            } else {
                $error = "Error al asignar el grado al año escolar: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_verificar->close();
    }
    header("Location: gestionar_anios_grados.php");
    exit();
}

// Procesar el formulario de activación/desactivación de grado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["activar_desactivar_grado"])) {
    $anio_grado_id = trim($_POST["anio_grado_id"]);
    $activo = trim($_POST["activo"]);

    // Validar los datos
    if (empty($anio_grado_id)) {
        $error = "ID de asignación no válido.";
    } else {
        // Preparar la consulta SQL para actualizar el estado
        $sql = "UPDATE anios_grados SET activo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $activo, $anio_grado_id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $mensaje = "Estado del grado actualizado correctamente.";
        } else {
            $error = "Error al actualizar el estado del grado: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: gestionar_anios_grados.php");
    exit();
}

// Consulta para obtener la lista de grados
$sql_grados = "SELECT * FROM grados";
$result_grados = $conn->query($sql_grados);

// Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT * FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// Consulta para obtener la lista de años y grados asignados
$sql_anios_grados = "SELECT ag.id, a.nombre AS anio_escolar, g.nombre AS grado, ag.activo
                    FROM anios_grados ag
                    INNER JOIN anios_escolares a ON ag.anio_escolar_id = a.id
                    INNER JOIN grados g ON ag.grado_id = g.id";
$result_anios_grados = $conn->query($sql_anios_grados);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gestionar Años y Grados - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .form-control, .form-select {
            border-radius: 0.375rem;
        }
        .btn {
            border-radius: 0.375rem;
        }
        .disabled-option {
            color: #6c757d;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                                    <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg">
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Gestionar Años y Grados</h3>
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Gestión de Años y Grados</p>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h4>Crear Grado</h4>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="mb-3">
                                            <label class="form-label" for="nombre_grado"><strong>Nombre del Grado</strong></label>
                                            <input class="form-control" type="text" id="nombre_grado" name="nombre_grado" required>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="crear_grado">Crear Grado</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <h4>Eliminar Grado</h4>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return confirm('¿Está seguro de eliminar este grado? Esta acción no se puede deshacer.');">
                                        <div class="mb-3">
                                            <label class="form-label" for="grado_id_eliminar"><strong>Seleccionar Grado a Eliminar</strong></label>
                                            <select class="form-select" id="grado_id_eliminar" name="grado_id" required>
                                                <option value="">Selecciona un grado</option>
                                                <?php
                                                $result_grados->data_seek(0);
                                                if ($result_grados->num_rows > 0) {
                                                    while ($row = $result_grados->fetch_assoc()) {
                                                        // Verificar si el grado está asignado a años escolares
                                                        $sql_asignado = "SELECT id FROM anios_grados WHERE grado_id = " . $row["id"];
                                                        $result_asignado = $conn->query($sql_asignado);
                                                        $esta_asignado = $result_asignado->num_rows > 0;
                                                        
                                                        // Verificar si hay estudiantes en este grado
                                                        $sql_estudiantes = "SELECT id FROM datos_academicos WHERE anio_grado_id IN (SELECT id FROM anios_grados WHERE grado_id = " . $row["id"] . ")";
                                                        $result_estudiantes = $conn->query($sql_estudiantes);
                                                        $tiene_estudiantes = $result_estudiantes->num_rows > 0;
                                                        
                                                        $disabled = $esta_asignado || $tiene_estudiantes;
                                                        $motivo = "";
                                                        
                                                        if ($tiene_estudiantes) {
                                                            $motivo = " (Tiene estudiantes)";
                                                        } elseif ($esta_asignado) {
                                                            $motivo = " (Asignado a años)";
                                                        }
                                                        
                                                        echo "<option value='" . htmlspecialchars($row["id"]) . "'" . 
                                                             ($disabled ? " disabled class='disabled-option'" : "") . ">" . 
                                                             htmlspecialchars($row["nombre"]) . 
                                                             $motivo . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <?php if ($result_grados->num_rows == 0): ?>
                                                <small class="text-muted">No hay grados disponibles para eliminar</small>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-danger" type="submit" name="eliminar_grado" id="btnEliminarGrado">Eliminar Grado</button>
                                    </form>
                                </div>
                            </div>

                            <hr>

                            <h4>Asignar Grado a Año Escolar</h4>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="anio_escolar_id"><strong>Año Escolar</strong></label>
                                        <select class="form-select" id="anio_escolar_id" name="anio_escolar_id" required>
                                            <option value="">Selecciona un año escolar</option>
                                            <?php
                                            if ($result_anios_escolares->num_rows > 0) {
                                                while ($row = $result_anios_escolares->fetch_assoc()) {
                                                    echo "<option value='" . htmlspecialchars($row["id"]) . "'>" . htmlspecialchars($row["nombre"]) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="grado_id"><strong>Grado</strong></label>
                                        <select class="form-select" id="grado_id" name="grado_id" required>
                                            <option value="">Selecciona un grado</option>
                                            <?php
                                            $result_grados->data_seek(0);
                                            if ($result_grados->num_rows > 0) {
                                                while ($row = $result_grados->fetch_assoc()) {
                                                    echo "<option value='" . htmlspecialchars($row["id"]) . "'>" . htmlspecialchars($row["nombre"]) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="secciones"><strong>Secciones (una por línea)</strong></label>
                                    <textarea class="form-control" id="secciones" name="secciones" rows="3" placeholder="Ejemplo:&#10;A&#10;B&#10;C"></textarea>
                                </div>
                                <button class="btn btn-primary" type="submit" name="asignar_grado">Asignar Grado</button>
                            </form>

                            <hr>

                            <h4>Lista de Años y Grados Asignados</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Año Escolar</th>
                                            <th>Grado</th>
                                            <th>Secciones</th>
                                            <th>Activo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_anios_grados->num_rows > 0) {
                                            while ($row = $result_anios_grados->fetch_assoc()) {
                                                // Obtener las secciones para este anio_grado
                                                $sql_secciones = "SELECT seccion FROM secciones_anio_grado WHERE anio_grado_id = " . $row["id"];
                                                $result_secciones = $conn->query($sql_secciones);
                                                $secciones = array();
                                                if ($result_secciones->num_rows > 0) {
                                                    while ($row_seccion = $result_secciones->fetch_assoc()) {
                                                        $secciones[] = htmlspecialchars($row_seccion["seccion"]);
                                                    }
                                                }
                                                $secciones_str = implode(", ", $secciones);

                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                                echo "<td>" . htmlspecialchars($row["anio_escolar"]) . "</td>";
                                                echo "<td>" . htmlspecialchars($row["grado"]) . "</td>";
                                                echo "<td>" . $secciones_str . "</td>";
                                                echo "<td>" . (htmlspecialchars($row["activo"]) ? "<span class='badge bg-success'>Activo</span>" : "<span class='badge bg-secondary'>Inactivo</span>") . "</td>";
                                                echo "<td>
                                                        <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' class='d-inline'>
                                                            <input type='hidden' name='anio_grado_id' value='" . htmlspecialchars($row["id"]) . "'>
                                                            <input type='hidden' name='activo' value='" . (htmlspecialchars($row["activo"]) ? "0" : "1") . "'>
                                                            <button type='submit' name='activar_desactivar_grado' class='btn btn-sm " . (htmlspecialchars($row["activo"]) ? "btn-warning" : "btn-success") . "'>
                                                                " . (htmlspecialchars($row["activo"]) ? "Desactivar" : "Activar") . "
                                                            </button>
                                                        </form>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center py-4'>No hay años y grados asignados.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Gestión Escolar <?php echo date('Y'); ?></span></div>
                </div>
            </footer>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEliminar = document.getElementById('grado_id_eliminar');
            const btnEliminar = document.getElementById('btnEliminarGrado');
            
            selectEliminar.addEventListener('change', function() {
                btnEliminar.disabled = this.value === '' || this.options[this.selectedIndex].disabled;
            });
            
            // Inicializar estado del botón
            btnEliminar.disabled = selectEliminar.value === '' || selectEliminar.options[selectEliminar.selectedIndex].disabled;
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>