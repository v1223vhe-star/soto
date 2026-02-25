<?php
date_default_timezone_set('America/Caracas');
require_once 'db.php';

// Obtener parámetros
$anio_escolar_id = $_GET["anio_escolar_id"] ?? null;
$grado_id = $_GET["grado_id"] ?? null;
$seccion = $_GET["seccion"] ?? null;
$mes = $_GET['mes'] ?? date('n');
$anio = $_GET['anio'] ?? date('Y');
$materia_id = $_GET['materia_id'] ?? null;

// Validar parámetros
if (!$anio_escolar_id || !$grado_id || !$seccion) {
    die("Parámetros incompletos");
}

// Configurar cabeceras para Excel con codificación UTF-8
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment;filename="Asistencias_'.$grado_id.'_'.$seccion.'_'.$mes.'_'.$anio.'.xls"');
header('Cache-Control: max-age=0');
// Forzar UTF-8 en el output
echo "\xEF\xBB\xBF"; // BOM para UTF-8

// Obtener datos del año escolar
$sql_anio = "SELECT nombre FROM anios_escolares WHERE id = $anio_escolar_id";
$result_anio = $conn->query($sql_anio);
$anio_escolar = $result_anio->fetch_assoc();

// Obtener datos del grado
$sql_grado = "SELECT nombre FROM grados WHERE id = $grado_id";
$result_grado = $conn->query($sql_grado);
$grado = $result_grado->fetch_assoc();

// Obtener datos de la materia si está especificada
$materia_nombre = '';
if ($materia_id) {
    $sql_materia = "SELECT nombre FROM materias WHERE id = $materia_id";
    $result_materia = $conn->query($sql_materia);
    $materia = $result_materia->fetch_assoc();
    $materia_nombre = $materia['nombre'];
}

// Configuración del mes
$dias_en_el_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$nombre_mes = date('F', mktime(0, 0, 0, $mes, 1, $anio));
$meses_espanol = [
    'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
    'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
    'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
    'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
];
$nombre_mes = $meses_espanol[$nombre_mes];

// Consulta para estudiantes
$sql_estudiantes = "SELECT 
                    e.id, e.cedula, e.nombres, e.apellidos
                    FROM estudiantes e
                    INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                    INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                    WHERE ag.anio_escolar_id = $anio_escolar_id 
                    AND ag.grado_id = $grado_id
                    AND da.seccion = '$seccion'
                    ORDER BY e.apellidos, e.nombres";
$result_estudiantes = $conn->query($sql_estudiantes);

// Crear contenido Excel con formato HTML y UTF-8
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Asistencias</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .subtitulo {
            font-size: 14px;
            text-align: center;
        }
        .info {
            font-size: 12px;
            text-align: center;
            margin-bottom: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ddd;
            padding: 5px;
        }
        td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        .presente {
            background-color: #d1fae5;
        }
        .ausente {
            background-color: #fee2e2;
        }
        .parcial {
            background-color: #fef3c7;
        }
        .fin-semana {
            background-color: #e0e7ff;
        }
    </style>
</head>
<body>';

// Título y encabezados
echo '<table>
    <tr>
        <td colspan="'.($dias_en_el_mes + 5).'" class="titulo">COLEGIO CONCEPCIÓN ACEVEDO DE TAYLHARDAT</td>
    </tr>
    <tr>
        <td colspan="'.($dias_en_el_mes + 5).'" class="subtitulo">REPORTE DE ASISTENCIAS</td>
    </tr>
    <tr>
        <td colspan="'.($dias_en_el_mes + 5).'" class="subtitulo">'.htmlspecialchars($anio_escolar['nombre']).' - '.htmlspecialchars($grado['nombre']).' Sección '.htmlspecialchars($seccion).'</td>
    </tr>
    <tr>
        <td colspan="'.($dias_en_el_mes + 5).'" class="info">Mes: '.htmlspecialchars($nombre_mes).' '.htmlspecialchars($anio).($materia_nombre ? ' - Materia: '.htmlspecialchars($materia_nombre) : '').'</td>
    </tr>
    <tr>
        <td colspan="'.($dias_en_el_mes + 5).'" class="info">Generado: '.date('d/m/Y H:i:s').'</td>
    </tr>
    <tr>
        <th width="15%">Estudiante</th>
        <th width="10%">Cédula</th>';

