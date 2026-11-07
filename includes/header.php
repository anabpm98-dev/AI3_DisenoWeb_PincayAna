<?php
// Valor por defecto
$tituloPagina = $tituloPagina ?? 'Sistema de solicitudes';
?>
<header class="topbar">
    <button class="topbar__btn" id="menu-toggle" type="button" aria-label="Mostrar u ocultar el menú" aria-expanded="true">&#9776;</button>
    <h1 class="topbar__title"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h1>
</header>