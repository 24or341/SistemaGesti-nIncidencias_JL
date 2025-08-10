<?php
declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\IncidenciaController;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

if (!in_array($user['role'] ?? '', ['empleado', 'administrador'], true)) {
    Response::error("Permiso denegado", 403);
}

$inputRaw = file_get_contents("php://input");
$data = json_decode($inputRaw, true);
if (!is_array($data)) {
    Response::error("JSON inválido", 400);
}

if (!isset($data['incidencia_id'], $data['nuevo_estado'])) {
    Response::error("Datos incompletos", 422);
}

try {
    IncidenciaController::actualizarEstado($data);
} catch (\Throwable $e) {
    Response::error("Error interno: " . $e->getMessage(), 500);
}
