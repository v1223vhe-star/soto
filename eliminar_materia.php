<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha pasado un ID de materia
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Eliminar las filas relacionadas en la tabla materias_anio_escolar
        $sql_relacionadas = "DELETE FROM materias_anio_escolar WHERE materia_id = $id";
        $conn->query($sql_relacionadas);

        // Eliminar la materia en la tabla materias
        $sql = "DELETE FROM materias WHERE id = $id";
        $conn->query($sql);

        // Confirmar la transacción
        $conn->commit();
        $mensaje = "Materia eliminada correctamente.";
    } catch (mysqli_sql_exception $exception) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        $error = "Error al eliminar la materia: " . $exception->getMessage();
    }
} else {
    $error = "ID de materia no especificado.";
}

// Redirigir de vuelta a la página de gestión de materias
header("Location: gestionar_materias.php");
exit();
?>