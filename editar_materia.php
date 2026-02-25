<?php
session_start();
include 'db.php';

// Verificar sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Inicializar variables
$mensaje = "";
$error = "";

// Validar ID de materia
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de materia no válido.";
    header("Location: gestionar_materias.php");
    exit();
}

$id_materia = $_GET["id"];

// Obtener información de la materia
$sql_materia = "SELECT * FROM materias WHERE id = $id_materia";
$result_materia = $conn->query($sql_materia);

if ($result_materia->num_rows == 0) {
    $error = "Materia no encontrada.";
    header("Location: gestionar_materias.php");
    exit();
}

$materia = $result_materia->fetch_assoc();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_materia"])) {
    $nombre = trim($_POST["nombre"]);
    $estado = trim($_POST["estado"]);

    // Validaciones
    if (empty($nombre) || empty($estado)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (strlen($nombre) > 100) {
        $error = "El nombre es demasiado largo (máx. 100 caracteres).";
    } else {
        // Actualizar en la base de datos
        $sql = "UPDATE materias SET nombre = ?, estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $estado, $id_materia);
        
        if ($stmt->execute()) {
            $mensaje = "Materia actualizada correctamente.";
            // Actualizar datos locales
            $materia["nombre"] = $nombre;
            $materia["estado"] = $estado;
        } else {
            $error = "Error al actualizar: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700&display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--secondary-color);
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3a5bc7;
            border-color: #3a5bc7;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .alert {
            border-radius: 0.5rem;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h4 class="mb-0 text-gray-800">Editar Materia</h4>
                    </div>
                </nav>
                
                <div class="container-fluid">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-white">Datos de la Materia</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_materia) ?>" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre" class="form-label fw-bold">Nombre de la Materia</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?= htmlspecialchars($materia["nombre"]) ?>" required>
                                            <div class="invalid-feedback">
                                                Por favor ingresa el nombre de la materia.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado" class="form-label fw-bold">Estado</label>
                                            <select class="form-select" id="estado" name="estado" required>
                                                <option value="">Selecciona un estado</option>
                                                <option value="cursando" <?= $materia["estado"] == "cursando" ? "selected" : "" ?>>Cursando</option>
                                                <option value="finalizada" <?= $materia["estado"] == "finalizada" ? "selected" : "" ?>>Finalizada</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor selecciona el estado.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary px-4 py-2" name="editar_materia">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                    <a href="gestionar_materias.php" class="btn btn-outline-secondary px-4 py-2">
                                        <i class=""></i>Volver
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center my-auto">
                        <span class="text-muted">Sistema de Gestión Escolar &copy; <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        (function() {
            'use strict'
            
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>

<?php
$conn->close();
?>