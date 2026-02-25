<?php
// filepath: c:\xampp\htdocs\mi-aplicacion-web\inscribir_alumno.php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Configuración para subida de archivos
$upload_dir = "assets/img/uploads/";
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png'];

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Obtener el año escolar activo
$sql_anio_escolar = "SELECT id, nombre FROM anios_escolares WHERE activo = 1";
$result_anio_escolar = $conn->query($sql_anio_escolar);

if ($result_anio_escolar->num_rows > 0) {
    $row_anio_escolar = $result_anio_escolar->fetch_assoc();
    $anio_escolar_id = $row_anio_escolar["id"];
    $anio_escolar_nombre = $row_anio_escolar["nombre"];
} else {
    $error = "No hay un año escolar activo configurado.";
}

// Obtener los grados activos para el año escolar actual
$sql_grados_activos = "SELECT g.id AS grado_id, g.nombre, ag.id AS anio_grado_id
                        FROM grados g
                        INNER JOIN anios_grados ag ON g.id = ag.grado_id
                        WHERE ag.anio_escolar_id = '$anio_escolar_id' AND ag.activo = 1";
$result_grados_activos = $conn->query($sql_grados_activos);

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar foto del alumno
    $foto_alumno = null;
    if (isset($_FILES['foto_alumno']) && $_FILES['foto_alumno']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['foto_alumno'];
        if (in_array($file_info['type'], $allowed_types) && $file_info['size'] <= $max_file_size) {
            $ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
            $filename = 'alumno_' . uniqid() . '.' . $ext;
            move_uploaded_file($file_info['tmp_name'], $upload_dir . $filename);
            $foto_alumno = $filename;
        }
    }

    // Procesar foto del representante
    $foto_representante = null;
    if (isset($_FILES['foto_representante']) && $_FILES['foto_representante']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['foto_representante'];
        if (in_array($file_info['type'], $allowed_types) && $file_info['size'] <= $max_file_size) {
            $ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
            $filename = 'representante_' . uniqid() . '.' . $ext;
            move_uploaded_file($file_info['tmp_name'], $upload_dir . $filename);
            $foto_representante = $filename;
        }
    }

    // Recoger los datos del formulario del estudiante
    $tipo_cedula_estudiante = $_POST["tipo_cedula_estudiante"];
    $cedula_estudiante = $_POST["cedula_estudiante"];
    $telefono_estudiante = $_POST["telefono_estudiante"];
    $codigo_telefono_estudiante = $_POST["codigo_telefono_estudiante"];

    $cedula_estudiante = $tipo_cedula_estudiante . $cedula_estudiante;
    $telefono_estudiante = $codigo_telefono_estudiante . $telefono_estudiante;

    $nombres_estudiante = $_POST["nombres_estudiante"];
    $apellidos_estudiante = $_POST["apellidos_estudiante"];
    $sexo = $_POST["sexo"];
    $fecha_nacimiento_estudiante = $_POST["fecha_nacimiento_estudiante"];
    $edad_estudiante = $_POST["edad_estudiante"];
    $observaciones = $_POST["observaciones"];
    $estado = $_POST["estado"];
    $municipio = $_POST["municipio"];
    $localidad = $_POST["localidad"];
    $plantel_procedencia = $_POST["plantel_procedencia"];

    // Recoger los datos del formulario del representante
    $tipo_cedula_representante = $_POST["tipo_cedula_representante"];
    $cedula_representante = $_POST["cedula_representante"];
    $telefono_representante = $_POST["telefono_representante"];
    $codigo_telefono_representante = $_POST["codigo_telefono_representante"];

    $cedula_representante = $tipo_cedula_representante . $cedula_representante;
    $telefono_representante = $codigo_telefono_representante . $telefono_representante;

    $nombres_representante = $_POST["nombres_representante"];
    $apellidos_representante = $_POST["apellidos_representante"];
    $fecha_nacimiento_representante = $_POST["fecha_nacimiento_representante"];
    $edad_representante = $_POST["edad_representante"];
    $nacionalidad = $_POST["nacionalidad"];
    $profesion_oficio = $_POST["profesion_oficio"];
    $parentesco = $_POST["parentesco"];
    $parroquia = $_POST["parroquia"];
    $sector = $_POST["sector"];
    $direccion = $_POST["direccion"];
    $correo_electronico_representante = $_POST["correo_electronico_representante"];

    // Recoger los datos del formulario de datos académicos
    $fecha_ingreso = $_POST["fecha_ingreso"];
    $escolaridad = $_POST["escolaridad"];
    $anio_grado_id = $_POST["grado_anio"];
    $seccion = $_POST["seccion"];

    // Validar los datos (puedes añadir más validaciones)
    if (empty($cedula_estudiante) || empty($nombres_estudiante) || empty($apellidos_estudiante) || empty($sexo) || empty($fecha_nacimiento_estudiante) || empty($edad_estudiante) || empty($cedula_representante) || empty($nombres_representante) || empty($apellidos_representante) || empty($fecha_nacimiento_representante) || empty($edad_representante) || empty($nacionalidad) || empty($profesion_oficio) || empty($telefono_representante) || empty($parentesco) || empty($parroquia) || empty($sector) || empty($direccion) || empty($fecha_ingreso) || empty($escolaridad) || empty($anio_grado_id) || empty($seccion)) {
        $error = "Por favor, rellena todos los campos obligatorios.";
    } else {
        // Iniciar transacción para asegurar la integridad de los datos
        $conn->begin_transaction();

        try {
            // Preparar la consulta SQL para insertar los datos del representante
            $sql_representante = "INSERT INTO representantes (cedula, nombres, apellidos, fecha_nacimiento, edad, nacionalidad, profesion_oficio, telefono, parentesco, parroquia, sector, direccion, correo_electronico, foto)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_representante = $conn->prepare($sql_representante);
            $stmt_representante->bind_param("ssssisssssssss", $cedula_representante, $nombres_representante, $apellidos_representante, $fecha_nacimiento_representante, $edad_representante, $nacionalidad, $profesion_oficio, $telefono_representante, $parentesco, $parroquia, $sector, $direccion, $correo_electronico_representante, $foto_representante);

            // Ejecutar la consulta del representante
            if ($stmt_representante->execute()) {
                $representante_id = $conn->insert_id; // Obtener el ID del representante insertado

                // Preparar la consulta SQL para insertar los datos del estudiante
                $sql_estudiante = "INSERT INTO estudiantes (representante_id, cedula, nombres, apellidos, sexo, fecha_nacimiento, edad, telefono, observaciones, estado, municipio, localidad, plantel_procedencia, foto)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_estudiante = $conn->prepare($sql_estudiante);
                $stmt_estudiante->bind_param("isssssisssssss", $representante_id, $cedula_estudiante, $nombres_estudiante, $apellidos_estudiante, $sexo, $fecha_nacimiento_estudiante, $edad_estudiante, $telefono_estudiante, $observaciones, $estado, $municipio, $localidad, $plantel_procedencia, $foto_alumno);

                // Ejecutar la consulta del estudiante
                if ($stmt_estudiante->execute()) {
                    $estudiante_id = $conn->insert_id; // Obtener el ID del estudiante insertado

                    // Registrar notificación
                    $sql_notificacion = "INSERT INTO notificaciones (tipo, mensaje) VALUES ('inscripcion_estudiante', 'Nuevo estudiante inscrito con ID: $estudiante_id')";
                    $conn->query($sql_notificacion);

                    // Preparar la consulta SQL para insertar los datos académicos
                    $sql_datos_academicos = "INSERT INTO datos_academicos (estudiante_id, fecha_ingreso, escolaridad, anio_grado_id, seccion, anio_escolar_id)
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_datos_academicos = $conn->prepare($sql_datos_academicos);
                    $stmt_datos_academicos->bind_param("issisi", $estudiante_id, $fecha_ingreso, $escolaridad, $anio_grado_id, $seccion, $anio_escolar_id);

                    // Ejecutar la consulta de datos académicos
                    if ($stmt_datos_academicos->execute()) {
                        // Commit la transacción si todas las consultas fueron exitosas
                        $conn->commit();
                        $mensaje = "Alumno inscrito correctamente.";
                    } else {
                        throw new Exception("Error al inscribir los datos académicos: " . $stmt_datos_academicos->error);
                    }
                } else {
                    throw new Exception("Error al inscribir el alumno: " . $stmt_estudiante->error);
                }
            } else {
                throw new Exception("Error al inscribir el representante: " . $stmt_representante->error);
            }
        } catch (Exception $e) {
            // Rollback la transacción en caso de error
            $conn->rollback();
            $error = "Error: " . $e->getMessage();
        } finally {
            // Cerrar los statements
            if (isset($stmt_representante)) $stmt_representante->close();
            if (isset($stmt_estudiante)) $stmt_estudiante->close();
            if (isset($stmt_datos_academicos)) $stmt_datos_academicos->close();
        }
    }
}

