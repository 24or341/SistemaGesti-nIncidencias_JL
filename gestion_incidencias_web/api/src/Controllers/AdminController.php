<?php
    namespace App\Controllers;

    use App\Services\AdminService;
    use App\Core\Response;
    use Exception;

    class AdminController
    {
        public static function login(array $data): void
        {
            if (empty($data['email']) || empty($data['password'])) {
                Response::error("Email y contraseña son requeridos", 422);
            }

            $admin = AdminService::login($data['email'], $data['password']);
            if (!$admin) {
                Response::error("Credenciales incorrectas", 401);
            }

            session_start();
            $_SESSION['usuario_id']     = $admin['id'];
            $_SESSION['usuario_nombre'] = $admin['nombre'];

            Response::success([
                'id'     => $admin['id'],
                'nombre' => $admin['nombre'],
                'role'   => 'administrador',
                'token'  => $admin['token']
            ], "Inicio de sesión exitoso");
        }

        public static function register(array $data): void
        {
            if (
                empty($data['nombre']) ||
                empty($data['apellido']) ||
                empty($data['email']) ||
                empty($data['password'])
            ) {
                Response::error("Todos los campos son obligatorios", 422);
            }

            $idNuevo = AdminService::registerRaw(
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $data['password']
            );

            if (!$idNuevo) {
                Response::error("Error al registrar el usuario", 500);
            }

            Response::success(
                ['id' => $idNuevo],
                "Usuario administrador registrado correctamente"
            );
        }

        public static function loginRaw(string $email, string $password): array
        {
            $admin = AdminService::login($email, $password);
            if (!$admin) {
                throw new Exception("No admin");
            }
            return [
                'id'       => $admin['id'],
                'nombre'   => $admin['nombre'],
                'apellido' => $admin['apellido'] ?? '',
                'email'    => $admin['email'],
                'token'    => $admin['token'],
                'role'     => 'administrador'
            ];
        }

        public static function registerRaw(array $data): int
        {
            if (
                empty($data['nombre'])   ||
                empty($data['apellido']) ||
                empty($data['email'])    ||
                empty($data['password'])
            ) {
                throw new Exception("Todos los campos son obligatorios", 422);
            }

            return AdminService::registerRaw(
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $data['password']
            );
        }
    }
?>