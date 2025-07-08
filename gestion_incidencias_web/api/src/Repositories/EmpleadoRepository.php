<?php

    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class EmpleadoRepository
    {
        public static function obtenerTodos(): array
        {
            $pdo = Database::getInstance();

            $query = "
                SELECT id, dni, nombre, apellido, email
                    FROM usuario
                    WHERE rol = 'empleado'
                    ORDER BY id ASC
            ";

            $stmt = $pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function obtenerPorEmail(string $email): ?array
        {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("
                SELECT id, nombre, apellido, dni, email, password
                    FROM usuario
                    WHERE email = :email
                    AND rol = 'empleado'
                    LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            $emp = $stmt->fetch(PDO::FETCH_ASSOC);
            return $emp ?: null;
        }

        public static function create(array $data): int {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("
                INSERT INTO usuario
                (nombre,apellido,dni,email,password,rol,creado_por)
                VALUES
                (:nombre,:apellido,:dni,:email,:password,:rol,:creado_por)
                RETURNING id
            ");
            $stmt->execute([
            'nombre'=>$data['nombre'],
            'apellido'=>$data['apellido'],
            'dni'=>$data['dni']        ?? null,
            'email'=>$data['email'],
            'password'=>$data['password'],
            'rol'=> 'empleado',
            'creado_por'=>$data['creado_por']   ?? null
            ]);
            return (int)$stmt->fetchColumn();
        }
    }

?>