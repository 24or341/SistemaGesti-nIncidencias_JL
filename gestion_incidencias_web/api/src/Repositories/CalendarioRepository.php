<?php
    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class CalendarioRepository
    {
        public static function guardarFecha(int $incidenciaId, string $fecha): bool
        {
            $pdo = Database::getInstance();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM calendario_incidencia WHERE incidencia_id = :id");
            $stmt->execute(['id' => $incidenciaId]);
            $existe = $stmt->fetchColumn() > 0;

            if ($existe) {
                $sql = "UPDATE calendario_incidencia SET fecha_programada = :fecha WHERE incidencia_id = :id";
            } else {
                $sql = "INSERT INTO calendario_incidencia (incidencia_id, fecha_programada) VALUES (:id, :fecha)";
            }

            $stmt = $pdo->prepare($sql);
            return $stmt->execute(['id' => $incidenciaId, 'fecha' => $fecha]);
        }
    }
?>