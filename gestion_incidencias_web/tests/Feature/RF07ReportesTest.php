<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Services\ReporteService;

final class RF07_ReportesTest extends TestCase
{
    private PDO $pdo;

    // Coordenadas dummy porque lat/long suelen ser NOT NULL en tu esquema
    private const DUMMY_LAT = -17.9900;
    private const DUMMY_LON = -70.2450;

    protected function setUp(): void
    {
        $this->pdo = Database::getInstance();
        // Cada test trabaja en su propia transacción: no altera datos reales
        $this->pdo->beginTransaction();

        $this->asegurarCatalogos();
    }

    protected function tearDown(): void
    {
        // Revierte todo lo insertado durante el test
        $this->pdo->rollBack();
    }

    /** Asegura que existan los catálogos mínimos requeridos (si faltan, se crean dentro de la transacción). */
    private function asegurarCatalogos(): void
    {
        foreach (['Pendiente', 'En desarrollo', 'Terminado'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM estado_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n' => $n]);
            if (!$q->fetchColumn()) {
                $ins = $this->pdo->prepare("INSERT INTO estado_incidencia (nombre) VALUES (:n)");
                $ins->execute(['n' => $n]);
            }
        }

        foreach (['Bacheo', 'Alumbrado', 'Limpieza'] as $n) {
            $q = $this->pdo->prepare("SELECT 1 FROM tipo_incidencia WHERE lower(nombre)=lower(:n)");
            $q->execute(['n' => $n]);
            if (!$q->fetchColumn()) {
                $ins = $this->pdo->prepare("INSERT INTO tipo_incidencia (nombre) VALUES (:n)");
                $ins->execute(['n' => $n]);
            }
        }
    }

    private function idEstado(string $nombre): int
    {
        $q = $this->pdo->prepare("SELECT id FROM estado_incidencia WHERE lower(nombre)=lower(:n) LIMIT 1");
        $q->execute(['n' => $nombre]);
        return (int)$q->fetchColumn();
    }

    private function idTipo(string $nombre): int
    {
        $q = $this->pdo->prepare("SELECT id FROM tipo_incidencia WHERE lower(nombre)=lower(:n) LIMIT 1");
        $q->execute(['n' => $nombre]);
        return (int)$q->fetchColumn();
    }

    /**
     * Inserta una incidencia con los campos mínimos que sabemos que existen en tu tabla.
     * @return int id de la incidencia creada
     */
    private function crearIncidencia(int $tipoId, int $estadoId, string $fechaISO, string $desc = 'desc'): int
    {
        $q = $this->pdo->prepare("
            INSERT INTO incidencia (
                descripcion, latitud, longitud, estado_id, tipo_id, fecha_reporte
            ) VALUES (
                :d, :lat, :lon, :e, :t, :f
            )
            RETURNING id
        ");
        $q->execute([
            'd'   => $desc,
            'lat' => self::DUMMY_LAT,
            'lon' => self::DUMMY_LON,
            'e'   => $estadoId,
            't'   => $tipoId,
            'f'   => $fechaISO, // 'YYYY-MM-DD HH:MM:SS'
        ]);
        return (int)$q->fetchColumn();
    }

    public function test_obtener_resumen_cuenta_por_estado_y_por_tipo_en_rango(): void
    {
        $pend   = $this->idEstado('Pendiente');
        $des    = $this->idEstado('En desarrollo');
        $term   = $this->idEstado('Terminado');

        $bache  = $this->idTipo('Bacheo');
        $alum   = $this->idTipo('Alumbrado');

        // Rango del día actual (de 00:00:00 a 23:59:59)
        $dia    = date('Y-m-d');
        $inicio = $dia . ' 00:00:00';
        $fin    = $dia . ' 23:59:59';

        // Crea datos dentro del rango
        $this->crearIncidencia($bache, $pend, $dia . ' 10:00:00');
        $this->crearIncidencia($bache, $pend, $dia . ' 11:00:00');
        $this->crearIncidencia($alum , $term, $dia . ' 12:30:00');
        // Fuera de rango (ayer) — no debe contarse
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $this->crearIncidencia($alum, $des, $ayer . ' 09:00:00');

        $resumen = ReporteService::obtenerResumen($inicio, $fin);

        // Estructura esperada
        $this->assertArrayHasKey('por_estado', $resumen);
        $this->assertArrayHasKey('por_tipo',   $resumen);

        // Índices por etiqueta para verificar totales
        $mapEstado = [];
        foreach ($resumen['por_estado'] as $r) {
            $mapEstado[$r['estado']] = (int)$r['total'];
        }

        $mapTipo = [];
        foreach ($resumen['por_tipo'] as $r) {
            $mapTipo[$r['tipo']] = (int)$r['total'];
        }

        // Verificaciones: 2 Pendiente, 1 Terminado (el "En desarrollo" fue fuera de rango)
        $this->assertSame(2, $mapEstado['Pendiente'] ?? 0);
        $this->assertSame(1, $mapEstado['Terminado']  ?? 0);
        $this->assertSame(0, $mapEstado['En desarrollo'] ?? 0); // puede que no exista el índice; por eso null-coalesce

        // Por tipo: 2 Bacheo, 1 Alumbrado
        $this->assertSame(2, $mapTipo['Bacheo']   ?? 0);
        $this->assertSame(1, $mapTipo['Alumbrado']?? 0);
    }

    public function test_obtener_por_rango_devuelve_lista_formateada_y_filtrada(): void
    {
        $pend  = $this->idEstado('Pendiente');
        $bache = $this->idTipo('Bacheo');

        $hoy   = date('Y-m-d');
        $man   = date('Y-m-d', strtotime('+1 day'));

        // En rango: hoy
        $id1 = $this->crearIncidencia($bache, $pend, $hoy . ' 08:15:00', 'r1');
        // Fuera de rango: mañana
        $this->crearIncidencia($bache, $pend, $man . ' 08:15:00', 'r2');

        $inicio = $hoy . ' 00:00:00';
        $fin    = $hoy . ' 23:59:59';

        $rows = App\Services\ReporteService::obtenerPorRango($inicio, $fin);

        // Debe traer solo la de hoy
        $this->assertNotEmpty($rows);
        $ids = array_map(fn($x) => (int)$x['id'], $rows);
        $this->assertContains($id1, $ids);

        // Formato esperado por fila
        $fila = $rows[0];
        $this->assertArrayHasKey('id',             $fila);
        $this->assertArrayHasKey('tipo',           $fila);
        $this->assertArrayHasKey('estado',         $fila);
        $this->assertArrayHasKey('fecha_reporte',  $fila);

        // La fecha viene como 'YYYY-MM-DD'
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string)$fila['fecha_reporte']);
    }

    public function test_rango_sin_datos_regresa_listas_vacias(): void
    {
        $inicio = '1999-01-01 00:00:00';
        $fin    = '1999-01-31 23:59:59';

        $resumen = ReporteService::obtenerResumen($inicio, $fin);
        $this->assertIsArray($resumen['por_estado']);
        $this->assertIsArray($resumen['por_tipo']);
        $this->assertCount(0, $resumen['por_estado']);
        $this->assertCount(0, $resumen['por_tipo']);

        $rows = ReporteService::obtenerPorRango($inicio, $fin);
        $this->assertIsArray($rows);
        $this->assertCount(0, $rows);
    }
}
