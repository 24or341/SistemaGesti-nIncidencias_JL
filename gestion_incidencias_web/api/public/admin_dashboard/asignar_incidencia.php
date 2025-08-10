<?php
// Configuración del archivo asignar_incidencia.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../../bootstrap.php'; // Carga las dependencias

use App\Core\Auth;
use App\Core\Response;
use App\Controllers\IncidenciaController;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Verifica el método de la solicitud
    Response::error("Método no permitido", 405); // Envía una respuesta de error
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

$inputRaw = file_get_contents('php://input'); // Obtiene el contenido bruto de la solicitud
$data = json_decode($inputRaw, true); // Decodifica el JSON
if (!is_array($data)) { // Verifica si los datos son un array
    Response::error("JSON inválido", 400); // Envía una respuesta de error
}

if (
    !isset($data['incidencia_id']) || // Verifica si falta el ID de la incidencia
    !isset($data['empleado_id'])   || // Verifica si falta el ID del empleado
    !isset($data['prioridad_id'])   // Verifica si falta el ID de la prioridad
) {
    Response::error("Faltan datos", 422); // Envía una respuesta de error
}

if (!empty($data['fecha_programada'])) { // Verifica si se proporcionó una fecha programada
    $fecha = (string)$data['fecha_programada']; // Convierte la fecha a string
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) { // Verifica el formato de la fecha
        Response::error("Formato de fecha inválido. Use YYYY-MM-DD", 422); // Envía una respuesta de error
    }

    $hoy = date('Y-m-d'); // Obtiene la fecha actual
    if ($fecha < $hoy) { // Verifica si la fecha programada es anterior a hoy
        Response::error("La fecha programada no puede ser anterior a hoy", 422); // Envía una respuesta de error
    }
}

\App\Controllers\EmpleadoController::validarAsignacionUnica($data); // Verifica la asignación única

try { // Intenta asignar el empleado
    IncidenciaController::asignarEmpleado($data); // Asigna el empleado a la incidencia
} catch (\Throwable $e) { // Captura cualquier excepción
    Response::error("Error interno: " . $e->getMessage(), 500); // Envía una respuesta de error
}
