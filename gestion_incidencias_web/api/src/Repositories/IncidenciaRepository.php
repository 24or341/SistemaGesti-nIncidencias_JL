<?php

    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class IncidenciaRepository
    {
        public static function obtenerTodas(): array
        {
            $pdo = Database::getInstance();

            $sql = "
                SELECT 
                    i.id,
                    ti.nombre  AS tipo,
                    ei.nombre  AS estado,
                    pr.nivel   AS prioridad,
                    i.descripcion,
                    i.latitud,
                    i.longitud,
                    TO_CHAR(i.fecha_reporte,'YYYY-MM-DD') AS fecha_reporte,
                    TO_CHAR(c.fecha_programada, 'YYYY-MM-DD') AS fecha_programada
                FROM incidencia i
                JOIN tipo_incidencia    ti ON i.tipo_id      = ti.id
                JOIN estado_incidencia  ei ON i.estado_id    = ei.id
                LEFT JOIN prioridad     pr ON i.prioridad_id = pr.id
                LEFT JOIN calendario_incidencia c ON i.id = c.incidencia_id
                ORDER BY i.fecha_reporte ASC
            ";

            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function asignarEmpleado(int $incidenciaId, int $empleadoId, int $prioridadId, ?string $fechaProgramada = null): bool
        {
            $pdo = Database::getInstance();
            $pdo->beginTransaction();

            $sqlCheck = "
                SELECT asignado_a
                FROM incidencia
                WHERE id = :incidencia_id
            ";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute(['incidencia_id' => $incidenciaId]);
            $result = $stmtCheck->fetch();

            if ($result && (int)$result['asignado_a'] === $empleadoId) {
                throw new \Exception("Esta incidencia ya fue asignada a este empleado.");
            }
            
            try {
                $sql1 = "
                    UPDATE incidencia
                    SET asignado_a = :empleado_id,
                        prioridad_id = :prioridad_id
                    WHERE id = :incidencia_id
                ";
                $stmt1 = $pdo->prepare($sql1);
                $stmt1->execute([
                    'empleado_id'    => $empleadoId,
                    'prioridad_id'   => $prioridadId,
                    'incidencia_id'  => $incidenciaId
                ]);

                if ($fechaProgramada) {
                    $sql2 = "
                        INSERT INTO calendario_incidencia (incidencia_id, fecha_programada)
                        VALUES (:incidencia_id, :fecha_programada)
                        ON CONFLICT (incidencia_id) DO UPDATE SET fecha_programada = EXCLUDED.fecha_programada
                    ";
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->execute([
                        'incidencia_id'     => $incidenciaId,
                        'fecha_programada'  => $fechaProgramada
                    ]);
                }

                $pdo->commit();
                return true;

            } catch (\Exception $e) {
                $pdo->rollBack();
                die(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
            }
        }

        public static function obtenerPorEmpleado(int $empleadoId, ?string $inicio = null, ?string $fin = null): array
        {
            $pdo = Database::getInstance();

            $sql = "
                SELECT 
                    i.id,
                    i.foto,
                    ti.nombre    AS tipo,
                    ei.nombre    AS estado,
                    pr.nivel     AS prioridad,
                    i.descripcion,
                    i.latitud,
                    i.longitud,
                    TO_CHAR(i.fecha_reporte, 'YYYY-MM-DD') AS fecha_reporte,
                    TO_CHAR(c.fecha_programada, 'YYYY-MM-DD') AS fecha_programada
                FROM incidencia i
                INNER JOIN tipo_incidencia   ti ON i.tipo_id   = ti.id
                INNER JOIN estado_incidencia ei ON i.estado_id = ei.id
                LEFT JOIN prioridad          pr ON i.prioridad_id = pr.id
                LEFT JOIN calendario_incidencia c ON i.id = c.incidencia_id
                WHERE i.asignado_a = :empleado_id
            ";

            $params = ['empleado_id' => $empleadoId];

            if ($inicio && $fin) {
                $sql .= " AND i.fecha_reporte BETWEEN :inicio AND :fin";
                $params['inicio'] = $inicio;
                $params['fin'] = $fin;
            }

            $sql .= " ORDER BY i.fecha_reporte ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function actualizarEstado(int $incidenciaId, int $nuevoEstado): bool
        {
            $pdo = Database::getInstance();

            $sql = "UPDATE incidencia SET estado_id = :estado_id WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                'estado_id' => $nuevoEstado,
                'id' => $incidenciaId
            ]);
        }

        public static function validarTelefono(string $celular): array
        {
            $pdo = Database::getInstance();

            $sql = "SELECT id FROM ciudadano WHERE celular = :celular LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['celular' => $celular]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        public static function registrarCiudadano(string $celular): int
        {
            $pdo = Database::getInstance();

            $sql = "INSERT INTO ciudadano (celular) VALUES (:celular) RETURNING id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['celular' => $celular]);

            return (int) $stmt->fetchColumn();
        }

        public static function obtenerPorCiudadano(int $idCelular): array
        {
            $pdo = Database::getInstance();

            $sql = "
                SELECT 
                    i.id,
                    ti.nombre AS tipo,
                    ei.nombre AS estado,
                    pr.nivel   AS prioridad,
                    i.descripcion,
                    i.latitud,
                    i.longitud,
                    TO_CHAR(i.fecha_reporte,'YYYY-MM-DD') AS fecha_reporte
                FROM incidencia i
                INNER JOIN tipo_incidencia    ti ON i.tipo_id      = ti.id
                INNER JOIN estado_incidencia  ei ON i.estado_id    = ei.id
                LEFT JOIN prioridad           pr ON i.prioridad_id = pr.id
                WHERE i.id_celular = :id_celular
                ORDER BY i.fecha_reporte ASC
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id_celular' => $idCelular]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>