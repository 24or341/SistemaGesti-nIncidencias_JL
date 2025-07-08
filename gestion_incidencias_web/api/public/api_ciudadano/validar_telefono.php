<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Response;
    use App\Controllers\CiudadanoController;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);

    if (!is_array($input) || !isset($input['celular']) || empty($input['celular'])) {
        Response::error("Número de teléfono requerido", 422);
    }

    $celular = $input['celular'];

    CiudadanoController::validarTelefono($celular);
?>