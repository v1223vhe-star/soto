<?php
session_start();
include 'db.php';

// Headers de seguridad
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario de la sesión
$user_id = $_SESSION["user_id"];

// Inicializar variables para mensajes y errores
$mensaje = "";
$error = "";
$search_results = [];
$search_query = "";
$viewing_profile = null;

// Procesar búsqueda de usuarios
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["search"])) {
        $search_query = trim($_GET["search"]);
        
        if (!empty($search_query)) {
            $search_term = "%" . $search_query . "%";
            $stmt = $conn->prepare("SELECT id, username, role, email FROM usuarios WHERE username LIKE ? OR email LIKE ?");
            $stmt->bind_param("ss", $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $search_results[] = $row;
            }
            $stmt->close();
        }
    } elseif (isset($_GET["view"])) {
        // Ver perfil de otro usuario
        $viewed_user_id = intval($_GET["view"]);
        $stmt = $conn->prepare("SELECT id, username, role, email FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $viewed_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $viewing_profile = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

// Si estamos viendo el perfil de otro usuario, obtener sus datos
if ($viewing_profile) {
    $profile_id = $viewing_profile["id"];
    $username = $viewing_profile["username"];
    $role = $viewing_profile["role"];
    $email = $viewing_profile["email"];
    
    // Configuración de avatar para el perfil visto
    $avatar = "assets/img/avatars/avatar1.jpeg";
    if (file_exists("assets/img/avatars/$profile_id.jpg")) {
        $avatar = "assets/img/avatars/$profile_id.jpg";
    }
} else {
    // Obtener información del usuario actual
    $stmt = $conn->prepare("SELECT id, username, role, email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row["username"];
        $role = $row["role"];
        $email = $row["email"];
    } else {
        // Si no se encuentra el usuario, redirigir al login
        header("Location: login.php");
        exit();
    }
    $stmt->close();

    // Configuración de avatar para el usuario actual
    $avatar = "assets/img/avatars/avatar1.jpeg";
    if (file_exists("assets/img/avatars/$user_id.jpg")) {
        $avatar = "assets/img/avatars/$user_id.jpg";
    }
}

// Procesar el formulario de actualización de perfil (solo para usuario actual)
if (!$viewing_profile && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["actualizar_perfil"])) {
    $nuevo_email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, introduce un email válido.";
    } elseif (empty($nuevo_email)) {
        $error = "El email no puede estar vacío.";
    } else {
        // Usar consultas preparadas para evitar SQL injection
        $stmt = $conn->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_email, $user_id);
        
        if ($stmt->execute()) {
            $mensaje = "Perfil actualizado correctamente.";
            $_SESSION["email"] = $nuevo_email;
            $email = $nuevo_email;
        } else {
            $error = "Error al actualizar el perfil: " . $conn->error;
        }
        $stmt->close();
    }
}

// Procesar subida de avatar (solo para usuario actual)
if (!$viewing_profile && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subir_avatar"])) {
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "assets/img/avatars/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . $user_id . ".jpg";
        $imageFileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        
        // Verificar si es una imagen real
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if ($check === false) {
            $error = "El archivo no es una imagen.";
        } elseif ($_FILES["avatar"]["size"] > 500000) {
            $error = "La imagen es demasiado grande (máx. 500KB).";
        } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error = "Solo se permiten archivos JPG, JPEG y PNG.";
        } else {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $mensaje = "Avatar actualizado correctamente.";
                $avatar = $target_file;
            } else {
                $error = "Error al subir el avatar.";
            }
        }
    } else {
        $error = "Error al subir el archivo.";
    }
}

