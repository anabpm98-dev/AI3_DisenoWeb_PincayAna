<?php
// Escapar texto para HTML
function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Leer un valor de POST limpio
function valorPost(string $clave): string
{
    $valor = $_POST[$clave] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

// Clase CSS según el valor
function claseEtiqueta(string $valor): string
{
    return strtolower(str_replace(' ', '-', $valor));
}

// Fecha en formato legible
function formatearFecha(string $fecha): string
{
    $marca = strtotime($fecha);
    return $marca === false ? '' : date('d/m/Y H:i', $marca);
}

// Mensaje y redirección
function redirigirConMensaje(string $destino, string $clave, string $mensaje): void
{
    $_SESSION[$clave] = $mensaje;
    header('Location: ' . $destino);
    exit;
}

// Leer un valor de GET limpio
function valorGet(string $clave): string
{
    $valor = $_GET[$clave] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

// Validar fecha AAAA-MM-DD
function fechaValida(string $fecha): bool
{
    $objeto = DateTime::createFromFormat('Y-m-d', $fecha);
    return $objeto !== false && $objeto->format('Y-m-d') === $fecha;
}