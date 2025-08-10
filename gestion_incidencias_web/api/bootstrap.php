<?php
// Configuración del archivo bootstrap.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto

require_once __DIR__ . '/../vendor/autoload.php'; // Carga las dependencias de Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Carga las variables de entorno
$dotenv->load(); // Carga las variables de entorno

if (($_ENV['APP_ENV'] ?? '') === 'local' && ($_ENV['APP_DEBUG'] ?? '') === 'true') { // Verifica si está en entorno local y en modo debug
    ini_set('display_errors', '1'); // Muestra los errores
    ini_set('display_startup_errors', '1'); // Muestra los errores de inicio
    error_reporting(E_ALL); // Reporta todos los errores
} else { // Si no está en entorno local o en modo debug
    error_reporting(0); // No reporta errores
}

\App\Core\Auth::init(); // Inicializa el sistema de autenticación
