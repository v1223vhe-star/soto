<?php
// filepath: c:\xampp\htdocs\mi-aplicacion-web\obtener_secciones.php
include 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['anio_grado_id'])) {
    echo json_encode([]);
    exit();
}

$anio_grado_id = $_GET['anio_grado_id'];

// Consulta para obtener las secciones disponibles para el año_grado_id seleccionado
$sql = "SELECT seccion FROM secciones_anio_grado WHERE anio_grado_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $anio_grado_id);
$stmt->execute();
$result = $stmt->get_result();

$secciones = [];
while ($row = $result->fetch_assoc()) {
    $secciones[] = $row['seccion'];
}

echo json_encode($secciones);

$stmt->close();
$conn->close();
?>