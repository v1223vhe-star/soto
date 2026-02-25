// Configuración del tiempo de inactividad (en milisegundos)
const INACTIVITY_TIMEOUT = 10 * 1000; // 30 minutos

let inactivityTimer;

function resetInactivityTimer() {
    // Limpiar el temporizador existente
    clearTimeout(inactivityTimer);
    
    // Configurar un nuevo temporizador
    inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
}

function logoutUser() {
    // Mostrar alerta de que la sesión está por expirar
    Swal.fire({
        title: 'Sesión inactiva',
        text: 'Serás redirigido al login por inactividad',
        icon: 'warning',
        confirmButtonText: 'Entendido',
        allowOutsideClick: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {
            // Redirigir al logout
            window.location.href = 'logout.php';
        }
    });
}

// Eventos que resetearán el temporizador
['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetInactivityTimer);
});

// Iniciar el temporizador al cargar la página
resetInactivityTimer();