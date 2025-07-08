<?php
    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class PrioridadRepository
    {
        public static function obtenerTodos(): array
        {
            $pdo = Database::getInstance();
            $stmt = $pdo->query("SELECT id, nivel FROM prioridad ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>