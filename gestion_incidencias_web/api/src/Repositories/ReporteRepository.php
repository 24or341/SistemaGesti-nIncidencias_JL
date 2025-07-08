<?php

    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class ReporteRepository
    {
        public static function contarPorEstado(string $inicio, string $fin): array
        {
            $pdo = Database::getInstance();
            $sql = "
                SELECT ei.nombre AS estado, COUNT(*) AS total
                FROM incidencia i
                INNER JOIN estado_incidencia ei ON i.estado_id = ei.id
                WHERE i.fecha_reporte BETWEEN :inicio AND :fin
                GROUP BY ei.nombre
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['inicio' => $inicio, 'fin' => $fin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function contarPorTipo(string $inicio, string $fin): array
        {
            $pdo = Database::getInstance();
            $sql = "
                SELECT ti.nombre AS tipo, COUNT(*) AS total
                FROM incidencia i
                INNER JOIN tipo_incidencia ti ON i.tipo_id = ti.id
                WHERE i.fecha_reporte BETWEEN :inicio AND :fin
                GROUP BY ti.nombre
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['inicio' => $inicio, 'fin' => $fin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function obtenerPorRango(string $inicio, string $fin): array
        {
            $pdo = Database::getInstance();
            $sql = "
                SELECT 
                    i.id,
                    ti.nombre AS tipo,
                    ei.nombre AS estado,
                    i.fecha_reporte
                FROM incidencia i
                INNER JOIN tipo_incidencia ti ON i.tipo_id = ti.id
                INNER JOIN estado_incidencia ei ON i.estado_id = ei.id
                WHERE i.fecha_reporte BETWEEN :inicio AND :fin
                ORDER BY i.fecha_reporte DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['inicio' => $inicio, 'fin' => $fin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public static function obtenerPorEmpleado(int $empleadoId, string $inicio, string $fin): array
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
                AND i.fecha_reporte BETWEEN :inicio AND :fin
                ORDER BY i.fecha_reporte ASC
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'empleado_id' => $empleadoId,
                'inicio'      => $inicio,
                'fin'         => $fin
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>