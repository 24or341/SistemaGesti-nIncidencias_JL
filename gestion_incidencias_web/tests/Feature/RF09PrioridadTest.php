<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Services\IncidenciaService;

final class RF09PrioridadTest extends TestCase
{
    private PDO $pdo;

    // En tu BD lat/long son NOT NULL
    private const DUMMY_LAT = -17.9900;
    private const DUMMY_LON = -70.2450;

    protected function setUp(): void
    {
        $this->pdo = Database::getInstance();
        // No transacciones anidadas, no TRUNCATE, no limpieza.
        $this->asegurarCatalogosMinimos();
    }

    protected function tearDown(): void
    {
        // Cierra solo si el repo dejó una transacción abierta (p.ej., al lanzar excepción antes del commit/rollback).
        if ($this->pdo instanceof PDO && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }


    private function asegurarCatalogosMinimos(): void
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

    private function anyPrioridadId(): int
    {
        $r = $this->pdo->query("SELECT id FROM prioridad ORDER BY id ASC LIMIT 1");
        $v = $r ? $r->fetchColumn() : false;
        if ($v === false) {
            $this->markTestSkipped('No hay filas en prioridad; agrega registros al catálogo de prioridades.');
        }
        return (int)$v;
    }

    private function otraPrioridadId(int $distintaDe): int
    {
        $q = $this->pdo->prepare("SELECT id FROM prioridad WHERE id <> :id ORDER BY id ASC LIMIT 1");
        $q->execute(['id'=>$distintaDe]);
        $v = $q->fetchColumn();
        if ($v === false) {
            $this->markTestSkipped('Se requiere al menos 2 filas en prioridad para esta prueba.');
        }
        return (int)$v;
    }

    private function etiquetaPrioridad(int $id): string
    {
        $q = $this->pdo->prepare("SELECT nivel FROM prioridad WHERE id=:id");
        $q->execute(['id'=>$id]);
        return (string)$q->fetchColumn();
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

    private function crearUsuarioEmpleado(): int
    {
        $email = 'emp_'.uniqid('', true).'@test.local';
        $pwd   = password_hash('secret', PASSWORD_DEFAULT);
        $q = $this->pdo->prepare("
            INSERT INTO usuario (nombre, apellido, dni, email, password, rol)
            VALUES ('T','User','00000000', :e, :p, 'empleado')
            RETURNING id
        ");
        $q->execute(['e'=>$email,'p'=>$pwd]);
        return (int)$q->fetchColumn();
    }

    private function crearIncidencia(int $tipoId, int $estadoId, ?int $prioridadId=null, ?int $asignadoA=null, string $desc='desc'): int
    {
        $q = $this->pdo->prepare("
            INSERT INTO incidencia (
                descripcion, latitud, longitud, estado_id, tipo_id, fecha_reporte, prioridad_id, asignado_a
            ) VALUES (
                :d, :lat, :lon, :e, :t, NOW(), :p, :a
            )
            RETURNING id
        ");
        $q->execute([
            'd'=>$desc,
            'lat'=>self::DUMMY_LAT,
            'lon'=>self::DUMMY_LON,
            'e'=>$estadoId,
            't'=>$tipoId,
            'p'=>$prioridadId,
            'a'=>$asignadoA
        ]);
        return (int)$q->fetchColumn();
    }

    private function prioridadInvalidaId(): int
    {
        $r = $this->pdo->query("SELECT COALESCE(MAX(id), 0) FROM prioridad");
        $max = (int)$r->fetchColumn();
        return $max + 1000;
    }

    public function test_rechaza_prioridad_invalida(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $tipoId     = $this->idTipo('Bacheo');
        $estadoId   = $this->idEstado('Pendiente');
        $incId      = $this->crearIncidencia($tipoId, $estadoId);

        $prioInval  = $this->prioridadInvalidaId();

        $this->expectException(\Throwable::class);
        IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioInval, null);
    }

    public function test_prioridad_se_guarda_con_asignacion(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $tipoId     = $this->idTipo('Alumbrado');
        $estadoId   = $this->idEstado('Pendiente');
        $prioId     = $this->anyPrioridadId();

        $incId = $this->crearIncidencia($tipoId, $estadoId);

        $ok = IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, null);
        $this->assertTrue($ok, 'La asignación debería completarse');

        $q = $this->pdo->prepare("SELECT asignado_a, prioridad_id FROM incidencia WHERE id=:id");
        $q->execute(['id'=>$incId]);
        $row = $q->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($empleadoId, (int)$row['asignado_a']);
        $this->assertEquals($prioId, (int)$row['prioridad_id']);
    }

    public function test_no_permite_reasignacion_invisible_mismo_empleado(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $tipoId     = $this->idTipo('Limpieza');
        $estadoId   = $this->idEstado('Pendiente');
        $prio1      = $this->anyPrioridadId();
        $prio2      = $this->otraPrioridadId($prio1);

        $incId = $this->crearIncidencia($tipoId, $estadoId, null, null, 'incidencia RF09');

        // 1) Primera asignación con prioridad prio1 (confirmada)
        $this->assertTrue(IncidenciaService::asignarEmpleado($incId, $empleadoId, $prio1, null));

        // 2) Intento de “reasignación invisible” (mismo empleado, otra prioridad)
        $thrown = null;
        try {
            IncidenciaService::asignarEmpleado($incId, $empleadoId, $prio2, null);
        } catch (\Throwable $e) {
            $thrown = $e;
            // El repo abre una tx y lanza excepción antes de hacer rollback → cerrarla aquí.
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
        $this->assertNotNull($thrown, 'Debe lanzarse excepción al intentar reasignar al mismo empleado');
        $this->assertStringContainsString('ya fue asignada', $thrown->getMessage());

        // 3) Confirma que NO cambió la prioridad en BD
        $q = $this->pdo->prepare("SELECT prioridad_id FROM incidencia WHERE id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertEquals($prio1, (int)$q->fetchColumn());
    }

    public function test_listado_incluye_etiqueta_prioridad_estable(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $tipoId     = $this->idTipo('Bacheo');
        $estadoId   = $this->idEstado('Pendiente');
        $prioId     = $this->anyPrioridadId();
        $etiqueta   = $this->etiquetaPrioridad($prioId);

        $incId = $this->crearIncidencia($tipoId, $estadoId);
        $this->assertTrue(IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, null));

        $rows  = \App\Services\IncidenciaService::obtenerTodas();
        $found = null;
        foreach ($rows as $r) {
            if ((int)$r['id'] === $incId) { $found = $r; break; }
        }
        $this->assertNotNull($found, 'Debe encontrarse la incidencia creada');
        $this->assertSame((string)$etiqueta, (string)$found['prioridad'], 'La etiqueta de prioridad debe ser estable y no ambigua');
    }
}
