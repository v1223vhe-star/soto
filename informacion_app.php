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
    <title>Información del Sistema - SACE</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="assets/img/logo.jpeg" type="image/png">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --text-color: #2b2d42;
            --text-light: #8d99ae;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--text-color);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }
        
        /* Header Styles */
        .system-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            border-radius: 0.5rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .system-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.05)" d="M0,0 L100,0 L100,100 L0,100 Z"></path></svg>');
            opacity: 0.1;
        }
        
        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .app-logo {
            height: 80px;
            width: auto;
            transition: transform 0.3s ease;
        }
        
        .app-logo:hover {
            transform: scale(1.05);
        }
        
        .school-logo {
            height: 100px;
            width: auto;
            max-width: 200px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        
        /* Card Styles */
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background: white;
            height: 100%;
            position: relative;
            z-index: 1;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        
        /* Section Styles */
        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }
        
        .system-description {
            background-color: white;
            border-radius: 0.5rem;
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        /* Feature List */
        .feature-list {
            column-count: 2;
            column-gap: 2rem;
        }
        
        .feature-list li {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 0.75rem;
            break-inside: avoid;
        }
        
        .feature-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            background-color: var(--accent-color);
            border-radius: 50%;
        }
        
        /* Badges */
        .feature-badge {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .feature-badge:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Contact Card */
        .contact-card {
            background: linear-gradient(135deg, var(--dark-color), #16213e);
            color: white;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .contact-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .contact-card .btn-outline-light:hover {
            color: var(--dark-color);
            background-color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .logo-container {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }
            
            .app-logo {
                height: 70px;
                margin-bottom: 1rem;
            }
            
            .school-logo {
                height: 80px;
                margin-top: 1rem;
            }
            
            .feature-list {
                column-count: 1;
            }
            
            .system-header {
                padding: 2rem 0;
            }
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <div class="container-fluid px-4">
                    <!-- System Header -->
                    <div class="system-header animate__animated animate__fadeIn">
                        <div class="container text-center">
                            <h1 class="display-4 fw-bold mb-3">Sistema de Asistencia y Control Estudiantil</h1>
                            <p class="lead mb-4">Una solución integral para la gestión académica institucional</p>
                            <div class="d-flex justify-content-center flex-wrap">
                                <span class="feature-badge animate__animated animate__fadeInUp" style="animation-delay: 0.1s"><i class="fas fa-check-circle me-2"></i>Control de Asistencia</span>
                                <span class="feature-badge animate__animated animate__fadeInUp" style="animation-delay: 0.2s"><i class="fas fa-check-circle me-2"></i>Registro Estudiantil</span>
                                <span class="feature-badge animate__animated animate__fadeInUp" style="animation-delay: 0.3s"><i class="fas fa-check-circle me-2"></i>Gestión Académica</span>
                                <span class="feature-badge animate__animated animate__fadeInUp" style="animation-delay: 0.4s"><i class="fas fa-check-circle me-2"></i>Reportes Automáticos</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Logo Section -->
                    <div class="logo-container animate__animated animate__fadeIn">
                        <div class="d-flex align-items-center">
                            <img src="assets/img/logo2.jpeg" alt="Logo SACE" class="app-logo me-3 floating">
                            <div>
                                <h3 class="mb-1 fw-bold">SACE v1.0.0</h3>
                                <p class="mb-0 text-muted">Plataforma de gestión educativa</p>
                            </div>
                        </div>
                        <img src="assets/img/logo.jpeg" alt="Logo de la Institución" class="school-logo">
                    </div>
                    
                    <!-- System Description -->
                    <div class="system-description animate__animated animate__fadeInUp">
                        <h3 class="section-title">Descripción del Sistema</h3>
                        <p class="lead mb-4">SACE es una plataforma web desarrollada para modernizar y optimizar los procesos administrativos y académicos en instituciones educativas, con especial énfasis en el control de asistencia y registro estudiantil.</p>
                        
                        <h4 class="mb-3">Funcionalidades Principales</h4>
                        <ul class="feature-list">
                            <li>Registro diario de asistencia con marcación de presentes/ausentes</li>
                            <li>Historial completo de asistencia por estudiante</li>
                            <li>Reportes automáticos de inasistencias recurrentes</li>
                            <li>Alertas tempranas para casos de ausentismo</li>
                            <li>Registro completo de datos personales y académicos</li>
                            <li>Historial académico a través de los años</li>
                            <li>Control de documentos y requisitos</li>
                            <li>Generación de reportes estadísticos</li>
                            <li>Módulo de comunicación con representantes</li>
                            <li>Panel de control personalizado por roles</li>
                        </ul>
                    </div>
                    
                    <!-- Features Grid -->
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4 animate__animated animate__fadeInLeft">
                            <div class="feature-card h-100 p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-database feature-icon me-3"></i>
                                    <h3 class="mb-0">Estructura de Datos</h3>
                                </div>
                                <p class="text-muted mb-4">El sistema cuenta con una base de datos relacional que incluye:</p>
                                
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user-graduate text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Estudiantes</h5>
                                        <p class="small text-muted mb-0">Registro completo de datos personales, académicos y de contacto</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Asistencia</h5>
                                        <p class="small text-muted mb-0">Registro diario de asistencia por estudiante y materia</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-layer-group text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Grados y Secciones</h5>
                                        <p class="small text-muted mb-0">Estructura académica configurable</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-book text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Materias</h5>
                                        <p class="small text-muted mb-0">Plan de estudios completo</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-users text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Representantes</h5>
                                        <p class="small text-muted mb-0">Información de contacto y parentesco</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4 animate__animated animate__fadeInRight">
                            <div class="feature-card h-100 p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-laptop-code feature-icon me-3"></i>
                                    <h3 class="mb-0">Tecnologías Utilizadas</h3>
                                </div>
                                <p class="text-muted mb-4">El sistema fue desarrollado con las siguientes tecnologías:</p>
                                
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fab fa-php text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">PHP 8.2</h5>
                                            <p class="small text-muted mb-0">Lenguaje backend</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fas fa-database text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">MySQL 10.4</h5>
                                            <p class="small text-muted mb-0">Base de datos</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fab fa-html5 text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">HTML5 & CSS3</h5>
                                            <p class="small text-muted mb-0">Frontend</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fab fa-js-square text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">JavaScript</h5>
                                            <p class="small text-muted mb-0">Interactividad</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fab fa-bootstrap text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">Bootstrap 5</h5>
                                            <p class="small text-muted mb-0">Framework CSS</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="p-3 border rounded text-center h-100">
                                            <i class="fas fa-server text-primary mb-2" style="font-size: 2.5rem;"></i>
                                            <h5 class="mb-1">Apache</h5>
                                            <p class="small text-muted mb-0">Servidor web</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Section -->
                    <div class="row animate__animated animate__fadeIn">
                        <div class="col-md-12">
                            <div class="contact-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="text-white mb-3"><i class="fas fa-lightbulb me-2"></i> ¿Necesitas ayuda?</h3>
                                        <p class="text-white-50 mb-4">Para más información sobre el sistema o soporte técnico, por favor contacta al equipo de desarrollo.</p>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-envelope me-2 text-white"></i>
                                                <span class="text-white">jorluismj04@gmail.com-v1223vhe@gmail.com</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone me-2 text-white"></i>
                                                <span class="text-white">+58 424-9065920
                                                +58 414-8626382</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <button class="btn btn-outline-light btn-lg px-4">Contactar al Equipo</button>
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
        // Enhanced animation trigger on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate__animated');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const animation = entry.target.getAttribute('data-animation');
                        entry.target.classList.add(animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, { 
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            animatedElements.forEach(element => {
                const animation = element.classList.contains('animate__fadeIn') ? 'animate__fadeIn' :
                                 element.classList.contains('animate__fadeInLeft') ? 'animate__fadeInLeft' :
                                 element.classList.contains('animate__fadeInRight') ? 'animate__fadeInRight' :
                                 element.classList.contains('animate__fadeInUp') ? 'animate__fadeInUp' : 'animate__fadeIn';
                element.setAttribute('data-animation', animation);
                observer.observe(element);
            });
        });
    </script>
</body>
</html>