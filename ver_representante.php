<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del representante desde la URL
$representante_id = $_GET['id'] ?? null;

if (!$representante_id) {
    header("Location: gestionar_representantes.php?error=ID de representante no válido");
    exit();
}

// Consulta para obtener los datos del representante
$sql_representante = "SELECT * FROM representantes WHERE id = ?";
$stmt = $conn->prepare($sql_representante);
$stmt->bind_param("i", $representante_id);
$stmt->execute();
$result_representante = $stmt->get_result();

if ($result_representante->num_rows === 0) {
    header("Location: gestionar_representantes.php?error=Representante no encontrado");
    exit();
}

$representante = $result_representante->fetch_assoc();

// Consulta para obtener los estudiantes asociados a este representante
$sql_estudiantes = "SELECT * FROM estudiantes WHERE representante_id = ?";
$stmt = $conn->prepare($sql_estudiantes);
$stmt->bind_param("i", $representante_id);
$stmt->execute();
$result_estudiantes = $stmt->get_result();

// Consulta para contar el número de estudiantes asociados
$sql_count = "SELECT COUNT(*) as total FROM estudiantes WHERE representante_id = ?";
$stmt = $conn->prepare($sql_count);
$stmt->bind_param("i", $representante_id);
$stmt->execute();
$count_result = $stmt->get_result();
$count_data = $count_result->fetch_assoc();
$total_estudiantes = $count_data['total'];
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Detalles del Representante - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <style>
        /* Estilo para foto carnet de representante */
.avatar-carnet {
    width: 120px;
    height: 150px;
    object-fit: cover;
    border: 3px solid #e9ecef;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.avatar-carnet:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Estilos para el placeholder del representante */
.avatar-carnet.avatar-placeholder {
    width: 120px;
    height: 150px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    color: #adb5bd;
    font-size: 2.5rem;
}

/* Modal para foto ampliada */
.modal-foto-carnet .modal-dialog {
    max-width: 90%;
    max-height: 90vh;
}

.modal-foto-carnet .modal-body img {
    max-height: 70vh;
    max-width: 100%;
    border-radius: 5px;
    margin: 0 auto;
    display: block;
}

.modal-foto-carnet .modal-footer {
    justify-content: center;
}
    </style>
    <style>
        .info-card {
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .info-header {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        
        /* Estilos para las fotos */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }

        .avatar-lg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }

        .avatar-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #adb5bd;
        }

        .avatar-container {
            position: relative;
            width: fit-content;
        }

        .avatar-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #4361ee;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border: 2px solid white;
        }
        
        .photo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .student-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }
        
        .student-item:hover {
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
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h3 class="text-dark mb-0 fw-bold">Detalles del Representante</h3>
                            </div>
                            <div>
                                <a href="gestionar_representantes.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Información básica del representante -->
                            <div class="card info-card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="photo-container">
    <div class="avatar-container" style="cursor: pointer;" onclick="ampliarFoto('<?= htmlspecialchars($representante['foto']) ?>')">
        <?php if ($representante['foto']): ?>
            <img src="assets/img/uploads/<?= htmlspecialchars($representante['foto']) ?>" 
                 class="avatar-carnet" 
                 alt="Foto del representante">
        <?php else: ?>
            <div class="avatar-carnet avatar-placeholder">
                <i class="fas fa-user-tie fa-2x"></i>
            </div>
        <?php endif; ?>
    </div>
    <div>
        <h4 class="mb-1"><?= htmlspecialchars($representante['nombres'] . ' ' . $representante['apellidos']) ?></h4>
        <p class="text-muted mb-2">Cédula: <?= htmlspecialchars($representante['cedula']) ?></p>
        <span class="badge bg-primary">
            <?= htmlspecialchars($representante['parentesco']) ?>
        </span>
    </div>
</div>
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user-shield me-2"></i>Información Personal
                                        </h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Cédula:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['cedula']) ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Fecha de Nacimiento:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['fecha_nacimiento']) ?> (<?= $representante['edad'] ?> años)</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Nacionalidad:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['nacionalidad']) ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Profesión/Oficio:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['profesion_oficio'] ?? 'No especificado') ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Teléfono:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['telefono']) ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Correo Electrónico:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($representante['correo_electronico'] ?? 'No especificado') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           <!-- Dirección -->
