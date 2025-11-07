<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Auth;
use App\Services\AdminService;
use RobThree\Auth\TwoFactorAuth;

final class RF02AuthTest extends TestCase
{
    /** Limpia cabeceras simuladas */
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['Authorization']);
    }

    /** 
     * 1) Generar y verificar token con claims esperados (id, role, exp).
     *    Cubre: Auth::generarToken() y Auth::verificarToken()
     */
    public function test_token_contiene_claims_y_se_verifica(): void
    {
        // Arrange
        $payload = [
            'user_id' => 123,
            'role'    => 'administrador',
            'email'   => 'admin@test.local',
            'nombre'  => 'Admin Test',
        ];

        // Act
        $jwt   = Auth::generarToken($payload, 3600);
        $datos = Auth::verificarToken($jwt);

        // Assert
        $this->assertSame(123, $datos['user_id']);
        $this->assertSame('administrador', $datos['role']);
        $this->assertArrayHasKey('iat', $datos);
        $this->assertArrayHasKey('exp', $datos);
        $this->assertGreaterThan($datos['iat'], $datos['exp']); // exp > iat
    }

    /**
     * 2) Extraer Bearer del servidor correctamente.
     *    Cubre: Auth::extractBearerFromServer()
     */
    public function test_extract_bearer_header(): void
    {
        // Arrange
        $fakeJwt = 'ey.fake.jwt';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $fakeJwt;

        // Act
        $extracted = Auth::extractBearerFromServer();

        // Assert
        $this->assertSame($fakeJwt, $extracted);
    }

    /**
     * 3) Emitir credenciales admin coherentes (payload + token válido).
     *    Cubre: AdminService::emitirTokenAdmin()
     */
    public function test_emitir_token_admin_devuelve_payload_valido(): void
    {
        // Arrange: simulamos el "row" crudo del admin (preLogin ya lo habría validado)
        $adminRow = [
            'id'      => 77,
            'nombre'  => 'Jorge',
            'apellido'=> 'Castañeda',
            'email'   => 'admin@demo.local',
            'dni'     => '12345678',
            // mfa_* son irrelevantes para emitir token, pero pueden existir
            'mfa_enabled' => 0,
            'mfa_secret'  => null,
        ];

        // Act
        $payload = AdminService::emitirTokenAdmin($adminRow);

        // Assert estructura
        $this->assertSame(77, $payload['id']);
        $this->assertSame('Jorge', $payload['nombre']);
        $this->assertSame('administrador', $payload['role']);
        $this->assertArrayHasKey('token', $payload);

        // Assert que el token es verificable y con claims coherentes
        $claims = Auth::verificarToken($payload['token']);
        $this->assertSame(77, $claims['user_id']);
        $this->assertSame('administrador', $claims['role']);
        $this->assertSame('admin@demo.local', $claims['email']);
    }

    /**
     * 4) Verificación MFA TOTP: código correcto → true.
     *    Cubre: AdminService::verificarOtp()
     */
    public function test_verificar_otp_valido_regresa_true(): void
    {
        // Arrange: generamos un secreto y un código válido con el MISMO issuer
        $tfa    = new TwoFactorAuth('Sistema Incidencias');
        $secret = $tfa->createSecret(160);
        $otp    = $tfa->getCode($secret); // código válido para "ahora"

        $adminRow = [
            'id'          => 7,
            'nombre'      => 'Admin MFA',
            'email'       => 'mfa@demo.local',
            'mfa_enabled' => 1,
            'mfa_secret'  => $secret,
        ];

        // Act
        $ok = AdminService::verificarOtp($adminRow, $otp);

        // Assert
        $this->assertTrue($ok);
    }

    /**
     * 5) Verificación MFA TOTP: código inválido → false.
     *    Cubre: AdminService::verificarOtp()
     */
    public function test_verificar_otp_invalido_regresa_false(): void
    {
        $tfa    = new TwoFactorAuth('Sistema Incidencias');
        $secret = $tfa->createSecret(160);

        $adminRow = [
            'id'          => 8,
            'nombre'      => 'Admin MFA',
            'email'       => 'mfa2@demo.local',
            'mfa_enabled' => 1,
            'mfa_secret'  => $secret,
        ];

        // Código obviamente inválido
        $ok = AdminService::verificarOtp($adminRow, '000000');

        $this->assertFalse($ok);
    }

    /**
     * 6) Token malformado o con firma inválida → excepción.
     *    Cubre manejo de error en Auth::verificarToken()
     */
    public function test_verificar_token_invalido_lanza_excepcion(): void
    {
        $this->expectException(Exception::class);
        Auth::verificarToken('no.es.un.jwt');
    }
}
