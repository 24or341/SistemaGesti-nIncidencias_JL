<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Services\IncidenciaService;
use App\Services\CalendarioService;

final class RF06_AsignacionYPanelTest extends TestCase
{
    private PDO $pdo;

    private const DUMMY_LAT = -17.9900;
    private const DUMMY_LON = -70.2450;

    protected function setUp(): void
    {
        $this->pdo = Database::getInstance();

        $this->seedCatalogosSiFaltan();
        $this->seedPrioridades();
    }

    protected function tearDown(): void
    {
        $this->limpiarDatosDePrueba();
    }

    private function limpiarDatosDePrueba(): void
    {
        $this->pdo->exec("
            TRUNCATE TABLE calendario_incidencia, incidencia, usuario
            RESTART IDENTITY CASCADE
        ");
    }

    private function seedCatalogosSiFaltan(): void
    {
        foreach (['Pendiente','En desarrollo','Terminado'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM estado_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n'=>$n]);
            if (!$q->fetchColumn()) {
                $ins = $this->pdo->prepare("INSERT INTO estado_incidencia (nombre) VALUES (:n)");
                $ins->execute(['n'=>$n]);
            }
        }
        foreach (['Bacheo','Alumbrado','Limpieza'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM tipo_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n'=>$n]);
            if (!$q->fetchColumn()) {
                $ins = $this->pdo->prepare("INSERT INTO tipo_incidencia (nombre) VALUES (:n)");
                $ins->execute(['n'=>$n]);
            }
        }
    }

    private function seedPrioridades(): void
    {
        $count = (int)$this->pdo->query("SELECT COUNT(*) FROM prioridad")->fetchColumn();
        if ($count === 0) {
            $ins = $this->pdo->prepare("INSERT INTO prioridad (id, nivel) VALUES (:id, :nivel)");
            foreach ([[2,'Alta'],[3,'Media'],[4,'Baja']] as [$id,$nivel]) {
                $ins->execute(['id'=>$id,'nivel'=>$nivel]);
            }
        }
    }

    private function anyPrioridadId(): int
    {
        $r = $this->pdo->query("SELECT id FROM prioridad ORDER BY id ASC LIMIT 1");
        return (int)$r->fetchColumn();
    }

    private function idEstado(string $nombre): int
    {
        $q = $this->pdo->prepare("SELECT id FROM estado_incidencia WHERE lower(nombre)=lower(:n) LIMIT 1");
        $q->execute(['n'=>$nombre]);
        return (int)$q->fetchColumn();
    }

    private function idTipo(string $nombre): int
    {
        $q = $this->pdo->prepare("SELECT id FROM tipo_incidencia WHERE lower(nombre)=lower(:n) LIMIT 1");
        $q->execute(['n'=>$nombre]);
        return (int)$q->fetchColumn();
    }

    private function crearUsuario(string $rol): int
    {
        $email = sprintf('%s_%s@test.local', $rol, uniqid());
        $hash  = password_hash('secret', PASSWORD_DEFAULT);
        $q = $this->pdo->prepare("
            INSERT INTO usuario (nombre,apellido,dni,email,password,rol)
            VALUES ('T','User','00000000', :e, :p, :r)
            RETURNING id
        ");
        $q->execute(['e'=>$email,'p'=>$hash,'r'=>$rol]);
        return (int)$q->fetchColumn();
    }

    private function crearIncidencia(
        int $tipoId,
        int $estadoId,
        string $desc = 'desc',
        ?float $lat = null,
        ?float $lon = null,
        ?int $asignadoA = null,
        ?int $prioridadId = null
    ): int {
        $lat = $lat ?? self::DUMMY_LAT;
        $lon = $lon ?? self::DUMMY_LON;

        $q = $this->pdo->prepare("
            INSERT INTO incidencia (
                descripcion, latitud, longitud, estado_id, tipo_id, fecha_reporte, prioridad_id, asignado_a
            ) VALUES (
                :d, :lat, :lon, :e, :t, NOW(), :p, :a
            )
            RETURNING id
        ");
        $q->execute([
            'd'=>$desc, 'lat'=>$lat, 'lon'=>$lon,
            'e'=>$estadoId, 't'=>$tipoId,
            'p'=>$prioridadId, 'a'=>$asignadoA
        ]);
        return (int)$q->fetchColumn();
    }

    public function test_asignacion_exitosa_con_fecha_valida(): void
    {
        $empleadoId = $this->crearUsuario('empleado');
        $tipoId     = $this->idTipo('Bacheo');
        $estadoId   = $this->idEstado('Pendiente');
        $prioId     = $this->anyPrioridadId();

        $incId = $this->crearIncidencia($tipoId, $estadoId);
        $fecha = date('Y-m-d', strtotime('+2 days'));

        $ok = IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, $fecha);
        $this->assertTrue($ok);

        $q = $this->pdo->prepare("SELECT asignado_a, prioridad_id FROM incidencia WHERE id=:id");
        $q->execute(['id'=>$incId]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($empleadoId, (int)$row['asignado_a']);
        $this->assertEquals($prioId, (int)$row['prioridad_id']);

        $q = $this->pdo->prepare("SELECT fecha_programada FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame($fecha, (string)$q->fetchColumn());
    }

    public function test_rechaza_fecha_pasada_en_programacion(): void
    {
        $tipoId = $this->idTipo('Alumbrado');
        $estId  = $this->idEstado('Pendiente');
        $incId  = $this->crearIncidencia($tipoId, $estId);

        $ayer = date('Y-m-d', strtotime('-1 day'));
        $ok   = CalendarioService::programar($incId, $ayer);
        $this->assertFalse($ok);

        $q = $this->pdo->prepare("SELECT COUNT(*) FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame('0', (string)$q->fetchColumn());
    }

    public function test_falta_prioridad_id_dispara_error_de_validacion(): void
    {
        $empleadoId = $this->crearUsuario('empleado');
        $tipoId     = $this->idTipo('Limpieza');
        $estId      = $this->idEstado('Pendiente');
        $incId      = $this->crearIncidencia($tipoId, $estId);

        $this->expectException(\Throwable::class);
        IncidenciaService::asignarEmpleado($incId, $empleadoId, 0, null);
    }

    public function test_no_permite_asignacion_duplicada_mismo_empleado(): void
    {
        $empleadoId = $this->crearUsuario('empleado');
        $tipoId     = $this->idTipo('Bacheo');
        $estId      = $this->idEstado('Pendiente');
        $prioId     = $this->anyPrioridadId();

        $incId = $this->crearIncidencia($tipoId, $estId, 'ya asignada', null, null, $empleadoId, $prioId);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ya fue asignada');
        IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, null);
    }

    public function test_listado_devuelve_etiquetas_y_geo_numerica(): void
    {
        $tipoId = $this->idTipo('Alumbrado');
        $estId  = $this->idEstado('En desarrollo');
        $id     = $this->crearIncidencia($tipoId, $estId, 'sin foto');

        $todas = IncidenciaService::obtenerTodas();
        $found = null;
        foreach ($todas as $r) { if ((int)$r['id'] === $id) { $found = $r; break; } }
        $this->assertNotNull($found);

        $this->assertSame('Alumbrado', (string)$found['tipo']);
        $this->assertMatchesRegularExpression('/desarrollo/i', (string)$found['estado']);
        $this->assertIsNumeric($found['latitud']);
        $this->assertIsNumeric($found['longitud']);
    }

    public function test_totales_por_estado_y_tipo_a_partir_del_listado(): void
    {
        $pend = $this->idEstado('Pendiente');
        $term = $this->idEstado('Terminado');
        $b    = $this->idTipo('Bacheo');
        $a    = $this->idTipo('Alumbrado');

        $this->crearIncidencia($b,$pend);
        $this->crearIncidencia($b,$pend);
        $this->crearIncidencia($a,$term);

        $rows = IncidenciaService::obtenerTodas();
        $totE = $totT = [];
        foreach ($rows as $r) {
            $totE[$r['estado']] = ($totE[$r['estado']] ?? 0) + 1;
            $totT[$r['tipo']]   = ($totT[$r['tipo']]   ?? 0) + 1;
        }
        $this->assertGreaterThanOrEqual(2, $totE['Pendiente'] ?? 0);
        $this->assertGreaterThanOrEqual(1, $totE['Terminado'] ?? 0);
        $this->assertGreaterThanOrEqual(2, $totT['Bacheo'] ?? 0);
        $this->assertGreaterThanOrEqual(1, $totT['Alumbrado'] ?? 0);
    }

    public function test_programar_fecha_valida_guarda_en_calendario(): void
    {
        $tipoId = $this->idTipo('Bacheo');
        $estId  = $this->idEstado('Pendiente');
        $incId  = $this->crearIncidencia($tipoId, $estId);

        $fecha = date('Y-m-d', strtotime('+3 days'));
        $ok    = CalendarioService::programar($incId, $fecha);
        $this->assertTrue($ok);

        $q = $this->pdo->prepare("SELECT fecha_programada FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame($fecha, (string)$q->fetchColumn());
    }
}
