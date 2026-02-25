<?php
include 'db.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['grado_id'])) {
        throw new Exception('Parámetro grado_id requerido');
    }

    $grado_id = intval($_GET['grado_id']);
    
    // Obtener año escolar activo
    $sql_anio = "SELECT id FROM anios_escolares WHERE activo = 1 LIMIT 1";
    $result_anio = $conn->query($sql_anio);
    
    if ($result_anio->num_rows === 0) {
        throw new Exception('No hay año escolar activo');
    }
    
    $anio_escolar = $result_anio->fetch_assoc();
    $anio_escolar_id = $anio_escolar['id'];
    
    // Obtener anio_grado_id
    $sql_anio_grado = "SELECT id FROM anios_grados 
                      WHERE grado_id = ? AND anio_escolar_id = ?";
    $stmt = $conn->prepare($sql_anio_grado);
    $stmt->bind_param("ii", $grado_id, $anio_escolar_id);
    $stmt->execute();
    $result_ag = $stmt->get_result();
    
    if ($result_ag->num_rows === 0) {
        echo json_encode([]);
        exit;
    }
    
    $anio_grado = $result_ag->fetch_assoc();
    $anio_grado_id = $anio_grado['id'];
    
    // Obtener secciones disponibles
    $sql_secciones = "SELECT DISTINCT seccion 
                     FROM secciones_anio_grado 
                     WHERE anio_grado_id = ? 
                     AND seccion != '0'  -- Excluir sección 0
                     ORDER BY seccion";
    $stmt = $conn->prepare($sql_secciones);
    $stmt->bind_param("i", $anio_grado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $secciones = [];
    while ($row = $result->fetch_assoc()) {
        $secciones[] = $row['seccion'];
    }
    
    echo json_encode($secciones);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>