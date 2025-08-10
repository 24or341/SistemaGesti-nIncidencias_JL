<?php
// Configuración del archivo actualizar_perfil.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../bootstrap.php'; // Carga el archivo de configuración

use App\Controllers\AdminController;
use App\Controllers\EmpleadoController;
use App\Core\Auth;
use App\Core\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') { // Verifica el método de la solicitud
    Response::error("Método no permitido", 405); // Envía una respuesta de error
}

$inputRaw = file_get_contents("php://input"); // Obtiene el contenido bruto de la solicitud
$input = json_decode($inputRaw, true); // Decodifica el JSON

if (!is_array($input)) { // Verifica si la decodificación fue exitosa
    Response::error("JSON inválido", 400); // Envía una respuesta de error
}

// extraer token
$token = Auth::extractBearerFromServer(); // Extrae el token de la cabecera
if ($token === null) { // Verifica si el token es nulo
    Response::error("Token inválido o no proporcionado", 401); // Envía una respuesta de error
}

try { // Intenta verificar el token
    $datosToken = Auth::verificarToken($token); // Verifica el token
} catch (\Exception $e) { // Captura cualquier excepción
    Response::error("Token inválido", 401); // Envía una respuesta de error
}

$role   = $datosToken['role'] ?? ''; // Obtiene el rol del usuario
$userId = $datosToken['user_id'] ?? null; // Obtiene el ID del usuario

if (!$userId || !in_array($role, ['administrador', 'empleado'], true)) { // Verifica si el usuario tiene un rol válido
    Response::error("Token no autorizado o rol inválido", 403); // Envía una respuesta de error
}

// Campos obligatorios
$nombre   = trim((string)($input['nombre'] ?? '')); // Obtiene el nombre
$apellido = trim((string)($input['apellido'] ?? '')); // Obtiene el apellido
$correo   = trim((string)($input['email'] ?? '')); // Obtiene el correo
$dni      = isset($input['dni']) ? trim((string)$input['dni']) : null; // Obtiene el DNI

if ($nombre === '' || $apellido === '' || $correo === '') { // Verifica campos obligatorios
    Response::error("Nombre, apellido y correo son obligatorios", 422); // Envía una respuesta de error
}

try { // Intenta actualizar el perfil
    if ($role === 'administrador') { // Si el rol es administrador
        AdminController::actualizarPerfil((int)$userId, $nombre, $apellido, $correo, $dni); // Actualiza el perfil del administrador
    } else { // Si el rol es empleado
        EmpleadoController::actualizarPerfil((int)$userId, $nombre, $apellido, $correo, $dni); // Actualiza el perfil del empleado
    }

    Response::success(null, "Perfil actualizado correctamente"); // Envía una respuesta de éxito
} catch (\PDOException $e) { // Captura cualquier excepción de PDO
    if ((string)$e->getCode() === '23505') { // Verifica si el código de error es 23505
        Response::error("El correo ya está en uso", 409); // Envía una respuesta de error
    }
    Response::error("Error al actualizar perfil: " . $e->getMessage(), 500); // Envía una respuesta de error
}
