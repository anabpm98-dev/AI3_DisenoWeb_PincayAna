<?php
// Estado del grupo de solicitudes
$paginaActiva = $paginaActiva ?? '';
$grupoAbierto = in_array($paginaActiva, ['registro', 'consulta'], true);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
        <h2 class="sidebar__title">Soporte técnico</h2>
        <p class="sidebar__subtitle">Sistema de solicitudes</p>
    </div>

    <nav aria-label="Menú principal">
        <ul class="menu">
            <li class="menu__item">
                <a class="menu__link<?php echo $paginaActiva === 'inicio' ? ' menu__link--activo' : ''; ?>" href="index.php">Inicio</a>
            </li>

            <li class="menu__item<?php echo $grupoAbierto ? ' menu__item--open' : ''; ?>">
                <button class="menu__group-toggle" type="button" aria-expanded="<?php echo $grupoAbierto ? 'true' : 'false'; ?>">
                    Solicitudes <span class="menu__arrow">&#9662;</span>
                </button>
                <ul class="submenu">
                    <li><a class="menu__link<?php echo $paginaActiva === 'registro' ? ' menu__link--activo' : ''; ?>" href="registro.php">Nueva solicitud</a></li>
                    <li><a class="menu__link<?php echo $paginaActiva === 'consulta' ? ' menu__link--activo' : ''; ?>" href="consulta.php">Ver solicitudes</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
<div class="overlay" id="overlay"></div>