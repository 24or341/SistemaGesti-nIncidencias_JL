<?php
declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\IncidenciaController;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error("Método no permitido", 405);
}

$token = Auth::extractBearerFromServer();
if ($token === null) {
    Response::error("Token requerido", 401);
}

try {
    $user = Auth::verificarToken($token);
} catch (\Exception $e) {
    Response::error("Token inválido", 401);
}

if (($user['role'] ?? '') !== 'empleado') {
    Response::error("Permiso denegado", 403);
}

if (!isset($_GET['usuario_id'])) {
    Response::error("ID del usuario requerido", 422);
}

$empleadoId = (int) $_GET['usuario_id'];

try {
    IncidenciaController::obtenerPorEmpleado($empleadoId);
} catch (\Throwable $e) {
    Response::error("Error interno: " . $e->getMessage(), 500);
}
