<!-- filepath: /c:/xampp/htdocs/mi-aplicacion-web/eliminar_estudiante.php -->
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

// Verificar si se ha proporcionado un ID de estudiante
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id_estudiante = $_GET["id"];

    // Primero, eliminar las referencias al estudiante en la tabla 'datos_academicos'
    $sql_eliminar_datos_academicos = "DELETE FROM datos_academicos WHERE estudiante_id = $id_estudiante";
    if ($conn->query($sql_eliminar_datos_academicos) === TRUE) {
        // Luego, eliminar el estudiante de la tabla 'estudiantes'
        $sql = "DELETE FROM estudiantes WHERE id = $id_estudiante";

        // Ejecutar la consulta
        if ($conn->query($sql) === TRUE) {
            // Registrar notificación
            $sql_notificacion = "INSERT INTO notificaciones (tipo, mensaje) VALUES ('eliminacion_estudiante', 'Estudiante con ID: $id_estudiante eliminado')";
            $conn->query($sql_notificacion);

            $mensaje = "Estudiante eliminado correctamente.";
        } else {
            $error = "Error al eliminar el estudiante: " . $conn->error;
        }
    } else {
        $error = "Error al eliminar los datos académicos del estudiante: " . $conn->error;
    }
} else {
    $error = "ID de estudiante no válido.";
}

// Redirigir a la página de gestión de estudiantes
header("Location: gestionar_representantes.php");
exit();

$conn->close();
?>