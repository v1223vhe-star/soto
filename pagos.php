<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$mensaje = "";
$error = "";

// Obtener el año escolar activo actualmente
$res_anio = $conn->query("SELECT id, nombre FROM anios_escolares WHERE activo = 1 LIMIT 1");
$anio_activo = $res_anio->fetch_assoc();
$anio_id = $anio_activo['id'] ?? 0;

// Historial de los últimos 5 pagos realizados en el sistema
$sql_historial = "SELECT p.*, e.nombres, e.apellidos 
                  FROM pagos p 
                  JOIN estudiantes e ON p.estudiante_id = e.id 
                  ORDER BY p.fecha_pago DESC LIMIT 5";
$historial_reciente = $conn->query($sql_historial);

// Obtener el término de búsqueda y el ID del estudiante seleccionado
$busqueda = $_GET["busqueda"] ?? null;
$estudiante_id = $_GET["estudiante_id"] ?? null;

// --- 1. Lógica de Búsqueda de Estudiantes ---
$result_estudiantes = null;
if (!empty($busqueda)) {
    // Buscamos por cédula, nombre o apellido
    $termino = "%" . $conn->real_escape_string($busqueda) . "%";
    $sql_estudiantes = "SELECT 
                        e.id, e.cedula, e.nombres, e.apellidos, 
                        g.nombre as grado_nombre, da.seccion
                        FROM estudiantes e
                        INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                        INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                        INNER JOIN grados g ON ag.grado_id = g.id
                        WHERE e.cedula LIKE '$termino' 
                        OR e.nombres LIKE '$termino' 
                        OR e.apellidos LIKE '$termino'
                        ORDER BY e.apellidos ASC LIMIT 10";
    $result_estudiantes = $conn->query($sql_estudiantes);
}

// --- 2. Datos del estudiante seleccionado para el formulario ---
$estudiante_seleccionado = null;
if ($estudiante_id) {
    $sql_estudiante_sel = "SELECT 
                            e.id, e.cedula, e.nombres, e.apellidos, 
                            g.nombre as grado, da.seccion
                            FROM estudiantes e
                            INNER JOIN datos_academicos da ON e.id = da.estudiante_id
                            INNER JOIN anios_grados ag ON da.anio_grado_id = ag.id
                            INNER JOIN grados g ON ag.grado_id = g.id
                            WHERE e.id = " . intval($estudiante_id);
    $result_estudiante_sel = $conn->query($sql_estudiante_sel);
    $estudiante_seleccionado = $result_estudiante_sel->fetch_assoc();
}

