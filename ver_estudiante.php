<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del estudiante desde la URL
$estudiante_id = $_GET['id'] ?? null;

if (!$estudiante_id) {
    header("Location: gestionar_estudiantes.php?error=ID de estudiante no válido");
    exit();
}

// Consulta para obtener los datos del estudiante
$sql_estudiante = "SELECT e.*, r.nombres as rep_nombres, r.apellidos as rep_apellidos, 
                  r.cedula as rep_cedula, r.telefono as rep_telefono, r.parentesco, r.foto as rep_foto
                  FROM estudiantes e
                  LEFT JOIN representantes r ON e.representante_id = r.id
                  WHERE e.id = ?";
$stmt = $conn->prepare($sql_estudiante);
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$result_estudiante = $stmt->get_result();

if ($result_estudiante->num_rows === 0) {
    header("Location: gestionar_estudiantes.php?error=Estudiante no encontrado");
    exit();
}

$estudiante = $result_estudiante->fetch_assoc();

// Consulta para obtener los datos académicos del estudiante
$sql_academico = "SELECT da.*, ag.grado_id, g.nombre as grado_nombre, ae.nombre as anio_escolar
                 FROM datos_academicos da
                 JOIN anios_grados ag ON da.anio_grado_id = ag.id
                 JOIN grados g ON ag.grado_id = g.id
                 JOIN anios_escolares ae ON da.anio_escolar_id = ae.id
                 WHERE da.estudiante_id = ?";
$stmt = $conn->prepare($sql_academico);
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$result_academico = $stmt->get_result();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Detalles del Estudiante - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <style>
        /* Estilo para foto carnet */
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

