<?php
declare(strict_types=1);

final class RF02AuthTest extends TestHttp
{
    private static string $adminEmail;
    private static string $adminPass = 'Admin#2025';
    private static string $empEmail;
    private static string $empPass   = 'Emp#2025';

    public static function setUpBeforeClass(): void
    {
        self::$adminEmail = self::uniqueEmail('admin');
        self::$empEmail   = self::uniqueEmail('emp');
    }

    public function test_RF02_01_registro_admin(): void
    {
        $url = self::api('register.php');
        $body = json_encode([
            'role'     => 'administrador',
            'nombre'   => 'Ana',
            'apellido' => 'Rojas',
            'email'    => self::$adminEmail,
            'password' => self::$adminPass,
        ], JSON_THROW_ON_ERROR);

        $r = self::request('POST', $url, ['Content-Type'=>'application/json'], $body);
        $this->assertSame(200, $r['code']);
        $this->assertIsArray($r['json']);
        $this->assertTrue($r['json']['success']);
        $this->assertIsNumeric($r['json']['data']['id']);
    }

    public function test_RF02_02_login_admin(): void
    {
        $url = self::api('login.php');
        $body = json_encode([
            'email'    => self::$adminEmail,
            'password' => self::$adminPass,
        ], JSON_THROW_ON_ERROR);

        $r = self::request('POST', $url, ['Content-Type'=>'application/json'], $body);
        $this->assertSame(200, $r['code']);
        $data = $r['json']['data'];
        $this->assertEquals('administrador', $data['role']);
        $this->assertNotEmpty($data['token']);

        $claims = self::jwtPayload($data['token']);
        $this->assertArrayHasKey('user_id', $claims);
        $this->assertEquals('administrador', $claims['role']);
        $this->assertArrayHasKey('exp', $claims);
    }

    public function test_RF02_03_registro_empleado(): void
    {
        $url = self::api('register.php');
        $body = json_encode([
            'role'     => 'empleado',
            'nombre'   => 'Luis',
            'apellido' => 'Mendoza',
            'dni'      => self::uniqueDni(),
            'email'    => self::$empEmail,
            'password' => self::$empPass,
        ], JSON_THROW_ON_ERROR);

        $r = self::request('POST', $url, ['Content-Type'=>'application/json'], $body);
        $this->assertSame(200, $r['code']);
        $this->assertTrue($r['json']['success']);
        $this->assertIsNumeric($r['json']['data']['id']);
    }

    public function test_RF02_04_login_empleado(): void
    {
        $url = self::api('login.php');
        $body = json_encode([
            'email'    => self::$empEmail,
            'password' => self::$empPass,
        ], JSON_THROW_ON_ERROR);

        $r = self::request('POST', $url, ['Content-Type'=>'application/json'], $body);
        $this->assertSame(200, $r['code']);
        $this->assertEquals('empleado', $r['json']['data']['role']);
        $this->assertNotEmpty($r['json']['data']['token']);
    }

    public function test_RF02_05_login_fallido(): void
    {
        $url = self::api('login.php');
        $body = json_encode([
            'email'    => self::$adminEmail,
            'password' => 'contrasenia_incorrecta',
        ], JSON_THROW_ON_ERROR);

        $r = self::request('POST', $url, ['Content-Type'=>'application/json'], $body);
        $this->assertSame(401, $r['code']);
        $this->assertFalse($r['json']['success']);
    }
}
