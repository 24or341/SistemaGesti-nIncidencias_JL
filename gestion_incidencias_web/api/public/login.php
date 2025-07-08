<?php
    require_once __DIR__ . '/../bootstrap.php';

    use App\Controllers\AdminController;
    use App\Controllers\EmpleadoController;
    use App\Core\Response;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Response::error("Método no permitido", 405);
    }

    $input    = json_decode(file_get_contents("php://input"), true);
    $email    = trim($input['email']    ?? '');
    $password = trim($input['password'] ?? '');

    if ($email === '' || $password === '') {
        Response::error("Email y contraseña requeridos", 422);
    }

    try {
        $admin = AdminController::loginRaw($email, $password);
        Response::success([
            'id'       => $admin['id'],
            'nombre'   => $admin['nombre'],
            'apellido' => $admin['apellido'],
            'role'     => 'administrador',
            'token'    => $admin['token']
        ], "Login administrador exitoso");
    } catch (\Exception $e) {
        try {
            $emp = EmpleadoController::loginRaw($email, $password);
            Response::success([
                'id'       => $emp['id'],
                'nombre'   => $emp['nombre'],
                'apellido' => $emp['apellido'],
                'role'     => 'empleado',
                'token'    => $emp['token']
            ], "Login empleado exitoso");
        } catch (\Exception $e2) {
            Response::error("Credenciales inválidas", 401);
        }
    }
?>