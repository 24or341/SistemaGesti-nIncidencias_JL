<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class IncidenciaRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function obtenerTodas(): array
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
            ORDER BY i.fecha_reporte ASC
        ";

        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new \RuntimeException('Error al consultar incidencias');
        }
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function obtenerTodasCiudadano(): array
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
                TO_CHAR(i.fecha_reporte, 'YYYY-MM-DD') AS fecha_reporte
            FROM incidencia i
            JOIN tipo_incidencia    ti ON i.tipo_id      = ti.id
            JOIN estado_incidencia  ei ON i.estado_id    = ei.id
            LEFT JOIN prioridad     pr ON i.prioridad_id = pr.id
            ORDER BY i.fecha_reporte ASC
        ";

        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new \RuntimeException('Error al consultar incidencias (ciudadano)');
        }
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public static function asignarEmpleado(int $incidenciaId, int $empleadoId, int $prioridadId, ?string $fechaProgramada = null): bool
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        // Verificar si ya está asignada a ese empleado
        $sqlCheck = "
            SELECT asignado_a
            FROM incidencia
            WHERE id = :incidencia_id
        ";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute(['incidencia_id' => $incidenciaId]);
        /** @var array<string, mixed>|false $row */
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (is_array($row) && array_key_exists('asignado_a', $row)) {
            $asignadoA = (int) $row['asignado_a'];
            if ($asignadoA === $empleadoId) {
                throw new \Exception("Esta incidencia ya fue asignada a este empleado.");
            }
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

            if ($fechaProgramada !== null) {
                $sql2 = "
                    INSERT INTO calendario_incidencia (incidencia_id, fecha_programada)
                    VALUES (:incidencia_id, :fecha_programada)
                    ON CONFLICT (incidencia_id) DO UPDATE 
                    SET fecha_programada = EXCLUDED.fecha_programada
                ";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute([
                    'incidencia_id'     => $incidenciaId,
                    'fecha_programada'  => $fechaProgramada
                ]);
            }

            $pdo->commit();
            return true;

        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
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

        if ($inicio !== null && $fin !== null) {
            $sql .= " AND i.fecha_reporte BETWEEN :inicio AND :fin";
            $params['inicio'] = $inicio;
            $params['fin']    = $fin;
        }

        $sql .= " ORDER BY i.fecha_reporte ASC";

        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute($params)) {
            throw new \RuntimeException('Error al consultar incidencias por empleado');
        }
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public static function actualizarEstado(int $incidenciaId, int $nuevoEstado): bool
    {
        $pdo = Database::getInstance();

        $sql = "UPDATE incidencia SET estado_id = :estado_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'estado_id' => $nuevoEstado,
            'id'        => $incidenciaId
        ]);
    }

    /**
     * @return array{}|array{ id:int }
     */
    public static function validarTelefono(string $celular): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT id FROM ciudadano WHERE celular = :celular LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['celular' => $celular]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($row) && isset($row['id'])) {
            return ['id' => (int) $row['id']];
        }
        return [];
    }

    public static function registrarCiudadano(string $celular): int
    {
        $pdo = Database::getInstance();

        $sql = "INSERT INTO ciudadano (celular) VALUES (:celular) RETURNING id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['celular' => $celular]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
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
        if (!$stmt->execute(['id_celular' => $idCelular])) {
            throw new \RuntimeException('Error al consultar incidencias del ciudadano');
        }
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
