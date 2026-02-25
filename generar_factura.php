<?php
require('fpdf/fpdf.php');
include 'db.php';

if (isset($_GET['id'])) {
    $pago_id = intval($_GET['id']);

    // Consulta para obtener datos detallados del pago y del estudiante
    $sql = "SELECT p.*, e.cedula, e.nombres, e.apellidos, a.nombre as anio_nombre 
            FROM pagos p 
            JOIN estudiantes e ON p.estudiante_id = e.id 
            JOIN anios_escolares a ON p.anio_escolar_id = a.id
            WHERE p.id = $pago_id";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $p = $result->fetch_assoc();

        // Configuración inicial del PDF
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        // --- ENCABEZADO ---
        // Aquí podrías añadir un logo: $pdf->Image('logo.png', 10, 10, 30);
        $pdf->Image('assets/img/logo.jpeg', 10, 10, 30);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode('UNIDAD EDUCATIVA COLEGIO "JESÚS SOTO"'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Fundado en el año 2005'), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Calle Las Flores con Victoria - Edif. Jesús Soto - Piso P/B - Local S/N'), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode(' Sector Vuela al Cacho - Parroquia la Sabanita - Ciudad Bolívar'), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Edo. Bolívar - Teléfono: 0212-0000000 | Rif: J-12345678-9'), 0, 1, 'C');
        
        $pdf->Ln(10);

        // Título y Número de Factura
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 10, utf8_decode('COMPROBANTE DE PAGO'), 0, 0, 'L');
        $pdf->SetTextColor(255, 0, 0);

        $pdf->Cell(60, 10, utf8_decode('N°: 000') . $p['id'], 0, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5);

        // --- DATOS DEL CLIENTE / ESTUDIANTE ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, utf8_decode('DATOS DEL ESTUDIANTE'), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 8, utf8_decode('Nombre:'), 'LR', 0);
        $pdf->Cell(160, 8, utf8_decode($p['nombres'] . ' ' . $p['apellidos']), 'R', 1);
        $pdf->Cell(30, 8, utf8_decode('RIF/C.I.:'), 'LRB', 0);
        $pdf->Cell(160, 8, utf8_decode($p['cedula']), 'RB', 1);
        $pdf->Ln(10);

        // --- TABLA DE DETALLES ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 8, utf8_decode('Descripción del Concepto'), 1, 0, 'C', true);
        $pdf->Cell(40, 8, utf8_decode('Año Escolar'), 1, 0, 'C', true);
        $pdf->Cell(30, 8, utf8_decode('Mes'), 1, 0, 'C', true);
        $pdf->Cell(30, 8, utf8_decode('Monto'), 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(90, 8, utf8_decode('Pago de Matrícula / Mensualidad'), 1, 0, 'L');
        $pdf->Cell(40, 8, utf8_decode($p['anio_nombre']), 1, 0, 'C');
        $pdf->Cell(30, 8, utf8_decode($p['mes']), 1, 0, 'C');
        $pdf->Cell(30, 8, number_format($p['monto'], 2) . '$', 1, 1, 'R');

        // Espacio de relleno para la tabla
        for($i=0; $i<3; $i++) {
            $pdf->Cell(90, 8, '', 1); $pdf->Cell(40, 8, '', 1); $pdf->Cell(30, 8, '', 1); $pdf->Cell(30, 8, '', 1, 1);
        }

        // --- TOTALES ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(160, 8, 'SUBTOTAL ', 0, 0, 'R');
        $pdf->Cell(30, 8, number_format($p['monto'], 2) . '$', 1, 1, 'R');
        $pdf->Cell(160, 8, 'I.V.A (16%) ', 0, 0, 'R');
        $pdf->Cell(30, 8, number_format($p['iva'], 2) . '$', 1, 1, 'R');
        $pdf->SetFillColor(0, 0, 0); $pdf->SetTextColor(255);
        $pdf->Cell(160, 8, 'TOTAL PAGADO ', 0, 0, 'R');
        $pdf->Cell(30, 8, number_format($p['total'], 2) . '$', 1, 1, 'R', true);

        // --- PIE DE PÁGINA ---
        $pdf->SetTextColor(0);
        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, utf8_decode('Fecha de emisión: ' . date('d/m/Y H:i:s', strtotime($p['fecha_pago']))), 0, 1, 'L');
        $pdf->Ln(15);
        
        // Área de Firma
        $pdf->Line(75, $pdf->GetY(), 135, $pdf->GetY());
        $pdf->Cell(0, 5, utf8_decode('Sello y Firma Autorizada'), 0, 1, 'C');

        // Salida del PDF
        $pdf->Output('I', 'Factura_' . $p['id'] . '.pdf');
    } else {
        echo "Error: No se encontró el registro del pago.";
    }
}