<?php
// Sesión para mensajes
session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

// Datos de la página
$tituloPagina = 'Ver solicitudes';
$paginaActiva = 'consulta';

// Valores iniciales
$solicitudes = [];
$errorConsulta = '';

// Mensajes de otras páginas
$mensaje = $_SESSION['mensaje'] ?? null;
$mensajeError = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['mensaje_error']);

try {
    $pdo = obtenerConexion();

    // Consulta de registros
    $sql = 'SELECT id_solicitud, nombre_solicitante, correo, area, descripcion,
                   prioridad, estado, fecha_creacion, comentario_resolucion, fecha_resolucion
            FROM solicitudes
            ORDER BY fecha_creacion DESC, id_solicitud DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll();
} catch (RuntimeException | PDOException $e) {
    // Detalle solo en el registro
    error_log('Error al consultar solicitudes: ' . $e->getMessage());
    $errorConsulta = 'No se pudieron cargar las solicitudes. Inténtalo de nuevo más tarde.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/includes/head.php'; ?>
    <title><?php echo escapar($tituloPagina); ?> | Sistema de solicitudes</title>
</head>
<body>
    <div class="layout">
        <?php require __DIR__ . '/includes/menu.php'; ?>

        <div class="content">
            <?php require __DIR__ . '/includes/header.php'; ?>

            <main class="main">
                <section class="panel panel--ancho">
                    <h2 class="panel__title">Solicitudes registradas</h2>

                    <?php if ($mensaje !== null): ?>
                        <div class="alerta alerta--exito" role="status"><?php echo escapar($mensaje); ?></div>
                    <?php endif; ?>

                    <?php if ($mensajeError !== null): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($mensajeError); ?></div>
                    <?php endif; ?>

                    <?php if ($errorConsulta !== ''): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($errorConsulta); ?></div>
                    <?php elseif (empty($solicitudes)): ?>
                        <p class="panel__text">Todavía no hay solicitudes registradas.</p>
                        <p class="panel__text"><a class="enlace" href="registro.php">Registrar la primera solicitud</a></p>
                    <?php else: ?>
                        <p class="panel__text">Total: <strong><?php echo count($solicitudes); ?></strong> solicitudes</p>

                        <div class="tabla-contenedor">
                            <table class="tabla">
                                <thead>
                                    <tr>
                                        <th>N.º</th>
                                        <th>Solicitante</th>
                                        <th>Correo</th>
                                        <th>Área</th>
                                        <th>Descripción</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Resolución</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($solicitudes as $fila): ?>
                                        <tr>
                                            <td><?php echo (int) $fila['id_solicitud']; ?></td>
                                            <td><?php echo escapar($fila['nombre_solicitante']); ?></td>
                                            <td><?php echo escapar($fila['correo']); ?></td>
                                            <td><?php echo escapar($fila['area']); ?></td>
                                            <td class="tabla__descripcion"><?php echo escapar($fila['descripcion']); ?></td>
                                            <td>
                                                <span class="etiqueta etiqueta--<?php echo escapar(claseEtiqueta($fila['prioridad'])); ?>">
                                                    <?php echo escapar($fila['prioridad']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="etiqueta etiqueta--<?php echo escapar(claseEtiqueta($fila['estado'])); ?>">
                                                    <?php echo escapar($fila['estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo escapar(formatearFecha($fila['fecha_creacion'])); ?></td>
                                            <td class="tabla__descripcion">
                                                <?php if ($fila['comentario_resolucion'] !== null): ?>
                                                    <span class="resolucion__fecha"><?php echo escapar(formatearFecha((string) ($fila['fecha_resolucion'] ?? ''))); ?></span>
                                                    <?php echo escapar($fila['comentario_resolucion']); ?>
                                                <?php else: ?>
                                                    &mdash;
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (in_array($fila['estado'], ['Pendiente', 'En proceso'], true)): ?>
                                                    <a class="btn btn--pequeno" href="resolver.php?id=<?php echo (int) $fila['id_solicitud']; ?>">Resolver</a>
                                                <?php else: ?>
                                                    &mdash;
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
</body>
</html>