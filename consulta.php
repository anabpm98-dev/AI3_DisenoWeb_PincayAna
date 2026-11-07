<?php
// Datos de la página
$tituloPagina = 'Ver solicitudes';
$paginaActiva = 'consulta';
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
                    <h2 class="panel__title">Solicitudes registradas</h2>
                    <p class="panel__text">**Tabla de solicitudes.**</p>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
</body>
</html>