<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener parámetro de búsqueda
$busqueda = $_GET["busqueda"] ?? null;

// Consulta para buscar estudiantes
$estudiantes = [];
if ($busqueda) {
    $busqueda_like = "%" . $conn->real_escape_string($busqueda) . "%";
    
    $sql = "SELECT 
            e.id, e.cedula, e.nombres, e.apellidos, e.sexo, e.fecha_nacimiento, 
            TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) as edad,
            e.telefono, e.estado, e.municipio, e.localidad, e.plantel_procedencia, e.observaciones,
            r.id as representante_id, r.cedula as cedula_representante, r.nombres as nombres_representante, 
            r.apellidos as apellidos_representante, r.telefono as telefono_representante, 
            r.parentesco, r.correo_electronico as email_representante,
            da.escolaridad, da.fecha_ingreso, 
            g.nombre as grado, g.id as grado_id,
            ae.nombre as anio_escolar, ae.id as anio_escolar_id,
            da.seccion
            FROM estudiantes e
            LEFT JOIN datos_academicos da ON e.id = da.estudiante_id
            LEFT JOIN anios_grados ag ON da.anio_grado_id = ag.id
            LEFT JOIN grados g ON ag.grado_id = g.id
            LEFT JOIN anios_escolares ae ON ag.anio_escolar_id = ae.id
            LEFT JOIN representantes r ON e.representante_id = r.id
            WHERE e.cedula LIKE '$busqueda_like' 
               OR CONCAT(e.nombres, ' ', e.apellidos) LIKE '$busqueda_like'
               OR CONCAT(r.nombres, ' ', r.apellidos) LIKE '$busqueda_like'
               OR r.cedula LIKE '$busqueda_like'
            ORDER BY e.apellidos, e.nombres";
    
    $result = $conn->query($sql);
    if ($result) {
        $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Estudiante - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        .student-card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .student-header {
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1rem 1.5rem;
        }
        .student-body {
            padding: 1.5rem;
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
        .search-box {
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .no-results {
            padding: 3rem 0;
            text-align: center;
        }
        .info-label {
            font-weight: 600;
            color: #4b5563;
        }
    </style>
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
                                <h3 class="text-dark mb-0 fw-bold">Buscar Estudiante</h3>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <!-- Formulario de búsqueda -->
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form action="buscar_estudiante.php" method="get" class="search-box">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" 
                                           name="busqueda" placeholder="Buscar por cédula, nombre o apellido" 
                                           value="<?= htmlspecialchars($busqueda) ?>" required>
                                    <button class="btn btn-primary px-4" type="submit">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resultados de búsqueda -->
                    <?php if ($busqueda): ?>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-4">
                                    <?php if (count($estudiantes) > 0): ?>
                                        Resultados para "<?= htmlspecialchars($busqueda) ?>"
                                        <span class="badge bg-primary ms-2"><?= count($estudiantes) ?> encontrados</span>
                                    <?php else: ?>
                                        No se encontraron resultados para "<?= htmlspecialchars($busqueda) ?>"
                                    <?php endif; ?>
                                </h5>
                            </div>
                        </div>

                        <?php foreach ($estudiantes as $estudiante): ?>
                            <div class="card student-card mb-4">
                                <div class="card-header student-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <?= htmlspecialchars($estudiante['apellidos']) ?>, <?= htmlspecialchars($estudiante['nombres']) ?>
                                            <small class="ms-2">C.I. <?= htmlspecialchars($estudiante['cedula']) ?></small>
                                        </h5>
                                        <span class="badge-estado <?= $estudiante['escolaridad'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $estudiante['escolaridad'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body student-body">
                                    <div class="row">
                                        <!-- Datos del Estudiante -->
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-user-graduate me-2"></i>Datos del Estudiante
                                            </h6>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Fecha Nacimiento:</div>
                                                <div class="col-sm-8">
                                                    <?= date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) ?> 
                                                    (<?= $estudiante['edad'] ?> años)
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Sexo:</div>
                                                <div class="col-sm-8"><?= ucfirst($estudiante['sexo']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Teléfono:</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($estudiante['telefono']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Ubicación:</div>
                                                <div class="col-sm-8">
                                                    <?= htmlspecialchars($estudiante['estado']) ?>, 
                                                    <?= htmlspecialchars($estudiante['municipio']) ?>, 
                                                    <?= htmlspecialchars($estudiante['localidad']) ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Plantel Procedencia:</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($estudiante['plantel_procedencia'] ?: 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 info-label">Observaciones:</div>
                                                <div class="col-sm-8"><?= nl2br(htmlspecialchars($estudiante['observaciones'] ?: 'Ninguna')) ?></div>
                                            </div>
                                        </div>

                                        <!-- Datos Académicos y Representante -->
                                        <div class="col-md-6">
                                            <?php if ($estudiante['grado']): ?>
                                                <h6 class="fw-bold text-primary mb-3">
                                                    <i class="fas fa-book me-2"></i>Datos Académicos
                                                </h6>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Año Escolar:</div>
                                                    <div class="col-sm-8"><?= htmlspecialchars($estudiante['anio_escolar']) ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Grado/Sección:</div>
                                                    <div class="col-sm-8">
                                                        <?= htmlspecialchars($estudiante['grado']) ?> - 
                                                        <?= htmlspecialchars($estudiante['seccion']) ?>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Fecha Ingreso:</div>
                                                    <div class="col-sm-8"><?= date('d/m/Y', strtotime($estudiante['fecha_ingreso'])) ?></div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($estudiante['representante_id']): ?>
                                                <h6 class="fw-bold text-primary mb-3 mt-4">
                                                    <i class="fas fa-user-tie me-2"></i>Datos del Representante
                                                </h6>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Nombre:</div>
                                                    <div class="col-sm-8">
                                                        <?= htmlspecialchars($estudiante['apellidos_representante']) ?>, 
                                                        <?= htmlspecialchars($estudiante['nombres_representante']) ?>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Cédula:</div>
                                                    <div class="col-sm-8"><?= htmlspecialchars($estudiante['cedula_representante']) ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Teléfono:</div>
                                                    <div class="col-sm-8"><?= htmlspecialchars($estudiante['telefono_representante']) ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Parentesco:</div>
                                                    <div class="col-sm-8"><?= htmlspecialchars($estudiante['parentesco']) ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 info-label">Correo:</div>
                                                    <div class="col-sm-8"><?= htmlspecialchars($estudiante['email_representante'] ?: 'N/A') ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                               <div class="card-footer bg-light d-flex justify-content-end">
     <?php if ($user_role == 'admin'): ?>
        <a href="editar_estudiante.php?id=<?= $estudiante['id'] ?>" 
           class="btn btn-sm btn-outline-primary me-2">
            <i class="fas fa-edit me-1"></i> Editar
        </a>
    <?php else: ?>
        <span class="btn btn-sm btn-outline-secondary me-2" title="Solo disponible para administradores">
            <i class="fas fa-lock me-1"></i> Editar
        </span>
    <?php endif; ?>
    <a href="gestionar_estudiantes.php?anio_escolar_id=<?= $estudiante['anio_escolar_id'] ?>&grado_id=<?= $estudiante['grado_id'] ?>&seccion=<?= $estudiante['seccion'] ?>" 
       class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-users me-1"></i> Ver Sección
    </a>
</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Ingrese un término de búsqueda</h5>
                            <p class="text-muted">Busque estudiantes por cédula, nombre o apellido</p>
                        </div>
                    <?php endif; ?>
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