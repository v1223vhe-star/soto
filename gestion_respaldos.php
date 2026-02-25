<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != 'admin') {
    header("Location: login.php");
    exit();
}

// Directorio donde se guardarán los respaldos
$backup_dir = "backups/";
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Función para crear respaldo
if (isset($_POST['crear_respaldo'])) {
    $fecha = date('Y-m-d_H-i-s');
    $backup_file = $backup_dir . "respaldo_" . $fecha . ".sql";
    
    // Comando para crear el respaldo
    $command = "mysqldump --user=" . DB_USER . " --password=" . DB_PASS . " --host=" . DB_HOST . " " . DB_NAME . " > " . $backup_file;
    
    system($command, $output);
    
    if ($output === 0) {
        $_SESSION['success_msg'] = "Respaldo creado correctamente: " . basename($backup_file);
    } else {
        $_SESSION['error_msg'] = "Error al crear el respaldo";
    }
    header("Location: gestion_respaldos.php");
    exit();
}

// Función para restaurar respaldo
if (isset($_POST['restaurar_respaldo']) && isset($_POST['backup_file'])) {
    $backup_file = $backup_dir . $_POST['backup_file'];
    
    if (file_exists($backup_file)) {
        // Comando para restaurar el respaldo
        $command = "mysql --user=" . DB_USER . " --password=" . DB_PASS . " --host=" . DB_HOST . " " . DB_NAME . " < " . $backup_file;
        
        system($command, $output);
        
        if ($output === 0) {
            $_SESSION['success_msg'] = "Respaldo restaurado correctamente: " . $_POST['backup_file'];
        } else {
            $_SESSION['error_msg'] = "Error al restaurar el respaldo";
        }
    } else {
        $_SESSION['error_msg'] = "El archivo de respaldo no existe";
    }
    header("Location: gestion_respaldos.php");
    exit();
}

// Función para eliminar respaldo
if (isset($_GET['eliminar']) && isset($_GET['archivo'])) {
    $backup_file = $backup_dir . $_GET['archivo'];
    
    if (file_exists($backup_file)) {
        if (unlink($backup_file)) {
            $_SESSION['success_msg'] = "Respaldo eliminado correctamente: " . $_GET['archivo'];
        } else {
            $_SESSION['error_msg'] = "Error al eliminar el respaldo";
        }
    } else {
        $_SESSION['error_msg'] = "El archivo de respaldo no existe";
    }
    header("Location: gestion_respaldos.php");
    exit();
}

// Obtener lista de respaldos existentes
$backups = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if ($file !== "." && $file !== ".." && pathinfo($file, PATHINFO_EXTENSION) === "sql") {
            $backups[] = [
                'nombre' => $file,
                'ruta' => $backup_dir . $file,
                'tamaño' => filesize($backup_dir . $file),
                'fecha' => date("Y-m-d H:i:s", filemtime($backup_dir . $file))
            ];
        }
    }
}

// Configuración de db.php (deberías tener estos datos en tu archivo db.php)
// define('DB_HOST', 'localhost');
// define('DB_USER', 'tu_usuario');
// define('DB_PASS', 'tu_contraseña');
// define('DB_NAME', 'distribuidora_pugas');
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gestión de Respaldos</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <style>
        .backup-card {
            transition: all 0.3s ease;
            border-left: 4px solid #6c5ce7;
        }
        .backup-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .file-size {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand navbar-glass shadow-sm">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none" id="sidebarToggleTop" type="button">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <span class="me-2 text-gray-700 fw-medium"><?php echo $_SESSION["username"]; ?></span>
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow">
                                    <a class="dropdown-item d-flex align-items-center" href="profile.php">
                                        <i class="fas fa-user-circle me-2 text-gray-500"></i>
                                        Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item d-flex align-items-center" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2 text-gray-500"></i>
                                        Salir
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="container-fluid pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="dashboard-title mb-0">Gestión de Respaldos</h3>
                        <form method="post">
                            <button type="submit" name="crear_respaldo" class="btn btn-primary">
                                <i class="fas fa-database me-2"></i>Crear Respaldo
                            </button>
                        </form>
                    </div>

                    <?php if (isset($_SESSION['success_msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_msg']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_msg']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_msg'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error_msg']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_msg']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Respaldos Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($backups)): ?>
                                        <div class="alert alert-info mb-0">
                                            No hay respaldos disponibles.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre del Archivo</th>
                                                        <th>Tamaño</th>
                                                        <th>Fecha de Creación</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($backups as $backup): ?>
                                                        <tr>
                                                            <td><?php echo $backup['nombre']; ?></td>
                                                            <td><?php echo formatSizeUnits($backup['tamaño']); ?></td>
                                                            <td><?php echo $backup['fecha']; ?></td>
                                                            <td>
                                                                <form method="post" action="gestion_respaldos.php" class="d-inline">
                                                                    <input type="hidden" name="backup_file" value="<?php echo $backup['nombre']; ?>">
                                                                    <button type="submit" name="restaurar_respaldo" class="btn btn-sm btn-success me-2" onclick="return confirm('¿Estás seguro de que deseas restaurar este respaldo? Esto sobrescribirá todos los datos actuales.')">
                                                                        <i class="fas fa-undo me-1"></i> Restaurar
                                                                    </button>
                                                                </form>
                                                                <a href="gestion_respaldos.php?archivo=<?php echo urlencode($backup['nombre']); ?>&eliminar=1" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este respaldo?')">
                                                                    <i class="fas fa-trash me-1"></i> Eliminar
                                                                </a>
                                                                <a href="<?php echo $backup['ruta']; ?>" class="btn btn-sm btn-info ms-2" download>
                                                                    <i class="fas fa-download me-1"></i> Descargar
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card backup-card shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-info-circle me-2 text-primary"></i>Información del Sistema</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Base de Datos
                                            <span class="badge bg-primary rounded-pill"><?php echo DB_NAME; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Tamaño de la BD
                                            <span class="badge bg-primary rounded-pill">
                                                <?php 
                                                    $result = $conn->query("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "'");
                                                    $row = $result->fetch_assoc();
                                                    echo round($row['size'], 2) . ' MB';
                                                ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Tablas
                                            <span class="badge bg-primary rounded-pill">
                                                <?php 
                                                    $result = $conn->query("SELECT COUNT(*) AS count FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "'");
                                                    $row = $result->fetch_assoc();
                                                    echo $row['count'];
                                                ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card backup-card shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-lightbulb me-2 text-warning"></i>Recomendaciones</h5>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Importante:</strong> Realiza respaldos periódicos para prevenir pérdida de datos.
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Realiza respaldos antes de actualizaciones importantes
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Almacena respaldos en un lugar seguro fuera del servidor
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Verifica periódicamente que los respaldos sean restaurables
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid py-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between small">
                        <div class="text-muted mb-2 mb-md-0">© <?= date('Y') ?> Gestión de Personal</div>
                        <div class="d-flex">
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Función para formatear el tamaño del archivo
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>