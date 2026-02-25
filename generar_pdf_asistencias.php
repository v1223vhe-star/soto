<?php
date_default_timezone_set('America/Caracas');
require('fpdf/fpdf.php');
require_once 'db.php';

session_start();
if (!isset($_SESSION["user_id"])) {
    die("Acceso no autorizado");
}

// Obtener parámetros
$anio_escolar_id = $_GET['anio_escolar_id'] ?? null;
$grado_id = $_GET['grado_id'] ?? null;
$seccion = $_GET['seccion'] ?? null;
$mes = $_GET['mes'] ?? date('n');
$anio = $_GET['anio'] ?? date('Y');
$materia_id = $_GET['materia_id'] ?? null;

// Validar parámetros
if (!$anio_escolar_id || !$grado_id || !$seccion || !$materia_id) {
    die("Parámetros incompletos");
}

// Configurar PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 15);

// Configuración de fuentes y colores
$pdf->SetFont('Arial', '', 8);
$ancho_util = 277; // Ancho útil considerando márgenes

// Colores institucionales
$color_primario = array(0, 51, 102); // Azul oscuro
$color_secundario = array(255, 204, 0); // Amarillo dorado
$color_fondo_dias = array(240, 240, 240); // Gris claro

// Nombres en español
$meses_espanol = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Obtener datos básicos
$anio_escolar = $conn->query("SELECT nombre FROM anios_escolares WHERE id = $anio_escolar_id")->fetch_assoc();
$grado = $conn->query("SELECT nombre FROM grados WHERE id = $grado_id")->fetch_assoc();
$materia = $conn->query("SELECT nombre FROM materias WHERE id = $materia_id")->fetch_assoc();

// Encabezado con logo
$pdf->Image('assets/img/logo.jpeg', 10, 8, 25); // Ajusta la ruta y posición según necesites
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor($color_primario[0], $color_primario[1], $color_primario[2]);
$pdf->Cell(0, 8, utf8_decode('COLEGIO CONCEPCIÓN ACEVEDO DE TAYLHARDAT'), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0); // Negro
$pdf->Cell(0, 6, utf8_decode('REPORTE DE ASISTENCIAS'), 0, 1, 'C');

// Información detallada
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, utf8_decode('Año Escolar: ' . $anio_escolar['nombre']), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Grado: ' . $grado['nombre'] . ' - Sección: ' . $seccion), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Materia: ' . $materia['nombre']), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Período: ' . $meses_espanol[$mes] . ' ' . $anio), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Generado: ' . date('d/m/Y H:i:s')), 0, 1, 'C');
$pdf->Ln(5);

// Calcular dimensiones
$dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$ancho_columnas_fijas = 50 + 20; // Nombre + Cédula
$ancho_disponible_dias = $ancho_util - $ancho_columnas_fijas - 24; // Restamos espacio para totales
$ancho_celda_dia = $ancho_disponible_dias / $dias_en_mes;

// Ajustar si los días son muchos
if ($ancho_celda_dia < 5) {
    $ancho_columnas_fijas = 40 + 15; // Reducimos ancho de columnas fijas
    $ancho_disponible_dias = $ancho_util - $ancho_columnas_fijas - 24;
    $ancho_celda_dia = $ancho_disponible_dias / $dias_en_mes;
    $pdf->SetFont('Arial', '', 6); // Fuente más pequeña si hay muchos días
}

// Cabecera de tabla
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor($color_primario[0], $color_primario[1], $color_primario[2]);
$pdf->SetTextColor(255, 255, 255); // Blanco para el texto del encabezado

// Encabezados de columnas
$pdf->Cell(50, 8, utf8_decode('ESTUDIANTE'), 1, 0, 'C', true);
$pdf->Cell(20, 8, utf8_decode('CÉDULA'), 1, 0, 'C', true);

// Días del mes
$pdf->SetFillColor($color_primario[0], $color_primario[1], $color_primario[2]); // Usar color azul
for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
    $pdf->Cell($ancho_celda_dia, 8, $dia, 1, 0, 'C', true);
}
// Totales
$pdf->SetFillColor($color_primario[0], $color_primario[1], $color_primario[2]);
$pdf->Cell(12, 8, utf8_decode('ASIST.'), 1, 0, 'C', true);
$pdf->Cell(12, 8, utf8_decode('FALTAS'), 1, 1, 'C', true);

// Datos de estudiantes
$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(0, 0, 0); // Texto negro
$estudiantes = $conn->query("
    SELECT e.id, e.cedula, e.nombres, e.apellidos
    FROM estudiantes e
    INNER JOIN datos_academicos da ON e.id = da.estudiante_id
    INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
    WHERE ag.anio_escolar_id = $anio_escolar_id 
    AND ag.grado_id = $grado_id
    AND da.seccion = '$seccion'
    ORDER BY e.apellidos, e.nombres
    LIMIT 22");

while ($estudiante = $estudiantes->fetch_assoc()) {
    $nombre = utf8_decode($estudiante['apellidos'] . ', ' . $estudiante['nombres']);
    $pdf->Cell(50, 6, $nombre, 1, 0);
    $pdf->Cell(20, 6, $estudiante['cedula'], 1, 0, 'C');
    
    $total_asistencias = 0;
    $total_dias = 0;
    
    for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
        $fecha = "$anio-$mes-" . sprintf("%02d", $dia);
        $numero_dia_semana = date('w', strtotime($fecha));
        $es_fin_semana = ($numero_dia_semana == 0 || $numero_dia_semana == 6);
        
        if ($es_fin_semana) {
            $pdf->SetFillColor($color_fondo_dias[0], $color_fondo_dias[1], $color_fondo_dias[2]);
            $pdf->Cell($ancho_celda_dia, 6, '-', 1, 0, 'C', true);
            $pdf->SetFillColor(255);
            continue;
        }
        
        $sql = "SELECT a.asistio 
               FROM asistencia a
               JOIN materias_anio_escolar mae ON a.materia_anio_escolar_id = mae.id
               WHERE a.estudiante_id = {$estudiante['id']} 
               AND a.fecha = '$fecha'
               AND mae.materia_id = $materia_id
               LIMIT 1";
        
        $result = $conn->query($sql);
        $asistio = $result->num_rows > 0 ? $result->fetch_assoc()['asistio'] : false;
        
        // Colores para asistencias/faltas
        if ($asistio) {
            $pdf->SetFillColor(220, 255, 220); // Verde claro para asistencia
            $simbolo = 'P'; // Presente
        } else {
            $pdf->SetFillColor(255, 220, 220); // Rojo claro para falta
            $simbolo = 'F'; // Falta
        }
        
        $pdf->Cell($ancho_celda_dia, 6, $simbolo, 1, 0, 'C', true);
        $pdf->SetFillColor(255);
        
        $total_dias++;
        if ($asistio) $total_asistencias++;
    }
    
    // Totales
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(12, 6, $total_asistencias, 1, 0, 'C');
    $pdf->Cell(12, 6, ($total_dias - $total_asistencias), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
}

// Pie de página
$pdf->SetY(-15);
$pdf->SetFont('Arial', 'I', 6);
$pdf->SetTextColor($color_primario[0], $color_primario[1], $color_primario[2]);
$pdf->Cell(0, 5, utf8_decode('COLEGIO CONCEPCIÓN ACEVEDO DE TAYLHARDAT - Generado el ' . date('d/m/Y H:i:s')), 0, 0, 'C');

// Salida
$pdf->Output('I', 'Asistencias_' . $seccion . '_' . $mes . '_' . $anio . '.pdf');
?>