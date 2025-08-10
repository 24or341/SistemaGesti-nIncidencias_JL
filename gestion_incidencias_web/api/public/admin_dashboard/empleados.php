<?php
// Configuración del archivo empleados.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../../bootstrap.php'; // Carga las dependencias

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\EmpleadoController;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { // Verifica el método de la solicitud
    Response::error("Método no permitido", 405); // Envía una respuesta de error
}

$token = Auth::extractBearerFromServer(); // Extrae el token de la cabecera
if ($token === null) { // Verifica si el token es nulo
    Response::error("Token requerido", 401); // Envía una respuesta de error
}

try { // Intenta verificar el token
    $user = Auth::verificarToken($token); // Verifica el token
} catch (\Exception $e) { // Captura cualquier excepción
    Response::error("Token inválido", 401); // Envía una respuesta de error
}

if (($user['role'] ?? '') !== 'administrador') { // Verifica si el usuario tiene el rol de administrador
    Response::error("Permiso denegado", 403); // Envía una respuesta de error
}

try { // Intenta listar los empleados
    EmpleadoController::listar(); // Llama al controlador para listar empleados
} catch (\Throwable $e) { // Captura cualquier excepción
    Response::error("Error interno: " . $e->getMessage(), 500); // Envía una respuesta de error
}
