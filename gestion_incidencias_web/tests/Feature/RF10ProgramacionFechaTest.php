<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Services\CalendarioService;
use App\Services\IncidenciaService;

final class RF10ProgramacionFechaTest extends TestCase
{
    private PDO $pdo;

    // Tu BD exige lat/long NOT NULL
    private const DUMMY_LAT = -17.9900;
    private const DUMMY_LON = -70.2450;

    protected function setUp(): void
    {
        $this->pdo = Database::getInstance();
        $this->asegurarCatalogosMinimos();
    }


    private function asegurarCatalogosMinimos(): void
    {
        foreach (['Pendiente','En desarrollo','Terminado'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM estado_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n'=>$n]);
            if (!$q->fetchColumn()) {
                $this->pdo->prepare("INSERT INTO estado_incidencia (nombre) VALUES (:n)")
                          ->execute(['n'=>$n]);
            }
        }
        foreach (['Bacheo','Alumbrado','Limpieza'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM tipo_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n'=>$n]);
            if (!$q->fetchColumn()) {
                $this->pdo->prepare("INSERT INTO tipo_incidencia (nombre) VALUES (:n)")
                          ->execute(['n'=>$n]);
            }
        }
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

    private function anyPrioridadId(): int
    {
        $r = $this->pdo->query("SELECT id FROM prioridad ORDER BY id ASC LIMIT 1");
        $v = $r ? $r->fetchColumn() : false;
        if ($v === false) {
            $this->markTestSkipped('No hay filas en prioridad; agrega registros al catálogo de prioridades.');
        }
        return (int)$v;
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

    private function crearIncidencia(int $tipoId, int $estadoId, string $desc='desc'): int
    {
        $q = $this->pdo->prepare("
            INSERT INTO incidencia (
                descripcion, latitud, longitud, estado_id, tipo_id, fecha_reporte
            ) VALUES (
                :d, :lat, :lon, :e, :t, NOW()
            )
            RETURNING id
        ");
        $q->execute([
            'd'=>$desc,
            'lat'=>self::DUMMY_LAT,
            'lon'=>self::DUMMY_LON,
            'e'=>$estadoId,
            't'=>$tipoId,
        ]);
        return (int)$q->fetchColumn();
    }


    public function test_programar_fecha_valida_con_servicio_inserta_en_calendario(): void
    {
        $tipoId   = $this->idTipo('Bacheo');
        $estadoId = $this->idEstado('Pendiente');
        $incId    = $this->crearIncidencia($tipoId, $estadoId);

        $fecha = date('Y-m-d', strtotime('+3 days'));
        $ok = CalendarioService::programar($incId, $fecha);
        $this->assertTrue($ok, 'Debe aceptar fechas de hoy/futuras');

        // Verifica persistencia directa
        $q = $this->pdo->prepare("SELECT TO_CHAR(fecha_programada,'YYYY-MM-DD') FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame($fecha, (string)$q->fetchColumn(), 'Debe persistir la fecha programada');

        // Verifica que la lectura general la exponga
        $rows = IncidenciaService::obtenerTodas();
        $found = null;
        foreach ($rows as $r) {
            if ((int)$r['id'] === $incId) { $found = $r; break; }
        }
        $this->assertNotNull($found, 'La incidencia debe aparecer en el listado');
        $this->assertSame($fecha, (string)($found['fecha_programada'] ?? ''), 'El listado debe mostrar la fecha programada');
    }

    public function test_programar_fecha_pasada_es_rechazada(): void
    {
        $tipoId   = $this->idTipo('Alumbrado');
        $estadoId = $this->idEstado('Pendiente');
        $incId    = $this->crearIncidencia($tipoId, $estadoId);

        $ayer = date('Y-m-d', strtotime('-1 day'));
        $ok = CalendarioService::programar($incId, $ayer);
        $this->assertFalse($ok, 'No debe permitir fechas pasadas');

        $q = $this->pdo->prepare("SELECT COUNT(*) FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame('0', (string)$q->fetchColumn(), 'No debe crear registro en calendario');
    }

    public function test_asignar_empleado_con_fecha_programada_persiste_y_es_visible(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $prioId     = $this->anyPrioridadId();
        $tipoId     = $this->idTipo('Limpieza');
        $estadoId   = $this->idEstado('Pendiente');
        $incId      = $this->crearIncidencia($tipoId, $estadoId);

        $fecha = date('Y-m-d', strtotime('+5 days'));

        // Asignación + fecha (el repositorio hace upsert en calendario)
        $ok = IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, $fecha);
        $this->assertTrue($ok, 'La asignación con fecha válida debe completarse');

        // Persiste en calendario
        $q = $this->pdo->prepare("SELECT TO_CHAR(fecha_programada,'YYYY-MM-DD') FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame($fecha, (string)$q->fetchColumn());

        // Visible en la vista del empleado
        $rows = IncidenciaService::obtenerPorEmpleado($empleadoId);
        $found = null;
        foreach ($rows as $r) {
            if ((int)$r['id'] === $incId) { $found = $r; break; }
        }
        $this->assertNotNull($found, 'La incidencia debe aparecer para el empleado');
        $this->assertSame($fecha, (string)($found['fecha_programada'] ?? ''), 'El empleado debe ver la misma fecha programada');
    }

    public function test_asignar_empleado_sin_fecha_no_crea_registro_en_calendario(): void
    {
        $empleadoId = $this->crearUsuarioEmpleado();
        $prioId     = $this->anyPrioridadId();
        $tipoId     = $this->idTipo('Bacheo');
        $estadoId   = $this->idEstado('Pendiente');
        $incId      = $this->crearIncidencia($tipoId, $estadoId);

        $ok = IncidenciaService::asignarEmpleado($incId, $empleadoId, $prioId, null);
        $this->assertTrue($ok, 'La asignación sin fecha debe ser posible (solo no programa)');

        $q = $this->pdo->prepare("SELECT COUNT(*) FROM calendario_incidencia WHERE incidencia_id=:id");
        $q->execute(['id'=>$incId]);
        $this->assertSame('0', (string)$q->fetchColumn(), 'No debe existir registro en calendario si no se envía fecha');
    }
}