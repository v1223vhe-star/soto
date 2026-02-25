<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Al inicio del archivo, después de verificar la sesión
$user_id = $_SESSION["user_id"];
$sql_user = "SELECT role FROM usuarios WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user_role = $result_user->fetch_assoc()['role'];

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Obtener parámetros de búsqueda
$busqueda = $_GET["busqueda"] ?? null;
$anio_escolar_id = $_GET["anio_escolar_id"] ?? null;
$grado_id = $_GET["grado_id"] ?? null;
$seccion = $_GET["seccion"] ?? null;

// Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT * FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// Consulta para obtener la lista de grados para el año escolar seleccionado
if ($anio_escolar_id) {
    $sql_grados = "SELECT g.id, g.nombre, COUNT(da.id) as total_estudiantes
                   FROM grados g
                   LEFT JOIN anios_grados ag ON g.id = ag.grado_id
                   LEFT JOIN datos_academicos da ON ag.id = da.anio_grado_id
                   WHERE ag.anio_escolar_id = $anio_escolar_id
                   GROUP BY g.id, g.nombre";
    $result_grados = $conn->query($sql_grados);
} else {
    $sql_grados = "SELECT * FROM grados";
    $result_grados = $conn->query($sql_grados);
}

// Consulta para obtener la lista de secciones con conteo de estudiantes
if ($anio_escolar_id && $grado_id) {
    $sql_secciones = "SELECT sag.seccion, COUNT(da.id) as total_estudiantes
                      FROM secciones_anio_grado sag
                      INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                      LEFT JOIN datos_academicos da ON (ag.id = da.anio_grado_id AND da.seccion = sag.seccion)
                      WHERE ag.anio_escolar_id = $anio_escolar_id AND ag.grado_id = $grado_id
                      GROUP BY sag.seccion";
    $result_secciones = $conn->query($sql_secciones);
}

