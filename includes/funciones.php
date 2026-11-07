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