<?php
    require_once __DIR__ . '/../../bootstrap.php';

    use App\Core\Response;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        Response::error("No se recibió la imagen correctamente", 400);
    }

    $archivo = $_FILES['foto'];

    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidos)) {
        Response::error("Formato de imagen no permitido", 415);
    }

    $directorio = __DIR__ . '/../../../public/uploads/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = uniqid('foto_', true) . '.' . $extension;
    $rutaFinal = $directorio . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
        Response::error("Error al guardar la imagen", 500);
    }

    $url = '/proyecto-final-teamjl/gestion_incidencias_web/public/uploads/' . $nombreArchivo;

    Response::success(['foto_url' => $url], "Imagen subida correctamente");
?>