<div class="card info-card shadow-sm mb-4">
    <div class="card-body">
        <div class="info-header">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-map-marker-alt me-2"></i>Dirección
            </h5>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <p class="mb-1 info-label">Parroquia:</p>
                <p class="mb-0 info-value"><?= htmlspecialchars($representante['parroquia']) ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <p class="mb-1 info-label">Sector:</p>
                <p class="mb-0 info-value"><?= htmlspecialchars($representante['sector']) ?></p>
            </div>
            
            <div class="col-12 mb-3">
                <p class="mb-1 info-label">Dirección Completa:</p>
                <p class="mb-0 info-value"><?= htmlspecialchars($representante['direccion']) ?></p>
            </div>
        </div>
    </div>

                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Resumen y estudiantes asociados -->
                            <div class="card info-card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-users me-2"></i>Estudiantes Asociados
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="mb-0 info-label">Total de Estudiantes:</p>
                                        <span class="badge bg-primary rounded-pill"><?= $total_estudiantes ?></span>
                                    </div>

                                    <?php if ($result_estudiantes->num_rows > 0): ?>
                                        <div class="list-group">
                                            <?php while ($estudiante = $result_estudiantes->fetch_assoc()): ?>
                                                <a href="ver_estudiante.php?id=<?= $estudiante['id'] ?>" class="list-group-item list-group-item-action p-0">
                                                    <div class="student-item">
                                                        <div class="avatar-container">
                                                            <?php if ($estudiante['foto']): ?>
                                                                <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['foto']) ?>" 
                                                                     class="avatar" 
                                                                     alt="Foto del estudiante">
                                                            <?php else: ?>
                                                                <div class="avatar avatar-placeholder">
                                                                    <i class="fas fa-user"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1"><?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></h6>
                                                            <small class="text-muted">Cédula: <?= htmlspecialchars($estudiante['cedula']) ?></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Este representante no tiene estudiantes asociados.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card info-card shadow-sm">
                                <div class="card-body">
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-cogs me-2"></i>Acciones
                                        </h5>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="editar_representante.php?id=<?= $representante['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>Editar Representante
                                        </a>
                                        <?php if ($_SESSION["role"] === 'admin'): ?>
                                            <a href="eliminar_representante.php?id=<?= $representante['id'] ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('¿Está seguro de eliminar este representante? Esta acción afectará a <?= $total_estudiantes ?> estudiante(s).');">
                                                <i class="fas fa-trash-alt me-2"></i>Eliminar Representante
                                            </a>
                                        <?php endif; ?>
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
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <!-- Modal para foto ampliada -->
<div class="modal fade modal-foto-carnet" id="fotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto del Representante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fotoAmpliada" src="" class="img-fluid" alt="Foto ampliada del representante">
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" onclick="zoomOut()">
                    <i class="fas fa-search-minus"></i> Alejar
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="zoomIn()">
                    <i class="fas fa-search-plus"></i> Acercar
                </button>
            </div>
        </div>
    </div>
</div>
<script>
let currentZoom = 1;
const zoomStep = 0.25;

function ampliarFoto(foto) {
    if (!foto) return;
    
    const modal = new bootstrap.Modal(document.getElementById('fotoModal'));
    const imgElement = document.getElementById('fotoAmpliada');
    imgElement.src = 'assets/img/uploads/' + foto;
    imgElement.style.transform = 'scale(1)';
    currentZoom = 1;
    modal.show();
}

function zoomIn() {
    const imgElement = document.getElementById('fotoAmpliada');
    currentZoom += zoomStep;
    imgElement.style.transform = `scale(${currentZoom})`;
}

function zoomOut() {
    const imgElement = document.getElementById('fotoAmpliada');
    if (currentZoom > zoomStep) {
        currentZoom -= zoomStep;
        imgElement.style.transform = `scale(${currentZoom})`;
    }
}

// Permitir zoom con la rueda del mouse
document.getElementById('fotoAmpliada')?.addEventListener('wheel', function(e) {
    e.preventDefault();
    if (e.deltaY < 0) {
        zoomIn();
    } else {
        zoomOut();
    }
});
</script>
</body>
</html>

<?php
$conn->close();
?>