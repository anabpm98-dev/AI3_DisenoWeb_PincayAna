<?php
// Sesión para mensajes
session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

// Datos de la página
$tituloPagina = 'Resolver solicitud';
$paginaActiva = 'consulta';

// Opciones permitidas
$resultados = ['Finalizada', 'Rechazada'];
$estadosAbiertos = ['Pendiente', 'En proceso'];

// Identificador recibido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    redirigirConMensaje('consulta.php', 'mensaje_error', 'La solicitud indicada no es válida.');
}

// Valores iniciales
$solicitud = null;
$errores = [];
$datos = ['resultado' => '', 'comentario' => ''];

try {
    $pdo = obtenerConexion();

    // Cargar solicitud
    $sql = 'SELECT id_solicitud, nombre_solicitante, correo, area, descripcion,
                   prioridad, estado, fecha_creacion
            FROM solicitudes
            WHERE id_solicitud = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $solicitud = $stmt->fetch() ?: null;
} catch (RuntimeException | PDOException $e) {
    // Detalle solo en el registro
    error_log('Error al cargar solicitud: ' . $e->getMessage());
    redirigirConMensaje('consulta.php', 'mensaje_error', 'No se pudo cargar la solicitud. Inténtalo de nuevo más tarde.');
}

// Solicitud inexistente
if ($solicitud === null) {
    redirigirConMensaje('consulta.php', 'mensaje_error', 'La solicitud indicada no existe.');
}

// Solicitud ya resuelta
if (!in_array($solicitud['estado'], $estadosAbiertos, true)) {
    redirigirConMensaje('consulta.php', 'mensaje_error', 'Esa solicitud ya fue resuelta.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos recibidos
    $datos['resultado'] = valorPost('resultado');
    $datos['comentario'] = valorPost('comentario');

    // Validación en servidor
    if (!in_array($datos['resultado'], $resultados, true)) {
        $errores['resultado'] = 'Selecciona si la solicitud se finaliza o se rechaza.';
    }

    $largoComentario = mb_strlen($datos['comentario']);
    if ($largoComentario < 10 || $largoComentario > 500) {
        $errores['comentario'] = 'El comentario debe tener entre 10 y 500 caracteres.';
    }

    // Actualización en la base
    if (empty($errores)) {
        try {
            // Sentencia preparada
            $sql = "UPDATE solicitudes
                    SET estado = :estado,
                        comentario_resolucion = :comentario,
                        fecha_resolucion = NOW()
                    WHERE id_solicitud = :id
                      AND estado IN ('Pendiente', 'En proceso')";
            $stmt = $pdo->prepare($sql);

            // Enlace de parámetros
            $stmt->bindValue(':estado', $datos['resultado'], PDO::PARAM_STR);
            $stmt->bindValue(':comentario', $datos['comentario'], PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            // Ejecución
            $stmt->execute();

            // Nadie fue actualizado
            if ($stmt->rowCount() === 0) {
                redirigirConMensaje('consulta.php', 'mensaje_error', 'No se pudo actualizar: la solicitud ya fue resuelta.');
            }

            redirigirConMensaje(
                'consulta.php',
                'mensaje',
                'Solicitud n.º ' . $id . ' marcada como ' . strtolower($datos['resultado']) . '.'
            );
        } catch (PDOException $e) {
            // Detalle solo en el registro
            error_log('Error al resolver solicitud: ' . $e->getMessage());
            $errores['general'] = 'No se pudo guardar la resolución. Inténtalo de nuevo más tarde.';
        }
    }
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
                <section class="panel">
                    <h2 class="panel__title">Solicitud n.º <?php echo (int) $solicitud['id_solicitud']; ?></h2>

                    <dl class="resumen">
                        <div><dt>Solicitante</dt><dd><?php echo escapar($solicitud['nombre_solicitante']); ?></dd></div>
                        <div><dt>Correo</dt><dd><?php echo escapar($solicitud['correo']); ?></dd></div>
                        <div><dt>Área</dt><dd><?php echo escapar($solicitud['area']); ?></dd></div>
                        <div><dt>Prioridad</dt><dd><?php echo escapar($solicitud['prioridad']); ?></dd></div>
                        <div><dt>Fecha</dt><dd><?php echo escapar(formatearFecha($solicitud['fecha_creacion'])); ?></dd></div>
                        <div class="resumen__completo"><dt>Descripción</dt><dd><?php echo escapar($solicitud['descripcion']); ?></dd></div>
                    </dl>
                </section>

                <section class="panel">
                    <h2 class="panel__title">Resolución</h2>

                    <?php if (isset($errores['general'])): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($errores['general']); ?></div>
                    <?php endif; ?>

                    <form class="formulario" method="post" action="resolver.php?id=<?php echo (int) $solicitud['id_solicitud']; ?>">
                        <div class="campo campo--completo">
                            <span class="campo__label">Resultado</span>
                            <div class="opciones">
                                <?php foreach ($resultados as $resultado): ?>
                                    <label class="opcion">
                                        <input type="radio" name="resultado" value="<?php echo escapar($resultado); ?>"<?php echo $datos['resultado'] === $resultado ? ' checked' : ''; ?> required>
                                        <?php echo escapar($resultado); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <span class="campo__error"><?php echo escapar($errores['resultado'] ?? ''); ?></span>
                        </div>

                        <div class="campo campo--completo">
                            <label class="campo__label" for="comentario">Comentario de resolución</label>
                            <textarea class="campo__input<?php echo isset($errores['comentario']) ? ' campo__input--error' : ''; ?>"
                                      id="comentario" name="comentario" rows="4" minlength="10" maxlength="500" required><?php echo escapar($datos['comentario']); ?></textarea>
                            <span class="campo__error"><?php echo escapar($errores['comentario'] ?? ''); ?></span>
                        </div>

                        <div class="campo--completo acciones">
                            <button class="btn" type="submit">Guardar resolución</button>
                            <a class="btn btn--secundario" href="consulta.php">Cancelar</a>
                        </div>
                    </form>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
</body>
</html>