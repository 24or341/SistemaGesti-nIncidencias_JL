<?php
// Configuración del archivo asignar_incidencia.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08

// Modificación del archivo asignar_incidencia.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-10-28
// Requerimiento: RF06 - Asignación de incidencias y panel de supervisión

// Funciones principales:
// Verificación de método HTTP (solo POST).
// Extracción del token Bearer (Auth::extractBearerFromServer()).
// Verificación/decodificación del JWT (Auth::verificarToken()).
// Validación de rol (requiere administrador).
// Lectura segura del cuerpo (file_get_contents) y decodificación JSON.
// Normalización y validación de IDs ($toInt – closure para castear incidencia_id, empleado_id, prioridad_id).
// Validación opcional de fecha_programada (formato YYYY-MM-DD y no anterior a hoy).
// Verificación de asignación única (EmpleadoController::validarAsignacionUnica()).
// Asignación de incidencia (IncidenciaController::asignarEmpleado()).
// Manejo centralizado de errores y respuestas (Response::error / flujo de éxito implícito).


declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../../bootstrap.php'; // Carga el bootstrap para inicializar el entorno

use App\Core\Auth; // Usa la clase Auth
use App\Core\Response; // Usa la clase Response
use App\Controllers\IncidenciaController; // Usa la clase IncidenciaController
use App\Controllers\EmpleadoController; // Usa la clase EmpleadoController

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Verifica que el método sea POST
    Response::error('Método no permitido', 405);
}

$token = Auth::extractBearerFromServer(); // Extrae el token Bearer
if ($token === null) { // Verifica que el token exista
    Response::error('Token requerido', 401);
}
/** @var string $token */

try { // Verifica el token y obtiene el usuario
    /** @var array{user_id:mixed,role:string,email?:string|null,nombre?:string|null,iat?:int,exp?:int} $user */
    $user = Auth::verificarToken($token);
} catch (\Exception $e) { // Captura errores de verificación
    Response::error('Token inválido', 401);
}

if ($user['role'] !== 'administrador') { // Verifica que el rol sea administrador
    Response::error('Permiso denegado', 403);
}

$inputRaw = file_get_contents('php://input'); // Lee el cuerpo de la solicitud
if ($inputRaw === false) { // Verifica que el cuerpo no esté vacío
    Response::error('Cuerpo de la solicitud vacío', 400);
}
$body = $inputRaw; // ya es string  

$data = json_decode($body, true); // Decodifica el JSON
if (!is_array($data)) {
    Response::error('JSON inválido', 400);
}
/** @var array<string,mixed> $payloadData */
$payloadData = $data;

/**
 * Normaliza un entero desde mixed solo si es int nativo o string de dígitos.
 * @return int|null
 */
$toInt = static function ($v): ?int { // Closure para normalizar enteros
    if (is_int($v)) { // Si ya es int, retornarlo
        return $v;
    }
    if (is_string($v) && preg_match('/^\d+$/', $v) === 1) { // Si es string de dígitos, convertir a int
        return (int) $v;
    }
    return null;
};

$incidenciaId = $toInt($payloadData['incidencia_id'] ?? null); // Normaliza incidencia_id
if ($incidenciaId === null) { // Verifica que incidencia_id sea válido
    Response::error('Faltan datos o son inválidos: incidencia_id', 422);
}
/** @var int $incidenciaId */

$empleadoId = $toInt($payloadData['empleado_id'] ?? null); // Normaliza empleado_id
if ($empleadoId === null) { // Verifica que empleado_id sea válido
    Response::error('Faltan datos o son inválidos: empleado_id', 422);
}
/** @var int $empleadoId */

$prioridadId = $toInt($payloadData['prioridad_id'] ?? null); // Normaliza prioridad_id
if ($prioridadId === null) { // Verifica que prioridad_id sea válido
    Response::error('Faltan datos o son inválidos: prioridad_id', 422);
}
/** @var int $prioridadId */

// Fecha programada (opcional)
$fecha = null; // Inicializa fecha como null
if (array_key_exists('fecha_programada', $payloadData)) { // Si se proporciona fecha_programada
    $rawFecha = $payloadData['fecha_programada'];
    if ($rawFecha !== null) { // Permite null para quitar fecha programada
        if (!is_string($rawFecha)) { // Verifica que sea string
            Response::error('Formato de fecha inválido. Use YYYY-MM-DD', 422);
        }
        $fecha = $rawFecha;            /** @var string $fecha */
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) !== 1) { // Verifica formato YYYY-MM-DD
            Response::error('Formato de fecha inválido. Use YYYY-MM-DD', 422);
        }
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) { // Verifica que no sea anterior a hoy
            Response::error('La fecha programada no puede ser anterior a hoy', 422);
        }
    }
}

$payload = [ // Construye el payload para la asignación
    'incidencia_id'    => $incidenciaId,
    'empleado_id'      => $empleadoId,
    'prioridad_id'     => $prioridadId,
];
if ($fecha !== null) { // Si se proporcionó fecha, agregar al payload
    $payload['fecha_programada'] = $fecha;
}

EmpleadoController::validarAsignacionUnica($payload); // Verifica asignación única

try { // Intenta asignar la incidencia
    IncidenciaController::asignarEmpleado($payload);
} catch (\Throwable $e) {
    Response::error('Error interno: ' . $e->getMessage(), 500);
}