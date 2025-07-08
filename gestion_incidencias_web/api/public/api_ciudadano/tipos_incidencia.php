<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Controllers\CiudadanoController;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        \App\Core\Response::error("Método no permitido", 405);
    }

    CiudadanoController::obtenerTiposIncidencia();
?>