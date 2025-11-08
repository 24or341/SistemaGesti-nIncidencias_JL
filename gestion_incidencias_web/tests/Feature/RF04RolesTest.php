<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Services\AdminService;
use App\Core\Auth;
use Firebase\JWT\JWT;

final class RF04RolesTest extends TestCase
{
    /**
     * AdminService::emitirTokenAdmin debe devolver role=administrador
     * y el JWT debe incluir ese claim y el user_id correcto.
     */
    public function testEmitirTokenAdminIncluyeRoleAdministrador(): void
    {
        $adminRow = [
            'id'      => 123,
            'nombre'  => 'Ada',
            'apellido'=> 'L.',
            'email'   => 'ada@example.com',
            // aunque viniera algo raro acá, emitirTokenAdmin lo ignora
            'role'    => 'empleado',
        ];

        $payload = AdminService::emitirTokenAdmin($adminRow);

        // respuesta de servicio
        $this->assertSame('administrador', $payload['role']);
        $this->assertArrayHasKey('token', $payload);

        // claims dentro del JWT
        $claims = Auth::verificarToken($payload['token']);
        $this->assertSame('administrador', $claims['role']);
        $this->assertSame(123, $claims['user_id']);
    }

    /**
     * Un token generado para empleado debe conservar role=empleado.
     * (Validamos la pieza JWT sin necesidad de BD).
     */
    public function testTokenDeEmpleadoTieneClaimRoleEmpleado(): void
    {
        $jwt = Auth::generarToken([
            'user_id' => 77,
            'nombre'  => 'Eli',
            'email'   => 'eli@example.com',
            'role'    => 'empleado',
        ]);

        $claims = Auth::verificarToken($jwt);
        $this->assertSame('empleado', $claims['role']);
        $this->assertSame(77, $claims['user_id']);
    }

    /**
     * No deben existir “ascensos”: aunque el array de entrada venga con role incorrecto,
     * emitirTokenAdmin fuerza role=administrador en el JWT.
     */
    public function testNoExisteAscensoIndebidoDesdePayload(): void
    {
        $adminRowMalicioso = [
            'id'     => 999,
            'nombre' => 'Mallory',
            'email'  => 'mallory@example.com',
            'role'   => 'empleado', // intento de degradar/alterar
        ];

        $payload = AdminService::emitirTokenAdmin($adminRowMalicioso);
        $claims  = Auth::verificarToken($payload['token']);

        $this->assertSame('administrador', $claims['role']);
    }

    /**
     * Un JWT sin claim 'role' debe ser rechazado por Auth::verificarToken().
     */
    public function testTokenSinRoleEsRechazado(): void
    {
        $this->expectException(\Exception::class);

        $now = time();
        // fabricamos un JWT sin 'role'
        $raw = [
            'iat'     => $now,
            'exp'     => $now + 3600,
            'user_id' => 1,
            // sin 'role'
        ];
        // Usa el mismo secreto que tu bootstrap carga para tests
        $secret = $_ENV['JWT_SECRET'] ?? 'test_secret';

        $jwt = JWT::encode($raw, $secret, 'HS256');

        // Debe lanzar excepción ("Token inválido o expirado")
        Auth::verificarToken($jwt);
    }
}
