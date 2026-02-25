<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, username, password, role FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];
            
            //  Nueva variable de sesión para mostrar el mensaje
            $_SESSION["show_welcome_alert"] = true;
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El email proporcionado no está registrado.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Iniciar Sesión - Colegio Jesús Soto</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
     <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root {
            --school-primary: #127749;
            --school-secondary: #1fcc7d;
            --school-accent: #771240;
        }
        
        body {
            background-color: #052215;
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .login-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            height: 600px;
        }
        
        .login-left {
            background: url('assets/img/dogs/image1.jpeg') center/cover no-repeat;
            position: relative;
            padding: 0;
            display: flex;
            align-items: flex-end;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(26, 82, 118, 0.9) 0%, rgba(26, 82, 118, 0.5) 50%, rgba(26, 82, 118, 0.1) 100%);
        }
        
        .school-info {
            position: relative;
            z-index: 2;
            padding: 30px;
            color: white;
            width: 100%;
        }
        
        .school-name {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .school-year {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .login-right {
            padding: 50px;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .welcome-title {
            color: var(--school-primary);
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 2rem;
            position: relative;
        }
        
        .welcome-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--school-accent);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .btn-school {
            background-color: var(--school-primary);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-school:hover {
            background-color: var(--school-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .form-control {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--school-secondary);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .login-links a {
            color: var(--school-primary);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .login-links a:hover {
            color: var(--school-secondary);
            text-decoration: none;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider-text {
            padding: 0 10px;
            color: #777;
            font-size: 0.9rem;
        }
        
        .form-floating label {
            color: #777;
        }
        
        @media (max-width: 992px) {
            .login-card {
                height: auto;
            }
            .login-left {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="login-card row g-0">
            <div class="col-lg-6 login-left">
                <div class="school-info">
                    <h1 class="school-name">UNIDAD EDUCATIVA COLEGIO JESÚS SOTO</h1>
                    <p class="school-year">Educando con excelencia desde 2005</p>
                </div>
            </div>
            <div class="col-lg-6 login-right">
                <div class="text-center mb-5">
                    <h2 class="welcome-title">BIENVENIDO</h2>
                    <p class="text-muted">Ingresa tus credenciales para acceder al sistema</p>
                </div>
                
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php } ?>
                
                <form method="post">
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" required>
                        <label for="email">Correo electrónico</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password">Contraseña</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">Recuérdame</label>
                        </div>
                        <a href="recuperar_password.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="btn btn-school btn-block w-100 mb-4">INGRESAR</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">¿No tienes una cuenta? <a href="register.php" class="text-decoration-none">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>