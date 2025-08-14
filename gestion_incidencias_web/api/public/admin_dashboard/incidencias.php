<?php
// Configuración del archivo incidencias.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Estricta
require_once __DIR__ . '/../../bootstrap.php'; // Bootstrap del sistema

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\IncidenciaController;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { // Verifica el método de la solicitud
    Response::error("Método no permitido", 405); // Método no permitido
} 

$token = Auth::extractBearerFromServer(); // Extrae el token del encabezado
if ($token === null) { // Verifica si el token es nulo
    Response::error("Token requerido", 401); // Token requerido
} 

try { // Verifica el token
    $user = Auth::verificarToken($token); // Verifica el token
} catch (\Exception $e) { // Captura la excepción
    Response::error("Token inválido", 401); // Token inválido
}

if (($user['role'] ?? '') !== 'administrador') { // Verifica el rol del usuario
    Response::error("Permiso denegado", 403); // Permiso denegado
}

try { // Intenta listar las incidencias
    IncidenciaController::listar(); // Lista las incidencias
} catch (\Throwable $e) { // Captura cualquier error
    Response::error("Error interno: " . $e->getMessage(), 500); // Error interno
}
