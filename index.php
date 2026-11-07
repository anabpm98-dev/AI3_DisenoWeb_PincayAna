<?php
// Datos de la página
$tituloPagina = 'Inicio';
$paginaActiva = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/includes/head.php'; ?>
    <title><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?> | Sistema de solicitudes</title>
</head>    
    
<body>
    <div class="layout">
        <?php require __DIR__ . '/includes/menu.php'; ?>

        <div class="content">
            <?php require __DIR__ . '/includes/header.php'; ?>

            <main class="main">
                <section class="panel">
                    <h2 class="panel__title">Bienvenida</h2>
                    <p class="panel__text">
                        Sistema para registrar y consultar solicitudes de soporte técnico.
                        Usa el menú de la izquierda o los accesos rápidos para navegar.
                    </p>
                </section>

                <section class="accesos">
                    <a class="acceso" href="registro.php">
                        <h3 class="acceso__title">Nueva solicitud</h3>
                        <p>Registrar una solicitud de soporte técnico.</p>
                    </a>
                    <a class="acceso" href="consulta.php">
                        <h3 class="acceso__title">Ver solicitudes</h3>
                        <p>Consultar las solicitudes registradas.</p>
                    </a>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
</body>
</html>