<?php
// Sesión para mensajes
session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

// Datos de la página
$tituloPagina = 'Nueva solicitud';
$paginaActiva = 'registro';

// Opciones permitidas
$areas = ['Administración', 'Atención al cliente', 'Sistemas', 'Taquilla', 'Otra'];
$prioridades = ['Alta', 'Media', 'Baja'];

// Valores iniciales
$datos = [
    'nombre' => '',
    'correo' => '',
    'area' => '',
    'prioridad' => 'Media',
    'descripcion' => '',
];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos recibidos
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = valorPost($campo);
    }

    // Validación en servidor
    $largoNombre = mb_strlen($datos['nombre']);
    if ($largoNombre < 3 || $largoNombre > 100) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }

    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($datos['correo']) > 120) {
        $errores['correo'] = 'Ingresa un correo válido.';
    }

    if (!in_array($datos['area'], $areas, true)) {
        $errores['area'] = 'Selecciona un área de la lista.';
    }

    if (!in_array($datos['prioridad'], $prioridades, true)) {
        $errores['prioridad'] = 'Selecciona una prioridad válida.';
    }

    $largoDescripcion = mb_strlen($datos['descripcion']);
    if ($largoDescripcion < 10 || $largoDescripcion > 1000) {
        $errores['descripcion'] = 'La descripción debe tener entre 10 y 1000 caracteres.';
    }

    // Inserción en la base
    if (empty($errores)) {
        try {
            $pdo = obtenerConexion();

            // Sentencia preparada
            $sql = 'INSERT INTO solicitudes (nombre_solicitante, correo, area, descripcion, prioridad)
                    VALUES (:nombre, :correo, :area, :descripcion, :prioridad)';
            $stmt = $pdo->prepare($sql);

            // Enlace de parámetros
            $stmt->bindValue(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindValue(':area', $datos['area'], PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':prioridad', $datos['prioridad'], PDO::PARAM_STR);

            // Ejecución
            $stmt->execute();

            // Mensaje y redirección
            $_SESSION['mensaje'] = 'Solicitud registrada correctamente.';
            header('Location: registro.php');
            exit;
        } catch (RuntimeException | PDOException $e) {
            // Detalle solo en el registro
            error_log('Error al guardar solicitud: ' . $e->getMessage());
            $errores['general'] = 'No se pudo guardar la solicitud. Inténtalo de nuevo más tarde.';
        }
    }
}

// Mensaje de confirmación
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);
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
                    <h2 class="panel__title">Registro de solicitudes</h2>

                    <?php if ($mensaje !== null): ?>
                        <div class="alerta alerta--exito" role="status"><?php echo escapar($mensaje); ?></div>
                    <?php endif; ?>

                    <?php if (isset($errores['general'])): ?>
                        <div class="alerta alerta--error" role="alert"><?php echo escapar($errores['general']); ?></div>
                    <?php endif; ?>

                    <form class="formulario" id="form-solicitud" method="post" action="registro.php" novalidate>
                        <div class="campo">
                            <label class="campo__label" for="nombre">Nombre del solicitante</label>
                            <input class="campo__input<?php echo isset($errores['nombre']) ? ' campo__input--error' : ''; ?>"
                                   type="text" id="nombre" name="nombre" maxlength="100"
                                   value="<?php echo escapar($datos['nombre']); ?>">
                            <span class="campo__error"><?php echo escapar($errores['nombre'] ?? ''); ?></span>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="correo">Correo electrónico</label>
                            <input class="campo__input<?php echo isset($errores['correo']) ? ' campo__input--error' : ''; ?>"
                                   type="email" id="correo" name="correo" maxlength="120"
                                   value="<?php echo escapar($datos['correo']); ?>">
                            <span class="campo__error"><?php echo escapar($errores['correo'] ?? ''); ?></span>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="area">Área</label>
                            <select class="campo__input<?php echo isset($errores['area']) ? ' campo__input--error' : ''; ?>"
                                    id="area" name="area">
                                <option value="">Selecciona un área</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo escapar($area); ?>"<?php echo $datos['area'] === $area ? ' selected' : ''; ?>>
                                        <?php echo escapar($area); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="campo__error"><?php echo escapar($errores['area'] ?? ''); ?></span>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="prioridad">Prioridad</label>
                            <select class="campo__input<?php echo isset($errores['prioridad']) ? ' campo__input--error' : ''; ?>"
                                    id="prioridad" name="prioridad">
                                <?php foreach ($prioridades as $prioridad): ?>
                                    <option value="<?php echo escapar($prioridad); ?>"<?php echo $datos['prioridad'] === $prioridad ? ' selected' : ''; ?>>
                                        <?php echo escapar($prioridad); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="campo__error"><?php echo escapar($errores['prioridad'] ?? ''); ?></span>
                        </div>

                        <div class="campo campo--completo">
                            <label class="campo__label" for="descripcion">Descripción del problema</label>
                            <textarea class="campo__input<?php echo isset($errores['descripcion']) ? ' campo__input--error' : ''; ?>"
                                      id="descripcion" name="descripcion" rows="5" maxlength="1000"><?php echo escapar($datos['descripcion']); ?></textarea>
                            <span class="campo__error"><?php echo escapar($errores['descripcion'] ?? ''); ?></span>
                        </div>

                        <div class="campo--completo">
                            <button class="btn" type="submit">Registrar solicitud</button>
                        </div>
                    </form>
                </section>
            </main>

            <?php require __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="assets/js/menu.js"></script>
    <script src="assets/js/formulario.js"></script>
</body>
</html>