<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Response;
    use App\Controllers\CiudadanoController;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $celular = $input['celular'] ?? null;

    if (empty($celular)) {
        Response::error("Número de teléfono requerido", 422);
    }

    CiudadanoController::validarTelefono($celular);
?>