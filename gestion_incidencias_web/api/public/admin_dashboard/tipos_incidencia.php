<?php
declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\PrioridadController; // not used but controller structure exists
use App\Core\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Método no permitido', 405);
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
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT id, nombre FROM tipo_incidencia ORDER BY nombre ASC");
    if ($stmt === false) {
        throw new \RuntimeException("Error al obtener tipos");
    }
    $tipos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    Response::success($tipos, "Tipos de incidencia obtenidos correctamente");
} catch (\Throwable $e) {
    Response::error("Error al obtener tipos de incidencia: " . $e->getMessage(), 500);
}
