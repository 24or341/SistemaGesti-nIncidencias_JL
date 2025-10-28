<?php
// Configuración del archivo prioridad.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\PrioridadController;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error("Método no permitido", 405);
    return;
}

// 1) Extraer y validar token no nulo/ vacío
$maybeToken = Auth::extractBearerFromServer();
if (!is_string($maybeToken) || $maybeToken === '') {
    Response::error("Token requerido", 401);
    return;
}
/** @var string $token */
$token = $maybeToken;

// 2) Verificar token y tipar $user
try {
    $verified = Auth::verificarToken($token);
} catch (\Exception $e) {
    Response::error("Token inválido", 401);
    return;
}

/** @var array{user_id:mixed, role:string, email?:string|null, nombre?:string|null, iat?:int, exp?:int} $user */
$user = $verified;

// 3) Chequeo de rol
if ($user['role'] !== 'administrador') {
    Response::error("Permiso denegado", 403);
    return;
}

// 4) Listar prioridades
try {
    PrioridadController::listar();
} catch (\Throwable $e) {
    Response::error("Error interno: " . $e->getMessage(), 500);
    return;
}