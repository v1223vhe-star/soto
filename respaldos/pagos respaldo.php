<?php
session_start();
include 'db.php'; // Asegúrate de que este archivo exista y contenga la conexión a $conn

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Al inicio del archivo, después de verificar la sesión
$user_id = $_SESSION["user_id"];
$sql_user = "SELECT role FROM usuarios WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user_role = $result_user->fetch_assoc()['role'];

// Inicializar variables para mensajes
$mensaje = "";
$error = "";

// Obtener parámetros de búsqueda/filtro
$anio_escolar_id = $_GET["anio_escolar_id"] ?? null;
$grado_id = $_GET["grado_id"] ?? null;
$seccion = $_GET["seccion"] ?? null;
$estudiante_id = $_GET["estudiante_id"] ?? null; // Nuevo: ID del estudiante seleccionado

// --- Consultas para Filtros Jerárquicos ---

// 1. Consulta para obtener la lista de años escolares
$sql_anios_escolares = "SELECT * FROM anios_escolares";
$result_anios_escolares = $conn->query($sql_anios_escolares);

// 2. Consulta para obtener la lista de grados para el año escolar seleccionado
if ($anio_escolar_id) {
    $sql_grados = "SELECT g.id, g.nombre, COUNT(da.id) as total_estudiantes
                   FROM grados g
                   LEFT JOIN anios_grados ag ON g.id = ag.grado_id
                   LEFT JOIN datos_academicos da ON ag.id = da.anio_grado_id
                   WHERE ag.anio_escolar_id = $anio_escolar_id
                   GROUP BY g.id, g.nombre";
    $result_grados = $conn->query($sql_grados);
} else {
    $sql_grados = "SELECT * FROM grados"; // Consulta de respaldo
    $result_grados = $conn->query($sql_grados);
}

// 3. Consulta para obtener la lista de secciones con conteo de estudiantes
if ($anio_escolar_id && $grado_id) {
    $sql_secciones = "SELECT sag.seccion, COUNT(da.id) as total_estudiantes
                      FROM secciones_anio_grado sag
                      INNER JOIN anios_grados ag ON sag.anio_grado_id = ag.id
                      LEFT JOIN datos_academicos da ON (ag.id = da.anio_grado_id AND da.seccion = sag.seccion)
                      WHERE ag.anio_escolar_id = $anio_escolar_id AND ag.grado_id = $grado_id
                      GROUP BY sag.seccion";
    $result_secciones = $conn->query($sql_secciones);
}

// 4. Consulta para obtener estudiantes si se seleccionó sección
$result_estudiantes = null;
if ($anio_escolar_id && $grado_id && $seccion) {
    $sql_estudiantes = "SELECT 
                        e.id, e.cedula, e.nombres, e.apellidos, e.telefono, 
                        da.escolaridad, 
                        g.nombre as grado_nombre,
                        ae.nombre as anio_escolar_nombre
                        FROM estudiantes e
                        INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                        INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                        INNER JOIN grados g ON ag.grado_id = g.id
                        INNER JOIN anios_escolares ae ON ag.anio_escolar_id = ae.id
                        WHERE ag.anio_escolar_id = $anio_escolar_id 
                        AND ag.grado_id = $grado_id
                        AND da.seccion = '$seccion'
                        ORDER BY e.apellidos, e.nombres";
    
    $result_estudiantes = $conn->query($sql_estudiantes);
}

// 5. Consulta para obtener datos del estudiante seleccionado (para pre-llenar el formulario)
$estudiante_seleccionado = null;
if ($estudiante_id) {
    $sql_estudiante_sel = "SELECT 
                            e.id, e.cedula, e.nombres, e.apellidos, e.telefono, 
                            g.nombre as grado,
                            da.seccion
                            FROM estudiantes e
                            INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                            INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                            INNER JOIN grados g ON ag.grado_id = g.id
                            WHERE e.id = $estudiante_id
                            LIMIT 1";
    $result_estudiante_sel = $conn->query($sql_estudiante_sel);
    if ($result_estudiante_sel->num_rows > 0) {
        $estudiante_seleccionado = $result_estudiante_sel->fetch_assoc();
    }
}

