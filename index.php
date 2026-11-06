<?php
require_once __DIR__ . '/config/conexion.php';

// Comprobar la conexión
try {
    $pdo = obtenerConexion();
    $mensaje = 'Conexión a la base de datos establecida correctamente.';
} catch (RuntimeException $e) {
    $mensaje = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de solicitudes</title>
</head>
<body>
    <h1>Sistema de solicitudes de soporte técnico</h1>
    <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
</body>
</html>