// Consulta para obtener estudiantes si se seleccionó sección
if ($anio_escolar_id && $grado_id && $seccion) {
    $sql_estudiantes = "SELECT 
                        e.id, e.cedula, e.nombres, e.apellidos, e.sexo, e.fecha_nacimiento, 
                        TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) as edad,
                        e.telefono, e.estado, e.municipio, e.localidad, e.plantel_procedencia, e.observaciones, e.foto as foto_estudiante,
                        r.id as representante_id, r.cedula as cedula_representante, r.nombres as nombres_representante, 
                        r.apellidos as apellidos_representante, r.telefono as telefono_representante, 
                        r.parentesco, r.correo_electronico as email_representante, r.foto as foto_representante,
                        da.escolaridad, da.fecha_ingreso, 
                        g.nombre as grado,
                        ae.nombre as anio_escolar
                        FROM estudiantes e
                        INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                        INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                        INNER JOIN grados g ON ag.grado_id = g.id
                        INNER JOIN anios_escolares ae ON ag.anio_escolar_id = ae.id
                        LEFT JOIN representantes r ON e.representante_id = r.id
                        WHERE ag.anio_escolar_id = $anio_escolar_id 
                        AND ag.grado_id = $grado_id
                        AND da.seccion = '$seccion'";
    
    // Si hay búsqueda, agregar filtro
    if ($busqueda) {
        $busqueda_like = "%" . $conn->real_escape_string($busqueda) . "%";
        $sql_estudiantes .= " AND (e.cedula LIKE '$busqueda_like' 
                              OR e.nombres LIKE '$busqueda_like' 
                              OR e.apellidos LIKE '$busqueda_like'
                              OR r.nombres LIKE '$busqueda_like'
                              OR r.apellidos LIKE '$busqueda_like')";
    }
    
    $sql_estudiantes .= " ORDER BY e.apellidos, e.nombres";
    $result_estudiantes = $conn->query($sql_estudiantes);
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estudiantes - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root { --primary-color: #6366f1; --primary-hover: #4f46e5; }
        body { font-family: 'Nunito', system-ui, -apple-system, sans-serif; letter-spacing: 0.02rem; }
        .details-row { display: none; background: #f8f9fa; }
        .details-row.active { display: table-row; animation: fadeIn 0.3s ease-in; }
        .toggle-details { cursor: pointer; transition: background 0.2s; }
        .toggle-details:hover { background-color: #f8f9fa; }
        .badge-estado { padding: 0.5em 1em; border-radius: 1rem; font-size: 0.75em; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .regular { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .pendiente { background: linear-gradient(135deg, #fcd34d, #f59e0b); color: #1c1917; }
        .repitiente { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .table-hover tbody tr:hover { transform: translateX(4px); transition: transform 0.2s ease; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
        .btn-success { background: linear-gradient(135deg, #22c55e, #16a34a); border: none; padding: 0.5rem 1.25rem; border-radius: 0.75rem; font-weight: 600; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .chevron-toggle { transition: transform 0.3s ease; }
        .chevron-toggle.rotated { transform: rotate(90deg); }
        .search-box { max-width: 600px; border-radius: 0.75rem; border: 1px solid #e5e7eb; }
        .alert { border-radius: 0.75rem; padding: 1rem 1.5rem; }
        .filter-card { border-radius: 0.75rem; margin-bottom: 1.5rem; }
        .filter-header { border-radius: 0.75rem 0.75rem 0 0; }
        .filter-title { font-size: 0.9rem; font-weight: 600; letter-spacing: 0.05em; }
        .filter-badge { background-color: #e0e7ff; color: var(--primary-color); padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .count-badge { position: absolute; top: -10px; right: -10px; }
        .search-container { position: relative; }
        .search-results { position: absolute; width: 100%; z-index: 1000; background: white; border: 1px solid #dee2e6; border-radius: 0 0 8px 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; display: none; }
        .search-item { padding: 10px 15px; cursor: pointer; transition: background 0.2s; }
        .search-item:hover { background-color: #f8f9fa; }
        .no-results { padding: 10px 15px; color: #6c757d; }
        .search-loading { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d; display: none; }
        
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
        
        .student-photo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .representative-photo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
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
                                <h3 class="text-dark mb-0 fw-bold">Gestión de Estudiantes</h3>
                            </div>
                            <div>
                                <a href="inscribir_alumno.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Nuevo Estudiante
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-3"></i>
                            <div><?= $mensaje ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3"></i>
                            <div><?= $error ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación jerárquica -->
                  <!-- Reemplazar la sección de navegación jerárquica con este nuevo diseño -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de búsqueda</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Filtro de Año Escolar -->
            <div class="col-md-4">
                <label for="anio_escolar" class="form-label small fw-bold text-muted">Año Escolar</label>
                <select class="form-select" id="anio_escolar" onchange="location = this.value">
                    <option value="gestionar_estudiantes.php">Seleccione un año</option>
                    <?php $result_anios_escolares->data_seek(0); ?>
                    <?php while ($row = $result_anios_escolares->fetch_assoc()): ?>
                        <option value="gestionar_estudiantes.php?anio_escolar_id=<?= $row['id'] ?>"
                            <?= $anio_escolar_id == $row['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nombre']) ?> (<?= date('Y', strtotime($row['fecha_inicio'])) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Filtro de Grado (solo visible si hay año seleccionado) -->
            <div class="col-md-4" <?= !$anio_escolar_id ? 'style="opacity:0.5;pointer-events:none"' : '' ?>>
                <label for="grado" class="form-label small fw-bold text-muted">Grado</label>
                <select class="form-select" id="grado" onchange="location = this.value" <?= !$anio_escolar_id ? 'disabled' : '' ?>>
                    <option value="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>">Seleccione un grado</option>
                    <?php if ($anio_escolar_id): ?>
                        <?php $result_grados->data_seek(0); ?>
                        <?php while ($row = $result_grados->fetch_assoc()): ?>
                            <option value="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $row['id'] ?>"
                                <?= $grado_id == $row['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nombre']) ?> (<?= $row['total_estudiantes'] ?? 0 ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Filtro de Sección (solo visible si hay grado seleccionado) -->
            <div class="col-md-4" <?= !$grado_id ? 'style="opacity:0.5;pointer-events:none"' : '' ?>>
                <label for="seccion" class="form-label small fw-bold text-muted">Sección</label>
                <select class="form-select" id="seccion" onchange="location = this.value" <?= !$grado_id ? 'disabled' : '' ?>>
                    <option value="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>">Seleccione una sección</option>
                    <?php if ($grado_id): ?>
                        <?php while ($row = $result_secciones->fetch_assoc()): ?>
                            <option value="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $row['seccion'] ?>"
                                <?= $seccion == $row['seccion'] ? 'selected' : '' ?>>
                                Sección <?= htmlspecialchars($row['seccion']) ?> (<?= $row['total_estudiantes'] ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
</div>



                    <!-- Mostrar años escolares si no se ha seleccionado ninguno -->
                    <?php if (!$anio_escolar_id): ?>
                        <div class="row">
                            <h4 class="mb-4">Seleccione un año escolar</h4>
                            <?php while ($row = $result_anios_escolares->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <a href="gestionar_estudiantes.php?anio_escolar_id=<?= $row['id'] ?>" class="card card-hover text-decoration-none">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
                                            <p class="text-muted"><?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($row['fecha_fin'])) ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    
                    <!-- Mostrar grados si se seleccionó año escolar pero no grado -->
                    <?php elseif (!$grado_id): ?>
                        <div class="row">
                            <h4 class="mb-4">Seleccione un grado</h4>
                            <?php while ($row = $result_grados->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <a href="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $row['id'] ?>" class="card card-hover text-decoration-none">
                                        <div class="card-body text-center position-relative">
                                            <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
                                            <?php if (isset($row['total_estudiantes'])): ?>
                                                <span class="badge bg-primary count-badge"><?= $row['total_estudiantes'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    
                    <!-- Mostrar secciones si se seleccionó año escolar y grado pero no sección -->
                    <?php elseif (!$seccion): ?>
                        <div class="row">
                            <h4 class="mb-4">Seleccione una sección</h4>
                            <?php while ($row = $result_secciones->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <a href="gestionar_estudiantes.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $row['seccion'] ?>" class="card card-hover text-decoration-none">
                                        <div class="card-body text-center position-relative">
                                            <h5 class="card-title">Sección <?= htmlspecialchars($row['seccion']) ?></h5>
                                            <span class="badge bg-primary count-badge"><?= $row['total_estudiantes'] ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    
                    <!-- Mostrar estudiantes si se seleccionó año, grado y sección -->
                    <?php else: ?>
                        <!-- Buscador en tiempo real -->
                        <div class="card filter-card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="search-container">
                                    <div class="input-group search-box">
                                        <input class="form-control ps-4" type="text" id="search-input"
                                               placeholder="Buscar estudiante por nombre, apellido o cédula..." 
                                               value="<?= htmlspecialchars($busqueda) ?>"
                                               style="height: 48px; border-radius: 0.75rem 0 0 0.75rem;">
                                        <button class="btn btn-primary px-4" id="search-button"
                                                style="border-radius: 0 0.75rem 0.75rem 0; height: 48px;">
                                            <i class="fas fa-search me-2"></i>Buscar
                                        </button>
                                    </div>
                                    <div class="search-results" id="search-results"></div>
                                    <div class="search-loading" id="search-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de estudiantes -->
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-dark m-0 fw-bold fs-5">Estudiantes - Sección <?= htmlspecialchars($seccion) ?></h6>
                                    <span class="badge bg-primary" id="total-students"><?= $result_estudiantes->num_rows ?> estudiantes</span>
                                </div>
                            </div>
                            
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="students-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;"></th>
                                                <th style="width: 60px;">Foto</th>
                                                <th>Cédula</th>
                                                <th>Estudiante</th>
                                                <th>Representante</th>
                                                <th>Escolaridad</th>
                                                <th style="width: 100px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody id="students-list">
                                            <?php if ($result_estudiantes->num_rows > 0): ?>
                                                <?php while ($estudiante = $result_estudiantes->fetch_assoc()): ?>
                                                    <tr class="toggle-details align-middle" data-id="<?= $estudiante['id'] ?>">
                                                        <td class="py-3">
                                                            <i class="fas fa-chevron-right chevron-toggle"></i>
                                                        </td>
                                                        <td>
                                                            <div class="avatar-container">
                                                                <?php if ($estudiante['foto_estudiante']): ?>
                                                                    <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['foto_estudiante']) ?>" 
                                                                         class="avatar" 
                                                                         alt="Foto del estudiante">
                                                                <?php else: ?>
                                                                    <div class="avatar avatar-placeholder">
                                                                        <i class="fas fa-user"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td><?= htmlspecialchars($estudiante['cedula']) ?></td>
                                                        <td class="fw-semibold"><?= htmlspecialchars($estudiante['apellidos']) ?>, <?= htmlspecialchars($estudiante['nombres']) ?></td>
                                                        <td><?= isset($estudiante['apellidos_representante']) ? htmlspecialchars($estudiante['apellidos_representante']) . ', ' . htmlspecialchars($estudiante['nombres_representante']) : 'N/A' ?></td>
                                                        <td>
                                                            <span class="badge-estado <?= $estudiante['escolaridad'] ?>">
                                                                <?= ucfirst(str_replace('_', ' ', $estudiante['escolaridad'])) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <?php if ($user_role == 'admin'): ?>
                                                                    <a href="editar_estudiante.php?id=<?= $estudiante['id'] ?>" 
                                                                       class="btn btn-sm btn-outline-primary rounded-circle p-2"
                                                                       title="Editar">
                                                                        <i class="fas fa-pen"></i>
                                                                    </a>
                                                                    <a href="eliminar_estudiante.php?id=<?= $estudiante['id'] ?>" 
                                                                       class="btn btn-sm btn-outline-danger rounded-circle p-2"
                                                                       title="Eliminar"
                                                                       onclick="return confirm('¿Está seguro de eliminar este estudiante?');">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="btn btn-sm btn-outline-secondary rounded-circle p-2 disabled"
                                                                          title="Acción no permitida">
                                                                        <i class="fas fa-ban"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr class="details-row" id="details-<?= $estudiante['id'] ?>">
                                                        <td colspan="7" class="p-4 border-top">
                                                            <div class="row">
                                                                <div class="col-md-6 border-end">
                                                                    <div class="student-photo-container">
                                                                        <?php if ($estudiante['foto_estudiante']): ?>
                                                                            <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['foto_estudiante']) ?>" 
                                                                                 class="avatar-lg" 
                                                                                 alt="Foto del estudiante">
                                                                        <?php else: ?>
                                                                            <div class="avatar-lg avatar-placeholder">
                                                                                <i class="fas fa-user fa-2x"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                        <div>
                                                                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($estudiante['apellidos']) ?>, <?= htmlspecialchars($estudiante['nombres']) ?></h5>
                                                                            <p class="text-muted mb-2">Cédula: <?= htmlspecialchars($estudiante['cedula']) ?></p>
                                                                            <span class="badge-estado <?= $estudiante['escolaridad'] ?>">
                                                                                <?= ucfirst(str_replace('_', ' ', $estudiante['escolaridad'])) ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <dl class="row mb-0">
                                                                        <dt class="col-sm-4 text-muted mt-3">Fecha Nacimiento:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) ?> (<?= $estudiante['edad'] ?> años)</dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Sexo:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= ucfirst($estudiante['sexo']) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Teléfono:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['telefono']) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Lugar de nacimiento:</dt>
                                                                        <dd class="col-sm-8 mt-3">
                                                                            <?= htmlspecialchars($estudiante['estado']) ?>, 
                                                                            <?= htmlspecialchars($estudiante['municipio']) ?>, 
                                                                            <?= htmlspecialchars($estudiante['localidad']) ?>
                                                                        </dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Plantel Procedencia:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['plantel_procedencia'] ?: 'N/A') ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Observaciones:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= nl2br(htmlspecialchars($estudiante['observaciones'] ?: 'Ninguna')) ?></dd>
                                                                    </dl>
                                                                </div>
                                                                
                                                                <div class="col-md-6 ps-4">
                                                                    <h6 class="fw-bold mb-3 text-primary">Datos Académicos</h6>
                                                                    <dl class="row mb-0">
                                                                        <dt class="col-sm-4 text-muted">Año Escolar:</dt>
                                                                        <dd class="col-sm-8"><?= htmlspecialchars($estudiante['anio_escolar']) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Grado:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['grado']) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Sección:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= htmlspecialchars($seccion) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Fecha Ingreso:</dt>
                                                                        <dd class="col-sm-8 mt-3"><?= date('d/m/Y', strtotime($estudiante['fecha_ingreso'])) ?></dd>
                                                                        
                                                                        <dt class="col-sm-4 text-muted mt-3">Escolaridad:</dt>
                                                                        <dd class="col-sm-8 mt-3">
                                                                            <span class="badge-estado <?= $estudiante['escolaridad'] ?>">
                                                                                <?= ucfirst(str_replace('_', ' ', $estudiante['escolaridad'])) ?>
                                                                            </span>
                                                                        </dd>
                                                                    </dl>
                                                                    
                                                                    <?php if (isset($estudiante['representante_id'])): ?>
                                                                    <div class="representative-photo-container">
                                                                        <?php if ($estudiante['foto_representante']): ?>
                                                                            <img src="assets/img/uploads/<?= htmlspecialchars($estudiante['foto_representante']) ?>" 
                                                                                 class="avatar-lg" 
                                                                                 alt="Foto del representante">
                                                                        <?php else: ?>
                                                                            <div class="avatar-lg avatar-placeholder">
                                                                                <i class="fas fa-user-tie fa-2x"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                        <div>
                                                                            <h6 class="fw-bold mb-3 text-primary">Datos del Representante</h6>
                                                                            <dl class="row mb-0">
                                                                                <dt class="col-sm-4 text-muted">Cédula:</dt>
                                                                                <dd class="col-sm-8"><?= htmlspecialchars($estudiante['cedula_representante']) ?></dd>
                                                                                
                                                                                <dt class="col-sm-4 text-muted mt-3">Nombres:</dt>
                                                                                <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['nombres_representante']) ?></dd>
                                                                                
                                                                                <dt class="col-sm-4 text-muted mt-3">Apellidos:</dt>
                                                                                <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['apellidos_representante']) ?></dd>
                                                                                
                                                                                <dt class="col-sm-4 text-muted mt-3">Teléfono:</dt>
                                                                                <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['telefono_representante']) ?></dd>
                                                                                
                                                                                <dt class="col-sm-4 text-muted mt-3">Correo:</dt>
                                                                                <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['email_representante'] ?: 'N/A') ?></dd>
                                                                                
                                                                                <dt class="col-sm-4 text-muted mt-3">Parentesco:</dt>
                                                                                <dd class="col-sm-8 mt-3"><?= htmlspecialchars($estudiante['parentesco']) ?></dd>
                                                                            </dl>
                                                                        </div>
                                                                    </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-user-graduate fs-1 text-muted mb-3"></i>
                                                            <p class="text-muted mb-0">No se encontraron estudiantes en esta sección</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toggle detalles del estudiante
            $('.toggle-details').on('click', function(e) {
                if(!$(e.target).closest('a').length) {
                    const id = $(this).data('id');
                    $(`#details-${id}`).toggleClass('active');
                    $(this).find('.chevron-toggle').toggleClass('rotated');
                }
            });
            
            // Buscador en tiempo real
            const searchInput = $('#search-input');
            const searchResults = $('#search-results');
            const searchLoading = $('#search-loading');
            const studentsList = $('#students-list');
            const studentsTable = $('#students-table');
            const totalStudents = $('#total-students');
            
            // Función para realizar la búsqueda
            function performSearch(query) {
                if (query.length >= 2) {
                    searchLoading.show();
                    
                    $.ajax({
                        url: 'buscar_estudiantes.php',
                        type: 'GET',
                        data: {
                            anio_escolar_id: <?= $anio_escolar_id ?>,
                            grado_id: <?= $grado_id ?>,
                            seccion: '<?= $seccion ?>',
                            busqueda: query
                        },
                        success: function(data) {
                            searchResults.empty();
                            
                            if (data.estudiantes && data.estudiantes.length > 0) {
                                data.estudiantes.forEach(estudiante => {
                                    const item = $('<div class="search-item"></div>');
                                    item.html(`
                                        <div class="fw-bold">${estudiante.apellidos}, ${estudiante.nombres}</div>
                                        <small class="text-muted">Cédula: ${estudiante.cedula}</small>
                                    `);
                                    item.on('click', function() {
                                        searchInput.val(`${estudiante.apellidos}, ${estudiante.nombres}`);
                                        searchResults.hide();
                                        filterStudents(estudiante.id);
                                    });
                                    searchResults.append(item);
                                });
                                searchResults.show();
                            } else {
                                searchResults.html('<div class="no-results">No se encontraron estudiantes</div>');
                                searchResults.show();
                            }
                        },
                        error: function() {
                            searchResults.html('<div class="no-results">Error al buscar</div>');
                            searchResults.show();
                        },
                        complete: function() {
                            searchLoading.hide();
                        }
                    });
                } else {
                    searchResults.hide();
                    // Si el campo está vacío, mostrar todos los estudiantes
                    if (query.length === 0) {
                        $('.toggle-details').show();
                        $('.details-row').removeClass('active');
                        totalStudents.text($('.toggle-details').length + ' estudiantes');
                    }
                }
            }
            
            // Función para filtrar estudiantes en la tabla
            function filterStudents(studentId = null) {
                if (studentId) {
                    // Mostrar solo el estudiante seleccionado
                    $('.toggle-details').hide();
                    $(`[data-id="${studentId}"]`).show();
                    totalStudents.text('1 estudiante');
                } else {
                    const query = searchInput.val().toLowerCase();
                    
                    if (query.length >= 2) {
                        let visibleCount = 0;
                        
                        $('.toggle-details').each(function() {
                            const text = $(this).text().toLowerCase();
                            if (text.includes(query)) {
                                $(this).show();
                                visibleCount++;
                            } else {
                                $(this).hide();
                            }
                        });
                        
                        totalStudents.text(visibleCount + ' estudiantes');
                    } else {
                        // Mostrar todos si la búsqueda está vacía
                        $('.toggle-details').show();
                        totalStudents.text($('.toggle-details').length + ' estudiantes');
                    }
                }
            }
            
            // Eventos del buscador
            searchInput.on('input', function() {
                const query = $(this).val();
                performSearch(query);
                filterStudents();
            });
            
            $('#search-button').on('click', function(e) {
                e.preventDefault();
                filterStudents();
            });
            
            // Ocultar resultados al hacer clic fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-container').length) {
                    searchResults.hide();
                }
            });
            
            // Manejar tecla Enter
            searchInput.on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    filterStudents();
                }
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>