/* Modal personalizado */
#fotoAmpliada {
    max-height: 70vh;
    max-width: 100%;
    border-radius: 5px;
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
        .badge-custom {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }
        .table-responsive {
            overflow-x: auto;
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
        
        .badge-estado {
            padding: 0.5em 1em;
            border-radius: 1rem;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .regular { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .pendiente { background: linear-gradient(135deg, #fcd34d, #f59e0b); color: #1c1917; }
        .repitiente { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
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
                                <h3 class="text-dark mb-0 fw-bold">Detalles del Estudiante</h3>
                            </div>
                            <div>
                                <a href="gestionar_estudiantes.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Información personal del estudiante -->
                            <div class="card info-card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="photo-container">
    <div class="avatar-container" style="cursor: pointer;" onclick="ampliarFoto('<?= htmlspecialchars($estudiante['foto']) ?>')">
        <?php if ($estudiante['foto']): ?>
            <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['foto']) ?>" 
                 class="avatar-carnet" 
                 alt="Foto del estudiante">
        <?php else: ?>
            <div class="avatar-carnet avatar-placeholder">
                <i class="fas fa-user-graduate fa-2x"></i>
            </div>
        <?php endif; ?>
    
                                            <h4 class="mb-1"><?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></h4>
                                            <p class="text-muted mb-2">Cédula: <?= htmlspecialchars($estudiante['cedula']) ?></p>
                                            <span class="badge bg-primary">
                                                <?= $estudiante['sexo'] === 'masculino' ? 'Masculino' : 'Femenino' ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user-graduate me-2"></i>Información Personal
                                        </h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Cédula:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['cedula']) ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Fecha de Nacimiento:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['fecha_nacimiento']) ?> (<?= $estudiante['edad'] ?> años)</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Teléfono:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['telefono'] ?? 'No especificado') ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <p class="mb-1 info-label">Plantel de Procedencia:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['plantel_procedencia'] ?? 'No especificado') ?></p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <p class="mb-1 info-label">Lugar de Nacimiento:</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 info-label">Estado:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['estado']) ?></p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 info-label">Municipio:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['municipio']) ?></p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 info-label">Ciudad:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['localidad']) ?></p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <p class="mb-1 info-label">Observaciones:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['observaciones'] ?? 'Ninguna') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Datos académicos -->
                            <div class="card info-card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-book me-2"></i>Datos Académicos
                                        </h5>
                                    </div>
                                    <?php if ($result_academico->num_rows > 0): ?>
                                        <?php while ($academico = $result_academico->fetch_assoc()): ?>
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">Año Escolar: <?= htmlspecialchars($academico['anio_escolar']) ?></h6>
                                                    <span class="badge bg-primary badge-custom">Grado: <?= htmlspecialchars($academico['grado_nombre']) ?></span>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <p class="mb-1 info-label">Fecha de Ingreso:</p>
                                                        <p class="mb-0 info-value"><?= htmlspecialchars($academico['fecha_ingreso']) ?></p>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <p class="mb-1 info-label">Sección:</p>
                                                        <p class="mb-0 info-value"><?= htmlspecialchars($academico['seccion']) ?></p>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <p class="mb-1 info-label">Escolaridad:</p>
                                                        <p class="mb-0 info-value">
                                                            <span class="badge-estado <?= $academico['escolaridad'] ?>">
                                                                <?php 
                                                                $escolaridad = [
                                                                    'regular' => 'Regular',
                                                                    'pendiente' => 'Pendiente',
                                                                    'repitiente' => 'Repitiente',
                                                                    'doble_inscripcion' => 'Doble Inscripción',
                                                                    'repite_pendiente' => 'Repite Pendiente',
                                                                    'equivalencia' => 'Equivalencia'
                                                                ];
                                                                echo $escolaridad[$academico['escolaridad']] ?? $academico['escolaridad'];
                                                                ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No se encontraron datos académicos para este estudiante.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Representante -->
                            <div class="card info-card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="info-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user-shield me-2"></i>Representante
                                        </h5>
                                    </div>
                                    <?php if ($estudiante['representante_id']): ?>
                                        <div class="photo-container mb-3">
                                            <?php if ($estudiante['rep_foto']): ?>
                                                <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['rep_foto']) ?>" 
                                                     class="avatar-lg" 
                                                     alt="Foto del representante">
                                            <?php else: ?>
                                                <div class="avatar-lg avatar-placeholder">
                                                    <i class="fas fa-user-tie fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($estudiante['rep_nombres'] . ' ' . $estudiante['rep_apellidos']) ?></h6>
                                                <p class="text-muted mb-2"><?= htmlspecialchars($estudiante['parentesco']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-1 info-label">Cédula:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['rep_cedula']) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="mb-1 info-label">Teléfono:</p>
                                            <p class="mb-0 info-value"><?= htmlspecialchars($estudiante['rep_telefono']) ?></p>
                                        </div>
                                        <div class="d-grid">
                                            <a href="ver_representante.php?id=<?= $estudiante['representante_id'] ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-2"></i>Ver detalles del representante
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Este estudiante no tiene representante asignado.
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
                                        <a href="editar_estudiante.php?id=<?= $estudiante['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>Editar Estudiante
                                        </a>
                                        <?php if ($_SESSION["role"] === 'admin'): ?>
                                            <a href="eliminar_estudiante.php?id=<?= $estudiante['id'] ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('¿Está seguro de eliminar este estudiante? Esta acción no se puede deshacer.');">
                                                <i class="fas fa-trash-alt me-2"></i>Eliminar Estudiante
                                            </a>
                                        <?php endif; ?>
                                        
                                        </a>
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
<div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto del Estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fotoAmpliada" src="" class="img-fluid" alt="Foto ampliada del estudiante">
            </div>
        </div>
    </div>
</div>
<script>
function ampliarFoto(foto) {
    if (!foto) return;
    
    const modal = new bootstrap.Modal(document.getElementById('fotoModal'));
    document.getElementById('fotoAmpliada').src = 'assets/img/uploads/' + foto;
    modal.show();
}
</script>
</body>
</html>

<?php
$conn->close();
?>