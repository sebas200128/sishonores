// js/main.js
$(document).ready(function() {
    // Toggle sidebar en móvil
    $('#menuToggle').click(function() {
        $('.sidebar').toggleClass('active');
    });
    
    // Auto-cerrar alertas
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Función para mostrar alertas
function showAlert(message, type) {
    let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                     </div>`;
    $('#alertContainer').html(alertHtml);
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// Función para confirmar acciones
function confirmAction(message, callback) {
    if(confirm(message)) {
        callback();
    }
}

// Validar DNI
function validarDNI(dni) {
    const regex = /^[0-9]{8}$/;
    return regex.test(dni);
}

// Validar nota (0-20)
function validarNota(nota) {
    let n = parseFloat(nota);
    return !isNaN(n) && n >= 0 && n <= 20;
}

// Obtener letra de nota
function getLetraNota(nota) {
    if(nota >= 18) return 'AD';
    if(nota >= 14) return 'A';
    if(nota >= 11) return 'B';
    return 'C';
}

// Obtener color de nota
function getColorNota(nota) {
    if(nota >= 18) return 'success';
    if(nota >= 14) return 'warning';
    if(nota >= 11) return 'info';
    return 'danger';
}