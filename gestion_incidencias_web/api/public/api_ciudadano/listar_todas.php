<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Response;
    use App\Services\IncidenciaService;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        Response::error("Método no permitido", 405);
    }

    $data = IncidenciaService::obtenerTodas();
    Response::success($data, "Listado completo de incidencias");
?>