// --- Lógica para guardar el pago (Simulado por ahora) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar_pago"])) {
    // Aquí iría la lógica REAL de inserción de pago a la base de datos
    // incluyendo la validación de todos los campos.
    
    // Simulación de éxito
    $nombre_estudiante = htmlspecialchars($_POST['nombre_estudiante']);
    $monto_total = htmlspecialchars($_POST['total_a_pagar']);
    $referencia = htmlspecialchars($_POST['numero_referencia'] ?: 'N/A');
    
    $mensaje = "¡Pago de **$monto_total** registrado con éxito para **$nombre_estudiante**! Referencia: $referencia.";
    
    // Nota: Deberías redirigir para evitar el reenvío del formulario (Post/Redirect/Get pattern)
    // Pero por simplicidad, solo mostramos el mensaje.
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root { --primary-color: #6366f1; --primary-hover: #4f46e5; }
        body { font-family: 'Nunito', system-ui, -apple-system, sans-serif; letter-spacing: 0.02rem; }
        /* Mantener los estilos clave para el diseño */
        .card { border: none; border-radius: 1rem; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
        .btn-success { background: linear-gradient(135deg, #22c55e, #16a34a); border: none; padding: 0.5rem 1.25rem; border-radius: 0.75rem; font-weight: 600; }
        .filter-card { border-radius: 0.75rem; margin-bottom: 1.5rem; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .count-badge { position: absolute; top: -10px; right: -10px; }
        /* Estilos adicionales para este módulo */
        .month-checkbox { display: inline-block; margin-right: 1rem; margin-bottom: 0.5rem;}
        .month-checkbox input[type="checkbox"] { transform: scale(1.2); margin-right: 0.5rem; }
        .form-section-title { border-bottom: 2px solid #e9ecef; padding-bottom: 10px; margin-bottom: 20px; color: var(--primary-color); }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; // Incluir la barra de navegación ?>
        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow-sm mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center">
                            <h3 class="text-dark mb-0 fw-bold">Gestión de Pagos</h3>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if ($mensaje) : ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-3"></i>
                            <div><?= $mensaje ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($error) : ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3"></i>
                            <div><?= $error ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm mb-4 filter-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros para seleccionar estudiante</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="anio_escolar" class="form-label small fw-bold text-muted">Año Escolar</label>
                                    <select class="form-select" id="anio_escolar" onchange="location = this.value">
                                        <option value="pagos.php">Seleccione un año</option>
                                        <?php $result_anios_escolares->data_seek(0); ?>
                                        <?php while ($row = $result_anios_escolares->fetch_assoc()): ?>
                                            <option value="pagos.php?anio_escolar_id=<?= $row['id'] ?>"
                                                <?= $anio_escolar_id == $row['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4" <?= !$anio_escolar_id ? 'style="opacity:0.5;pointer-events:none"' : '' ?>>
                                    <label for="grado" class="form-label small fw-bold text-muted">Grado</label>
                                    <select class="form-select" id="grado" onchange="location = this.value" <?= !$anio_escolar_id ? 'disabled' : '' ?>>
                                        <option value="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>">Seleccione un grado</option>
                                        <?php if ($anio_escolar_id): ?>
                                            <?php $result_grados->data_seek(0); ?>
                                            <?php while ($row = $result_grados->fetch_assoc()): ?>
                                                <option value="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $row['id'] ?>"
                                                    <?= $grado_id == $row['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($row['nombre']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4" <?= !$grado_id ? 'style="opacity:0.5;pointer-events:none"' : '' ?>>
                                    <label for="seccion" class="form-label small fw-bold text-muted">Sección</label>
                                    <select class="form-select" id="seccion" onchange="location = this.value" <?= !$grado_id ? 'disabled' : '' ?>>
                                        <option value="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>">Seleccione una sección</option>
                                        <?php if ($grado_id): ?>
                                            <?php $result_secciones->data_seek(0); ?>
                                            <?php while ($row = $result_secciones->fetch_assoc()): ?>
                                                <option value="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $row['seccion'] ?>"
                                                    <?= $seccion == $row['seccion'] ? 'selected' : '' ?>>
                                                    Sección <?= htmlspecialchars($row['seccion']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($anio_escolar_id && $grado_id && $seccion && !$estudiante_id): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-white">
                                <h6 class="text-dark m-0 fw-bold fs-5">Estudiantes - Sección <?= htmlspecialchars($seccion) ?></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cédula</th>
                                                <th>Estudiante</th>
                                                <th>Teléfono</th>
                                                <th>Escolaridad</th>
                                                <th style="width: 100px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result_estudiantes->num_rows > 0): ?>
                                                <?php while ($estudiante = $result_estudiantes->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($estudiante['cedula']) ?></td>
                                                        <td class="fw-semibold"><?= htmlspecialchars($estudiante['apellidos']) ?>, <?= htmlspecialchars($estudiante['nombres']) ?></td>
                                                        <td><?= htmlspecialchars($estudiante['telefono']) ?></td>
                                                        <td>
                                                            <span class="badge bg-info text-dark">
                                                                <?= ucfirst(str_replace('_', ' ', $estudiante['escolaridad'])) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&estudiante_id=<?= $estudiante['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary rounded-pill"
                                                               title="Registrar Pago">
                                                                <i class="fas fa-dollar-sign me-1"></i>Pagar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        No se encontraron estudiantes en esta sección.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    
                    <?php elseif ($estudiante_seleccionado): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-primary text-white filter-header">
                                <h5 class="m-0 fw-bold"><i class="fas fa-credit-card me-2"></i>Registrar Pago para Estudiante</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="pagos.php?anio_escolar_id=<?= $anio_escolar_id ?>&grado_id=<?= $grado_id ?>&seccion=<?= $seccion ?>&estudiante_id=<?= $estudiante_id ?>">
                                    <input type="hidden" name="estudiante_id" value="<?= $estudiante_seleccionado['id'] ?>">
                                    <input type="hidden" name="nombre_estudiante" value="<?= htmlspecialchars($estudiante_seleccionado['apellidos']) ?>, <?= htmlspecialchars($estudiante_seleccionado['nombres']) ?>">

                                    <h6 class="form-section-title fw-bold">1. Datos del Estudiante</h6>
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Nombres:</label>
                                            <p class="form-control-plaintext fw-semibold"><?= htmlspecialchars($estudiante_seleccionado['nombres']) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Apellidos:</label>
                                            <p class="form-control-plaintext fw-semibold"><?= htmlspecialchars($estudiante_seleccionado['apellidos']) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Cédula:</label>
                                            <p class="form-control-plaintext fw-semibold"><?= htmlspecialchars($estudiante_seleccionado['cedula']) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Grado y Sección:</label>
                                            <p class="form-control-plaintext fw-semibold"><?= htmlspecialchars($estudiante_seleccionado['grado']) ?> (<?= htmlspecialchars($estudiante_seleccionado['seccion']) ?>)</p>
                                        </div>
                                    </div>
                                    
                                    <h6 class="form-section-title fw-bold">2. Meses y Concepto del Pago</h6>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <label for="meses_pagados" class="form-label small fw-bold text-muted">Meses del Año (Seleccione los pagados):</label>
                                            <div class="border p-3 rounded">
                                                <?php
                                                    $meses = ['Septiembre','Octubre','Noviembre','Diciembre','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto'    ];
                                                    foreach ($meses as $mes) {
                                                        echo "<div class='month-checkbox'><input type='checkbox' id='mes_$mes' name='meses[]' value='$mes'><label for='mes_$mes' class='ms-1'>$mes</label></div>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="concepto" class="form-label fw-bold">Por Concepto de:</label>
                                            <select class="form-select" id="concepto" name="concepto" required>
                                                <option value="">Seleccione el concepto</option>
                                                <option value="Inscripción">Inscripción</option>
                                                <option value="Mensualidad" selected>Mensualidad</option>
                                                <option value="Cuota">Cuota/Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fecha_pago" class="form-label fw-bold">Fecha del Pago:</label>
                                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>

                                    <h6 class="form-section-title fw-bold">3. Detalles y Montos</h6>
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="metodo_pago" class="form-label fw-bold">Método de Pago:</label>
                                            <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                                <option value="">Seleccione el método</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Transferencia bancaria">Transferencia bancaria</option>
                                                <option value="Pago Móvil">Pago Móvil</option>
                                                <option value="Punto de Venta">Punto de Venta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="banco_emisor" class="form-label">Banco Emisor (Opcional):</label>
                                            <input type="text" class="form-control" id="banco_emisor" name="banco_emisor" placeholder="Ej: Banesco, Venezuela">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="numero_referencia" class="form-label">Nro. de Referencia (Si aplica):</label>
                                            <input type="text" class="form-control" id="numero_referencia" name="numero_referencia" placeholder="Referencia/Lote/Número de cheque">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="monto" class="form-label fw-bold">Monto ($):</label>
                                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" value="0.00" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="iva" class="form-label">I.V.A ($) (Si aplica):</label>
                                            <input type="number" step="0.01" class="form-control" id="iva" name="iva" value="0.00">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="total_a_pagar" class="form-label fw-bold">Total a Pagar ($):</label>
                                            <input type="number" step="0.01" class="form-control" id="total_a_pagar" name="total_a_pagar" value="0.00" readonly required>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" name="guardar_pago" class="btn btn-success px-4 me-2">
                                            <i class="fas fa-save me-2"></i>Guardar Pago
                                        </button>
                                        <a href="pagos.php" class="btn btn-outline-secondary px-4">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <footer class="bg-white sticky-footer border-top">
                <div class="container my-auto">
                    <div class="text-center my-auto py-3 text-muted">
                        <span>© Gestión Escolar <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Script para calcular el total a pagar
        $(document).ready(function() {
            const montoInput = $('#monto');
            const ivaInput = $('#iva');
            const totalInput = $('#total_a_pagar');

            function calcularTotal() {
                const monto = parseFloat(montoInput.val()) || 0;
                const iva = parseFloat(ivaInput.val()) || 0;
                const total = monto + iva;
                totalInput.val(total.toFixed(2));
            }

            montoInput.on('input', calcularTotal);
            ivaInput.on('input', calcularTotal);
            
            // Inicializar el cálculo al cargar
            calcularTotal(); 
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>