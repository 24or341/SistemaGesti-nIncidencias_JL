<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Auth;
    use App\Core\Response;
    use App\Controllers\IncidenciaController;

    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer\s+(.+)$/', $hdr, $m)) {
        Response::error("Token requerido", 401);
    }

    try {
        $user = Auth::verificarToken($m[1]);
    } catch (\Exception $e) {
        Response::error("Token inválido", 401);
    }

    if (($user['role'] ?? '') !== 'administrador') {
        Response::error("Permiso denegado", 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        Response::error("Método no permitido", 405);
    }

    IncidenciaController::listar();
?>
