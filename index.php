<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Mostrar alerta de bienvenida si es necesario
if (isset($_SESSION["show_welcome_alert"]) && $_SESSION["show_welcome_alert"] === true) {
    // Eliminamos la bandera para que no se muestre al recargar
    unset($_SESSION["show_welcome_alert"]);
    
    // Usamos JavaScript para mostrar SweetAlert2
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '¡Inicio de sesión exitoso!',
                html: '<b>Bienvenido/a, " . htmlspecialchars($_SESSION["username"]) . "!</b>',
                icon: 'success',
                confirmButtonText: 'Continuar',
                timer: 1000,  // Cierra automáticamente después de 3 segundos
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        });
    </script>";
}
// Consulta para obtener el número de usuarios
$sql = "SELECT COUNT(*) AS total_usuarios FROM usuarios";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_usuarios = $row["total_usuarios"];
} else {
    $total_usuarios = 0;
    mostrarMensaje('Error!', 'Error al obtener el total de usuarios: ' . $conn->error, 'error');
}

// Consulta para obtener el número de estudiantes
$sql_estudiantes = "SELECT COUNT(*) AS total_estudiantes FROM estudiantes";
$result_estudiantes = $conn->query($sql_estudiantes);

if ($result_estudiantes) {
    $row_estudiantes = $result_estudiantes->fetch_assoc();
    $total_estudiantes = $row_estudiantes["total_estudiantes"];
} else {
    $total_estudiantes = 0;
    mostrarMensaje('Error!', 'Error al obtener el total de estudiantes: ' . $conn->error, 'error');
}

// Consulta para obtener el número de materias
$sql_materias = "SELECT COUNT(*) AS total_materias FROM materias";
$result_materias = $conn->query($sql_materias);

if ($result_materias) {
    $row_materias = $result_materias->fetch_assoc();
    $total_materias = $row_materias["total_materias"];
} else {
    $total_materias = 0;
    mostrarMensaje('Error!', 'Error al obtener el total de materias: ' . $conn->error, 'error');
}

// Consulta para obtener el número de representantes
$sql_representantes = "SELECT COUNT(*) AS total_representantes FROM representantes";
$result_representantes = $conn->query($sql_representantes);

if ($result_representantes) {
    $row_representantes = $result_representantes->fetch_assoc();
    $total_representantes = $row_representantes["total_representantes"];
} else {
    $total_representantes = 0;
    mostrarMensaje('Error!', 'Error al obtener el total de representantes: ' . $conn->error, 'error');
}

// Obtener el año escolar activo
$sql_anio_escolar = "SELECT id, nombre FROM anios_escolares WHERE activo = TRUE LIMIT 1";
$result_anio_escolar = $conn->query($sql_anio_escolar);

if ($result_anio_escolar) {
    if ($result_anio_escolar->num_rows > 0) {
        $anio_escolar = $result_anio_escolar->fetch_assoc();
        $anio_escolar_id = $anio_escolar["id"];
        $anio_escolar_nombre = $anio_escolar["nombre"];
    } else {
        $anio_escolar_id = 0;
        $anio_escolar_nombre = "Ninguno Activo";
    }
} else {
    $anio_escolar_id = 0;
    $anio_escolar_nombre = "Error al obtener año escolar";
    mostrarMensaje('Error!', 'Error al obtener el año escolar activo: ' . $conn->error, 'error');
}

// Obtener el número de materias en el año escolar activo
$sql_materias_anio = "SELECT COUNT(*) AS total_materias_anio FROM materias_anio_escolar WHERE anio_escolar_id = $anio_escolar_id";
$result_materias_anio = $conn->query($sql_materias_anio);

if ($result_materias_anio) {
    $row_materias_anio = $result_materias_anio->fetch_assoc();
    $total_materias_anio = $row_materias_anio["total_materias_anio"];
} else {
    $total_materias_anio = 0;
    mostrarMensaje('Error!', 'Error al obtener el total de materias del año escolar: ' . $conn->error, 'error');
}

// Procesar la solicitud de borrado de notificaciones
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrar_notificaciones"])) {
    $sql_borrar_notificaciones = "DELETE FROM notificaciones"; // Puedes agregar una condición WHERE para borrar solo las notificaciones "vistas"
    if ($conn->query($sql_borrar_notificaciones) === TRUE) {
        mostrarMensaje('Éxito!', 'Notificaciones borradas correctamente.', 'success');
    } else {
        mostrarMensaje('Error!', 'Error al borrar las notificaciones: ' . $conn->error, 'error');
    }
}

// Consulta para obtener las últimas notificaciones
$sql_notificaciones = "SELECT * FROM notificaciones ORDER BY fecha DESC LIMIT 5";
$result_notificaciones = $conn->query($sql_notificaciones);

