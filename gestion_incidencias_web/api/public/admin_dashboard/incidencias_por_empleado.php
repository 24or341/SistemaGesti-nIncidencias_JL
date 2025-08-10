<?php
// Configuración del archivo incidencias_por_empleados.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../../bootstrap.php'; // Carga las dependencias

use App\Core\Auth;
use App\Core\Response;
use App\Core\Database;
use App\Repositories\IncidenciaRepository;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { // Verifica el método de la solicitud
    Response::error('Método no permitido', 405); // Envía una respuesta de error
}

$token = Auth::extractBearerFromServer(); // Extrae el token de la cabecera
if ($token === null) { // Verifica si el token es nulo
    Response::error("Token requerido", 401); // Envía una respuesta de error
}

try { // Intenta verificar el token
    $user = Auth::verificarToken($token); // Verifica el token
} catch (\Exception $e) { // Captura cualquier excepción
    Response::error("Token inválido", 401); // Envía una respuesta de error
}

if (($user['role'] ?? '') !== 'administrador') { // Verifica si el usuario tiene el rol de administrador
    Response::error("Permiso denegado", 403); // Envía una respuesta de error
}

$inicio = $_GET['inicio'] ?? null; // Obtiene la fecha de inicio
$fin    = $_GET['fin'] ?? null; // Obtiene la fecha de fin

try { // Intenta obtener las incidencias por empleado
    $pdo = Database::getInstance(); // Obtiene la instancia de la base de datos

    $stmtEmp = $pdo->query("
        SELECT id, nombre || ' ' || apellido AS nombre_completo
        FROM usuario
        WHERE rol = 'empleado'
        ORDER BY apellido, nombre
    "); // Ejecuta la consulta
    if ($stmtEmp === false) { // Verifica si la consulta falló
        throw new \RuntimeException("Error al consultar empleados"); // Lanza una excepción
    } // Fin de la verificación de errores
    $empleados = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC); // Obtiene todos los empleados

    $data = []; // Inicializa el array de datos
    foreach ($empleados as $emp) { // Itera sobre los empleados
        $empId = isset($emp['id']) ? (int)$emp['id'] : null; // Obtiene el ID del empleado
        if ($empId === null) { // Verifica si el ID del empleado es nulo
            continue; // Salta al siguiente empleado
        } // Fin de la verificación del ID del empleado
        $incidencias = IncidenciaRepository::obtenerPorEmpleado($empId, $inicio, $fin); // Obtiene las incidencias del empleado
        $data[] = [
            'empleado_id' => $empId,
            'empleado'    => $emp['nombre_completo'] ?? '',
            'incidencias' => $incidencias
        ]; // Agrega los datos del empleado y sus incidencias
    }

    Response::success($data, 'Incidencias agrupadas por empleado obtenidas correctamente'); // Envía la respuesta de éxito
} catch (\Throwable $e) { // Captura cualquier excepción
    Response::error('Error obteniendo incidencias por empleado: ' . $e->getMessage(), 500); // Envía una respuesta de error
}
