<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene permisos
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Verificar rol de administrador
$is_admin = ($_SESSION["role"] === 'admin');

// Inicializar variables para mensajes
$mensaje = $_GET['mensaje'] ?? "";
$error = $_GET['error'] ?? "";

// Procesar búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$filtro_parentesco = $_GET['parentesco'] ?? '';

// Consulta base para obtener representantes
$sql_representantes = "SELECT r.*, 
                      (SELECT COUNT(*) FROM estudiantes e WHERE e.representante_id = r.id) as num_estudiantes
                      FROM representantes r WHERE 1=1";

// Aplicar filtros
if (!empty($busqueda)) {
    $sql_representantes .= " AND (r.nombres LIKE '%$busqueda%' OR r.apellidos LIKE '%$busqueda%' OR r.cedula LIKE '%$busqueda%')";
}

if (!empty($filtro_parentesco)) {
    $sql_representantes .= " AND r.parentesco = '$filtro_parentesco'";
}

// Ordenación
$orden = $_GET['orden'] ?? 'apellidos';
$direccion = $_GET['dir'] ?? 'ASC';
$sql_representantes .= " ORDER BY $orden $direccion";

$result_representantes = $conn->query($sql_representantes);

// Obtener opciones únicas de parentesco para el filtro
$sql_parentescos = "SELECT DISTINCT parentesco FROM representantes ORDER BY parentesco";
$result_parentescos = $conn->query($sql_parentescos);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gestionar Representantes - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <style>
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .badge-estudiantes {
            background-color: #6366f1;
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .sortable:hover {
            cursor: pointer;
            background-color: #f8f9fa;
        }
        .sort-icon {
            margin-left: 5px;
        }
        .filter-card {
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
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
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h3 class="text-dark mb-0 fw-bold">Gestión de Representantes</h3>
                            </div>
                            <div>
                                <a href="registrar_representante.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Nuevo Representante
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show">
                            <i class="fas fa-check-circle me-3"></i>
                            <div><?= htmlspecialchars($mensaje) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-3"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtros y búsqueda -->
                    <div class="card filter-card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="get" action="gestionar_representantes.php" class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="busqueda" 
                                               placeholder="Buscar por nombre, apellido o cédula..." 
                                               value="<?= htmlspecialchars($busqueda) ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="parentesco">
                                        <option value="">Todos los parentescos</option>
                                        <?php while ($row = $result_parentescos->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($row['parentesco']) ?>" 
                                                <?= ($filtro_parentesco == $row['parentesco']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['parentesco']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de representantes -->
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">Listado de Representantes</h6>
                            <span class="badge bg-primary">
                                <?= $result_representantes->num_rows ?> representantes
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sortable" onclick="sortTable('cedula')">
                                                Cédula
                                                <?= ($orden == 'cedula') ? '<i class="fas fa-sort-'.($direccion == 'ASC' ? 'up' : 'down').' sort-icon"></i>' : '' ?>
                                            </th>
                                            <th class="sortable" onclick="sortTable('apellidos')">
                                                Nombre Completo
                                                <?= ($orden == 'apellidos') ? '<i class="fas fa-sort-'.($direccion == 'ASC' ? 'up' : 'down').' sort-icon"></i>' : '' ?>
                                            </th>
                                            <th class="sortable" onclick="sortTable('parentesco')">
                                                Parentesco
                                                <?= ($orden == 'parentesco') ? '<i class="fas fa-sort-'.($direccion == 'ASC' ? 'up' : 'down').' sort-icon"></i>' : '' ?>
                                            </th>
                                            <th>Teléfono</th>
                                            <th>
                                                Estudiantes
                                                <i class="fas fa-info-circle" data-bs-toggle="tooltip" 
                                                   title="Número de estudiantes asociados"></i>
                                            </th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result_representantes->num_rows > 0): ?>
                                            <?php while ($row = $result_representantes->fetch_assoc()): ?>
                                                <tr class="card-hover">
                                                    <td><?= htmlspecialchars($row['cedula']) ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['apellidos']) ?>, <?= htmlspecialchars($row['nombres']) ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['parentesco']) ?></td>
                                                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                                                    <td>
                                                        <span class="badge badge-estudiantes">
                                                            <?= $row['num_estudiantes'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        
                                                        <div class="d-flex gap-2">
                                                            <a href="ver_representante.php?id=<?= $row['id'] ?>" 
                                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if ($is_admin): ?>
                                                            <a href="editar_representante.php?id=<?= $row['id'] ?>" 
                                                               class="btn btn-sm btn-primary" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <?php endif; ?>
                                                            <?php if ($is_admin): ?>
                                                                <a href="eliminar_representante.php?id=<?= $row['id'] ?>" 
                                                                   class="btn btn-sm btn-danger" title="Eliminar"
                                                                   onclick="return confirm('¿Está seguro de eliminar este representante? Esta acción afectará a <?= $row['num_estudiantes'] ?> estudiante(s).');">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-user-shield fs-1 text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No se encontraron representantes</p>
                                                        <a href="registrar_representante.php" class="btn btn-primary mt-3">
                                                            <i class="fas fa-plus me-2"></i>Agregar Representante
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
    
    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro de realizar esta acción?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="confirmAction" class="btn btn-danger">Confirmar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    
    <script>
        // Ordenar tabla
        function sortTable(column) {
            const url = new URL(window.location.href);
            const currentOrder = url.searchParams.get('orden');
            const currentDir = url.searchParams.get('dir');
            
            let newDir = 'ASC';
            if (currentOrder === column) {
                newDir = currentDir === 'ASC' ? 'DESC' : 'ASC';
            }
            
            url.searchParams.set('orden', column);
            url.searchParams.set('dir', newDir);
            window.location.href = url.toString();
        }
        
        // Inicializar tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Confirmación de eliminación con modal
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                const deleteUrl = $(this).attr('href');
                $('#confirmAction').attr('href', deleteUrl);
                $('#confirmModal').modal('show');
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>