<?php
// Verificar si el rol del usuario está definido en la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el rol del usuario
$user_role = $_SESSION["role"] ?? null;
?>
<nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-dark p-0 navbar-dark" style="background: linear-gradient(135deg, #127749 0%, #127749 100%);">
    <div class="container-fluid d-flex flex-column p-0" style="padding-left: 0;">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="padding: 1.5rem 0.5rem;">
    <div class="d-flex align-items-center">
        <!-- Logo de la aplicación 
        <div class="logo-container me-2" style="width: 40px; height: 40px;">
            <img src="assets/img/logo2.jpeg" alt="Logo SACE" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;">
        </div>-->
        <div class="sidebar-brand-text text-center" style="font-size: 1rem; font-weight: 600; letter-spacing: 1px;">
           <!-- <span>SACE</span><br> -->
            <span style="font-size: 0.65rem; opacity: 0.8;">Sistema Académico</span>
        </div>
    </div>
</a>
        <hr class="sidebar-divider my-0" style="border-color: rgba(255,255,255,0.1); margin: 0.5rem 1rem;">
        <ul class="navbar-nav text-light" id="accordionSidebar" style="padding: 0 0.5rem;">
            <!-- Nuevo módulo de Búsqueda de Estudiantes 
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBusqueda" aria-expanded="true" aria-controls="collapseBusqueda" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-search me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Buscar Estudiante</span>
                    <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                </a>
                <div id="collapseBusqueda" class="collapse" aria-labelledby="headingBusqueda" data-parent="#accordionSidebar">
                    <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                        <form class="px-2" action="buscar_estudiante.php" method="get">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm" name="busqueda" placeholder="Cédula o nombre" required>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block text-center">Buscar por cédula o nombre</small>
                        </form>
                    </div>
                </div>
            </li> -->

            <li class="nav-item">
                <a class="nav-link" href="index.php" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-tachometer-alt me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Inicio</span>
                </a>
            </li>

            <!-- Gestionar Estudiantes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEstudiantes" aria-expanded="true" aria-controls="collapseEstudiantes" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-graduation-cap me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Estudiantes</span>
                    <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                </a>
                <div id="collapseEstudiantes" class="collapse" aria-labelledby="headingEstudiantes" data-parent="#accordionSidebar">
                    <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                        <a class="collapse-item" href="inscribir_alumno.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Registrar Alumno</a>

                       <!-- <a class="collapse-item" href="asistencias.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Asistencia</a> -->

                        <a class="collapse-item" href="gestionar_estudiantes.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Lista Estudiantes</a>
                        <a class="collapse-item" href="gestionar_representantes.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Representantes</a>
                        
                    </div>
                </div>
            </li>

<!-- Gestionar Pagos -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePagos" aria-expanded="true" aria-controls="collapsePagos" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-book me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Gestionar Pago</span>
                    <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                </a>
                <div id="collapsePagos" class="collapse" aria-labelledby="headingPagos" data-parent="#accordionSidebar">
                    <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                        <a class="collapse-item" href="pagos.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Gestionar Pagos</a>
                        <a class="collapse-item" href="#.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Comprobante de Pago</a>
                        
                    </div>
                </div>
            </li>

            <!-- Gestionar Materias 
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMaterias" aria-expanded="true" aria-controls="collapseMaterias" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-book me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Materias</span>
                    <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                </a>
                <div id="collapseMaterias" class="collapse" aria-labelledby="headingMaterias" data-parent="#accordionSidebar">
                    <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                        <a class="collapse-item" href="gestionar_materias.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Lista Materias</a>
                        <a class="collapse-item" href="asignar_materias.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Asignar Materias</a>
                        
                    </div>
                </div>
            </li> -->

            <!-- Gestionar Año Escolar -->
            <li class="nav-item">
                <a class="nav-link" href="gestionar_anios_escolares.php" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-calendar-alt me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Año Escolar</span>
                </a>
            </li>
 <?php if ($user_role == 'admin'): ?>
            <!-- Gestionar Años y Grados -->
            <li class="nav-item">
                <a class="nav-link" href="gestionar_anios_grados.php" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-layer-group me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Años/Grados</span>
                </a>
            </li>
            <?php endif; ?>
            <!-- Reportes Automáticos -->
            <li class="nav-item">
                <a class="nav-link" href="reportes.php" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-chart-bar me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Gráficos</span>
                </a>
            </li>
            
            <!-- Actualizar Grados -->
            <li class="nav-item">
                <a class="nav-link" href="#" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-arrow-up me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Actualizar Grados</span>
                </a>
            </li>
             <!-- Nuevo módulo de Ayuda -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAyuda" aria-expanded="true" aria-controls="collapseAyuda" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-question-circle me-2" style="width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.85rem;">Ayuda</span>
                    <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                </a>
                <div id="collapseAyuda" class="collapse" aria-labelledby="headingAyuda" data-parent="#accordionSidebar">
                    <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                        <a class="collapse-item" href="#" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-1"></i> Información
                        </a>
                        <a class="collapse-item" href="#" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">
                            <i class="fas fa-book me-1"></i> Manual de Usuario
                        </a>
                    </div>
                </div>
            </li>

            

            
            <!-- Panel de Control de Usuarios (Solo Admin) -->
            <?php if ($user_role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseUsuarios" aria-expanded="true" aria-controls="collapseUsuarios" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                        <i class="fas fa-users-cog me-2" style="width: 20px; text-align: center;"></i>
                        <span style="font-size: 0.85rem;">Panel Control</span>
                        <i class="fas fa-chevron-right float-end mt-1" style="font-size: 0.7rem;"></i>
                    </a>
                    <div id="collapseUsuarios" class="collapse" aria-labelledby="headingUsuarios" data-parent="#accordionSidebar">
                        <div class="bg-dark py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.2)!important; margin: 0 0.5rem;">
                            <a class="collapse-item" href="gestionar_usuarios.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Usuarios</a>
                            <a class="collapse-item" href="profile.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Perfil</a>
                            <a class="collapse-item" href="respaldo.php" style="color: #e9ecef; padding: 0.4rem 1rem; border-left: 2px solid transparent; transition: all 0.2s; font-size: 0.8rem;">Respaldo</a>
                        </div>
                    </div>
                </li>
                  
                 

            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php" style="border-radius: 4px; margin: 0.1rem 0; padding: 0.5rem 0.75rem;">
                        <i class="fas fa-user me-2" style="width: 20px; text-align: center;"></i>
                        <span style="font-size: 0.85rem;">Perfil</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <!-- Versión del sistema -->
        <div class="text-center py-2" style="color: rgba(255,255,255,0.5); font-size: 0.7rem; margin-top: auto;">
            Beta 1.6.0
        </div>
    </div>
</nav>

<!--<script>
// Actualizar la cookie de sesión cada minuto
setInterval(function() {
    document.cookie = `session_time=${new Date().getTime()}; path=/; secure; samesite=Strict`;
}, 60000); // 60 segundos
</script> -->

<!-- En nav.php (antes del cierre del body) 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/auto-logout.js"></script>-->
<style>
    .sidebar {
        width: 220px;
        min-height: 100vh;
        transition: all 0.3s;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .nav-link {
        transition: all 0.2s;
    }
    
    .nav-link:hover {
        background-color: rgba(255,255,255,0.1)!important;
        transform: translateX(3px);
    }
    
    .nav-link:hover i {
        color: #4eacfd!important;
    }
    
    .collapse-item {
        transition: all 0.2s;
    }
    
    .collapse-item:hover {
        background-color: rgba(255,255,255,0.1)!important;
        border-left: 2px solid #4eacfd!important;
        padding-left: 1.2rem!important;
    }
    
    .sidebar-brand {
        transition: all 0.3s;
    }
    
    .sidebar-brand:hover {
        transform: scale(1.05);
    }
    
    .fas.fa-chevron-right {
        transition: transform 0.2s;
    }
    
    .nav-link:not(.collapsed) .fa-chevron-right {
        transform: rotate(90deg);
    }
</style>