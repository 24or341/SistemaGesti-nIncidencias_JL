<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Controllers\CiudadanoController;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        \App\Core\Response::error("Método no permitido", 405);
    }

    $data = [
        'tipo_id'     => $_POST['tipo_id'] ?? null,
        'latitud'     => $_POST['latitud'] ?? null,
        'longitud'    => $_POST['longitud'] ?? null,
        'descripcion' => $_POST['descripcion'] ?? null,
        'direccion'   => $_POST['direccion'] ?? null,
        'zona'        => $_POST['zona'] ?? null,
        'id_celular'  => $_POST['ciudadano_id'] ?? null,
    ];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $data['foto'] = base64_encode(file_get_contents($fileTmpPath));
    } else {
        $data['foto'] = null;
    }

    CiudadanoController::registrarIncidencia($data);
?>
