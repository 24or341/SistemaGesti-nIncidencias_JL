<?php
namespace App\Controllers;

use App\Services\IncidenciaService;
use App\Services\CalendarioService;
use App\Core\Response;

class IncidenciaController
{
    public static function listar(): void
    {
        try {
            /** @var array<int, array<string, mixed>> $incidencias */
            $incidencias = IncidenciaService::obtenerTodas();

            foreach ($incidencias as &$incidencia) {
                $foto = $incidencia['foto'] ?? null;

                if (is_resource($foto)) {
                    $bin = stream_get_contents($foto);
                    $incidencia['foto'] = ($bin !== false) ? base64_encode($bin) : null;
                } elseif (is_string($foto)) {
                    $incidencia['foto'] = base64_encode($foto);
                } else {
                    $incidencia['foto'] = null;
                }
            }
            unset($incidencia);

            Response::success($incidencias, "Listado de incidencias");
        } catch (\Exception $e) {
            Response::error("Error al obtener incidencias: " . $e->getMessage(), 500);
        }
    }

    /**
     * @param array{incidencia_id:int|string, nuevo_estado:int|string} $data
     */
    public static function actualizarEstado(array $data): void
    {
        // El PHPDoc ya garantiza las claves; validamos valores/forma.
        $incidenciaId = is_numeric($data['incidencia_id']) ? (int)$data['incidencia_id'] : 0;
        $nuevoEstado  = is_numeric($data['nuevo_estado'])   ? (int)$data['nuevo_estado']   : 0;

        if ($incidenciaId <= 0 || $nuevoEstado <= 0) {
            Response::error("Parámetros inválidos", 422);
        }

        $ok = IncidenciaService::actualizarEstado($incidenciaId, $nuevoEstado);
        $ok ? Response::success([], "Estado actualizado correctamente")
            : Response::error("No se pudo actualizar el estado", 500);
    }

    /**
     * @param array{
     *   incidencia_id:int|string,
     *   empleado_id:int|string,
     *   prioridad_id:int|string,
     *   fecha_programada?:string|null
     * } $data
     */
    public static function asignarEmpleado(array $data): void
    {
        // El PHPDoc ya garantiza las claves; validamos forma y rango.
        $incidenciaId = is_numeric($data['incidencia_id']) ? (int)$data['incidencia_id'] : 0;
        $empleadoId   = is_numeric($data['empleado_id'])   ? (int)$data['empleado_id']   : 0;
        $prioridadId  = is_numeric($data['prioridad_id'])  ? (int)$data['prioridad_id']  : 0;

        if ($incidenciaId <= 0 || $empleadoId <= 0 || $prioridadId <= 0) {
            Response::error("IDs inválidos para la asignación", 422);
        }

        $fechaProgramada = $data['fecha_programada'] ?? null;
        if ($fechaProgramada !== null) {
            if (!is_string($fechaProgramada)) {
                Response::error("Formato de fecha inválido", 422);
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaProgramada)) {
                Response::error("Formato de fecha inválido. Use YYYY-MM-DD", 422);
            }
        }

        $ok = false;
        try {
            $ok = IncidenciaService::asignarEmpleado(
                $incidenciaId,
                $empleadoId,
                $prioridadId,
                $fechaProgramada
            );
        } catch (\Throwable $e) {
            error_log('[asignarEmpleado] ' . $e->getMessage());
            Response::error("Error interno al asignar la incidencia", 500);
        }

        $ok ? Response::success([], "Incidencia asignada y priorizada")
            : Response::error("Error al asignar la incidencia", 500);
    }

    public static function obtenerPorEmpleado(int $empleadoId): void
    {
        try {
            /** @var array<int, array<string, mixed>> $incidencias */
            $incidencias = IncidenciaService::obtenerPorEmpleado($empleadoId);

            foreach ($incidencias as &$incidencia) {
                $foto = $incidencia['foto'] ?? null;

                if (is_resource($foto)) {
                    $bin = stream_get_contents($foto);
                    $incidencia['foto'] = ($bin !== false) ? base64_encode($bin) : null;
                } elseif (is_string($foto)) {
                    $incidencia['foto'] = base64_encode($foto);
                } else {
                    $incidencia['foto'] = null;
                }
            }
            unset($incidencia);

            Response::success($incidencias, "Incidencias asignadas al empleado");
        } catch (\Exception $e) {
            Response::error("Error al obtener incidencias: " . $e->getMessage(), 500);
        }
    }

    public static function programarFecha(): void
    {
        $raw = file_get_contents("php://input");
        if ($raw === false) {
            Response::error("No se pudo leer el cuerpo de la solicitud", 400);
        }
        /** @var string $raw */ // afirmar para PHPStan

        /** @var mixed $data */
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            Response::error("JSON inválido", 400);
        }

        $incidenciaId = $data['incidencia_id'] ?? null;
        $fecha        = $data['fecha'] ?? null;

        if (!is_numeric($incidenciaId) || !is_string($fecha)) {
            Response::error("Datos incompletos o inválidos", 400);
        }

        $id = (int)$incidenciaId;

        $ok = CalendarioService::programar($id, $fecha);
        if (!$ok) {
            Response::error("La fecha no puede ser anterior a hoy", 422);
        }

        Response::success([], "Fecha programada correctamente");
    }
}
