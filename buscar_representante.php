<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

// Obtener el término de búsqueda
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

// Buscar representantes que coincidan con el término
$sql = "SELECT id, cedula, nombres, apellidos, telefono, parentesco 
        FROM representantes 
        WHERE cedula LIKE ? OR nombres LIKE ? OR apellidos LIKE ?
        LIMIT 10";
$stmt = $conn->prepare($sql);
$searchTerm = "%$q%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$representantes = [];
while ($row = $result->fetch_assoc()) {
    $representantes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($representantes);

$stmt->close();
$conn->close();
?>