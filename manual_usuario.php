<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - SACE</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .manual-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .manual-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .section-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .version-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent-color);
        }
        
        .doc-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .feature-list {
            list-style-type: none;
            padding-left: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list li i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .doc-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold mb-0">Manual de Usuario</h3>
                        <div>
                            <span class="badge bg-primary">Versión 1.0</span>
                            <span class="badge bg-secondary">Actualizado: <?php echo date('d/m/Y'); ?></span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-8 mx-auto animate__animated animate__fadeIn">
                            <div class="card shadow manual-card">
                                <div class="card-header py-3 text-white" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color));">
                                    <h4 class="m-0 font-weight-bold"><i class="fas fa-book me-2"></i>Documentación Completa del Sistema</h4>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf doc-icon animate__animated animate__pulse animate__infinite"></i>
                                    <h3 class="mb-3">Manual de Usuario SACE</h3>
                                    <p class="lead mb-4">Guía completa con instrucciones detalladas para utilizar todas las funcionalidades del sistema de gestión académica.</p>
                                    
                                    <div class="d-flex justify-content-center gap-3 mb-4">
                                        <a href="assets/docs/SACE.pdf" class="btn btn-primary btn-lg btn-icon-split" download>
                                            <span class="icon text-white-50">
                                                <i class="fas fa-download"></i>
                                            </span>
                                            <span class="text">Descargar PDF (3.1 MB)</span>
                                        </a>
                                    </div>
                                    
                                    <div class="alert alert-info text-start">
                                        <i class="fas fa-info-circle me-2"></i> Para visualizar correctamente el manual, necesitarás Adobe Acrobat Reader o cualquier otro visor de PDF.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row animate__animated animate__fadeInUp">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 manual-card section-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><i class="fas fa-list-check me-2"></i> Contenido del Manual</h5>
                                    <ul class="feature-list mt-3">
                                        <li><i class="fas fa-angle-right"></i> Introducción al sistema SACE</li>
                                        <li><i class="fas fa-angle-right"></i> Requisitos del sistema</li>
                                        <li><i class="fas fa-angle-right"></i> Instrucciones de acceso</li>
                                        <li><i class="fas fa-angle-right"></i> Dashboard y navegación</li>
                                        <li><i class="fas fa-angle-right"></i> Gestión de estudiantes</li>
                                        <li><i class="fas fa-angle-right"></i> Registro de asistencia</li>
                                        <li><i class="fas fa-angle-right"></i> Control académico</li>
                                        <li><i class="fas fa-angle-right"></i> Generación de reportes</li>
                                        <li><i class="fas fa-angle-right"></i> Configuración del sistema</li>
                                        <li><i class="fas fa-angle-right"></i> Preguntas frecuentes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 manual-card section-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><i class="fas fa-question-circle me-2"></i> Soporte Técnico</h5>
                                    <div class="mt-3">
                                        <p>Si necesitas ayuda adicional con el sistema o tienes preguntas sobre el manual, por favor contacta al equipo de soporte:</p>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-envelope fa-lg text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Correo Electrónico</h6>
                                                <p class="mb-0 small">soporte@sace.edu.ve</p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-phone fa-lg text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Teléfono</h6>
                                                <p class="mb-0 small">+58 424-1234567</p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock fa-lg text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Horario de Atención</h6>
                                                <p class="mb-0 small">Lunes a Viernes, 8:00 AM - 5:00 PM</p>
                                            </div>
                                        </div>
                                        
                                        <hr class="my-4">
                                        
                                        <a href="soporte.php" class="btn btn-outline-primary">
                                            <i class="fas fa-headset me-2"></i> Formulario de Soporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4 animate__animated animate__fadeIn">
                        <div class="col-md-12">
                            <div class="card manual-card">
                                <div class="card-body">
                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
        // Simple animation trigger on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate__animated');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        const animation = entry.target.getAttribute('data-animation');
                        entry.target.classList.add(animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            animatedElements.forEach(element => {
                element.style.opacity = 0;
                const animation = element.classList.contains('animate__fadeIn') ? 'animate__fadeIn' :
                                 element.classList.contains('animate__fadeInUp') ? 'animate__fadeInUp' : 'animate__fadeIn';
                element.setAttribute('data-animation', animation);
                observer.observe(element);
            });
        });
    </script>
</body>
</html>