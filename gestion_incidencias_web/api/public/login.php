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

if ($email === '' || $password === '') { // Verifica si faltan campos
    Response::error("Email y contraseña requeridos", 422); // Envía una respuesta de error
}

try { // Intenta iniciar sesión como administrador
    $admin = AdminController::loginRaw($email, $password); // Llama al método de inicio de sesión
    Response::success([ // Envía una respuesta de éxito
        'id'       => $admin['id'], // ID del administrador
        'nombre'   => $admin['nombre'], // Nombre del administrador
        'apellido' => $admin['apellido'] ?? '', // Apellido del administrador
        'email'    => $admin['email'], // Email del administrador
        'dni'      => $admin['dni'] ?? null, // DNI del administrador
        'role'     => 'administrador', // Rol del usuario
        'token'    => $admin['token'] // Token de acceso
    ], "Login administrador exitoso"); // Envía una respuesta de éxito
} catch (\Exception $e) { // Captura cualquier excepción
    try { // Intenta iniciar sesión como empleado
        $emp = EmpleadoController::loginRaw($email, $password); // Llama al método de inicio de sesión
        if ($emp === null) { // Si no se encuentra el empleado
            throw new \Exception("No existe"); // Lanza una excepción
        }
        Response::success([ // Envía una respuesta de éxito
            'id'       => $emp['id'], // ID del empleado
            'nombre'   => $emp['nombre'], // Nombre del empleado
            'apellido' => $emp['apellido'] ?? '', // Apellido del empleado
            'email'    => $emp['email'], // Email del empleado
            'dni'      => $emp['dni'] ?? null, // DNI del empleado
            'role'     => 'empleado', // Rol del usuario
            'token'    => $emp['token'] // Token de acceso
        ], "Login empleado exitoso"); // Envía una respuesta de éxito
    } catch (\Exception $e2) { // Captura cualquier excepción
        Response::error("Credenciales inválidas", 401); // Envía una respuesta de error
    }
}
