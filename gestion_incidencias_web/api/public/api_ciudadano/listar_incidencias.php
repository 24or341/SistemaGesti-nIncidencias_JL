<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Response;
    use App\Controllers\CiudadanoController;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        Response::error("Método no permitido", 405);
    }

    if (!isset($_GET['ciudadano_id'])) {
        Response::error("ID del ciudadano requerido", 422);
    }

    $idCelular = (int)$_GET['ciudadano_id'];
    CiudadanoController::obtenerHistorialIncidencias($idCelular);
?>