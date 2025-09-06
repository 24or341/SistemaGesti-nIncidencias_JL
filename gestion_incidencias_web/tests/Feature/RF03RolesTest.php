<?php
declare(strict_types=1);

final class RF03RolesTest extends TestHttp
{
    private static string $adminEmail;
    private static string $adminPass = 'Admin#2025';
    private static string $empEmail;
    private static string $empPass   = 'Emp#2025';

    private static string $adminToken;
    private static string $empToken;
    private static int    $empId;

    public static function setUpBeforeClass(): void
    {
        self::$adminEmail = self::uniqueEmail('admin');
        self::$empEmail   = self::uniqueEmail('emp');

        // Crear admin
        $r = self::request('POST', self::api('register.php'),
            ['Content-Type'=>'application/json'],
            json_encode([
                'role'=>'administrador','nombre'=>'Ana','apellido'=>'Rojas',
                'email'=>self::$adminEmail,'password'=>self::$adminPass
            ], JSON_THROW_ON_ERROR)
        );
        if ($r['code'] !== 200) throw new RuntimeException('No se pudo registrar admin');

        // Login admin
        $r = self::request('POST', self::api('login.php'),
            ['Content-Type'=>'application/json'],
            json_encode(['email'=>self::$adminEmail,'password'=>self::$adminPass], JSON_THROW_ON_ERROR)
        );
        self::$adminToken = $r['json']['data']['token'] ?? '';

        // Crear empleado
        $r = self::request('POST', self::api('register.php'),
            ['Content-Type'=>'application/json'],
            json_encode([
                'role'=>'empleado','nombre'=>'Luis','apellido'=>'Mendoza','dni'=>self::uniqueDni(),
                'email'=>self::$empEmail,'password'=>self::$empPass
            ], JSON_THROW_ON_ERROR)
        );
        if ($r['code'] !== 200) throw new RuntimeException('No se pudo registrar empleado');

        // Login empleado
        $r = self::request('POST', self::api('login.php'),
            ['Content-Type'=>'application/json'],
            json_encode(['email'=>self::$empEmail,'password'=>self::$empPass], JSON_THROW_ON_ERROR)
        );
        self::$empToken = $r['json']['data']['token'] ?? '';
        self::$empId    = (int)($r['json']['data']['id'] ?? 0);
    }

    public function test_RF03_01_admin_puede_listar_empleados(): void
    {
        $r = self::request('GET', self::api('admin_dashboard/empleados.php'), [
            'Authorization' => 'Bearer ' . self::$adminToken
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertTrue($r['json']['success']);
        $this->assertIsArray($r['json']['data']);
    }

    public function test_RF03_02_empleado_bloqueado_en_endpoint_admin(): void
    {
        $r = self::request('GET', self::api('admin_dashboard/empleados.php'), [
            'Authorization' => 'Bearer ' . self::$empToken
        ]);
        $this->assertSame(403, $r['code']);
        $this->assertFalse($r['json']['success']);
    }

    public function test_RF03_03_empleado_ve_incidencias_asignadas(): void
    {
        $r = self::request('GET',
            self::api('api_empleados/incidencias_asignadas.php?usuario_id=' . self::$empId),
            ['Authorization' => 'Bearer ' . self::$empToken]
        );
        $this->assertSame(200, $r['code']);
        $this->assertTrue($r['json']['success']);
        $this->assertIsArray($r['json']['data']);
    }

    public function test_RF03_04_admin_bloqueado_en_endpoint_de_empleado(): void
    {
        $r = self::request('GET',
            self::api('api_empleados/incidencias_asignadas.php?usuario_id=' . self::$empId),
            ['Authorization' => 'Bearer ' . self::$adminToken]
        );
        $this->assertSame(403, $r['code']);
        $this->assertFalse($r['json']['success']);
    }

    public function test_RF03_05_middleware_frontend_redirige_a_incidencias(): void
    {
        // Login en FRONTEND como empleado (AuthController::login) para que se cree la sesión
        $cookie = tempnam(sys_get_temp_dir(), 'cookie');
        $login = self::request(
            'POST',
            self::admin('index.php?path=auth/login'),
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            http_build_query(['email'=>self::$empEmail,'password'=>self::$empPass]),
            false,
            $cookie
        );
        $this->assertContains($login['code'], [200,302], 'Login frontend debe responder 200/302');

        // Intentar abrir dashboard (no seguir redirecciones para inspeccionar Location)
        $dash = self::request('GET', self::admin('index.php?path=dashboard'), [], null, false, $cookie);

        $this->assertSame(302, $dash['code'], 'Debe redirigir');
        $location = $dash['headers']['Location'][0] ?? '';
        $this->assertStringContainsString('path=incidencias', $location, 'Middleware debe mandar a incidencias para rol empleado');
        @unlink($cookie);
    }
}
