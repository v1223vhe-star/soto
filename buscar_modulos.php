<?php
// filepath: c:\xampp\htdocs\mi-aplicacion-web\buscar_modulos.php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener el término de búsqueda
$termino_busqueda = $_GET["termino_busqueda"] ?? '';

// Escapar el término de búsqueda para prevenir inyección SQL
$termino_busqueda = $conn->real_escape_string($termino_busqueda);

// Lista de módulos disponibles
$modulos = array(
    "inicio" => "index.php",
    "estudiantes" => "gestionar_estudiantes.php",
    "representantes" => "gestionar_representantes.php",
    "materias" => "gestionar_materias.php",
    "usuarios" => "gestionar_usuarios.php",
    // Agrega más módulos aquí
);

// Buscar módulos que coincidan con el término de búsqueda
$resultados = array();
foreach ($modulos as $nombre => $url) {
    if (stripos($nombre, $termino_busqueda) !== false) {
        $resultados[] = array("nombre" => $nombre, "url" => $url);
    }
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($resultados);

$conn->close();
?>