<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Auth;
    use App\Core\Response;
    use App\Controllers\IncidenciaController;

    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s+(.*)/', $token, $match)) {
        Response::error("Token no proporcionado", 401);
    }

    try {
        $user = Auth::verificarToken($match[1]);
    } catch (Exception $e) {
        Response::error("Token inválido", 401);
    }

    if (($user['role'] ?? '') !== 'administrador') {
        Response::error("Acceso denegado", 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    IncidenciaController::programarFecha();
?>