// Formulario de solicitudes
const formulario = document.getElementById('form-solicitud');
const campos = formulario.querySelectorAll('input, select, textarea');
const PATRON_CORREO = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Mostrar u ocultar el error de un campo
function mostrarError(campo, mensaje) {
    const contenedor = campo.closest('.campo');
    contenedor.querySelector('.campo__error').textContent = mensaje;
    campo.classList.toggle('campo__input--error', mensaje !== '');
}

// Validar un campo
function validarCampo(campo) {
    const valor = campo.value.trim();
    let mensaje = '';

    if (campo.name === 'nombre' && valor.length < 3) {
        mensaje = 'El nombre debe tener al menos 3 caracteres.';
    } else if (campo.name === 'correo' && !PATRON_CORREO.test(valor)) {
        mensaje = 'Ingresa un correo válido.';
    } else if (campo.name === 'area' && valor === '') {
        mensaje = 'Selecciona un área.';
    } else if (campo.name === 'descripcion' && valor.length < 10) {
        mensaje = 'La descripción debe tener al menos 10 caracteres.';
    }

    mostrarError(campo, mensaje);
    return mensaje === '';
}

// Validar al salir de cada campo
campos.forEach(campo => {
    campo.addEventListener('blur', () => validarCampo(campo));
});

// Validar al enviar
formulario.addEventListener('submit', evento => {
    let todoValido = true;

    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            todoValido = false;
        }
    });

    if (!todoValido) {
        evento.preventDefault();
    }
});