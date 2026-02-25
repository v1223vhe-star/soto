<?php
session_start();
include 'db.php';

// Verificar sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Verificar rol del usuario
$es_admin = ($_SESSION["role"] ?? '') === 'admin';
$es_usuario = ($_SESSION["role"] ?? '') === 'user';

// Inicializar variables
$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['error']);

// Obtener parámetros
$anio_escolar_id = $_GET["anio_escolar_id"] ?? null;
$grado_id = $_GET["grado_id"] ?? null;
$seccion = $_GET["seccion"] ?? null;
$mes = $_GET['mes'] ?? date('n');
$anio = $_GET['anio'] ?? date('Y');
$busqueda_cedula = $_GET['cedula'] ?? null;
$materia_id = $_GET['materia_id'] ?? null;

// Días de la semana
$dias_semana = [
    'Mon' => 'Lun', 
    'Tue' => 'Mar', 
    'Wed' => 'Mié',
    'Thu' => 'Jue', 
    'Fri' => 'Vie', 
    'Sat' => 'Sáb', 
    'Sun' => 'Dom'
];

// Consulta para años escolares
$sql_anios_escolares = "SELECT * FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// Consulta para grados
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

// Consulta para secciones
if ($anio_escolar_id && $grado_id) {
    $sql_secciones = "SELECT sag.seccion, COUNT(da.id) as total_estudiantes
                      FROM secciones_anio_grado sag
                      INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                      LEFT JOIN datos_academicos da ON (ag.id = da.anio_grado_id AND da.seccion = sag.seccion)
                      WHERE ag.anio_escolar_id = $anio_escolar_id AND ag.grado_id = $grado_id
                      GROUP BY sag.seccion";
    $result_secciones = $conn->query($sql_secciones);
}

// Consulta para materias del grado
if ($grado_id) {
    $sql_materias = "SELECT m.id, m.nombre 
                     FROM materias m
                     INNER JOIN materias_grados mg ON m.id = mg.materia_id
                     WHERE mg.grado_id = $grado_id
                     ORDER BY m.nombre";
    $result_materias = $conn->query($sql_materias);
    $materias = [];
    while ($row = $result_materias->fetch_assoc()) {
        $materias[$row['id']] = $row['nombre'];
    }
    
    // Si hay materias pero no se ha seleccionado ninguna, redirigir a la primera
    if (count($materias) > 0 && !$materia_id) {
        $primer_materia_id = array_key_first($materias);
        header("Location: asistencias.php?anio_escolar_id=$anio_escolar_id&grado_id=$grado_id&seccion=$seccion&mes=$mes&anio=$anio&materia_id=$primer_materia_id");
        exit();
    }
}

// Consulta para estudiantes
if ($anio_escolar_id && $grado_id && $seccion && $materia_id) {
    $sql_estudiantes = "SELECT 
                        e.id, e.cedula, e.nombres, e.apellidos,
                        da.anio_grado_id, ag.grado_id, g.nombre as grado_nombre
                        FROM estudiantes e
                        INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                        INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                        INNER JOIN grados g ON ag.grado_id = g.id
                        WHERE ag.anio_escolar_id = $anio_escolar_id 
                        AND ag.grado_id = $grado_id
                        AND da.seccion = '$seccion'";
    
    if ($busqueda_cedula) {
        $sql_estudiantes .= " AND e.cedula LIKE '%$busqueda_cedula%'";
    }
    
    $sql_estudiantes .= " ORDER BY e.apellidos, e.nombres";
    $result_estudiantes = $conn->query($sql_estudiantes);
}

// Consulta para horario (simplificada para esta base de datos)
if ($grado_id && $seccion) {
    $sql_horario = "SELECT h.dia_semana, h.materia_id, h.hora_inicio, h.hora_fin
                    FROM horarios h
                    INNER JOIN secciones_anio_grado sag ON h.seccion_anio_grado_id = sag.id
                    INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                    WHERE ag.grado_id = $grado_id AND sag.seccion = '$seccion'
                    ORDER BY h.dia_semana, h.hora_inicio";
    $result_horario = $conn->query($sql_horario);
    $horario = [];
    while ($row = $result_horario->fetch_assoc()) {
        $horario[$row['dia_semana']][] = $row;
    }
}

