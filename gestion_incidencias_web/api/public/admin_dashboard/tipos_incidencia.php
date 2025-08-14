<?php
// Configuración del archivo tipos_incidencia.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../../bootstrap.php'; // Carga el archivo de configuración

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\PrioridadController; // not used but controller structure exists
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

try { // Intenta obtener los tipos de incidencia
    $pdo = Database::getInstance(); // Obtiene la instancia de la base de datos
    $stmt = $pdo->query("SELECT id, nombre FROM tipo_incidencia ORDER BY nombre ASC"); // Ejecuta la consulta
    if ($stmt === false) { // Verifica si la consulta falló
        throw new \RuntimeException("Error al obtener tipos"); // Lanza excepción si falla
    }
    $tipos = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtiene todos los tipos de incidencia
    Response::success($tipos, "Tipos de incidencia obtenidos correctamente"); // Respuesta exitosa
} catch (\Throwable $e) { // Captura cualquier excepción
    Response::error("Error al obtener tipos de incidencia: " . $e->getMessage(), 500); // Error interno del servidor
}
