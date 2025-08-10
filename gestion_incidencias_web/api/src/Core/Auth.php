<?php
// Configuración del archivo Auth.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto

namespace App\Core; // Espacio de nombres para la lógica central de la aplicación

use Firebase\JWT\JWT; // Usa JWT
use Firebase\JWT\Key; // Usa JWT
use Exception;

/**
 * Autenticación / JWT helper
 *
 * Payload standard:
 * - user_id (int|string)
 * - role (string)
 * - email (string|null)
 * - nombre (string|null)
 */
class Auth
{
    private static string $secret; // Clave secreta para firmar los tokens

    public static function init(): void // Inicializa la clase Auth
    {
        self::$secret = (string) ($_ENV['JWT_SECRET'] ?? 'clave_predeterminada'); // Clave secreta para firmar los tokens
    }

    /**
     * @param array<string,mixed> $datos Must contain keys: user_id, role, (email, nombre optional)
     */
    public static function generarToken(array $datos, int $expiracionSegundos = 86400): string // Genera un token JWT
    {
        $now = time(); // Tiempo actual
        $payload = [ // Carga útil del token
            'iat' => $now, // Tiempo de emisión
            'exp' => $now + $expiracionSegundos, // Tiempo de expiración
            'user_id' => $datos['user_id'] ?? ($datos['id'] ?? null), // ID del usuario
            'role'    => $datos['role'] ?? '', // Rol del usuario
            'email'   => $datos['email'] ?? null, // Correo electrónico del usuario
            'nombre'  => $datos['nombre'] ?? null, // Nombre del usuario
        ];

        return JWT::encode($payload, self::$secret, 'HS256'); // Genera el token JWT
    }

    /**
     * @return array{user_id:mixed,role:string,email?:string|null,nombre?:string|null,iat?:int,exp?:int}
     * @throws Exception
     */
    public static function verificarToken(string $jwt): array // Verifica un token JWT
    {
        try { // Intenta decodificar el token
            $decoded = JWT::decode($jwt, new Key(self::$secret, 'HS256')); // Decodifica el token
            $arr = (array) $decoded; // Normaliza la carga útil

            if (!isset($arr['user_id'], $arr['role'])) { // Verifica que existan los claims necesarios
                throw new Exception("Token inválido: claims faltantes"); // Lanza una excepción si faltan claims
            }

            return [
                'user_id' => $arr['user_id'], // ID del usuario
                'role'    => (string) ($arr['role'] ?? ''), // Rol del usuario
                'email'   => $arr['email'] ?? null, // Correo electrónico del usuario
                'nombre'  => $arr['nombre'] ?? null, // Nombre del usuario
                'iat'     => isset($arr['iat']) ? (int)$arr['iat'] : null, // Tiempo de emisión
                'exp'     => isset($arr['exp']) ? (int)$arr['exp'] : null, // Tiempo de expiración
            ];
        } catch (Exception $e) { // Captura cualquier excepción
            throw new Exception("Token inválido o expirado"); // Lanza una excepción si el token es inválido o ha expirado
        }
    }

    /**
     * Extrae un token Bearer del servidor y lo retorna o null.
     */
    public static function extractBearerFromServer(): ?string // Extrae el token Bearer del servidor
    {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? null; // Obtiene el encabezado de autorización
        if (!is_string($hdr)) { // Verifica que el encabezado sea una cadena
            return null; // Retorna null si no es una cadena
        }
        if (!preg_match('/Bearer\s+(.+)$/i', $hdr, $m)) { // Verifica el formato del token Bearer
            return null; // Retorna null si el formato es inválido
        }
        return $m[1] ?? null; // Retorna el token Bearer extraído
    }
}
