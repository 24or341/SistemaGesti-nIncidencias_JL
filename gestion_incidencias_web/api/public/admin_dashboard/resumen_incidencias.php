<?php
// Configuración del archivo resumen_incidencias.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\Auth;
use App\Core\Response;
use App\Core\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { // Verifica el método de la solicitud
    Response::error('Método no permitido', 405); // Método no permitido
}

$token = Auth::extractBearerFromServer(); // Extrae el token del encabezado
if ($token === null) { // Verifica si el token es nulo
    Response::error("Token requerido", 401); // Token requerido
}

try { // Intenta verificar el token
    $user = Auth::verificarToken($token); // Verifica el token
} catch (\Exception $e) { // Captura la excepción
    Response::error("Token inválido", 401); // Token inválido
}

if (($user['role'] ?? '') !== 'administrador') { // Verifica el rol del usuario
    Response::error("Permiso denegado", 403); // Permiso denegado
}

try { // Intenta obtener las estadísticas
    $pdo = Database::getInstance(); // Obtiene la instancia de la base de datos
    $stmt = $pdo->query("
        SELECT ei.nombre AS estado, COUNT(*) AS total
        FROM incidencia i
        INNER JOIN estado_incidencia ei ON i.estado_id = ei.id
        GROUP BY ei.nombre
    "); // Ejecuta la consulta
    if ($stmt === false) { // Verifica si la consulta falló
        throw new \RuntimeException("Error al consultar resumen"); // Lanza excepción si falla
    }

    $resumen = [
        'Pendiente'     => 0,
        'En Desarrollo' => 0,
        'Terminado'     => 0,
    ]; // Inicializa el resumen

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) { // Itera sobre los resultados
        $estado = $row['estado'] ?? null; // Obtiene el estado
        $total = isset($row['total']) ? (int)$row['total'] : 0; // Obtiene el total
        if ($estado !== null) {
            $resumen[$estado] = $total;
        } // Asigna el total al estado correspondiente
    }

    Response::success($resumen, "Resumen de incidencias"); // Respuesta exitosa
} catch (\Throwable $e) { // Captura cualquier excepción
    Response::error("Error al cargar resumen: " . $e->getMessage(), 500); // Error interno del servidor
}
