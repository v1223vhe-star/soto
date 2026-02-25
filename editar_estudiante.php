<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$sql_user = "SELECT role FROM usuarios WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user_role = $result_user->fetch_assoc()['role'];

if ($user_role != 'admin') {
    header("Location: acceso_denegado.php"); // Crea esta página
    exit();
}

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Verificar si se ha proporcionado un ID de estudiante para editar
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $error = "ID de estudiante no válido.";
    // Puedes redirigir a la página de gestión de estudiantes si lo deseas
    // header("Location: gestionar_estudiantes.php");
    // exit();
} else {
    $id_estudiante = $_GET["id"];

    // Consulta para obtener la información del estudiante y su representante
    $sql_estudiante = "SELECT e.*, r.cedula as cedula_representante, r.nombres as nombres_representante, 
                       r.apellidos as apellidos_representante, r.telefono as telefono_representante,
                       r.fecha_nacimiento as fecha_nacimiento_representante, r.edad as edad_representante,
                       r.nacionalidad, r.profesion_oficio, r.parentesco, r.parroquia, r.sector, r.direccion,
                       r.correo_electronico as correo_representante
                       FROM estudiantes e
                       LEFT JOIN representantes r ON e.representante_id = r.id
                       WHERE e.id = $id_estudiante";
    $result_estudiante = $conn->query($sql_estudiante);

    if ($result_estudiante->num_rows == 0) {
        $error = "Estudiante no encontrado.";
        // Puedes redirigir a la página de gestión de estudiantes si lo deseas
        // header("Location: gestionar_estudiantes.php");
        // exit();
    } else {
        $estudiante = $result_estudiante->fetch_assoc();

        // Procesar el formulario de edición de estudiante
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_estudiante"])) {
            // Procesar datos del estudiante
            $tipo_cedula_estudiante = $_POST["tipo_cedula_estudiante"];
            $cedula_estudiante = $_POST["cedula_estudiante"];
            $telefono_estudiante = $_POST["telefono_estudiante"];
            $codigo_telefono_estudiante = $_POST["codigo_telefono_estudiante"];
            
            $cedula = $tipo_cedula_estudiante . $cedula_estudiante;
            $telefono = $codigo_telefono_estudiante . $telefono_estudiante;
            
            $nombres = $_POST["nombres"];
            $apellidos = $_POST["apellidos"];
            $sexo = $_POST["sexo"];
            $fecha_nacimiento = $_POST["fecha_nacimiento"];
            $edad = $_POST["edad"];
            $observaciones = $_POST["observaciones"];
            $estado = $_POST["estado"];
            $municipio = $_POST["municipio"];
            $localidad = $_POST["localidad"];
            $plantel_procedencia = $_POST["plantel_procedencia"];

            // Procesar datos del representante
            $tipo_cedula_representante = $_POST["tipo_cedula_representante"];
            $cedula_representante = $_POST["cedula_representante"];
            $telefono_representante = $_POST["telefono_representante"];
            $codigo_telefono_representante = $_POST["codigo_telefono_representante"];
            
            $cedula_representante_completa = $tipo_cedula_representante . $cedula_representante;
            $telefono_representante_completo = $codigo_telefono_representante . $telefono_representante;
            
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
            $correo_representante = $_POST["correo_representante"];

            // Validar los datos
            if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($sexo) || 
                empty($fecha_nacimiento) || empty($edad) || empty($estado) || empty($municipio) || 
                empty($localidad) || empty($cedula_representante_completa) || empty($nombres_representante) || 
                empty($apellidos_representante) || empty($fecha_nacimiento_representante) || 
                empty($edad_representante) || empty($nacionalidad) || empty($profesion_oficio) || 
                empty($telefono_representante_completo) || empty($parentesco) || empty($parroquia) || 
                empty($sector) || empty($direccion)) {
                $error = "Por favor, rellena todos los campos obligatorios.";
            } else {
                // Iniciar transacción
                $conn->begin_transaction();
                
                try {
                    // Actualizar datos del representante
                    $sql_representante = "UPDATE representantes SET 
                        cedula = '$cedula_representante_completa',
                        nombres = '$nombres_representante',
                        apellidos = '$apellidos_representante',
                        fecha_nacimiento = '$fecha_nacimiento_representante',
                        edad = '$edad_representante',
                        nacionalidad = '$nacionalidad',
                        profesion_oficio = '$profesion_oficio',
                        telefono = '$telefono_representante_completo',
                        parentesco = '$parentesco',
                        parroquia = '$parroquia',
                        sector = '$sector',
                        direccion = '$direccion',
                        correo_electronico = '$correo_representante'
                        WHERE id = " . $estudiante["representante_id"];
                    
                    if (!$conn->query($sql_representante)) {
                        throw new Exception("Error al actualizar el representante: " . $conn->error);
                    }
                    
                    // Actualizar datos del estudiante
                    $sql = "UPDATE estudiantes SET 
                        cedula = '$cedula',
                        nombres = '$nombres',
                        apellidos = '$apellidos',
                        sexo = '$sexo',
                        fecha_nacimiento = '$fecha_nacimiento',
                        edad = '$edad',
                        telefono = '$telefono',
                        observaciones = '$observaciones',
                        estado = '$estado',
                        municipio = '$municipio',
                        localidad = '$localidad',
                        plantel_procedencia = '$plantel_procedencia'
                        WHERE id = $id_estudiante";

                    if ($conn->query($sql)) {
                        // Registrar notificación
                        $sql_notificacion = "INSERT INTO notificaciones (tipo, mensaje) VALUES ('edicion_estudiante', 'Estudiante con ID: $id_estudiante actualizado')";
                        $conn->query($sql_notificacion);

                        $mensaje = "Estudiante actualizado exitosamente.";
                        // Actualizar la información del estudiante con los nuevos datos
                        $estudiante["cedula"] = $cedula;
                        $estudiante["nombres"] = $nombres;
                        $estudiante["apellidos"] = $apellidos;
                        $estudiante["sexo"] = $sexo;
                        $estudiante["fecha_nacimiento"] = $fecha_nacimiento;
                        $estudiante["edad"] = $edad;
                        $estudiante["telefono"] = $telefono;
                        $estudiante["observaciones"] = $observaciones;
                        $estudiante["estado"] = $estado;
                        $estudiante["municipio"] = $municipio;
                        $estudiante["localidad"] = $localidad;
                        $estudiante["plantel_procedencia"] = $plantel_procedencia;
                        
                        // Actualizar datos del representante en el array
                        $estudiante["cedula_representante"] = $cedula_representante_completa;
                        $estudiante["nombres_representante"] = $nombres_representante;
                        $estudiante["apellidos_representante"] = $apellidos_representante;
                        $estudiante["telefono_representante"] = $telefono_representante_completo;
                        $estudiante["fecha_nacimiento_representante"] = $fecha_nacimiento_representante;
                        $estudiante["edad_representante"] = $edad_representante;
                        $estudiante["nacionalidad"] = $nacionalidad;
                        $estudiante["profesion_oficio"] = $profesion_oficio;
                        $estudiante["parentesco"] = $parentesco;
                        $estudiante["parroquia"] = $parroquia;
                        $estudiante["sector"] = $sector;
                        $estudiante["direccion"] = $direccion;
                        $estudiante["correo_representante"] = $correo_representante;
                        
                        $conn->commit();
                    } else {
                        throw new Exception("Error al actualizar el estudiante: " . $conn->error);
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = $e->getMessage();
                }
            }
        }
        
        // Separar la cédula y teléfono del estudiante para mostrarlos en los campos divididos
        if (isset($estudiante["cedula"])) {
            $tipo_cedula_estudiante = substr($estudiante["cedula"], 0, 2);
            $cedula_estudiante = substr($estudiante["cedula"], 2);
        } else {
            $tipo_cedula_estudiante = "V-";
            $cedula_estudiante = "";
        }
        
        if (isset($estudiante["telefono"]) && strlen($estudiante["telefono"]) >= 4) {
            $codigo_telefono_estudiante = substr($estudiante["telefono"], 0, 4);
            $telefono_estudiante = substr($estudiante["telefono"], 4);
        } else {
            $codigo_telefono_estudiante = "0416";
            $telefono_estudiante = "";
        }
        
        // Separar la cédula y teléfono del representante para mostrarlos en los campos divididos
        if (isset($estudiante["cedula_representante"])) {
            $tipo_cedula_representante = substr($estudiante["cedula_representante"], 0, 2);
            $cedula_representante = substr($estudiante["cedula_representante"], 2);
        } else {
            $tipo_cedula_representante = "V-";
            $cedula_representante = "";
        }
        
        if (isset($estudiante["telefono_representante"]) && strlen($estudiante["telefono_representante"]) >= 4) {
            $codigo_telefono_representante = substr($estudiante["telefono_representante"], 0, 4);
            $telefono_representante = substr($estudiante["telefono_representante"], 4);
        } else {
            $codigo_telefono_representante = "0416";
            $telefono_representante = "";
        }
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Editar Estudiante - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Barra de navegación lateral -->
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
                            <h3 class="text-dark mb-1">Editar Estudiante</h3>
                            <p class="text-muted mb-0">Actualice la información del estudiante y su representante</p>
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
                            <p class="text-primary m-0 fw-bold"><i class="fas fa-user-edit me-2"></i>Formulario de Edición</p>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-id-card me-1"></i>
                                ID: <?php echo htmlspecialchars($id_estudiante); ?>
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <?php if ($estudiante) : ?>
                                <!-- Tabs de navegación entre secciones -->
                                <div class="section-tabs mb-4">
                                    <div class="section-tab active" onclick="showSection('estudiante')">
                                        <i class="fas fa-user me-1"></i> Estudiante
                                    </div>
                                    <div class="section-tab" onclick="showSection('representante')">
                                        <i class="fas fa-user-tie me-1"></i> Representante
                                    </div>
                                </div>
                                
                                <!-- Formulario para editar estudiante -->
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_estudiante); ?>">
                                    <!-- Sección de Información del Estudiante -->
                                    <div class="form-section" id="estudiante-section">
                                        <h4><i class="fas fa-user"></i> Información del Estudiante</h4>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="cedula_estudiante"><strong>Cédula</strong></label>
                                                    <div class="input-group">
                                                        <select class="form-select" id="tipo_cedula_estudiante" name="tipo_cedula_estudiante" style="max-width: 80px;">
                                                            <option value="V-" <?php echo ($tipo_cedula_estudiante == 'V-') ? 'selected' : ''; ?>>V-</option>
                                                            <option value="E-" <?php echo ($tipo_cedula_estudiante == 'E-') ? 'selected' : ''; ?>>E-</option>
                                                        </select>
                                                        <input class="form-control" type="text" id="cedula_estudiante" name="cedula_estudiante" value="<?php echo htmlspecialchars($cedula_estudiante); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="nombres"><strong>Nombres</strong></label>
                                                    <input class="form-control" type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($estudiante["nombres"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="apellidos"><strong>Apellidos</strong></label>
                                                    <input class="form-control" type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($estudiante["apellidos"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="sexo"><strong>Sexo</strong></label>
                                                    <select class="form-select" id="sexo" name="sexo" required>
                                                        <option value="masculino" <?php if ($estudiante["sexo"] == "masculino") echo "selected"; ?>>Masculino</option>
                                                        <option value="femenino" <?php if ($estudiante["sexo"] == "femenino") echo "selected"; ?>>Femenino</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="fecha_nacimiento"><strong>Fecha de Nacimiento</strong></label>
                                                    <input class="form-control" type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($estudiante["fecha_nacimiento"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="edad"><strong>Edad</strong></label>
                                                    <input class="form-control" type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($estudiante["edad"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="telefono_estudiante"><strong>Teléfono</strong></label>
                                                    <div class="input-group">
                                                        <select class="form-select" id="codigo_telefono_estudiante" name="codigo_telefono_estudiante" style="max-width: 80px;">
                                                            <option value="0416" <?php echo ($codigo_telefono_estudiante == '0416') ? 'selected' : ''; ?>>0416</option>
                                                            <option value="0426" <?php echo ($codigo_telefono_estudiante == '0426') ? 'selected' : ''; ?>>0426</option>
                                                            <option value="0414" <?php echo ($codigo_telefono_estudiante == '0414') ? 'selected' : ''; ?>>0414</option>
                                                            <option value="0424" <?php echo ($codigo_telefono_estudiante == '0424') ? 'selected' : ''; ?>>0424</option>
                                                            <option value="0412" <?php echo ($codigo_telefono_estudiante == '0412') ? 'selected' : ''; ?>>0412</option>
                                                            <option value="N/A" <?php echo ($codigo_telefono_estudiante == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                                        </select>
                                                        <input class="form-control" type="text" id="telefono_estudiante" name="telefono_estudiante" value="<?php echo htmlspecialchars($telefono_estudiante); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="plantel_procedencia"><strong>Plantel de Procedencia</strong></label>
                                                    <input class="form-control" type="text" id="plantel_procedencia" name="plantel_procedencia" value="<?php echo htmlspecialchars($estudiante["plantel_procedencia"]); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label" for="observaciones"><strong>Observaciones</strong></label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?php echo htmlspecialchars($estudiante["observaciones"]); ?></textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="estado"><strong>Estado</strong></label>
                                                    <select class="form-select" id="estado" name="estado">
                                                        <option value="">Selecciona un estado</option>
                                                        <option value="Amazonas" <?php echo ($estudiante["estado"] == 'Amazonas') ? 'selected' : ''; ?>>Amazonas</option>
                                                        <option value="Anzoátegui" <?php echo ($estudiante["estado"] == 'Anzoátegui') ? 'selected' : ''; ?>>Anzoátegui</option>
                                                        <option value="Apure" <?php echo ($estudiante["estado"] == 'Apure') ? 'selected' : ''; ?>>Apure</option>
                                                        <option value="Aragua" <?php echo ($estudiante["estado"] == 'Aragua') ? 'selected' : ''; ?>>Aragua</option>
                                                        <option value="Barinas" <?php echo ($estudiante["estado"] == 'Barinas') ? 'selected' : ''; ?>>Barinas</option>
                                                        <option value="Bolívar" <?php echo ($estudiante["estado"] == 'Bolívar') ? 'selected' : ''; ?>>Bolívar</option>
                                                        <option value="Carabobo" <?php echo ($estudiante["estado"] == 'Carabobo') ? 'selected' : ''; ?>>Carabobo</option>
                                                        <option value="Cojedes" <?php echo ($estudiante["estado"] == 'Cojedes') ? 'selected' : ''; ?>>Cojedes</option>
                                                        <option value="Delta Amacuro" <?php echo ($estudiante["estado"] == 'Delta Amacuro') ? 'selected' : ''; ?>>Delta Amacuro</option>
                                                        <option value="Falcón" <?php echo ($estudiante["estado"] == 'Falcón') ? 'selected' : ''; ?>>Falcón</option>
                                                        <option value="Guárico" <?php echo ($estudiante["estado"] == 'Guárico') ? 'selected' : ''; ?>>Guárico</option>
                                                        <option value="Lara" <?php echo ($estudiante["estado"] == 'Lara') ? 'selected' : ''; ?>>Lara</option>
                                                        <option value="Mérida" <?php echo ($estudiante["estado"] == 'Mérida') ? 'selected' : ''; ?>>Mérida</option>
                                                        <option value="Miranda" <?php echo ($estudiante["estado"] == 'Miranda') ? 'selected' : ''; ?>>Miranda</option>
                                                        <option value="Monagas" <?php echo ($estudiante["estado"] == 'Monagas') ? 'selected' : ''; ?>>Monagas</option>
                                                        <option value="Nueva Esparta" <?php echo ($estudiante["estado"] == 'Nueva Esparta') ? 'selected' : ''; ?>>Nueva Esparta</option>
                                                        <option value="Portuguesa" <?php echo ($estudiante["estado"] == 'Portuguesa') ? 'selected' : ''; ?>>Portuguesa</option>
                                                        <option value="Sucre" <?php echo ($estudiante["estado"] == 'Sucre') ? 'selected' : ''; ?>>Sucre</option>
                                                        <option value="Táchira" <?php echo ($estudiante["estado"] == 'Táchira') ? 'selected' : ''; ?>>Táchira</option>
                                                        <option value="Trujillo" <?php echo ($estudiante["estado"] == 'Trujillo') ? 'selected' : ''; ?>>Trujillo</option>
                                                        <option value="Vargas" <?php echo ($estudiante["estado"] == 'Vargas') ? 'selected' : ''; ?>>Vargas</option>
                                                        <option value="Yaracuy" <?php echo ($estudiante["estado"] == 'Yaracuy') ? 'selected' : ''; ?>>Yaracuy</option>
                                                        <option value="Zulia" <?php echo ($estudiante["estado"] == 'Zulia') ? 'selected' : ''; ?>>Zulia</option>
                                                        <option value="Distrito Capital" <?php echo ($estudiante["estado"] == 'Distrito Capital') ? 'selected' : ''; ?>>Distrito Capital</option>
                                                        <option value="Dependencias Federales" <?php echo ($estudiante["estado"] == 'Dependencias Federales') ? 'selected' : ''; ?>>Dependencias Federales</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="municipio"><strong>Municipio</strong></label>
                                                    <input class="form-control" type="text" id="municipio" name="municipio" value="<?php echo htmlspecialchars($estudiante["municipio"]); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="localidad"><strong>Localidad</strong></label>
                                                    <input class="form-control" type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($estudiante["localidad"]); ?>">
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
                                                            <option value="V-" <?php echo ($tipo_cedula_representante == 'V-') ? 'selected' : ''; ?>>V-</option>
                                                            <option value="E-" <?php echo ($tipo_cedula_representante == 'E-') ? 'selected' : ''; ?>>E-</option>
                                                        </select>
                                                        <input class="form-control" type="text" id="cedula_representante" name="cedula_representante" value="<?php echo htmlspecialchars($cedula_representante); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="nombres_representante"><strong>Nombres</strong></label>
                                                    <input class="form-control" type="text" id="nombres_representante" name="nombres_representante" value="<?php echo htmlspecialchars($estudiante["nombres_representante"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="apellidos_representante"><strong>Apellidos</strong></label>
                                                    <input class="form-control" type="text" id="apellidos_representante" name="apellidos_representante" value="<?php echo htmlspecialchars($estudiante["apellidos_representante"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="fecha_nacimiento_representante"><strong>Fecha de Nacimiento</strong></label>
                                                    <input class="form-control" type="date" id="fecha_nacimiento_representante" name="fecha_nacimiento_representante" value="<?php echo htmlspecialchars($estudiante["fecha_nacimiento_representante"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="edad_representante"><strong>Edad</strong></label>
                                                    <input class="form-control" type="number" id="edad_representante" name="edad_representante" value="<?php echo htmlspecialchars($estudiante["edad_representante"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="nacionalidad"><strong>Nacionalidad</strong></label>
                                                    <input class="form-control" type="text" id="nacionalidad" name="nacionalidad" value="<?php echo htmlspecialchars($estudiante["nacionalidad"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="profesion_oficio"><strong>Profesión u Oficio</strong></label>
                                                    <input class="form-control" type="text" id="profesion_oficio" name="profesion_oficio" value="<?php echo htmlspecialchars($estudiante["profesion_oficio"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="telefono_representante"><strong>Teléfono</strong></label>
                                                    <div class="input-group">
                                                        <select class="form-select" id="codigo_telefono_representante" name="codigo_telefono_representante" style="max-width: 80px;">
                                                            <option value="0416" <?php echo ($codigo_telefono_representante == '0416') ? 'selected' : ''; ?>>0416</option>
                                                            <option value="0426" <?php echo ($codigo_telefono_representante == '0426') ? 'selected' : ''; ?>>0426</option>
                                                            <option value="0414" <?php echo ($codigo_telefono_representante == '0414') ? 'selected' : ''; ?>>0414</option>
                                                            <option value="0424" <?php echo ($codigo_telefono_representante == '0424') ? 'selected' : ''; ?>>0424</option>
                                                            <option value="0412" <?php echo ($codigo_telefono_representante == '0412') ? 'selected' : ''; ?>>0412</option>
                                                        </select>
                                                        <input class="form-control" type="text" id="telefono_representante" name="telefono_representante" value="<?php echo htmlspecialchars($telefono_representante); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label required-field" for="parentesco"><strong>Parentesco</strong></label>
                                            <input class="form-control" type="text" id="parentesco" name="parentesco" value="<?php echo htmlspecialchars($estudiante["parentesco"]); ?>" required>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="parroquia"><strong>Parroquia</strong></label>
                                                    <input class="form-control" type="text" id="parroquia" name="parroquia" value="<?php echo htmlspecialchars($estudiante["parroquia"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="sector"><strong>Sector</strong></label>
                                                    <input class="form-control" type="text" id="sector" name="sector" value="<?php echo htmlspecialchars($estudiante["sector"]); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label required-field" for="direccion"><strong>Dirección</strong></label>
                                                    <input class="form-control" type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($estudiante["direccion"]); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label" for="correo_representante"><strong>Correo Electrónico</strong></label>
                                            <input class="form-control" type="email" id="correo_representante" name="correo_representante" value="<?php echo htmlspecialchars($estudiante["correo_representante"]); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
                                            <i class="fas fa-arrow-left me-1"></i> Anterior
                                        </button>
                                        <button type="button" class="btn btn-primary ms-auto" id="nextBtn">
                                            Siguiente <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                        <button type="submit" class="btn btn-success" id="submitBtn" name="editar_estudiante" style="display: none;">
                                            <i class="fas fa-save me-1"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            <?php else : ?>
                                <p><?php echo $error; ?></p>
                            <?php endif; ?>
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
            const sections = ['estudiante', 'representante'];
            let currentSection = 0;
            
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
                
                return isValid;
            }
            
            // Calcular edad automáticamente al cambiar la fecha de nacimiento
            document.getElementById('fecha_nacimiento').addEventListener('change', function() {
                calculateAge(this, 'edad');
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
            
            // Mostrar la primera sección al cargar la página
            showCurrentSection();
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>