// Consulta para obtener los últimos mensajes
$sql_mensajes = "SELECT * FROM mensajes ORDER BY fecha DESC LIMIT 5";
$result_mensajes = $conn->query($sql_mensajes);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Inicio - Sistema de Gestión de Estudiantes y Pagos de Mensualidad</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
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
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            background-color: white;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .search-box {
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .search-btn {
            border-radius: 0 20px 20px 0;
            background-color: var(--primary-color);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 1.75rem;
            opacity: 0.8;
        }
        
        .stat-card .card-body {
            padding: 1.5rem;
        }
        
        .stat-card .stat-title {
            font-size: 0.85rem;
            font-weight: 500;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
            padding: 0.25rem 0.4rem;
        }
        
        footer {
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .scroll-to-top {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Colores para las tarjetas */
        .card-primary {
            border-left: 4px solid var(--primary-color);
        }
        
        .card-success {
            border-left: 4px solid var(--success-color);
        }
        
        .card-info {
            border-left: 4px solid var(--accent-color);
        }
        
        .card-warning {
            border-left: 4px solid var(--warning-color);
        }
        
        .card-secondary {
            border-left: 4px solid var(--secondary-color);
        }
        
        .card-danger {
            border-left: 4px solid var(--danger-color);
        }
        
        /* Efecto de gradiente para las tarjetas */
        .card-primary:hover {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
        }
        
        .card-success:hover {
            background: linear-gradient(135deg, rgba(75, 181, 67, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stat-card .stat-value {
                font-size: 1.5rem;
            }
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
                        
                        <!-- Barra de búsqueda mejorada -->
                        <div class="d-none d-sm-inline-block me-auto ms-md-3 my-2 my-md-0 mw-100 navbar-search">
                            <div class="input-group">
                                <input class="form-control search-box" type="text" placeholder="Buscar módulo..." 
                                       id="termino_busqueda" name="termino_busqueda" autocomplete="off">
                                <button class="btn search-btn" type="button" id="boton_buscar">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div id="resultados_busqueda" class="mt-1 rounded shadow" 
                                 style="position: absolute; z-index: 1000; width: 100%; background-color: white; display: none;">
                            </div>
                        </div>
                        
                        <ul class="navbar-nav flex-nowrap ms-auto align-items-center">
                            <!-- Notificaciones -->
                            <li class="nav-item dropdown no-arrow mx-2">
                                <a class="nav-link position-relative" href="#" id="alertsDropdown" role="button" 
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php if ($result_notificaciones && $result_notificaciones->num_rows > 0) : ?>
                                        <span class="notification-badge badge bg-danger rounded-pill"><?php echo $result_notificaciones->num_rows; ?></span>
                                    <?php endif; ?>
                                    <i class="fas fa-bell fa-lg"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in py-0" 
                                     aria-labelledby="alertsDropdown" style="min-width: 300px;">
                                    <div class="dropdown-header bg-light py-3">
                                        <h6 class="m-0 font-weight-bold">Centro de Alertas</h6>
                                    </div>
                                    <div class="px-3 py-2" style="max-height: 300px; overflow-y: auto;">
                                        <?php if ($result_notificaciones && $result_notificaciones->num_rows > 0) : ?>
                                            <?php while ($row = $result_notificaciones->fetch_assoc()) : ?>
                                                <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                                    <div class="me-3">
                                                        <div class="icon-circle bg-primary text-white">
                                                            <i class="fas fa-info-circle"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="small text-muted"><?php echo date('d M, Y', strtotime($row["fecha"])); ?></div>
                                                        <span class="font-weight-bold"><?php echo htmlspecialchars($row["mensaje"]); ?></span>
                                                    </div>
                                                </a>
                                                <div class="dropdown-divider m-0"></div>
                                            <?php endwhile; ?>
                                        <?php else : ?>
                                            <div class="text-center py-3">
                                                <i class="fas fa-bell-slash text-muted fa-2x mb-2"></i>
                                                <p class="small text-muted mb-0">No hay notificaciones</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown-footer bg-light py-2 px-3">
                                        <form method="post" action="" class="d-flex justify-content-between">
                                            <a class="btn btn-sm btn-link text-primary p-0" href="#">Ver todas</a>
                                            <button class="btn btn-sm btn-link text-danger p-0" type="submit" name="borrar_notificaciones">
                                                Limpiar todo
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            
                            <div class="d-none d-sm-block topbar-divider"></div>
                            
                            <!-- Info Usuario -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                                   role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-none d-lg-inline me-2 text-gray-600 fw-medium"><?php echo $_SESSION["username"]; ?></span>
                                    <img class="user-avatar rounded-circle" src="assets/img/avatars/1.jpg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in py-2" aria-labelledby="userDropdown">
                                    <div class="dropdown-header">
                                        <h6 class="m-0 font-weight-bold">Mi Cuenta</h6>
                                    </div>
                                    <a class="dropdown-item d-flex align-items-center" href="profile.php">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item d-flex align-items-center" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2 text-muted"></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <!-- Logo del colegio -->
            <img src="assets/img/logo.jpeg" alt="Logo del Colegio" class="me-3" style="height: 50px;">
            <div>
                <h3 class="fw-bold mb-0">Inicio</h3>
                <p class="text-muted mb-0">Resumen general del sistema</p>
            </div>
        </div>
        <div>
            <span class="badge bg-light text-dark">
                <i class="fas fa-calendar-alt me-1"></i>
                <?php echo date('d M, Y'); ?>
            </span>
        </div>
    </div>
    
    
                    
                    <div class="row g-4">
                        <!-- Tarjeta de Usuarios -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Total Usuarios</h6>
                                            <h2 class="stat-value text-primary"><?php echo $total_usuarios; ?></h2>
                                        </div>
                                        <div class="card-icon text-primary">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <i class="fas fa-arrow-up me-1"></i> 5% este mes
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Estudiantes -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Total Estudiantes</h6>
                                            <h2 class="stat-value text-success"><?php echo $total_estudiantes; ?></h2>
                                        </div>
                                        <div class="card-icon text-success">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-arrow-up me-1"></i> 12% este mes
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Materias 
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-info h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Total Materias</h6>
                                            <h2 class="stat-value text-info"><?php echo $total_materias; ?></h2>
                                        </div>
                                        <div class="card-icon text-info">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-equals me-1"></i> Sin cambios
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        
                        <!-- Tarjeta de Representantes -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Total Representantes</h6>
                                            <h2 class="stat-value text-warning"><?php echo $total_representantes; ?></h2>
                                        </div>
                                        <div class="card-icon text-warning">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-arrow-up me-1"></i> 8% este mes
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Año Escolar -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-secondary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Año Escolar Activo</h6>
                                            <h2 class="stat-value text-secondary"><?php echo $anio_escolar_nombre; ?></h2>
                                        </div>
                                        <div class="card-icon text-secondary">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <i class="fas fa-check-circle me-1"></i> Activo
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Materias en Año Escolar 
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card card-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stat-title">Materias en Año Activo</h6>
                                            <h2 class="stat-value text-danger"><?php echo $total_materias_anio; ?></h2>
                                        </div>
                                        <div class="card-icon text-danger">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            <i class="fas fa-arrow-down me-1"></i> 2% menos
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            
            <footer class="footer py-3">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Sistema de Gestión de Estudiantes y Pagos de Mensualidad <?php echo date('Y'); ?></div>
                        <div>
                            <a href="#" class="text-muted">Política de Privacidad</a>
                            &middot;
                            <a href="#" class="text-muted">Términos &amp; Condiciones</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
        <a class="scroll-to-top" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>
    
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const terminoBusquedaInput = document.getElementById('termino_busqueda');
            const resultadosBusquedaDiv = document.getElementById('resultados_busqueda');
            const botonBuscar = document.getElementById('boton_buscar');

            terminoBusquedaInput.addEventListener('input', function() {
                const terminoBusqueda = this.value.trim();

                if (terminoBusqueda.length > 0) {
                    // Realizar la búsqueda usando AJAX
                    fetch('buscar_modulos.php?termino_busqueda=' + terminoBusqueda)
                        .then(response => response.json())
                        .then(data => {
                            // Mostrar los resultados
                            resultadosBusquedaDiv.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(modulo => {
                                    const a = document.createElement('a');
                                    a.href = modulo.url;
                                    a.className = 'dropdown-item d-flex align-items-center py-2';
                                    a.innerHTML = `
                                        <i class="fas fa-search me-2 text-muted"></i>
                                        <div>
                                            <div class="fw-medium">${modulo.nombre}</div>
                                            <div class="small text-muted">${modulo.descripcion || ''}</div>
                                        </div>
                                    `;
                                    resultadosBusquedaDiv.appendChild(a);
                                });
                                resultadosBusquedaDiv.style.display = 'block';
                            } else {
                                resultadosBusquedaDiv.innerHTML = `
                                    <div class="dropdown-item text-center py-3">
                                        <i class="fas fa-search-minus text-muted mb-2"></i>
                                        <div class="small text-muted">No se encontraron resultados</div>
                                    </div>
                                `;
                                resultadosBusquedaDiv.style.display = 'block';
                            }
                        });
                } else {
                    resultadosBusquedaDiv.style.display = 'none';
                }
            });

            botonBuscar.addEventListener('click', function(event) {
                event.preventDefault();
                const terminoBusqueda = terminoBusquedaInput.value.trim();
                if (terminoBusqueda.length > 0) {
                    window.location.href = 'resultados_busqueda.php?termino_busqueda=' + terminoBusqueda;
                }
            });

            // Ocultar los resultados al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!terminoBusquedaInput.contains(event.target) && !resultadosBusquedaDiv.contains(event.target)) {
                    resultadosBusquedaDiv.style.display = 'none';
                }
            });
        });
    </script>
   
</body>

</html>
<?php
$conn->close();
?>