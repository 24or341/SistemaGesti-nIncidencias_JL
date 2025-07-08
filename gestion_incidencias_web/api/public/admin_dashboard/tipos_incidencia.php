<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Auth;
    use App\Core\Response;
    use App\Core\Database;

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

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT id, nombre FROM tipo_incidencia ORDER BY nombre ASC");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::success($tipos, "Tipos de incidencia obtenidos correctamente");
    } catch (Exception $e) {
        Response::error("Error al obtener tipos de incidencia: " . $e->getMessage());
    }
?>