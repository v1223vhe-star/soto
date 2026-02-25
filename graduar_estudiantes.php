<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Verificar permisos de administrador
if ($_SESSION["role"] != 'admin') {
    header("Location: index.php");
    exit();
}

// Obtener el año escolar activo
$sql_anio_escolar = "SELECT id, nombre FROM anios_escolares WHERE activo = 1 LIMIT 1";
$result_anio_escolar = $conn->query($sql_anio_escolar);
$anio_escolar = $result_anio_escolar->fetch_assoc();
$anio_escolar_id = $anio_escolar['id'];

// Procesar el formulario de graduación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['graduar_estudiantes'])) {
    $estudiantes_ids = $_POST['estudiantes'];
    $grado_id = $_POST['grado_id'];
    $fecha_graduacion = $_POST['fecha_graduacion'];
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        foreach ($estudiantes_ids as $estudiante_id) {
            // 1. Registrar la graduación
            $sql_graduacion = "INSERT INTO graduaciones (estudiante_id, grado_id, anio_escolar_id, fecha_graduacion) 
                              VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_graduacion);
            $stmt->bind_param("iiis", $estudiante_id, $grado_id, $anio_escolar_id, $fecha_graduacion);
            $stmt->execute();
            
            // 2. Actualizar el estado del estudiante a "graduado"
            $sql_estado = "UPDATE estudiantes SET estado = 'graduado' WHERE id = ?";
            $stmt = $conn->prepare($sql_estado);
            $stmt->bind_param("i", $estudiante_id);
            $stmt->execute();
            
            // 3. Registrar notificación
            $sql_notificacion = "INSERT INTO notificaciones (tipo, mensaje) 
                               VALUES ('graduacion', 'Estudiante ID $estudiante_id graduado')";
            $conn->query($sql_notificacion);
        }
        
        $conn->commit();
        $mensaje_exito = "Estudiantes graduados exitosamente!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error al graduar estudiantes: " . $e->getMessage();
    }
}

// Obtener estudiantes elegibles para graduación (último grado)
$sql_estudiantes = "SELECT e.id, e.cedula, e.nombres, e.apellidos, g.nombre as grado, da.seccion
                   FROM estudiantes e
                   JOIN datos_academicos da ON e.id = da.estudiante_id
                   JOIN anios_grados ag ON da.anio_grado_id = ag.id
                   JOIN grados g ON ag.grado_id = g.id
                   WHERE da.anio_escolar_id = ? 
                   AND e.estado = 'activo'
                   AND ag.grado_id IN (SELECT MAX(id) FROM grados)";
$stmt = $conn->prepare($sql_estudiantes);
$stmt->bind_param("i", $anio_escolar_id);
$stmt->execute();
$result_estudiantes = $stmt->get_result();

// Obtener grados disponibles
$sql_grados = "SELECT id, nombre FROM grados ORDER BY nombre";
$result_grados = $conn->query($sql_grados);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Graduar Estudiantes - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #4bb543;
            --warning-color: #f8961e;
            --danger-color: #f94144;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e0e0e0;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: rgba(75, 181, 67, 0.1);
            color: var(--success-color);
        }
        
        .graduation-icon {
            color: var(--success-color);
            font-size: 1.2rem;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow-sm mb-4 topbar static-top navbar-light">
                    <div class="container-fluid px-4">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-none d-lg-inline me-2 text-gray-600"><?php echo $_SESSION["username"]; ?></span>
                                    <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                        Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="fw-bold mb-0">Graduar Estudiantes</h3>
                            <p class="text-muted mb-0">Seleccione los estudiantes a graduar</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo date('d M, Y'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (isset($mensaje_exito)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $mensaje_exito; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-graduate me-2"></i>Lista de Estudiantes
                            </h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                Año Escolar: <?php echo $anio_escolar['nombre']; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="grado_id" class="form-label">Grado de Graduación</label>
                                        <select class="form-select" id="grado_id" name="grado_id" required>
                                            <option value="">Seleccione un grado</option>
                                            <?php while ($grado = $result_grados->fetch_assoc()): ?>
                                                <option value="<?php echo $grado['id']; ?>"><?php echo $grado['nombre']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_graduacion" class="form-label">Fecha de Graduación</label>
                                        <input type="date" class="form-control" id="fecha_graduacion" name="fecha_graduacion" required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="40">
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th>Cédula</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Grado Actual</th>
                                                <th>Sección</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result_estudiantes->num_rows > 0): ?>
                                                <?php while ($estudiante = $result_estudiantes->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="estudiantes[]" value="<?php echo $estudiante['id']; ?>" class="estudiante-checkbox">
                                                        </td>
                                                        <td><?php echo $estudiante['cedula']; ?></td>
                                                        <td><?php echo $estudiante['nombres']; ?></td>
                                                        <td><?php echo $estudiante['apellidos']; ?></td>
                                                        <td><?php echo $estudiante['grado']; ?></td>
                                                        <td><?php echo $estudiante['seccion']; ?></td>
                                                        <td>
                                                            <span class="badge badge-success">Activo</span>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <i class="fas fa-user-graduate graduation-icon me-2"></i>
                                                        No hay estudiantes elegibles para graduación
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if ($result_estudiantes->num_rows > 0): ?>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" name="graduar_estudiantes" class="btn btn-success">
                                            <i class="fas fa-graduation-cap me-2"></i>Graduar Estudiantes Seleccionados
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright">
                        <span>Copyright © Gestión Escolar <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar/deseleccionar todos los estudiantes
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.estudiante-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
            
            // Validación antes de enviar el formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const checked = document.querySelectorAll('.estudiante-checkbox:checked');
                if (checked.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe seleccionar al menos un estudiante para graduar',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>