// Configuración del mes
$dias_en_el_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$nombre_mes = date('F', mktime(0, 0, 0, $mes, 1, $anio));
$meses_espanol = [
    'January' => 'Enero', 
    'February' => 'Febrero', 
    'March' => 'Marzo',
    'April' => 'Abril', 
    'May' => 'Mayo', 
    'June' => 'Junio',
    'July' => 'Julio', 
    'August' => 'Agosto', 
    'September' => 'Septiembre',
    'October' => 'Octubre', 
    'November' => 'Noviembre', 
    'December' => 'Diciembre'
];
$nombre_mes = $meses_espanol[$nombre_mes];
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Asistencias - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
     <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root { --primary-color: #4e73df; --primary-hover: #2e59d9; }
        body { font-family: 'Nunito', system-ui, -apple-system, sans-serif; background-color: #f8f9fc; }
        .card { border: none; border-radius: 0.5rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .btn-success { background-color: #1cc88a; border: none; }
        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .search-box { max-width: 600px; border-radius: 0.5rem; }
        .alert { border-radius: 0.5rem; }
        .filter-card { border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .filter-title { font-size: 0.9rem; font-weight: 600; }
        .card-hover:hover { transform: translateY(-2px); transition: all 0.3s ease; }
        .table-responsive { overflow-x: auto; }
        .asistencia-table th, .asistencia-table td { text-align: center; vertical-align: middle; }
        .asistencia-check { transform: scale(1.3); cursor: pointer; }
        .presente { background-color: #d1fae5 !important; }
        .ausente { background-color: #fee2e2 !important; }
        .dia-festivo { background-color: #fef3c7 !important; }
        .dia-fin-semana { background-color: #e0e7ff !important; }
        .materia-cell { font-size: 0.8rem; padding: 2px !important; }
        .materia-nombre { font-weight: bold; }
        .resumen-materia { border-left: 3px solid var(--primary-color); padding-left: 10px; margin-bottom: 5px; }
        .resumen-materia span { font-weight: bold; }
        .badge-materia { font-size: 0.7rem; margin: 1px; }
        .dia-actual { border: 2px solid var(--primary-color) !important; }
        .hover-cell:hover { opacity: 0.8; cursor: pointer; }
        .navbar { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .table thead th { background-color: #f8f9fc; }
        .table-bordered { border: 1px solid #e3e6f0; }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .materia-selector { border-left: 3px solid var(--primary-color); }
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
                                <h3 class="text-dark mb-0 fw-bold">Gestión de Asistencias</h3>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-3"></i>
                            <div><?= htmlspecialchars($mensaje) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación jerárquica -->
                    <div class="d-flex align-items-center mb-4">
                        <a href="asistencias.php" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-home"></i>
                        </a>
                        <?php if ($anio_escolar_id): ?>
                            <?php 
                            $result_anios_escolares->data_seek(0);
                            while ($row = $result_anios_escolares->fetch_assoc()) {
                                if ($row['id'] == $anio_escolar_id) {
                                    echo '<span class="me-2"><i class="fas fa-chevron-right"></i></span>';
                                    echo '<a href="asistencias.php?anio_escolar_id='.$row['id'].'" class="btn btn-sm btn-outline-primary me-2">';
                                    echo htmlspecialchars($row['nombre']).'</a>';
                                    break;
                                }
                            }
                            ?>
                        <?php endif; ?>
                        
                        <?php if ($grado_id): ?>
                            <?php 
                            $result_grados->data_seek(0);
                            while ($row = $result_grados->fetch_assoc()) {
                                if ($row['id'] == $grado_id) {
                                    echo '<span class="me-2"><i class="fas fa-chevron-right"></i></span>';
                                    echo '<a href="asistencias.php?anio_escolar_id='.$anio_escolar_id.'&grado_id='.$row['id'].'" class="btn btn-sm btn-outline-primary me-2">';
                                    echo htmlspecialchars($row['nombre']).'</a>';
                                    break;
                                }
                            }
                            ?>
                        <?php endif; ?>
                        
                        <?php if ($seccion): ?>
                            <span class="me-2"><i class="fas fa-chevron-right"></i></span>
                            <span class="btn btn-sm btn-light me-2">Sección <?= htmlspecialchars($seccion) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Mostrar años escolares si no se ha seleccionado ninguno -->
                    <?php if (!$anio_escolar_id): ?>
                        <div class="row">
                            <h4 class="mb-4">Seleccione un año escolar</h4>
                            <?php while ($row = $result_anios_escolares->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <a href="asistencias.php?anio_escolar_id=<?= $row['id'] ?>" class="card card-hover text-decoration-none">
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
                                    <a href="asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $row['id'] ?>" class="card card-hover text-decoration-none">
                                        <div class="card-body text-center position-relative">
                                            <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
                                            <?php if (isset($row['total_estudiantes'])): ?>
                                                <span class="badge bg-primary"><?= $row['total_estudiantes'] ?></span>
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
                                    <a href="asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $row['seccion'] ?>" class="card card-hover text-decoration-none">
                                        <div class="card-body text-center position-relative">
                                            <h5 class="card-title">Sección <?= htmlspecialchars($row['seccion']) ?></h5>
                                            <span class="badge bg-primary"><?= $row['total_estudiantes'] ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    
                    <!-- Mostrar registro de asistencias si se seleccionó año, grado y sección -->
                    <?php else: ?>
                        <!-- Selector de materia -->
                        <?php if (count($materias) > 0): ?>
                        <div class="card shadow-sm mb-4 materia-selector">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Seleccione la materia para tomar asistencia</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($materias as $id => $nombre): ?>
                                        <a href="asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&mes=<?= $mes ?>&anio=<?= $anio ?>&materia_id=<?= $id ?>" 
                                           class="btn btn-sm <?= $materia_id == $id ? 'btn-primary' : 'btn-outline-primary' ?>">
                                            <?= htmlspecialchars($nombre) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Solo mostrar el resto si hay una materia seleccionada -->
                        <?php if ($materia_id): ?>
                        <!-- Filtros de mes y año -->
                        <div class="card filter-card shadow-sm mb-4">
                            <div class="card-body">
                                <form method="get" action="asistencias.php">
                                    <input type="hidden" name="anio_escolar_id" value="<?= $anio_escolar_id ?>">
                                    <input type="hidden" name="grado_id" value="<?= $grado_id ?>">
                                    <input type="hidden" name="seccion" value="<?= $seccion ?>">
                                    <input type="hidden" name="materia_id" value="<?= $materia_id ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Mes</label>
                                                <select class="form-select" name="mes">
                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                        $nombre_mes_selector = date('F', mktime(0, 0, 0, $i, 1, $anio));
                                                        $nombre_mes_selector = $meses_espanol[$nombre_mes_selector];
                                                        echo "<option value='$i' ".($mes == $i ? 'selected' : '').">$nombre_mes_selector</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Año</label>
                                                <select class="form-select" name="anio">
                                                    <?php
                                                    $anio_actual = date('Y');
                                                    for ($i = $anio_actual - 5; $i <= $anio_actual + 5; $i++) {
                                                        echo "<option value='$i' ".($anio == $i ? 'selected' : '').">$i</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Buscar por cédula</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="cedula" placeholder="Ingrese cédula" value="<?= htmlspecialchars($busqueda_cedula) ?>">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    <?php if ($busqueda_cedula): ?>
                                                        <a href="asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&mes=<?= $mes ?>&anio=<?= $anio ?>&materia_id=<?= $materia_id ?>" class="btn btn-outline-secondary">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Información de la sección -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 class="fw-bold">Información del Grupo</h5>
                                        <p class="mb-1"><strong>Grado:</strong> <?= htmlspecialchars($estudiante['grado_nombre'] ?? '') ?></p>
                                        <p class="mb-1"><strong>Sección:</strong> <?= htmlspecialchars($seccion) ?></p>
                                        <p class="mb-1"><strong>Mes:</strong> <?= "$nombre_mes $anio" ?></p>
                                        <p class="mb-1"><strong>Materia:</strong> <?= htmlspecialchars($materias[$materia_id] ?? '') ?></p>
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="fw-bold">Materias del Grado</h5>
                                        <div class="d-flex flex-wrap">
                                            <?php foreach ($materias as $id => $nombre): ?>
                                                <span class="badge <?= $materia_id == $id ? 'bg-primary' : 'bg-light text-dark' ?> badge-materia me-2 mb-2"><?= htmlspecialchars($nombre) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones rápidas (solo para administradores) -->
                        <?php if ($es_admin): ?>
                        <div class="mb-4 asistencia-buttons">
                            <button class="btn btn-success btn-sm me-2" id="marcar-todos-presente">
                                <i class="fas fa-check-circle me-1"></i> Marcar todos presente
                            </button>
                            <button class="btn btn-danger btn-sm me-2" id="marcar-todos-ausente">
                                <i class="fas fa-times-circle me-1"></i> Marcar todos ausente
                            </button>
                        </div>
                        <?php endif; ?>
                        <!-- Reemplaza esta parte en el código (busca el formulario actual) -->


                        <!-- Tabla de asistencias -->
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-dark m-0 fw-bold fs-5">
                                        Asistencias - <?= "$nombre_mes $anio" ?>
                                        <small class="text-muted">Sección <?= htmlspecialchars($seccion) ?></small>
                                        <small class="text-muted">- <?= htmlspecialchars($materias[$materia_id] ?? '') ?></small>
                                    </h6>
                                    <div>
                                        <a href="generar_pdf_asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&mes=<?= $mes ?>&anio=<?= $anio ?>&materia_id=<?= $materia_id ?>" 
                                           class="btn btn-danger me-2" target="_blank">
                                            <i class="fas fa-file-pdf me-2"></i>Generar PDF
                                        </a>
                                        <a href="generar_excel_asistencias.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&mes=<?= $mes ?>&anio=<?= $anio ?>&materia_id=<?= $materia_id ?>" 
                                           class="btn btn-success">
                                            <i class="fas fa-file-excel me-2"></i>Generar Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-0">
                                <?php if ($result_estudiantes && $result_estudiantes->num_rows > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php while ($estudiante = $result_estudiantes->fetch_assoc()): 
                                            $total_asistencias = 0;
                                            $total_dias = 0;
                                            
                                            // Pre-calcular asistencias para el resumen
                                            $asistencias_por_dia = [];
                                            for ($i = 1; $i <= $dias_en_el_mes; $i++) {
                                                $fecha = "$anio-$mes-".sprintf("%02d", $i);
                                                $dia_semana = date('D', strtotime($fecha));
                                                $es_fin_semana = ($dia_semana == 'Sat' || $dia_semana == 'Sun');
                                                
                                                $sql_asistencia = "SELECT a.asistio
                                                                  FROM asistencia a
                                                                  JOIN materias_anio_escolar mae ON a.materia_anio_escolar_id = mae.id
                                                                  WHERE a.estudiante_id = ".$estudiante['id']." 
                                                                  AND mae.materia_id = $materia_id
                                                                  AND a.fecha = '$fecha'";
                                                
                                                $result_asistencia = $conn->query($sql_asistencia);
                                                $asistio = false;
                                                
                                                if ($result_asistencia && $result_asistencia->num_rows > 0) {
                                                    $row = $result_asistencia->fetch_assoc();
                                                    $asistio = (bool)$row['asistio'];
                                                }
                                                
                                                if (!$es_fin_semana) {
                                                    $asistencias_por_dia[$i] = $asistio;
                                                    $total_dias++;
                                                    if ($asistio) $total_asistencias++;
                                                }
                                            }
                                            
                                            $porcentaje = $total_dias > 0 ? round(($total_asistencias / $total_dias) * 100) : 0;
                                            $clase_porcentaje = $porcentaje >= 80 ? 'text-success' : ($porcentaje >= 50 ? 'text-warning' : 'text-danger');
                                        ?>
                                        
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asistencias-<?= $estudiante['id'] ?>" role="button">
                                                <div>
                                                    <h6 class="mb-1"><?= htmlspecialchars($estudiante['apellidos'].", ".$estudiante['nombres']) ?></h6>
                                                    <small class="text-muted">Cédula: <?= htmlspecialchars($estudiante['cedula']) ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success me-2">Asist: <?= $total_asistencias ?></span>
                                                    <span class="badge bg-danger me-2">Falt: <?= $total_dias - $total_asistencias ?></span>
                                                    <span class="badge <?= $clase_porcentaje ?>"><?= $porcentaje ?>%</span>
                                                </div>
                                            </div>
                                            
                                            <div class="collapse mt-3" id="asistencias-<?= $estudiante['id'] ?>">
                                                <div class="d-flex flex-wrap gap-2">
                                                   <?php for ($i = 1; $i <= $dias_en_el_mes; $i++): 
    $fecha = "$anio-$mes-".sprintf("%02d", $i);
    $dia_semana = date('D', strtotime($fecha));
    $dia_semana_es = $dias_semana[$dia_semana] ?? $dia_semana;
    $es_fin_semana = ($dia_semana == 'Sat' || $dia_semana == 'Sun');
    
    if ($es_fin_semana) continue;
    
    $asistio = $asistencias_por_dia[$i] ?? false;
    $clase_boton = $asistio ? 'btn-success' : 'btn-outline-danger';
    $icono = $asistio ? 'fa-check' : 'fa-times';
    $hoy = date('Y-m-d') == $fecha ? 'border border-2 border-primary' : '';
    
    // Solo permitir marcar el día actual para usuarios normales
    $disabled = ($es_usuario && date('Y-m-d') != $fecha) ? 'disabled' : '';
?>
<div class="d-flex flex-column align-items-center">
    <small><?= $dia_semana_es ?></small>
    <button class="btn btn-sm <?= $clase_boton ?> <?= $hoy ?> asistencia-btn" 
            style="width: 40px;"
            data-estudiante-id="<?= $estudiante['id'] ?>" 
            data-fecha="<?= $fecha ?>"
            data-dia-semana="<?= $dia_semana ?>"
            data-materia-id="<?= $materia_id ?>"
            data-asistio="<?= $asistio ? '1' : '0' ?>"
            <?= $disabled ?>>
        <?= $i ?>
        <i class="fas <?= $icono ?> ms-1"></i>
    </button>
</div>
<?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">No hay estudiantes en esta sección</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
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

    <!-- Scripts -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Función para guardar asistencia
        function guardarAsistencia(estudiante_id, fecha, dia_semana, materia_id, estado) {
            return $.ajax({
                url: 'guardar_asistencia.php',
                type: 'POST',
                data: {
                    estudiante_id: estudiante_id,
                    fecha: fecha,
                    dia_semana: dia_semana,
                    materia_id: materia_id,
                    estado: estado
                }
            });
        }
        
        // Click en botón de asistencia
        $('.asistencia-btn').click(function() {
            var boton = $(this);
            var estudiante_id = boton.data('estudiante-id');
            var fecha = boton.data('fecha');
            var dia_semana = boton.data('dia-semana');
            var materia_id = boton.data('materia-id');
            var nuevoEstado = boton.data('asistio') === '1' ? '0' : '1';
            
            // Actualizar apariencia del botón inmediatamente
            if (nuevoEstado === '1') {
                boton.removeClass('btn-outline-danger').addClass('btn-success');
                boton.find('i').removeClass('fa-times').addClass('fa-check');
            } else {
                boton.removeClass('btn-success').addClass('btn-outline-danger');
                boton.find('i').removeClass('fa-check').addClass('fa-times');
            }
            boton.data('asistio', nuevoEstado);
            
            // Guardar en servidor
            guardarAsistencia(estudiante_id, fecha, dia_semana, materia_id, nuevoEstado)
                .fail(function(xhr, status, error) {
                    console.error("Error al guardar la asistencia:", error);
                    // Revertir cambios si falla
                    if (nuevoEstado === '1') {
                        boton.removeClass('btn-success').addClass('btn-outline-danger');
                        boton.find('i').removeClass('fa-check').addClass('fa-times');
                        boton.data('asistio', '0');
                    } else {
                        boton.removeClass('btn-outline-danger').addClass('btn-success');
                        boton.find('i').removeClass('fa-times').addClass('fa-check');
                        boton.data('asistio', '1');
                    }
                    alert("Error al guardar la asistencia. Por favor, intente nuevamente.");
                });
        });
        // Añade esto dentro del $(document).ready(function() {

// Buscador en tiempo real
$('#buscador-estudiantes').on('input', function() {
    var busqueda = $(this).val().toLowerCase().trim();
    var resultadosEncontrados = 0;
    
    $('.estudiante-item').each(function() {
        var nombre = $(this).data('nombre');
        var cedula = $(this).data('cedula');
        
        if (nombre.includes(busqueda) || cedula.includes(busqueda)) {
            $(this).show();
            resultadosEncontrados++;
        } else {
            $(this).hide();
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (resultadosEncontrados === 0 && busqueda.length > 0) {
        $('.no-resultados').show();
    } else {
        $('.no-resultados').hide();
    }
});

// Para mantener el buscador al recargar (si había una búsqueda previa)
<?php if ($busqueda_cedula): ?>
$('#buscador-estudiantes').val('<?= htmlspecialchars($busqueda_cedula) ?>').trigger('input');
<?php endif; ?>
        <?php if ($es_admin): ?>
        // Acciones rápidas para marcar todos (solo para administradores)
        $('#marcar-todos-presente').click(function() {
            if (confirm('¿Marcar TODOS los estudiantes como PRESENTE para todos los días?')) {
                $('.asistencia-btn').each(function() {
                    var boton = $(this);
                    if (boton.data('asistio') !== '1') {
                        boton.click(); // Simular click para cambiar estado
                    }
                });
            }
        });
        
        $('#marcar-todos-ausente').click(function() {
            if (confirm('¿Marcar TODOS los estudiantes como AUSENTE para todos los días?')) {
                $('.asistencia-btn').each(function() {
                    var boton = $(this);
                    if (boton.data('asistio') !== '0') {
                        boton.click(); // Simular click para cambiar estado
                    }
                });
            }
        });
        <?php endif; ?>
    });
    </script>
</body>
</html>

<?php $conn->close(); ?>