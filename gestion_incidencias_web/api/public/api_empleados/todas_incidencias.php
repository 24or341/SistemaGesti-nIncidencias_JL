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

if (($user['role'] ?? '') !== 'administrador') {
    Response::error("Permiso denegado", 403);
}

try {
    IncidenciaController::listar();
} catch (\Throwable $e) {
    Response::error("Error interno: " . $e->getMessage(), 500);
}