// Procesar el formulario de cambio de contraseña (solo para usuario actual)
if (!$viewing_profile && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cambiar_contrasena"])) {
    $contrasena_actual = $_POST["contrasena_actual"];
    $nueva_contrasena = $_POST["nueva_contrasena"];
    $confirmar_contrasena = $_POST["confirmar_contrasena"];

    // Validar los datos
    if (empty($contrasena_actual) || empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $error = "Por favor, rellena todos los campos para cambiar la contraseña.";
    } elseif (strlen($nueva_contrasena) < 8) {
        $error = "La nueva contraseña debe tener al menos 8 caracteres.";
    } elseif ($nueva_contrasena != $confirmar_contrasena) {
        $error = "La nueva contraseña y la confirmación no coinciden.";
    } else {
        // Consulta preparada para verificar la contraseña actual
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $contrasena_hash = $row["password"];

            // Verificar la contraseña actual
            if (password_verify($contrasena_actual, $contrasena_hash)) {
                // Hash la nueva contraseña
                $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

                // Actualizar la contraseña con consulta preparada
                $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $nueva_contrasena_hash, $user_id);

                if ($stmt->execute()) {
                    $mensaje = "Contraseña cambiada correctamente.";
                    // Enviar email de notificación
                    $asunto = "Cambio de contraseña";
                    $mensaje_email = "Hola $username,\n\nTu contraseña ha sido cambiada exitosamente.\n\nSi no realizaste este cambio, por favor contacta al administrador inmediatamente.";
                    mail($email, $asunto, $mensaje_email);
                } else {
                    $error = "Error al cambiar la contraseña: " . $conn->error;
                }
            } else {
                $error = "La contraseña actual es incorrecta.";
            }
        } else {
            $error = "Error al verificar la contraseña actual.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?php echo $viewing_profile ? "Perfil de " . htmlspecialchars($username) : "Mi Perfil"; ?> - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        .animate__animated {
            animation-duration: 1s;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .page-item:not(:first-child) {
            margin-left: 5px;
        }

        .page-link {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            text-decoration: none;
            color: #007bff;
            background-color: #fff;
        }

        .page-link:hover {
            background-color: #e9ecef;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .hidden {
            display: none;
        }
        
        .avatar-container {
            position: relative;
            display: inline-block;
        }
        
        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .avatar-upload:hover {
            background: rgba(0,0,0,0.7);
        }
        
        .password-strength {
            height: 5px;
            margin-top: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        /* Estilos para la búsqueda */
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-results {
            margin-top: 20px;
        }
        
        .user-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .user-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
        
        .user-info {
            flex-grow: 1;
        }
        
        .view-profile-btn {
            white-space: nowrap;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-header-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 30px;
        }
        
        .profile-header-info h2 {
            margin-bottom: 5px;
        }
        
        .profile-header-info .role-badge {
            font-size: 1rem;
        }
        
        .back-to-profile {
            margin-bottom: 20px;
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
                        
                        <!-- Barra de búsqueda -->
                        <form class="d-none d-sm-inline-block form-inline me-auto ms-md-3 my-2 my-md-0 mw-100 navbar-search" method="GET" action="profile.php">
                            <div class="input-group">
                                <input class="form-control bg-light border-0 small" type="search" name="search" placeholder="Buscar usuarios..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </form>
                        
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <!-- Agrega la información del usuario y el botón de logout -->
                        </ul>
                    </div>
                </nav>
                
                <!-- Mostrar resultados de búsqueda si existen -->
                <?php if (!empty($search_query)): ?>
                <div class="container-fluid">
                    <div class="card shadow mb-4 animate__animated animate__fadeIn">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Resultados de búsqueda para "<?php echo htmlspecialchars($search_query); ?>"</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($search_results)): ?>
                                <div class="search-results">
                                    <?php foreach ($search_results as $user): 
                                        $user_avatar = "assets/img/default-avatar.png";
                                        if (file_exists("assets/img/avatars/{$user['id']}.jpg")) {
                                            $user_avatar = "assets/img/avatars/{$user['id']}.jpg";
                                        }
                                    ?>
                                        <div class="user-card">
                                            <img src="<?php echo $user_avatar; ?>" class="user-avatar" alt="Avatar de <?php echo htmlspecialchars($user['username']); ?>">
                                            <div class="user-info">
                                                <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                                                <p class="text-muted mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
                                            </div>
                                            <a href="profile.php?view=<?php echo $user['id']; ?>" class="btn btn-primary view-profile-btn">
                                                Ver perfil
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No se encontraron usuarios que coincidan con tu búsqueda.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Contenido principal -->
                <div class="container-fluid">
                    <?php if ($viewing_profile): ?>
                        <!-- Vista de perfil de otro usuario -->
                        <div class="back-to-profile">
                            <a href="profile.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a mi perfil
                            </a>
                        </div>
                        
                        <div class="profile-header">
                            <img src="<?php echo $avatar; ?>" class="profile-header-avatar" alt="Avatar de <?php echo htmlspecialchars($username); ?>">
                            <div class="profile-header-info">
                                <h2><?php echo htmlspecialchars($username); ?></h2>
                                <span class="badge bg-primary role-badge"><?php echo htmlspecialchars($role); ?></span>
                                <p class="text-muted mt-2"><?php echo htmlspecialchars($email); ?></p>
                            </div>
                        </div>
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Información pública</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Nombre de usuario</strong></label>
                                            <input class="form-control" type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Rol</strong></label>
                                            <input class="form-control" type="text" value="<?php echo htmlspecialchars($role); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email</strong></label>
                                    <input class="form-control" type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Vista de perfil del usuario actual -->
                        <h3 class="text-dark mb-4">Mi Perfil</h3>
                        <?php if ($mensaje) : ?>
                            <div class="alert alert-success animate__animated animate__fadeIn" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($error) : ?>
                            <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card shadow mb-4 animate__animated animate__fadeInLeft">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 fw-bold">Foto de Perfil</p>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="avatar-container">
                                            <img src="<?php echo $avatar; ?>" class="rounded-circle mb-3" width="200" height="200" alt="Avatar" id="avatar-preview">
                                            <form method="post" enctype="multipart/form-data" id="avatar-form">
                                                <label for="avatar-input" class="avatar-upload" title="Cambiar foto">
                                                    <i class="fas fa-camera"></i>
                                                </label>
                                                <input type="file" id="avatar-input" name="avatar" accept="image/*" class="d-none">
                                                <button type="submit" name="subir_avatar" class="d-none" id="avatar-submit"></button>
                                            </form>
                                        </div>
                                        <h5 class="mt-3"><?php echo htmlspecialchars($username); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($role); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card shadow animate__animated animate__fadeInRight">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 fw-bold">Información del Perfil</p>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <div id="page1">
                                                <div class="mb-3">
                                                    <label class="form-label" for="username"><strong>Usuario</strong></label>
                                                    <input class="form-control" type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="role"><strong>Rol</strong></label>
                                                    <input class="form-control" type="text" id="role" name="role" value="<?php echo htmlspecialchars($role); ?>" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="email"><strong>Email</strong></label>
                                                    <input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                                </div>
                                                <button class="btn btn-primary next-page" type="button">Siguiente</button>
                                            </div>

                                            <div id="page2" class="hidden">
                                                <div class="mb-3">
                                                    <label class="form-label" for="contrasena_actual"><strong>Contraseña Actual</strong></label>
                                                    <input class="form-control" type="password" id="contrasena_actual" name="contrasena_actual" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="nueva_contrasena"><strong>Nueva Contraseña</strong></label>
                                                    <input class="form-control" type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                                                    <div class="password-strength">
                                                        <div class="password-strength-bar" id="password-strength-bar"></div>
                                                    </div>
                                                    <small class="text-muted">Mínimo 8 caracteres</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="confirmar_contrasena"><strong>Confirmar Contraseña</strong></label>
                                                    <input class="form-control" type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                                                </div>
                                                <button class="btn btn-primary prev-page" type="button">Anterior</button>
                                                <button class="btn btn-primary" type="submit" name="cambiar_contrasena">Cambiar Contraseña</button>
                                            </div>
                                            <div class="pagination">
                                                <div class="page-item active">
                                                    <a class="page-link" href="#" data-page="1">1</a>
                                                </div>
                                                <div class="page-item">
                                                    <a class="page-link" href="#" data-page="2">2</a>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary animate__animated animate__pulse mt-3" type="submit" name="actualizar_perfil">Actualizar Perfil</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Footer -->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Gestión Escolar 2025</span></div>
                </div>
            </footer>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const page1 = document.getElementById('page1');
            const page2 = document.getElementById('page2');
            const nextPageButtons = document.querySelectorAll('.next-page');
            const prevPageButtons = document.querySelectorAll('.prev-page');
            const pageLinks = document.querySelectorAll('.pagination .page-link');
            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarForm = document.getElementById('avatar-form');
            const nuevaContrasena = document.getElementById('nueva_contrasena');
            const passwordStrengthBar = document.getElementById('password-strength-bar');
            const searchInput = document.querySelector('input[name="search"]');

            // Navegación entre páginas
            function showPage(pageId) {
                if (pageId === 'page1') {
                    page1.classList.remove('hidden');
                    page2.classList.add('hidden');
                } else if (pageId === 'page2') {
                    page2.classList.remove('hidden');
                    page1.classList.add('hidden');
                }

                // Actualizar el estado activo de los enlaces de paginación
                pageLinks.forEach(link => {
                    link.parentElement.classList.remove('active');
                    if (link.dataset.page === pageId.slice(-1)) {
                        link.parentElement.classList.add('active');
                    }
                });
            }

            nextPageButtons.forEach(button => {
                button.addEventListener('click', function() {
                    showPage('page2');
                });
            });

            prevPageButtons.forEach(button => {
                button.addEventListener('click', function() {
                    showPage('page1');
                });
            });

            pageLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    showPage('page' + this.dataset.page);
                });
            });

            // Avatar upload
            if (avatarInput) {
                avatarInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                        document.getElementById('avatar-submit').click();
                    }
                });
            }

            // Password strength meter
            if (nuevaContrasena) {
                nuevaContrasena.addEventListener('input', function() {
                    const strength = calculatePasswordStrength(this.value);
                    updateStrengthMeter(strength);
                });
            }

            function calculatePasswordStrength(password) {
                let strength = 0;
                
                // Longitud mínima
                if (password.length >= 8) strength += 1;
                
                // Contiene números
                if (password.match(/\d/)) strength += 1;
                
                // Contiene letras minúsculas
                if (password.match(/[a-z]/)) strength += 1;
                
                // Contiene letras mayúsculas
                if (password.match(/[A-Z]/)) strength += 1;
                
                // Contiene caracteres especiales
                if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
                
                return strength;
            }

            function updateStrengthMeter(strength) {
                let width = 0;
                let color = '#dc3545'; // Rojo por defecto
                
                switch(strength) {
                    case 1:
                        width = 20;
                        color = '#dc3545';
                        break;
                    case 2:
                        width = 40;
                        color = '#fd7e14';
                        break;
                    case 3:
                        width = 60;
                        color = '#ffc107';
                        break;
                    case 4:
                        width = 80;
                        color = '#28a745';
                        break;
                    case 5:
                        width = 100;
                        color = '#28a745';
                        break;
                }
                
                passwordStrengthBar.style.width = width + '%';
                passwordStrengthBar.style.backgroundColor = color;
            }

            // Mejorar la experiencia de búsqueda
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        this.form.submit();
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>