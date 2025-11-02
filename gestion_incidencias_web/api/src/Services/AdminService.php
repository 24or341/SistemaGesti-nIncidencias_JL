<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AdminRepository;
use RobThree\Auth\TwoFactorAuth;
use App\Core\Auth;

class AdminService
{
    /**
     * @return array<string,mixed>|null
     */
    public static function login(string $email, string $password): ?array
    {
        $admin = AdminRepository::obtenerPorEmail($email);

        if (!$admin || !isset($admin['password']) || !password_verify($password, (string)$admin['password'])) {
            return null;
        }

        $token = Auth::generarToken([
            'user_id' => $admin['id'],
            'nombre'  => $admin['nombre'],
            'email'   => $admin['email'],
            'role'    => 'administrador'
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

    /**
     * Pre-login: verifica email+password y devuelve el row crudo del admin (incluye mfa_*).
     * @return array<string,mixed>|null
     */
    public static function preLogin(string $email, string $password): ?array
    {
        $admin = AdminRepository::obtenerPorEmail($email);
        if (!$admin || !isset($admin['password']) || !password_verify($password, (string)$admin['password'])) {
            return null;
        }
        return $admin; // incluye mfa_secret y mfa_enabled
    }

    /**
     * Emite el payload final (con JWT) para el admin ya autenticado.
     * @param array<string,mixed> $admin
     * @return array<string,mixed>
     */
    public static function emitirTokenAdmin(array $admin): array
    {
        $token = Auth::generarToken([
            'user_id' => $admin['id'],
            'nombre'  => $admin['nombre'],
            'email'   => $admin['email'],
            'role'    => 'administrador'
        ]);
        return [
            'id'       => $admin['id'],
            'nombre'   => $admin['nombre'],
            'apellido' => $admin['apellido'] ?? '',
            'email'    => $admin['email'],
            'dni'      => $admin['dni'] ?? null,
            'role'     => 'administrador',
            'token'    => $token
        ];
    }

    /**
     * Verifica el OTP TOTP contra el secreto del admin.
     * @param array<string,mixed> $admin
     */
    public static function verificarOtp(array $admin, string $otp): bool
    {
        if (empty($admin['mfa_enabled']) || empty($admin['mfa_secret'])) {
            return false;
        }
        $tfa = new TwoFactorAuth('Sistema Incidencias'); // issuer
        // window=1 → tolera +-30s de desfasaje
        return $tfa->verifyCode((string)$admin['mfa_secret'], trim($otp), 1);
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
