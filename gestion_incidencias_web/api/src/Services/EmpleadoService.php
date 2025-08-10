<?php
    declare(strict_types=1);
    namespace App\Services;

    use App\Repositories\EmpleadoRepository;
    use App\Core\Auth;

    class EmpleadoService
    {
        public static function obtenerTodos(): array
        {
            return EmpleadoRepository::obtenerTodos();
        }

        public static function registerRaw(
            string $nombre,
            string $apellido,
            string $dni,
            string $email,
            string $password
        ): int {
            if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $creadoPor = $_SESSION['usuario_id'] ?? null;

            return EmpleadoRepository::create([
                'nombre'     => $nombre,
                'apellido'   => $apellido,
                'dni'        => $dni,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'creado_por' => $creadoPor
            ]);
        }

        public static function loginRaw(string $email, string $password): ?array
        {
            $emp = EmpleadoRepository::obtenerPorEmail($email);
            if (!$emp || !isset($emp['password']) || !password_verify($password, (string)$emp['password'])) {
                return null;
            }

            $token = Auth::generarToken([
                'user_id' => $emp['id'],
                'nombre'  => $emp['nombre'],
                'email'   => $emp['email'],
                'role'    => 'empleado'
            ]);

            return [
                'id'       => $emp['id'],
                'nombre'   => $emp['nombre'],
                'apellido' => $emp['apellido'],
                'email'    => $emp['email'],
                'dni'      => $emp['dni'],
                'role'     => 'empleado',
                'token'    => $token
            ];
        }

        public static function yaAsignado(int $incidenciaId, int $empleadoId): bool
        {
            try {
                $pdo = \App\Core\Database::getInstance();
                $stmt = $pdo->prepare('
                    SELECT COUNT(*) 
                    FROM asignaciones 
                    WHERE incidencia_id = :incidencia 
                    AND empleado_id = :empleado
                ');
                $stmt->execute([
                    'incidencia' => $incidenciaId,
                    'empleado'   => $empleadoId
                ]);
                return $stmt->fetchColumn() > 0;
            } catch (\PDOException $e) {
                error_log('Error en yaAsignado: ' . $e->getMessage());
                return false;
            }
        }
        public static function actualizarPerfil(int $id, string $nombre, string $apellido, string $correo, ?string $dni): void {
            EmpleadoRepository::actualizar($id, $nombre, $apellido, $correo, $dni);
        }
    }
?>