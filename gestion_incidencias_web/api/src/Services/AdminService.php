<?php

    namespace App\Services;

    use App\Repositories\AdminRepository;
    use App\Core\Auth;

    class AdminService
    {
        public static function login(string $email, string $password): ?array
        {
            $admin = AdminRepository::obtenerPorEmail($email);

            if (!$admin || !password_verify($password, $admin['password'])) {
                return null;
            }

            $token = Auth::generarToken([
                'admin_id' => $admin['id'],
                'nombre'   => $admin['nombre'],
                'email'    => $admin['email'],
                'role'     => 'administrador'
            ]);

            return [
                'id'       => $admin['id'],
                'nombre'   => $admin['nombre'],
                'apellido' => $admin['apellido'] ?? '',
                'email'    => $admin['email'],
                'dni'      => $admin['dni'] ?? null,
                'token'    => $token
            ];
        }


        public static function registerRaw(string $nombre,string $apellido,string $email,string $password): int {
            return AdminRepository::create([
            'nombre'=>$nombre,'apellido'=>$apellido,
            'email'=>$email,'password'=>password_hash($password,PASSWORD_BCRYPT),
            'rol'=>'administrador'
            ]);
        }

        public static function actualizarPerfil(int $id, string $nombre, string $apellido, string $correo, ?string $dni): void {
            AdminRepository::actualizar($id, $nombre, $apellido, $correo, $dni);
        }
    }

?>