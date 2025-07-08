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
    if (($user['role'] ?? '') !== 'empleado') {
        Response::error("Permiso denegado", 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['incidencia_id'], $data['nuevo_estado'])) {
        Response::error("Datos incompletos", 422);
    }

    IncidenciaController::actualizarEstado($data);
?>