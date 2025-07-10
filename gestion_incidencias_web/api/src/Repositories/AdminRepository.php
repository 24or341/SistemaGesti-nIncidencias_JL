<?php

    namespace App\Repositories;

    use App\Core\Database;
    use PDO;

    class AdminRepository
    {
        public static function obtenerPorEmail(string $email): ?array
        {
            $pdo = Database::getInstance();
            $sql = "
                SELECT *
                FROM usuario
                WHERE email = :email
                AND rol = 'administrador'
                LIMIT 1
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            return $admin ?: null;
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
            'rol'=>$data['rol'],
            'creado_por'=>$data['creado_por']  ?? null
            ]);
            return (int)$stmt->fetchColumn();
        }

        public static function actualizar(int $id, string $nombre, string $apellido, string $correo, ?string $dni): void {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("
                UPDATE usuario
                SET nombre = :nombre,
                    apellido = :apellido,
                    email = :correo,
                    dni = :dni
                WHERE id = :id AND rol = 'administrador'
            ");
            $stmt->execute([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'correo'   => $correo,
                'dni'      => $dni,
                'id'       => $id
            ]);
        }

    }

?>