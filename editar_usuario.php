<!-- filepath: c:\xampp\htdocs\mi-aplicacion-web\editar_usuario.php -->
<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Verificar si se proporciona un ID de usuario
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: gestionar_usuarios.php");
    exit();
}

$user_id = $_GET["id"];

// Consulta para obtener los datos del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id = " . $user_id;
$result_usuario = $conn->query($sql_usuario);

if ($result_usuario->num_rows == 0) {
    header("Location: gestionar_usuarios.php");
    exit();
}

$usuario = $result_usuario->fetch_assoc();

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $username = $_POST["username"];
    $email = $_POST["email"];
    $role = $_POST["role"];

    // Validar los datos (puedes agregar más validaciones)
    if (empty($username) || empty($email) || empty($role)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Actualizar los datos del usuario en la base de datos
        $sql_update = "UPDATE usuarios SET username = '$username', email = '$email', role = '$role' WHERE id = " . $user_id;

        if ($conn->query($sql_update) === TRUE) {
            $mensaje = "Usuario actualizado correctamente.";
        } else {
            $error = "Error al actualizar el usuario: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Editar Usuario - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
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
                                        <img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- Contenido principal -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Editar Usuario</h3>
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Formulario de Edición de Usuario</p>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $user_id; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input class="form-control" type="text" name="username" value="<?php echo $usuario["username"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" value="<?php echo $usuario["email"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="role">
                                        <option value="admin" <?php if ($usuario["role"] == "admin") echo "selected"; ?>>Admin</option>
                                        <option value="user" <?php if ($usuario["role"] == "user") echo "selected"; ?>>User</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary" type="submit">Guardar Cambios</button>
                                <a href="gestionar_usuarios.php" class="btn btn-secondary">Cancelar</a>
                            </form>
                        </div>
                    </div>
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
</body>

</html>

<?php
$conn->close();
?>