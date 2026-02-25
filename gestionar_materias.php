<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$error = "";

// Procesar creación de materia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_materia"])) {
    $nombre = trim($_POST["nombre"]);
    $estado = trim($_POST["estado"]);

    if (empty($nombre) || empty($estado)) {
        $error = "Por favor, complete todos los campos obligatorios.";
    } else {
        $sql = "INSERT INTO materias (nombre, estado) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $estado);
        
        if ($stmt->execute()) {
            $mensaje = "Materia creada correctamente.";
        } else {
            $error = "Error al crear la materia: " . $conn->error;
        }
        $stmt->close();
    }
}

// Consulta para obtener materias con grados asignados
$sql_materias = "SELECT m.id, m.nombre, m.estado,
                GROUP_CONCAT(DISTINCT g.nombre ORDER BY g.nombre SEPARATOR ', ') AS grados_asignados
                FROM materias m
                LEFT JOIN materias_grados mg ON m.id = mg.materia_id
                LEFT JOIN grados g ON mg.grado_id = g.id
                GROUP BY m.id
                ORDER BY m.nombre";
$result_materias = $conn->query($sql_materias);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Materias - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
     <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
        }
        .grados-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .grados-cell:hover {
            white-space: normal;
            overflow: visible;
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
                        <div class="d-flex justify-content-between w-100 align-items-center">
                            <h3 class="text-dark mb-0">Gestión de Materias</h3>
                            <div>
                                <span class="me-2 text-gray-600"><?= htmlspecialchars($_SESSION["username"]) ?></span>
                                <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg" width="40" height="40">
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Crear Nueva Materia</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nombre" class="form-label">Nombre*</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="estado" class="form-label">Estado*</label>
                                                <select class="form-select" id="estado" name="estado" required>
                                                    <option value="">Seleccionar...</option>
                                                    <option value="cursando">Cursando</option>
                                                    <option value="finalizada">Finalizada</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="submit" name="crear_materia" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Crear Materia
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Listado de Materias con Grados Asignados</h6>
                                    <div class="d-flex">
                                        <input type="text" id="buscarMateria" class="form-control form-control-sm me-2" placeholder="Buscar materia..." style="width: 200px;">
                                        <span class="badge bg-primary rounded-pill align-self-center">
                                            <?= $result_materias->num_rows ?> materias
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="tablaMaterias">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="40%">Nombre</th>
                                                    <th width="20%">Estado</th>
                                                    <th width="25%">Grados Asignados</th>
                                                    <th width="10%">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result_materias->num_rows > 0) : ?>
                                                    <?php while ($row = $result_materias->fetch_assoc()) : ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                            <td>
                                                                <span class="badge <?= $row['estado'] == 'cursando' ? 'bg-success' : 'bg-secondary' ?>">
                                                                    <?= htmlspecialchars($row['estado']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="grados-cell" title="<?= htmlspecialchars($row['grados_asignados']) ?>">
                                                                <?= $row['grados_asignados'] ? htmlspecialchars($row['grados_asignados']) : 'Sin asignar' ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group" role="group">
                                                                    <a href="editar_materia.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="eliminar_materia.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" 
                                                                       onclick="return confirm('¿Está seguro de eliminar esta materia?');">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">No hay materias registradas</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="bg-white sticky-footer border-top">
                <div class="container my-auto">
                    <div class="text-center my-auto py-3 text-muted">
                        <span>© Gestión Escolar <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#buscarMateria").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#tablaMaterias tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>