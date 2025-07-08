<?php

    namespace App\Core;

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Exception;

    class Auth
    {
        private static string $secret;

        public static function init(): void
        {
            self::$secret = $_ENV['JWT_SECRET'] ?? 'clave_predeterminada';
        }

        public static function generarToken(array $datos, int $expiracionSegundos = 86400): string
        {
            $payload = [
                'iat' => time(),
                'exp' => time() + $expiracionSegundos,
                'data' => $datos + ['role' => $datos['role'] ?? '']
            ];

            return JWT::encode($payload, self::$secret, 'HS256');
        }

        public static function verificarToken(string $jwt): array
        {
            try {
                $decoded = JWT::decode($jwt, new Key(self::$secret, 'HS256'));
                return (array) $decoded->data;
            } catch (Exception $e) {
                throw new Exception("Token inválido o expirado");
            }
        }
    }

?>