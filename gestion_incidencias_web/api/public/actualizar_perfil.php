<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\EmpleadoController;
use App\Core\Auth;
use App\Core\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    Response::error("Método no permitido", 405);
}

$input = json_decode(file_get_contents("php://input"), true);

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$datosToken = Auth::verificarToken($token);

if (!$datosToken) {
    Response::error("Token inválido", 401);
}

$role   = $datosToken['role'] ?? '';
$userId = $datosToken['admin_id'] ?? $datosToken['id'] ?? null;

if (!$userId || !in_array($role, ['administrador', 'empleado'])) {
    Response::error("Token no autorizado o rol inválido", 403);
}

// Campos obligatorios
$nombre   = trim($input['nombre'] ?? '');
$apellido = trim($input['apellido'] ?? '');
$correo   = trim($input['email'] ?? '');
$dni      = trim($input['dni'] ?? '');

if ($nombre === '' || $apellido === '' || $correo === '') {
    Response::error("Nombre, apellido y correo son obligatorios", 422);
}

try {
    if ($role === 'administrador') {
        AdminController::actualizarPerfil($userId, $nombre, $apellido, $correo, $dni);
    } else {
        EmpleadoController::actualizarPerfil($userId, $nombre, $apellido, $correo, $dni);
    }

    Response::success(null, "Perfil actualizado correctamente");
} catch (\PDOException $e) {
    if ($e->getCode() === '23505') {
        Response::error("El correo ya está en uso", 409);
    }
    Response::error("Error al actualizar perfil: " . $e->getMessage(), 500);
}
