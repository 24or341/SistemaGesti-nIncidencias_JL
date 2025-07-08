<?php
    namespace App\Controllers;

    use App\Services\IncidenciaService;
    use App\Core\Response;

    class IncidenciaController
    {
        public static function listar(): void
        {
            try {
                $data = IncidenciaService::obtenerTodas();
                Response::success($data, "Listado de incidencias");
            } catch (\Exception $e) {
                Response::error("Error al obtener incidencias: " . $e->getMessage(), 500);
            }
        }

        public static function actualizarEstado(array $data): void
        {
            if (empty($data['incidencia_id']) || empty($data['nuevo_estado'])) {
                Response::error("Datos incompletos", 422);
            }

            $resultado = IncidenciaService::actualizarEstado($data['incidencia_id'], $data['nuevo_estado']);

            if ($resultado) {
                Response::success([], "Estado actualizado correctamente");
            } else {
                Response::error("No se pudo actualizar el estado", 500);
            }
        }

        public static function asignarEmpleado(array $data): void
        {
            if (
                empty($data['incidencia_id']) ||
                empty($data['empleado_id']) ||
                empty($data['prioridad_id'])
            ) {
                Response::error("Datos incompletos para la asignación", 422);
            }

            $fecha_programada = $data['fecha_programada'] ?? null;

            try {
                $ok = IncidenciaService::asignarEmpleado(
                    (int)$data['incidencia_id'],
                    (int)$data['empleado_id'],
                    (int)$data['prioridad_id'],
                    $fecha_programada
                );
            } catch (\Throwable $e) {
                error_log('[asignarEmpleado] ' . $e->getMessage());
                Response::error("Error interno al asignar la incidencia", 500);
            }

            if ($ok) {
                Response::success([], "Incidencia asignada y priorizada");
            } else {
                Response::error("Error al asignar la incidencia", 500);
            }
        }

        public static function obtenerPorEmpleado(int $empleadoId): void
        {
            try {
                $incidencias = IncidenciaService::obtenerPorEmpleado($empleadoId);

                foreach ($incidencias as &$incidencia) {
                    if (isset($incidencia['foto']) && !empty($incidencia['foto'])) {
                        if (is_resource($incidencia['foto'])) {
                            $binario = stream_get_contents($incidencia['foto']);
                            $incidencia['foto'] = base64_encode($binario);
                        } else {
                            $incidencia['foto'] = base64_encode($incidencia['foto']);
                        }
                    }
                }

                Response::success($incidencias, "Incidencias asignadas al empleado");
            } catch (\Exception $e) {
                Response::error("Error al obtener incidencias: " . $e->getMessage(), 500);
            }
        }

        public static function programarFecha(): void
        {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['incidencia_id']) || empty($data['fecha'])) {
                \App\Core\Response::error("Datos incompletos", 400);
            }

            $id = (int)$data['incidencia_id'];
            $fecha = $data['fecha'];

            $ok = \App\Services\CalendarioService::programar($id, $fecha);

            if (!$ok) {
                \App\Core\Response::error("La fecha no puede ser anterior a hoy", 422);
            }

            \App\Core\Response::success([], "Fecha programada correctamente");
        }
    }
?>