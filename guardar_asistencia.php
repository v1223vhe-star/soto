<?php
session_start();
include 'db.php';

// Verificar sesión
if (!isset($_SESSION["user_id"])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

// Obtener datos del POST
$estudiante_id = $_POST['estudiante_id'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$dia_semana = $_POST['dia_semana'] ?? null;
$materia_id = $_POST['materia_id'] ?? null;
$estado = $_POST['estado'] ?? null;

// Validar datos
if (!$estudiante_id || !$fecha || !$dia_semana || $materia_id === null || $estado === null) {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

try {
    // Convertir estado a booleano (para compatibilidad con el sistema anterior)
    $asistio = ($estado == '1'); // 1 = presente, otros = no presente
    
    // Obtener el año escolar del estudiante
    $sql_anio_escolar = "SELECT ag.anio_escolar_id 
                         FROM datos_academicos da
                         JOIN anios_grados ag ON da.anio_grado_id = ag.id
                         WHERE da.estudiante_id = ?";
    $stmt = $conn->prepare($sql_anio_escolar);
    $stmt->bind_param("i", $estudiante_id);
    $stmt->execute();
    $result_anio = $stmt->get_result();
    
    if ($result_anio->num_rows == 0) {
        throw new Exception("No se encontró el año escolar para el estudiante");
    }
    
    $anio_escolar_id = $result_anio->fetch_assoc()['anio_escolar_id'];
    
    // Si materia_id es 0, significa que estamos en modo "todas las materias"
    if ($materia_id == 0) {
        // Obtener todas las materias del horario para ese día
        $sql_horario = "SELECT h.materia_id 
                        FROM horarios h
                        INNER JOIN secciones_anio_grado sag ON h.seccion_anio_grado_id = sag.id
                        INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                        INNER JOIN datos_academicos da ON ag.id = da.anio_grado_id
                        WHERE da.estudiante_id = ?
                        AND h.dia_semana = ?
                        AND ag.anio_escolar_id = ?";
        $stmt = $conn->prepare($sql_horario);
        $stmt->bind_param("isi", $estudiante_id, $dia_semana, $anio_escolar_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            guardarAsistenciaMateria($estudiante_id, $fecha, $row['materia_id'], $asistio, $conn, $anio_escolar_id);
        }
    } else {
        // Modo materia específica
        guardarAsistenciaMateria($estudiante_id, $fecha, $materia_id, $asistio, $conn, $anio_escolar_id);
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}

function guardarAsistenciaMateria($estudiante_id, $fecha, $materia_id, $asistio, $conn, $anio_escolar_id) {
    // Verificar si ya existe un registro para esta combinación
    $sql_check = "SELECT a.id, mae.id as materia_anio_id
                  FROM asistencia a
                  JOIN materias_anio_escolar mae ON a.materia_anio_escolar_id = mae.id
                  WHERE a.estudiante_id = ?
                  AND a.fecha = ?
                  AND mae.materia_id = ?
                  AND mae.anio_escolar_id = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("isii", $estudiante_id, $fecha, $materia_id, $anio_escolar_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Actualizar registro existente
        $row = $result->fetch_assoc();
        $sql_update = "UPDATE asistencia SET asistio = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ii", $asistio, $row['id']);
        $stmt->execute();
    } else {
        // Crear nuevo registro
        // Primero necesitamos el ID de materias_anio_escolar
        $sql_mae = "SELECT id FROM materias_anio_escolar 
                    WHERE materia_id = ? 
                    AND anio_escolar_id = ?";
        $stmt = $conn->prepare($sql_mae);
        $stmt->bind_param("ii", $materia_id, $anio_escolar_id);
        $stmt->execute();
        $result_mae = $stmt->get_result();
        
        if ($result_mae->num_rows > 0) {
            $row_mae = $result_mae->fetch_assoc();
            $sql_insert = "INSERT INTO asistencia 
                          (estudiante_id, materia_anio_escolar_id, fecha, dia_semana, asistio) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $dia_semana = date('D', strtotime($fecha));
            $stmt->bind_param("iissi", $estudiante_id, $row_mae['id'], $fecha, $dia_semana, $asistio);
            $stmt->execute();
        } else {
            // Si no existe la relación materia-año, la creamos
            $sql_insert_mae = "INSERT INTO materias_anio_escolar (anio_escolar_id, materia_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert_mae);
            $stmt->bind_param("ii", $anio_escolar_id, $materia_id);
            $stmt->execute();
            $mae_id = $conn->insert_id;
            
            // Ahora insertamos la asistencia
            $sql_insert = "INSERT INTO asistencia 
                          (estudiante_id, materia_anio_escolar_id, fecha, dia_semana, asistio) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $dia_semana = date('D', strtotime($fecha));
            $stmt->bind_param("iissi", $estudiante_id, $mae_id, $fecha, $dia_semana, $asistio);
            $stmt->execute();
        }
    }
}

$conn->close();
?>