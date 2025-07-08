<?php
    namespace App\Controllers;

    use App\Services\EmpleadoService;
    use App\Core\Response;
    use App\Core\Auth;
    use Exception;

    class EmpleadoController
    {
        public static function registrar(array $data): void
        {
            if (
                empty($data['nombre']) ||
                empty($data['apellido']) ||
                empty($data['dni']) ||
                empty($data['email']) ||
                empty($data['password'])
            ) {
                Response::error("Todos los campos son obligatorios", 422);
            }

            $adminId   = null;

            $idNuevo = EmpleadoService::registerRaw(
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['email'],
                $data['password'],
                $adminId
            );
            
            if (!$idNuevo) {
                Response::error("Error al registrar el empleado", 500);
            }

            Response::success(
                ['id' => $idNuevo],
                "Empleado registrado correctamente"
            );
        }

        public static function login(array $data): void
        {
            if (empty($data['email']) || empty($data['password'])) {
                Response::error("Correo y contraseña obligatorios", 422);
            }

            $resp = EmpleadoService::loginRaw($data['email'], $data['password']);
            if (!$resp) {
                Response::error("Credenciales inválidas", 401);
            }

            session_start();
            $_SESSION['usuario_id']     = $resp['id'];
            $_SESSION['usuario_nombre'] = $resp['nombre'];

            Response::success([
                'id'     => $resp['id'],
                'nombre' => $resp['nombre'],
                'role'   => 'empleado',
                'token'  => $resp['token']
            ], "Login exitoso");
        }

        public static function loginRaw(string $email, string $password): ?array
        {
            $emp = EmpleadoService::loginRaw($email, $password);
            if (!$emp) {
                return null;
            }
            return [
                'id'       => $emp['id'],
                'nombre'   => $emp['nombre'],
                'apellido' => $emp['apellido'],
                'email'    => $emp['email'],
                'role'     => 'empleado',
                'token'    => $emp['token']
            ];
        }

        public static function registerRaw(array $data): int
        {
            if (
                empty($data['nombre'])   ||
                empty($data['apellido']) ||
                empty($data['dni'])      ||
                empty($data['email'])    ||
                empty($data['password'])
            ) {
                throw new Exception("Todos los campos son obligatorios (incluye dni)", 422);
            }

            if (!isset($data['creado_por']) && !empty($_SESSION['usuario_id'])) {
                $data['creado_por'] = $_SESSION['usuario_id'];
            }

            return EmpleadoService::registerRaw(
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['email'],
                $data['password'],
                $data['creado_por'] ?? null
            );
        }
        
        public static function listar(): void
        {
            try {
                $lista = EmpleadoService::obtenerTodos();
                Response::success($lista, "Empleados encontrados");
            } catch (Exception $e) {
                Response::error("Error al obtener empleados: " . $e->getMessage(), 500);
            }
        }

        public static function validarAsignacionUnica(array $data): void
        {
            $empleadoId   = (int) $data['empleado_id'];
            $incidenciaId = (int) $data['incidencia_id'];

            if (\App\Services\EmpleadoService::yaAsignado($empleadoId, $incidenciaId)) {
                \App\Core\Response::error("La incidencia ya está asignada a este empleado", 409);
            }
        }
    }
?>