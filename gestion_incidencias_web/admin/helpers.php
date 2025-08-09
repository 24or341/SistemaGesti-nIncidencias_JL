<?php
// Configuración del archivo helpers.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Activa tipado estricto.

/**
 * @param string               $view
 * @param array<string,mixed> $data
 * @return void
 */
function view(string $view, array $data = []): void // Renderiza una vista dentro del layout principal.
{
    extract($data); // Extrae variables del array $data para usarlas en la vista.
    ob_start(); // Inicia el almacenamiento en búfer de salida.
    require __DIR__ . "/views/{$view}.php"; // Incluye la vista específica.
    $content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer.
    require __DIR__ . "/views/layout.php"; // Incluye el layout principal que usa $content.
}

/**
 * @param string $path
 * @return string
 */
function url(string $path): string // Construye una URL completa para un path dado.
{
    return BASE_URL . $path; // Construye y devuelve la URL completa para el path dado.
}

/**
 * @param string               $view
 * @param array<string,mixed> $data
 * @return void
 */
function authView(string $view, array $data = []): void // Renderiza una vista de autenticación dentro del layout de auth.
{
    extract($data); // Extrae variables del array $data para usarlas en la vista.
    ob_start(); // Inicia el almacenamiento en búfer de salida.
    require __DIR__ . "/views/auth/{$view}.php"; // Incluye la vista específica de auth.
    $content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer.
    require __DIR__ . "/views/auth/layout.php"; // Incluye el layout de auth que usa $content.
}
