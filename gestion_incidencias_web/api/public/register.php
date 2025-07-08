<?php
    require_once __DIR__ . '/../bootstrap.php';
    use App\Controllers\AdminController;
    use App\Controllers\EmpleadoController;
    use App\Core\Response;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $role = $data['role'] ?? null;
    if ($role === 'administrador') {
        try {
            $id = AdminController::registerRaw($data);
            Response::success(['id'=>$id], "Administrador registrado correctamente");
        } catch (\PDOException $e) {
            if ($e->getCode() === '23505') {
                Response::error("El correo ya está registrado", 409);
            }
            Response::error($e->getMessage(), 500);
        }
    } elseif ($role === 'empleado') {
        try {
            $id = EmpleadoController::registerRaw($data);
            Response::success(['id'=>$id], "Empleado registrado correctamente");
        } catch (\PDOException $e) {
            if ($e->getCode() === '23505') {
                Response::error("El correo ya está registrado", 409);
            }
            Response::error($e->getMessage(), 500);
        }
    } else {
        Response::error("Debe indicar un campo 'role' válido", 422);
    }
?>