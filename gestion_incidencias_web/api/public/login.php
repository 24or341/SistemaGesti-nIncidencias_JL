<?php
// Configuración del archivo login.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto
require_once __DIR__ . '/../bootstrap.php'; // Carga el archivo de configuración

use App\Controllers\AdminController;
use App\Controllers\EmpleadoController;
use App\Core\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Verifica el método de la solicitud
    Response::error("Método no permitido", 405); // Envía una respuesta de error
}

$inputRaw = file_get_contents("php://input"); // Obtiene el contenido bruto de la solicitud
$input = json_decode($inputRaw, true); // Decodifica el JSON

if (!is_array($input)) { // Verifica si la decodificación fue exitosa
    Response::error("JSON inválido", 400); // Envía una respuesta de error
}

$email    = trim((string)($input['email']    ?? '')); // Obtiene el email
$password = trim((string)($input['password'] ?? '')); // Obtiene la contraseña
$otp      = trim((string)($input['otp']      ?? '')); // Obtiene el OTP

if ($email === '' || $password === '') { // Verifica si faltan campos
    Response::error("Email y contraseña requeridos", 422); // Envía una respuesta de error
}

try {
    // Paso 1: pre-login admin (verifica email+password pero no emite token aún)
    $adminRow = \App\Services\AdminService::preLogin($email, $password);

    if ($adminRow) {
        // Si el admin tiene MFA habilitado:
        if (!empty($adminRow['mfa_enabled'])) {
            if ($otp === '') {
                // Pedir OTP (paso 2 en el frontend)
                Response::success(['mfa_required' => true], "OTP requerido");
            }

            $ok = \App\Services\AdminService::verificarOtp($adminRow, $otp);
            if (!$ok) {
                Response::error("OTP inválido", 401);
            }

            // OTP válido → emitir token y completar login
            $payload = \App\Services\AdminService::emitirTokenAdmin($adminRow);
            Response::success($payload, "Login administrador (MFA) exitoso");
        }

        // Admin sin MFA → flujo normal
        $payload = \App\Services\AdminService::emitirTokenAdmin($adminRow);
        Response::success($payload, "Login administrador exitoso");
    }

    // Si no es admin válido, intenta como empleado (flujo que ya tenías)
    $emp = \App\Controllers\EmpleadoController::loginRaw($email, $password);
    if ($emp === null) {
        throw new \Exception("No existe");
    }
    Response::success([
        'id'       => $emp['id'],
        'nombre'   => $emp['nombre'],
        'apellido' => $emp['apellido'] ?? '',
        'email'    => $emp['email'],
        'dni'      => $emp['dni'] ?? null,
        'role'     => 'empleado',
        'token'    => $emp['token']
    ], "Login empleado exitoso");

} catch (\Exception $e) {
    Response::error("Credenciales inválidas", 401);
}