// Meses del año escolar
$meses_escolares = ['Septiembre', 'Octubre', 'Noviembre', 'Diciembre', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'];

// Si hay un estudiante seleccionado, buscar qué meses ya pagó en este año escolar
$meses_pagados = [];
if ($estudiante_id && $anio_id) {
    $check_pagos = $conn->query("SELECT mes FROM pagos WHERE estudiante_id = $estudiante_id AND anio_escolar_id = $anio_id");
    while ($row = $check_pagos->fetch_assoc()) {
        $meses_pagados[] = $row['mes'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar_pago"])) {
    $e_id = intval($_POST['estudiante_id']);
    $mes_a_pagar = $_POST['mes'];
    $monto = floatval($_POST['monto']);
    $iva = floatval($_POST['iva']);
    $total = $monto + $iva;

    // Validación de seguridad extra: verificar si ya se pagó el mes
    $check = $conn->query("SELECT id FROM pagos WHERE estudiante_id = $e_id AND mes = '$mes_a_pagar' AND anio_escolar_id = $anio_id");
    
    if ($check->num_rows > 0) {
        $error = "Error: El mes de $mes_a_pagar ya fue cancelado anteriormente.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pagos (estudiante_id, anio_escolar_id, mes, monto, iva, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisddd", $e_id, $anio_id, $mes_a_pagar, $monto, $iva, $total);
        
        if ($stmt->execute()) {
            $mensaje = "¡Pago de $mes_a_pagar registrado con éxito!";
            // Actualizar lista de meses pagados para refrescar la interfaz
            $meses_pagados[] = $mes_a_pagar;
        } else {
            $error = "Error al registrar el pago: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Pagos - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Barra de navegación superior -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow">
                                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                                        <span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $_SESSION["username"]; ?></span>
                                        <img class="border rounded-circle img-profile" src="assets/img/avatars/1.jpg">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Cerrar sesión</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

        
        <div class="container-fluid py-4">
            <h3 class="text-dark fw-bold mb-4">Gestión de Pagos</h3>




            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?= $mensaje ?></div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="pagos.php" class="row g-2 justify-content-center">
                        <div class="col-md-8">
                            <input type="text" name="busqueda" class="form-control" 
                                   placeholder="Buscar por Cédula, Nombre o Apellido..." 
                                   value="<?= htmlspecialchars($busqueda ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($result_estudiantes && !$estudiante_id): ?>
                <div class="card shadow mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Estudiante</th>
                                    <th>Grado/Sección</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($e = $result_estudiantes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $e['cedula'] ?></td>
                                        <td><?= $e['apellidos'] . ", " . $e['nombres'] ?></td>
                                        <td><?= $e['grado_nombre'] . " (" . $e['seccion'] . ")" ?></td>
                                        <td>
                                            <a href="pagos.php?estudiante_id=<?= $e['id'] ?>&busqueda=<?= $busqueda ?>" 
                                               class="btn btn-sm btn-success">Seleccionar</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($estudiante_id): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold">Estatus de Mensualidades: <?= $anio_activo['nombre'] ?></h6>
    </div>
    <div class="card-body">
        <div class="row">
            <?php 
            $meses = ['Septiembre', 'Octubre', 'Noviembre', 'Diciembre', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'];
            foreach($meses as $m): 
                $esta_pagado = in_array($m, $meses_pagados);
            ?>
                <div class="col-md-2 col-sm-4 col-6 mb-2">
                    <div class="border rounded p-2 text-center <?= $esta_pagado ? 'bg-light text-success' : 'text-danger' ?>" style="border-width: 2px !important;">
                        <small class="d-block font-weight-bold"><?= $m ?></small>
                        <i class="fas <?= $esta_pagado ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                        <small class="d-block"><?= $esta_pagado ? 'Pagado' : 'Pendiente' ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

            <?php if ($estudiante_seleccionado): ?>
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Registrar Pago: <?= $estudiante_seleccionado['nombres'] ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="nombre_estudiante" value="<?= $estudiante_seleccionado['apellidos'] ?>">
                            
                            <h6 class="form-section-title">Detalles del Estudiante</h6>
                            <p><strong>Cédula:</strong> <?= $estudiante_seleccionado['cedula'] ?> | 
                               <strong>Grado:</strong> <?= $estudiante_seleccionado['grado'] ?> (<?= $estudiante_seleccionado['seccion'] ?>)</p>

<div class="form-group">
    <label class="form-label font-weight-bold">Seleccionar Mes a Pagar:</label>
    <select name="mes" class="form-control" required>
        <option value="">-- Seleccione un mes --</option>
        <?php foreach($meses as $m): ?>
            <?php $ya_pagado = in_array($m, $meses_pagados); ?>
            <option value="<?= $m ?>" <?= $ya_pagado ? 'disabled class="bg-gray-200 text-danger"' : '' ?>>
                <?= $m ?> <?= $ya_pagado ? ' (YA CANCELADO)' : '' ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">Los meses en rojo ya han sido procesados en este año escolar.</small>
</div>
<input type="hidden" name="estudiante_id" value="<?= $estudiante_seleccionado['id'] ?>">

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

                                    <h6 class="form-section-title fw-bold">Detalles y Montos</h6>
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

                            <button type="submit" name="guardar_pago" class="btn btn-success">Guardar Pago</button>
                            <a href="pagos.php" class="btn btn-secondary">Volver a buscar</a>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Pagos Recientes</h6>
        <i class="fas fa-history text-gray-300"></i>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Estudiante</th>
                        <th>Mes Pagado</th>
                        <th>Total ($)</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($historial_reciente && $historial_reciente->num_rows > 0): ?>
                        
                        <th>Acción</th>

<?php while ($h = $historial_reciente->fetch_assoc()): ?>
<tr>
    <td><?= $h['nombres'] . " " . $h['apellidos'] ?></td>
    <td><?= $h['mes'] ?></td>
    <td><?= number_format($h['total'], 2) ?>$</td>
    <td><?= date('d/m/Y', strtotime($h['fecha_pago'])) ?></td>
    <td>
        <a href="generar_factura.php?id=<?= $h['id'] ?>" class="btn btn-sm btn-danger" title="Descargar PDF">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    </td>
</tr>
<?php endwhile; ?>

                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No hay pagos registrados recientemente.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



                <!-- Footer -->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Gestión Escolar 2026</span></div>
                </div>
            </footer>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
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