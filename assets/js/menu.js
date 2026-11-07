// Elementos del menú
const cuerpo = document.body;
const botonMenu = document.getElementById('menu-toggle');
const fondo = document.getElementById('overlay');
const botonesGrupo = document.querySelectorAll('.menu__group-toggle');
const consultaCelular = window.matchMedia('(max-width: 768px)');

// Saber si el menú está visible
function menuVisible() {
    return consultaCelular.matches
        ? cuerpo.classList.contains('menu-abierto')
        : !cuerpo.classList.contains('menu-cerrado');
}

// Actualizar estado accesible
function actualizarBoton() {
    botonMenu.setAttribute('aria-expanded', String(menuVisible()));
}

// Abrir y cerrar el menú lateral
botonMenu.addEventListener('click', () => {
    const clase = consultaCelular.matches ? 'menu-abierto' : 'menu-cerrado';
    cuerpo.classList.toggle(clase);
    actualizarBoton();
});

// Cerrar al tocar el fondo oscuro
fondo.addEventListener('click', () => {
    cuerpo.classList.remove('menu-abierto');
    actualizarBoton();
});

// Submenús desplegables
botonesGrupo.forEach(boton => {
    boton.addEventListener('click', () => {
        const item = boton.closest('.menu__item');
        const abierto = item.classList.toggle('menu__item--open');
        boton.setAttribute('aria-expanded', String(abierto));
    });
});

// Ajustar al cambiar el tamaño de la ventana
consultaCelular.addEventListener('change', () => {
    cuerpo.classList.remove('menu-abierto');
    actualizarBoton();
});

// Estado inicial
actualizarBoton();