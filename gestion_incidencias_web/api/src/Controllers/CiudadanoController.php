<?php

    namespace App\Controllers;

    use App\Core\Response;
    use App\Core\Database;
    use Exception;

    class CiudadanoController
    {
        public static function registrarIncidencia(array $data): void
        {
            if (
                empty($data['tipo_id']) ||
                empty($data['latitud']) ||
                empty($data['longitud']) ||
                empty($data['descripcion'])
            ) {
                Response::error("Todos los campos son obligatorios", 422);
            }

            $tipo_id = $data['tipo_id'];
            $lat = $data['latitud'];
            $lng = $data['longitud'];
            $descripcion = $data['descripcion'];
            $fecha = date("Y-m-d H:i:s");
            $zona = $data['zona'] ?? 'Zona no disponible';
            $id_celular = $data['id_celular'] ?? null;

            $foto = null;
            if (!empty($data['foto'])) {
                $foto = base64_decode($data['foto']);
            }

            try {
                $pdo = Database::getInstance();

                $stmt = $pdo->prepare("
                    INSERT INTO incidencia (
                        tipo_id, descripcion, foto, latitud, longitud, fecha_reporte, estado_id, zona, id_celular
                    ) VALUES (
                        :tipo_id, :descripcion, :foto, :lat, :lng, :fecha, 1, :zona, :id_celular
                    )
                ");

                $stmt->bindParam(':tipo_id', $tipo_id, \PDO::PARAM_INT);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':foto', $foto, \PDO::PARAM_LOB);
                $stmt->bindParam(':lat', $lat);
                $stmt->bindParam(':lng', $lng);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':zona', $zona);
                $stmt->bindParam(':id_celular', $id_celular, \PDO::PARAM_INT);

                $stmt->execute();

                Response::success([], "Incidencia reportada correctamente.");
            } catch (Exception $e) {
                Response::error("Error al registrar la incidencia: " . $e->getMessage(), 500);
            }
        }

        public static function obtenerTiposIncidencia(): void
        {
            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->query("SELECT id, nombre FROM tipo_incidencia ORDER BY nombre ASC");
                $tipos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                Response::json($tipos);
            } catch (Exception $e) {
                Response::error("Error al obtener tipos de incidencia: " . $e->getMessage(), 500);
            }
        }

        public static function obtenerHistorialIncidencias(int $idCelular): void
        {
            try {
                $incidencias = \App\Repositories\IncidenciaRepository::obtenerPorCiudadano($idCelular);
                Response::success($incidencias, "Historial de incidencias del ciudadano");
            } catch (Exception $e) {
                Response::error("Error al obtener historial de incidencias: " . $e->getMessage(), 500);
            }
        }

        public static function validarTelefono(string $celular): void
        {
            try {
                $pdo = Database::getInstance();

                $stmt = $pdo->prepare("SELECT id FROM ciudadano WHERE celular = :celular");
                $stmt->bindParam(':celular', $celular);
                $stmt->execute();
                $ciudadano = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($ciudadano) {
                    Response::success(['id' => $ciudadano['id']], "Teléfono ya registrado.");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO ciudadano (celular) VALUES (:celular) RETURNING id");
                    $stmt->bindParam(':celular', $celular);
                    $stmt->execute();
                    $nuevoId = $stmt->fetchColumn();

                    Response::success(['id' => $nuevoId], "Nuevo ciudadano registrado.");
                }
            } catch (Exception $e) {
                Response::error("Error al validar el teléfono: " . $e->getMessage(), 500);
            }
        }
    }

?>