// Obtener el rol del usuario
$user_role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Inscribir Alumno - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
            background-color: #f5f7fb;
            font-family: 'Nunito', sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border-left: 4px solid var(--primary-color);
        }
        
        .form-section h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.25rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .form-section h4 i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }
        
        .alert-info {
            background-color: rgba(76, 201, 240, 0.1);
            border-color: rgba(76, 201, 240, 0.2);
            color: #1a6a82;
        }
        
        /* Estilos para los tabs de sección */
        .section-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }
        
        .section-tab {
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
            color: #6c757d;
            transition: all 0.3s;
        }
        
        .section-tab:hover, .section-tab.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
        
        .section-tab.active {
            font-weight: 600;
        }
        
        /* Efecto de hover para los campos */
        .form-group {
            position: relative;
            margin-bottom: 1.25rem;
        }
        
        .form-group:hover .form-label {
            color: var(--primary-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .form-section {
                padding: 1rem;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-section {
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        /* Estilo para campos requeridos */
        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        /* Estilos para radio buttons */
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.15em;
        }

        .form-check-label {
            margin-left: 0.3em;
            font-weight: normal;
        }

        /* Estilos para campos de archivo */
        .form-control[type="file"] {
            padding: 0.375rem;
        }

        .form-control[type="file"]::file-selector-button {
            padding: 0.375rem 0.75rem;
            margin-right: 0.75rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .form-control[type="file"]::file-selector-button:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Barra de navegación superior -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow">
                                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                                        <span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $_SESSION["username"]; ?></span>
                                        <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Cerrar sesión</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <!-- Contenido principal -->
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="text-dark mb-1">Formulario de Registro</h3>
                            <p class="text-muted mb-0">Complete todos los campos requeridos para registrar un nuevo estudiante</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo date('d M, Y'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <p class="text-primary m-0 fw-bold"><i class="fas fa-user-graduate me-2"></i>Formulario de registro</p>
                            <?php if ($anio_escolar_nombre) : ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Año Escolar: <?php echo htmlspecialchars($anio_escolar_nombre); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <!-- Tabs de navegación entre secciones -->
                            <div class="section-tabs mb-4">
                                <div class="section-tab active" onclick="showSection('estudiante')">
                                    <i class="fas fa-user me-1"></i> Estudiante
                                </div>
                                <div class="section-tab" onclick="showSection('representante')">
                                    <i class="fas fa-user-tie me-1"></i> Representante
                                </div>
                                <div class="section-tab" onclick="showSection('academico')">
                                    <i class="fas fa-graduation-cap me-1"></i> Académico
                                </div>
                            </div>
                            
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="inscripcionForm" enctype="multipart/form-data">
                                <!-- Sección de Información del Estudiante -->
                                <div class="form-section" id="estudiante-section">
                                    <h4><i class="fas fa-user"></i> Información del Estudiante</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="cedula_estudiante"><strong>Cédula</strong></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="tipo_cedula_estudiante" name="tipo_cedula_estudiante" style="max-width: 80px;">
                                                        <option value="V-">V-</option>
                                                        <option value="E-">E-</option>
                                                    </select>
                                                    <input class="form-control" type="text" id="cedula_estudiante" name="cedula_estudiante" required>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="nombres_estudiante"><strong>Nombres</strong></label>
                                                <input class="form-control" type="text" id="nombres_estudiante" name="nombres_estudiante" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="apellidos_estudiante"><strong>Apellidos</strong></label>
                                                <input class="form-control" type="text" id="apellidos_estudiante" name="apellidos_estudiante" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="sexo"><strong>Genero</strong></label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="sexo" id="sexo_masculino" value="masculino" required>
                                                        <label class="form-check-label" for="sexo_masculino">Masculino</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="sexo" id="sexo_femenino" value="femenino">
                                                        <label class="form-check-label" for="sexo_femenino">Femenino</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="fecha_nacimiento_estudiante"><strong>Fecha de Nacimiento</strong></label>
                                                <input class="form-control" type="date" id="fecha_nacimiento_estudiante" name="fecha_nacimiento_estudiante" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="edad_estudiante"><strong>Edad</strong></label>
                                                <input class="form-control" type="number" id="edad_estudiante" name="edad_estudiante" min="3" max="25" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="telefono_estudiante"><strong>Teléfono de emergencia</strong></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="codigo_telefono_estudiante" name="codigo_telefono_estudiante" style="max-width: 80px;">
                                                        <option value="0416">0416</option>
                                                        <option value="0426">0426</option>
                                                        <option value="0414">0414</option>
                                                        <option value="0424">0424</option>
                                                        <option value="0412">0412</option>
                                                    </select>
                                                    <input class="form-control" type="text" id="telefono_estudiante" name="telefono_estudiante" maxlength="7" pattern="[0-9]{7}" title="Debe contener exactamente 7 dígitos">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="plantel_procedencia"><strong>Plantel de Procedencia</strong></label>
                                                <input class="form-control" type="text" id="plantel_procedencia" name="plantel_procedencia">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="foto_alumno"><strong>Foto del Alumno</strong></label>
                                                <input class="form-control" type="file" id="foto_alumno" name="foto_alumno" accept="image/jpeg, image/png">
                                                <small class="text-muted">Formatos aceptados: JPG, PNG (Máx. 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="observaciones"><strong>Observaciones</strong></label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="estado"><strong>Estado</strong></label>
                                                <select class="form-select" id="estado" name="estado">
                <option value="">Selecciona un estado</option>
                <option value="Amazonas">Amazonas</option>
                <option value="Anzoátegui">Anzoátegui</option>
                <option value="Apure">Apure</option>
                <option value="Aragua">Aragua</option>
                <option value="Barinas">Barinas</option>
                <option value="Bolívar">Bolívar</option>
                <option value="Carabobo">Carabobo</option>
                <option value="Cojedes">Cojedes</option>
                <option value="Delta Amacuro">Delta Amacuro</option>
                <option value="Falcón">Falcón</option>
                <option value="Guárico">Guárico</option>
                <option value="Lara">Lara</option>
                <option value="Mérida">Mérida</option>
                <option value="Miranda">Miranda</option>
                <option value="Monagas">Monagas</option>
                <option value="Nueva Esparta">Nueva Esparta</option>
                <option value="Portuguesa">Portuguesa</option>
                <option value="Sucre">Sucre</option>
                <option value="Táchira">Táchira</option>
                <option value="Trujillo">Trujillo</option>
                <option value="Vargas (La Guaira)">Vargas (La Guaira)</option>
                <option value="Yaracuy">Yaracuy</option>
                <option value="Zulia">Zulia</option>
                <option value="Distrito Capital">Distrito Capital</option>
            </select>
        </div>
        </div>
        <div class="col-md-4">
    <div class="mb-3">
        <label class="form-label" for="municipio"><strong>Municipio</strong></label>
        <select class="form-select" id="municipio" name="municipio">
            <option value="">Selecciona un municipio</option>
            <!-- Los municipios se cargarán dinámicamente -->
        </select>
    </div>
</div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="localidad"><strong>Localidad</strong></label>
                                                <input class="form-control" type="text" id="localidad" name="localidad">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sección de Información del Representante -->
                                <div class="form-section" id="representante-section" style="display: none;">
                                    <h4><i class="fas fa-user-tie"></i> Información del Representante</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="cedula_representante"><strong>Cédula</strong></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="tipo_cedula_representante" name="tipo_cedula_representante" style="max-width: 80px;">
                                                        <option value="V-">V-</option>
                                                        <option value="E-">E-</option>
                                                    </select>
                                                    <input class="form-control" type="text" id="cedula_representante" name="cedula_representante" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="nombres_representante"><strong>Nombres</strong></label>
                                                <input class="form-control" type="text" id="nombres_representante" name="nombres_representante" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="apellidos_representante"><strong>Apellidos</strong></label>
                                                <input class="form-control" type="text" id="apellidos_representante" name="apellidos_representante" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="fecha_nacimiento_representante"><strong>Fecha de Nacimiento</strong></label>
                                                <input class="form-control" type="date" id="fecha_nacimiento_representante" name="fecha_nacimiento_representante" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="edad_representante"><strong>Edad</strong></label>
                                                <input class="form-control" type="number" id="edad_representante" name="edad_representante" min="18" max="99" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="nacionalidad"><strong>Nacionalidad</strong></label>
                                                <input class="form-control" type="text" id="nacionalidad" name="nacionalidad" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="profesion_oficio"><strong>Profesión u Oficio</strong></label>
                                                <input class="form-control" type="text" id="profesion_oficio" name="profesion_oficio" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="telefono_representante"><strong>Teléfono</strong></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="codigo_telefono_representante" name="codigo_telefono_representante" style="max-width: 80px;">
                                                        <option value="0416">0416</option>
                                                        <option value="0426">0426</option>
                                                        <option value="0414">0414</option>
                                                        <option value="0424">0424</option>
                                                        <option value="0412">0412</option>
                                                    </select>
                                                    <input class="form-control" type="text" id="telefono_representante" name="telefono_representante" maxlength="7" pattern="[0-9]{7}" title="Debe contener exactamente 7 dígitos" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="parentesco"><strong>Parentesco</strong></label>
                                                <input class="form-control" type="text" id="parentesco" name="parentesco" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="foto_representante"><strong>Foto del Representante</strong></label>
                                                <input class="form-control" type="file" id="foto_representante" name="foto_representante" accept="image/jpeg, image/png">
                                                <small class="text-muted">Formatos aceptados: JPG, PNG (Máx. 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="parroquia"><strong>Parroquia</strong></label>
                                                <input class="form-control" type="text" id="parroquia" name="parroquia" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="sector"><strong>Sector</strong></label>
                                                <input class="form-control" type="text" id="sector" name="sector" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="direccion"><strong>Dirección</strong></label>
                                                <input class="form-control" type="text" id="direccion" name="direccion" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="correo_electronico_representante"><strong>Correo Electrónico</strong></label>
                                        <input class="form-control" type="email" id="correo_electronico_representante" name="correo_electronico_representante">
                                    </div>
                                </div>
                                
                                <!-- Sección de Datos Académicos -->
                                <div class="form-section" id="academico-section" style="display: none;">
                                    <h4><i class="fas fa-graduation-cap"></i> Datos Académicos</h4>
                                    
                                    <?php if ($anio_escolar_nombre) : ?>
                                        <div class="alert alert-info d-flex align-items-center" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <div>
                                                <strong>Año Escolar Activo:</strong> <?php echo htmlspecialchars($anio_escolar_nombre); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="fecha_ingreso"><strong>Fecha de Ingreso</strong></label>
                                                <input class="form-control" type="date" id="fecha_ingreso" name="fecha_ingreso" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="escolaridad"><strong>Escolaridad</strong></label>
                                                <select class="form-select" id="escolaridad" name="escolaridad" required>
                                                    <option value="regular">Regular</option>
                                                    <option value="pendiente">Pendiente</option>
                                                    <option value="repitiente">Repitiente</option>
                                                    <option value="doble_inscripcion">Doble Inscripción</option>
                                                    <option value="repite_pendiente">Repite Pendiente</option>
                                                    <option value="equivalencia">Equivalencia</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="grado_anio"><strong>Grado / Año</strong></label>
                                                <select class="form-select" id="grado_anio" name="grado_anio" required>
                                                    <option value="">Selecciona un grado</option>
                                                    <?php
                                                    if ($result_grados_activos->num_rows > 0) {
                                                        while ($row = $result_grados_activos->fetch_assoc()) {
                                                            echo "<option value='" . $row["anio_grado_id"] . "' data-grado-id='" . $row["grado_id"] . "'>" . $row["nombre"] . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required-field" for="seccion"><strong>Sección</strong></label>
                                                <select class="form-select" id="seccion" name="seccion" required>
                                                    <option value="">Selecciona una sección</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
                                        <i class="fas fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-primary ms-auto" id="nextBtn">
                                        Siguiente <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                        <i class="fas fa-save me-1"></i> Registrar Alumno
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Gestión Escolar <?php echo date('Y'); ?></span></div>
                </div>
            </footer>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables para controlar las secciones
            const sections = ['estudiante', 'representante', 'academico'];
            let currentSection = 0;
            // --- NUEVA LÓGICA DE MUNICIPIOS ---
    const municipiosPorEstado = {
        "Amazonas": ["Alto Orinoco", "Atabapo", "Atures", "Autana", "Manapiare", "Maroa", "Río Negro"],
        "Anzoátegui": ["Anaco", "Aragua", "Bruzual", "Cajigal", "Freites", "Libertad", "Miranda", "Simón Rodríguez", "Sotillo"],
        "Apure": ["Achaguas", "Biruaca", "Muñoz", "Páez", "Pedro Camejo", "Rómulo Gallegos", "San Fernando"],
        "Aragua": ["Bolívar", "Camatagua", "Girardot", "José Félix Ribas", "Linares Alcántara", "Mario Briceño Iragorry", "Ocumare", "Zamora"],
        "Barinas": ["Alberto Arvelo Torrealba", "Barinas", "Bolívar", "Cruz Paredes", "Ezequiel Zamora", "Obispos", "Pedraza", "Rojas", "Sosa"],
        "Bolívar": ["Angostura del Orinoco", "Caroní", "Cedeño", "El Callao", "Gran Sabana", "Padre Pedro Chien", "Piar", "Roscio", "Sifontes", "Sucre", "Padre Chien"],
        "Carabobo": ["Bejuma", "Carlos Arvelo", "Diego Ibarra", "Guacara", "Juan José Mora", "Libertador", "Los Guayos", "Naguanagua", "Puerto Cabello", "Valencia"],
        "Cojedes": ["Anzoátegui", "Falcón", "Girardot", "Lima Blanco", "Pao de San Juan Bautista", "Ricaurte", "Rómulo Gallegos", "San Carlos", "Tinaco"],
        "Delta Amacuro": ["Antonio Díaz", "Casacoima", "Pedernales", "Tucupita"],
        "Distrito Capital": ["Libertador"],
        "Falcón": ["Acosta", "Bolívar", "Carirubana", "Colina", "Dabajuro", "Miranda", "Petit", "Silva", "Zamora"],
        "Guárico": ["Camaguán", "Chaguaramas", "Infante", "Miranda", "Monagas", "Ortiz", "Ribas", "Roscio", "Zaraza"],
        "Lara": ["Andrés Eloy Blanco", "Crespo", "Iribarren", "Jiménez", "Morán", "Palavecino", "Simón Planas", "Torres", "Urdaneta"],
        "Mérida": ["Alberto Adriani", "Andrés Bello", "Aricagua", "Campo Elías", "Guaraque", "Libertador", "Miranda", "Tovar", "Zea"],
        "Miranda": ["Baruta", "Chacao", "El Hatillo", "Guaicaipuro", "Los Salias", "Páez", "Paz Castillo", "Plaza", "Sucre", "Urdaneta"],
        "Monagas": ["Acosta", "Aguasay", "Bolívar", "Caripe", "Cedeño", "Libertador", "Maturín", "Piar", "Punceres", "Sotillo"],
        "Nueva Esparta": ["Antolín del Campo", "Arismendi", "Díaz", "García", "Gómez", "Maneiro", "Marcano", "Mariño", "Península de Macanao", "Tubores", "Villalba"],
        "Portuguesa": ["Araure", "Esteller", "Guanare", "Guanarito", "Ospino", "Páez", "Papelón", "San Genaro de Boconoíto", "San Rafael de Onoto", "Turén"],
        "Sucre": ["Andrés Eloy Blanco", "Andrés Mata", "Arismendi", "Benítez", "Bermúdez", "Bolívar", "Cajigal", "Cruz Salmerón Acosta", "Libertador", "Mariño", "Mejía", "Montes", "Ribero", "Sucre", "Valdez"],
        "Táchira": ["Ayacucho", "Bolívar", "Cárdenas", "Cordero", "Junín", "Libertad", "San Cristóbal", "Torbes", "Uribante"],
        "Trujillo": ["Boconó", "Carache", "Escuque", "Miranda", "Motatán", "Pampán", "Trujillo", "Valera"],
        "Vargas (La Guaira)": ["Vargas"],
        "Yaracuy": ["Aristides Bastidas", "Bruzual", "Cocorote", "Independencia", "Nirgua", "Peña", "San Felipe", "Urachiche"],
        "Zulia": ["Almirante Padilla", "Baralt", "Cabimas", "Catatumbo", "Colón", "Lagunillas", "Machiques", "Mara", "Maracaibo", "Miranda", "San Francisco"]
    };

    document.getElementById('estado').addEventListener('change', function() {
        const estado = this.value;
        const municipioSelect = document.getElementById('municipio');
        
        municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
        
        if (municipiosPorEstado[estado]) {
            municipiosPorEstado[estado].forEach(muni => {
                const option = document.createElement('option');
                option.value = muni;
                option.textContent = muni;
                municipioSelect.appendChild(option);
            });
        }
    });
    // --- FIN LÓGICA DE MUNICIPIOS ---
            
            // Mostrar sección actual y ocultar las demás
            function showCurrentSection() {
                sections.forEach((section, index) => {
                    const sectionElement = document.getElementById(`${section}-section`);
                    const tabElement = document.querySelector(`.section-tab:nth-child(${index + 1})`);
                    
                    if (index === currentSection) {
                        sectionElement.style.display = 'block';
                        tabElement.classList.add('active');
                    } else {
                        sectionElement.style.display = 'none';
                        tabElement.classList.remove('active');
                    }
                });
                
                // Mostrar/ocultar botones de navegación
                document.getElementById('prevBtn').style.display = currentSection === 0 ? 'none' : 'block';
                document.getElementById('nextBtn').style.display = currentSection === sections.length - 1 ? 'none' : 'block';
                document.getElementById('submitBtn').style.display = currentSection === sections.length - 1 ? 'block' : 'none';
            }
            
            // Función para mostrar una sección específica
            window.showSection = function(section) {
                currentSection = sections.indexOf(section);
                showCurrentSection();
            }
            
            // Navegación entre secciones
            document.getElementById('nextBtn').addEventListener('click', function() {
                if (validateCurrentSection()) {
                    currentSection++;
                    showCurrentSection();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
            
            document.getElementById('prevBtn').addEventListener('click', function() {
                currentSection--;
                showCurrentSection();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Validar campos requeridos antes de avanzar
            function validateCurrentSection() {
                const currentSectionId = `${sections[currentSection]}-section`;
                const requiredInputs = document.querySelectorAll(`#${currentSectionId} [required]`);
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        
                        // Mostrar mensaje de error con SweetAlert2
                        if (input.id === 'cedula_estudiante' || input.id === 'cedula_representante') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Campo requerido',
                                text: 'Por favor, complete el número de cédula',
                                confirmButtonColor: '#4361ee'
                            });
                        } else {
                            const label = document.querySelector(`label[for="${input.id}"]`).textContent.replace(' *', '');
                            Swal.fire({
                                icon: 'error',
                                title: 'Campo requerido',
                                text: `Por favor, complete el campo: ${label}`,
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                // Validar que se haya seleccionado un sexo
                if (currentSectionId === 'estudiante-section') {
                    const sexoSelected = document.querySelector('input[name="sexo"]:checked');
                    if (!sexoSelected) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Campo requerido',
                            text: 'Por favor, seleccione el sexo del estudiante',
                            confirmButtonColor: '#4361ee'
                        });
                        isValid = false;
                    }
                }
                
                return isValid;
            }
            
            // Cargar secciones al cambiar el grado
            const gradoSelect = document.getElementById('grado_anio');
            const seccionSelect = document.getElementById('seccion');
            
            gradoSelect.addEventListener('change', function() {
                const anioGradoId = this.value;
                seccionSelect.innerHTML = '<option value="">Selecciona una sección</option>';
                
                if (anioGradoId) {
                    fetch('obtener_secciones.php?anio_grado_id=' + anioGradoId)
                        .then(response => {
                            if (!response.ok) throw new Error('Error en la respuesta del servidor');
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.length > 0) {
                                data.forEach(seccion => {
                                    const option = document.createElement('option');
                                    option.value = seccion;
                                    option.textContent = seccion;
                                    seccionSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar secciones:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudieron cargar las secciones. Por favor intente nuevamente.',
                                confirmButtonColor: '#4361ee'
                            });
                        });
                }
            });
            
            // Mostrar la primera sección al cargar la página
            showCurrentSection();
            
            // Validar formulario al enviar
            document.getElementById('inscripcionForm').addEventListener('submit', function(e) {
                if (!validateCurrentSection()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Formulario incompleto',
                        text: 'Por favor complete todos los campos requeridos antes de enviar.',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
            
            // Calcular edad automáticamente al cambiar la fecha de nacimiento
            document.getElementById('fecha_nacimiento_estudiante').addEventListener('change', function() {
                calculateAge(this, 'edad_estudiante');
            });
            
            document.getElementById('fecha_nacimiento_representante').addEventListener('change', function() {
                calculateAge(this, 'edad_representante');
            });
            
            function calculateAge(dateInput, ageInputId) {
                const birthDate = new Date(dateInput.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                document.getElementById(ageInputId).value = age;
            }
        });
    </script>
</body>

</html>
<?php
$conn->close();
?>