<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PrioridadRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function obtenerTodos(): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT id, nivel AS prioridad FROM prioridad ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            // Evitar retornar valores inesperados si la ejecución falla
            throw new \RuntimeException('No fue posible obtener las prioridades.');
        }

        /** @var array<int, array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
