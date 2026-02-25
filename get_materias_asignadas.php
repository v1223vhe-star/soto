<?php
include 'db.php';

header('Content-Type: application/json');

if (isset($_GET['grado_id'])) {
    $grado_id = intval($_GET['grado_id']);
    $result = $conn->query("SELECT materia_id FROM materias_grados WHERE grado_id = $grado_id");
    
    $materias = [];
    while ($row = $result->fetch_assoc()) {
        $materias[] = $row['materia_id'];
    }
    
    echo json_encode($materias);
    exit();
}

echo json_encode([]);
?>