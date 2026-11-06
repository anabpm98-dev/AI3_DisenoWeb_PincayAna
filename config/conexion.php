<?php
// Datos de acceso (XAMPP por defecto)
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_sistema_web');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function obtenerConexion(): PDO
{
    // Cadena de conexión
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    // Opciones de PDO
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        // Conexión a SQL
        return new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        // Error solo al registro del servidor
        error_log('Error de conexión: ' . $e->getMessage());

        // Mensaje genérico para el usuario
        throw new RuntimeException('No se pudo conectar con la base de datos.');
    }
}