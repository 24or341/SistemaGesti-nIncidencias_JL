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
                'id' => $admin['id'],
                'nombre' => $admin['nombre'],
                'email' => $admin['email'],
                'token' => $token
            ];
        }

        public static function registerRaw(string $nombre,string $apellido,string $email,string $password): int {
            return AdminRepository::create([
            'nombre'=>$nombre,'apellido'=>$apellido,
            'email'=>$email,'password'=>password_hash($password,PASSWORD_BCRYPT),
            'rol'=>'administrador'
            ]);
        }
    }

?>