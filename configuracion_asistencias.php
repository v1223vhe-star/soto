<?php
session_start();
include 'db.php';

// Verificar sesión y permisos de admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != 'admin') {
    header("Location: login.php");
    exit();
}

// Procesar el formulario de configuración
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dias_modificacion = intval($_POST['dias_modificacion']);
    
    // Validar que sea un número positivo
    if ($dias_modificacion >= 0) {
        $sql = "UPDATE configuracion_asistencias SET dias_modificacion = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $dias_modificacion);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Configuración actualizada correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar la configuración.";
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "El número de días debe ser un valor positivo.";
    }
    
    header("Location: configuracion_asistencias.php");
    exit();
}

// Obtener la configuración actual
$sql = "SELECT dias_modificacion FROM configuracion_asistencias WHERE id = 1";
$result = $conn->query($sql);
$config = $result->fetch_assoc();
$dias_actuales = $config['dias_modificacion'] ?? 7;
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Asistencias - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow-sm mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h3 class="text-dark mb-0 fw-bold">Configuración de Asistencias</h3>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if (isset($_SESSION['mensaje'])) : ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-3"></i>
                            <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
                        </div>
                        <?php unset($_SESSION['mensaje']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3"></i>
                            <div><?= htmlspecialchars($_SESSION['error']) ?></div>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Límite de modificación de asistencias</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="configuracion_asistencias.php">
                                <div class="form-group">
                                    <label for="dias_modificacion">Días permitidos para modificar asistencias:</label>
                                    <input type="number" class="form-control" id="dias_modificacion" 
                                           name="dias_modificacion" min="0" value="<?= $dias_actuales ?>" required>
                                    <small class="form-text text-muted">
                                        Establece 0 para no permitir modificaciones después del día de la asistencia.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                            </form>
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
</body>
</html>

<?php $conn->close(); ?>