<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Procesar formulario de asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grado_id = $_POST['grado_id'];
    $materias = $_POST['materias'] ?? [];

    // Eliminar asignaciones anteriores para este grado
    $conn->query("DELETE FROM materias_grados WHERE grado_id = $grado_id");

    // Insertar nuevas asignaciones
    foreach ($materias as $materia_id) {
        $materia_id = intval($materia_id);
        $conn->query("INSERT INTO materias_grados (materia_id, grado_id) VALUES ($materia_id, $grado_id)");
    }

    $_SESSION['mensaje'] = "Materias asignadas correctamente al grado";
    header("Location: asignar_materias.php");
    exit();
}

// Obtener todos los grados
$grados = $conn->query("SELECT id, nombre FROM grados ORDER BY nombre");
$materias = $conn->query("SELECT id, nombre FROM materias ORDER BY nombre");

// Obtener todas las asignaciones para mostrar en la tabla
$asignaciones_completas = $conn->query("
    SELECT g.id as grado_id, g.nombre as grado_nombre, 
           m.id as materia_id, m.nombre as materia_nombre
    FROM grados g
    LEFT JOIN materias_grados mg ON g.id = mg.grado_id
    LEFT JOIN materias m ON mg.materia_id = m.id
    ORDER BY g.nombre, m.nombre
");

// Organizar los datos por grado para el acordeón
$grados_con_materias = [];
while ($row = $asignaciones_completas->fetch_assoc()) {
    $grado_id = $row['grado_id'];
    if (!isset($grados_con_materias[$grado_id])) {
        $grados_con_materias[$grado_id] = [
            'nombre' => $row['grado_nombre'],
            'materias' => []
        ];
    }
    if ($row['materia_id']) {
        $grados_con_materias[$grado_id]['materias'][] = [
            'id' => $row['materia_id'],
            'nombre' => $row['materia_nombre']
        ];
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias - Gestión Escolar</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .materias-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .materia-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .materia-item:last-child {
            border-bottom: none;
        }
        .materia-item:hover {
            background-color: #e9ecef;
        }
        .badge-materia {
            font-size: 0.9em;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .grado-card {
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        .grado-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .grado-header {
            cursor: pointer;
            padding: 10px 15px;
            background-color: #f1f8ff;
            border-bottom: 1px solid #dee2e6;
        }
        .tabla-materias th {
            background-color: #f8f9fa;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow-sm mb-4 topbar static-top navbar-light">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h3 class="text-dark mb-0 fw-bold">Asignación de Materias por Grado</h3>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <?php if (isset($_SESSION['mensaje'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['mensaje'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['mensaje']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4 grado-card">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Asignar Materias</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" id="form-asignacion">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Seleccione el Grado/Año</label>
                                            <select class="form-select" id="select-grado" name="grado_id" required>
                                                <option value="">-- Seleccionar Grado --</option>
                                                <?php 
                                                $grados->data_seek(0);
                                                while($grado = $grados->fetch_assoc()): ?>
                                                    <option value="<?= $grado['id'] ?>"><?= htmlspecialchars($grado['nombre']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <h5 class="fw-bold">Materias Disponibles</h5>
                                            <div class="materias-container" id="materias-container">
                                                <?php 
                                                $materias->data_seek(0);
                                                while($materia = $materias->fetch_assoc()): ?>
                                                    <div class="materia-item">
                                                        <div class="form-check">
                                                            <input class="form-check-input materia-checkbox" type="checkbox" 
                                                                   name="materias[]" 
                                                                   value="<?= $materia['id'] ?>"
                                                                   id="materia-<?= $materia['id'] ?>">
                                                            <label class="form-check-label" for="materia-<?= $materia['id'] ?>">
                                                                <?= htmlspecialchars($materia['nombre']) ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Guardar Asignaciones
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow mb-4 grado-card">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Materias Asignadas por Grado</h6>
                                </div>
                                <div class="card-body">
                                    <div class="accordion" id="accordionGrados">
                                        <?php if (empty($grados_con_materias)): ?>
                                            <div class="alert alert-info">
                                                No hay grados con materias asignadas aún.
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($grados_con_materias as $grado_id => $grado_data): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading<?= $grado_id ?>">
                                                        <button class="accordion-button collapsed grado-header" type="button" 
                                                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $grado_id ?>" 
                                                                aria-expanded="false" aria-controls="collapse<?= $grado_id ?>">
                                                            <?= htmlspecialchars($grado_data['nombre']) ?>
                                                            <span class="badge bg-primary ms-2">
                                                                <?= count($grado_data['materias']) ?> materias
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?= $grado_id ?>" class="accordion-collapse collapse" 
                                                         aria-labelledby="heading<?= $grado_id ?>" data-bs-parent="#accordionGrados">
                                                        <div class="accordion-body">
                                                            <?php if (!empty($grado_data['materias'])): ?>
                                                                <div class="d-flex flex-wrap">
                                                                    <?php foreach ($grado_data['materias'] as $materia): ?>
                                                                        <span class="badge bg-success badge-materia">
                                                                            <?= htmlspecialchars($materia['nombre']) ?>
                                                                        </span>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="alert alert-warning mb-0">
                                                                    No hay materias asignadas a este grado.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar materias asignadas al seleccionar un grado
            $('#select-grado').change(function() {
                var grado_id = $(this).val();
                if (grado_id) {
                    $.ajax({
                        url: 'get_materias_asignadas.php',
                        type: 'GET',
                        dataType: 'json',
                        data: { grado_id: grado_id },
                        success: function(response) {
                            // Desmarcar todos los checkboxes primero
                            $('.materia-checkbox').prop('checked', false);
                            
                            // Marcar las materias asignadas
                            if (response && response.length > 0) {
                                response.forEach(function(materia_id) {
                                    $('#materia-' + materia_id).prop('checked', true);
                                });
                                
                                // Mostrar notificación
                                toastr.success(response.length + ' materias asignadas a este grado');
                            } else {
                                toastr.info('Este grado no tiene materias asignadas');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al cargar materias asignadas:", error);
                            toastr.error('Error al cargar las materias asignadas');
                        }
                    });
                } else {
                    // Si no se selecciona ningún grado, desmarcar todos
                    $('.materia-checkbox').prop('checked', false);
                }
            });
        });
    </script>
</body>
</html>