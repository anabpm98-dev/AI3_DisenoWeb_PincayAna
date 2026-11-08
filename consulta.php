<?php
// Sesión para mensajes
session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

// Datos de la página
$tituloPagina = 'Ver solicitudes';
$paginaActiva = 'consulta';

// Opciones permitidas
$prioridades = ['Alta', 'Media', 'Baja'];
$estados = ['Pendiente', 'Finalizada', 'Rechazada'];

// Valores iniciales
$solicitudes = [];
$totalGeneral = 0;
$errorConsulta = '';
$avisoFiltros = '';

// Mensajes de otras páginas
$mensaje = $_SESSION['mensaje'] ?? null;
$mensajeError = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['mensaje_error']);

// Filtros recibidos
$filtros = [
    'prioridad' => valorGet('prioridad'),
    'estado' => valorGet('estado'),
    'desde' => valorGet('desde'),
    'hasta' => valorGet('hasta'),
];

// Validación de filtros
if (!in_array($filtros['prioridad'], $prioridades, true)) {
    $filtros['prioridad'] = '';
}

if (!in_array($filtros['estado'], $estados, true)) {
    $filtros['estado'] = '';
}

foreach (['desde', 'hasta'] as $campo) {
    if ($filtros[$campo] !== '' && !fechaValida($filtros[$campo])) {
        $filtros[$campo] = '';
        $avisoFiltros = 'Alguna fecha no era válida y se ignoró.';
    }
}

if ($filtros['desde'] !== '' && $filtros['hasta'] !== '' && $filtros['desde'] > $filtros['hasta']) {
    $filtros['desde'] = '';
    $filtros['hasta'] = '';
    $avisoFiltros = 'La fecha "Desde" no puede ser posterior a "Hasta"; se ignoraron las fechas.';
}

// Saber si hay filtros activos
$hayFiltros = implode('', $filtros) !== '';

try {
    $pdo = obtenerConexion();

    // Total sin filtros
    $stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM solicitudes');
    $stmtTotal->execute();
    $totalGeneral = (int) $stmtTotal->fetchColumn();

    // Condiciones según filtros
    $condiciones = [];
    $parametros = [];

    if ($filtros['prioridad'] !== '') {
        $condiciones[] = 'prioridad = :prioridad';
        $parametros[':prioridad'] = $filtros['prioridad'];
    }

    if ($filtros['estado'] !== '') {
        $condiciones[] = 'estado = :estado';
        $parametros[':estado'] = $filtros['estado'];
    }

    if ($filtros['desde'] !== '') {
        $condiciones[] = 'fecha_creacion >= :desde';
        $parametros[':desde'] = $filtros['desde'] . ' 00:00:00';
    }

    if ($filtros['hasta'] !== '') {
        $condiciones[] = 'fecha_creacion <= :hasta';
        $parametros[':hasta'] = $filtros['hasta'] . ' 23:59:59';
    }

    // Consulta de registros
    $sql = 'SELECT id_solicitud, nombre_solicitante, correo, area, descripcion,
                   prioridad, estado, fecha_creacion, comentario_resolucion, fecha_resolucion
            FROM solicitudes';

    if (!empty($condiciones)) {
        $sql .= ' WHERE ' . implode(' AND ', $condiciones);
    }

    $sql .= ' ORDER BY fecha_creacion DESC, id_solicitud DESC';

    // Sentencia preparada
    $stmt = $pdo->prepare($sql);

    // Enlace de parámetros
    foreach ($parametros as $nombre => $valor) {
        $stmt->bindValue($nombre, $valor, PDO::PARAM_STR);
    }

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

                    <?php if ($avisoFiltros !== ''): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($avisoFiltros); ?></div>
                    <?php endif; ?>

                    <?php if ($errorConsulta !== ''): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($errorConsulta); ?></div>
                    <?php elseif ($totalGeneral === 0): ?>
                        <p class="panel__text">Todavía no hay solicitudes registradas.</p>
                        <p class="panel__text"><a class="enlace" href="registro.php">Registrar la primera solicitud</a></p>
                    <?php else: ?>
                        <form class="filtros" method="get" action="consulta.php">
                            <div class="campo">
                                <label class="campo__label" for="filtro-prioridad">Prioridad</label>
                                <select class="campo__input" id="filtro-prioridad" name="prioridad">
                                    <option value="">Todas</option>
                                    <?php foreach ($prioridades as $prioridad): ?>
                                        <option value="<?php echo escapar($prioridad); ?>"<?php echo $filtros['prioridad'] === $prioridad ? ' selected' : ''; ?>>
                                            <?php echo escapar($prioridad); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="campo">
                                <label class="campo__label" for="filtro-estado">Estado</label>
                                <select class="campo__input" id="filtro-estado" name="estado">
                                    <option value="">Todos</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?php echo escapar($estado); ?>"<?php echo $filtros['estado'] === $estado ? ' selected' : ''; ?>>
                                            <?php echo escapar($estado); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="campo">
                                <label class="campo__label" for="filtro-desde">Desde</label>
                                <input class="campo__input" type="date" id="filtro-desde" name="desde"
                                       value="<?php echo escapar($filtros['desde']); ?>">
                            </div>

                            <div class="campo">
                                <label class="campo__label" for="filtro-hasta">Hasta</label>
                                <input class="campo__input" type="date" id="filtro-hasta" name="hasta"
                                       value="<?php echo escapar($filtros['hasta']); ?>">
                            </div>

                            <div class="filtros__acciones">
                                <button class="btn" type="submit">Filtrar</button>
                                <a class="btn btn--secundario" href="consulta.php">Limpiar</a>
                            </div>
                        </form>

                        <?php if ($hayFiltros): ?>
                            <p class="panel__text">Mostrando <strong><?php echo count($solicitudes); ?></strong> de <?php echo $totalGeneral; ?> solicitudes</p>
                        <?php else: ?>
                            <p class="panel__text">Total: <strong><?php echo $totalGeneral; ?></strong> solicitudes</p>
                        <?php endif; ?>

                        <?php if (empty($solicitudes)): ?>
                            <p class="panel__text">Ninguna solicitud coincide con los filtros.</p>
                        <?php else: ?>
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
                                                    <?php if (in_array($fila['estado'], ['Pendiente'], true)): ?>
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
                    <?php endif; ?>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
</body>
</html>