// Encabezados de días
for ($i = 1; $i <= $dias_en_el_mes; $i++) {
    echo '<th width="3%">'.$i.'</th>';
}

echo '<th width="5%">Asist.</th>
        <th width="5%">Falt.</th>
        <th width="5%">% Asist.</th>
    </tr>';

// Datos de estudiantes
if ($result_estudiantes && $result_estudiantes->num_rows > 0) {
    while ($estudiante = $result_estudiantes->fetch_assoc()) {
        $total_asistencias = 0;
        $total_dias = 0;
        
        echo '<tr>
            <td>'.htmlspecialchars($estudiante['apellidos'].', '.$estudiante['nombres']).'</td>
            <td>'.htmlspecialchars($estudiante['cedula']).'</td>';
        
        for ($i = 1; $i <= $dias_en_el_mes; $i++) {
            $fecha = "$anio-$mes-".sprintf("%02d", $i);
            $dia_semana = date('D', strtotime($fecha));
            $es_fin_semana = ($dia_semana == 'Sat' || $dia_semana == 'Sun');
            
            if ($es_fin_semana) {
                echo '<td class="fin-semana">-</td>';
                continue;
            }
            
            // Consultar asistencia
            $sql_asistencia = "SELECT a.asistio
                              FROM asistencia a
                              JOIN materias_anio_escolar mae ON a.materia_anio_escolar_id = mae.id
                              WHERE a.estudiante_id = ".$estudiante['id']." 
                              AND a.fecha = '$fecha'";
            
            if ($materia_id) {
                $sql_asistencia .= " AND mae.materia_id = $materia_id";
            }
            
            $result_asistencia = $conn->query($sql_asistencia);
            $asistio = false;
            $parcial = false;
            
            if ($result_asistencia && $result_asistencia->num_rows > 0) {
                if (!$materia_id) {
                    // Verificar si asistió a todas las materias del día
                    $sql_total_materias = "SELECT COUNT(*) as total 
                                          FROM horarios h
                                          INNER JOIN secciones_anio_grado sag ON h.seccion_anio_grado_id = sag.id
                                          INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                                          WHERE ag.grado_id = $grado_id 
                                          AND sag.seccion = '$seccion'
                                          AND h.dia_semana = '$dia_semana'";
                    $result_total = $conn->query($sql_total_materias);
                    $total_materias = $result_total->fetch_assoc()['total'];
                    
                    $asistio_todas = ($result_asistencia->num_rows == $total_materias);
                    $asistio_algunas = ($result_asistencia->num_rows > 0);
                    
                    if ($asistio_todas) {
                        $asistio = true;
                    } elseif ($asistio_algunas) {
                        $parcial = true;
                    }
                } else {
                    $row = $result_asistencia->fetch_assoc();
                    $asistio = $row['asistio'];
                }
            }
            
            $clase = '';
            $simbolo = '';
            
            if ($asistio) {
                $clase = 'presente';
                $simbolo = '✓';
                $total_asistencias++;
                $total_dias++;
            } elseif ($parcial) {
                $clase = 'parcial';
                $simbolo = '½';
                $total_asistencias += 0.5;
                $total_dias++;
            } else {
                $clase = 'ausente';
                $simbolo = '✗';
                $total_dias++;
            }
            
            echo '<td class="'.$clase.'">'.$simbolo.'</td>';
        }
        
        $porcentaje = $total_dias > 0 ? round(($total_asistencias / $total_dias) * 100) : 0;
        
        echo '<td>'.$total_asistencias.'</td>
             <td>'.($total_dias - $total_asistencias).'</td>
             <td>'.$porcentaje.'%</td>
         </tr>';
    }
} else {
    echo '<tr><td colspan="'.($dias_en_el_mes + 5).'">No hay estudiantes en esta sección</td></tr>';
}

echo '</table>
</body>
</html>';

$conn->close();
?>