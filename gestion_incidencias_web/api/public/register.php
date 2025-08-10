<?php
// Configuración del archivo register.php
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
$data = json_decode($inputRaw, true); // Decodifica el JSON

if (!is_array($data)) { // Verifica si la decodificación fue exitosa
    Response::error("JSON inválido", 400); // Envía una respuesta de error
}

$role = $data['role'] ?? null; // Obtiene el rol del usuario
if ($role === 'administrador') { // Verifica si el rol es administrador
    try { // Intenta registrar un nuevo administrador
        $id = AdminController::registerRaw($data); // Llama al método de registro
        Response::success(['id'=>$id], "Administrador registrado correctamente"); // Envía una respuesta de éxito
    } catch (\PDOException $e) { // Captura cualquier excepción de PDO
        if ((string)$e->getCode() === '23505') { // Verifica si el error es de clave duplicada
            Response::error("El correo ya está registrado", 409); // Envía una respuesta de error
        }
        Response::error($e->getMessage(), 500); // Envía una respuesta de error
    } catch (\Exception $e) { // Captura cualquier otra excepción
        Response::error($e->getMessage(), 500); // Envía una respuesta de error
    }
} elseif ($role === 'empleado') { // Verifica si el rol es empleado
    try { // Intenta registrar un nuevo empleado
        $id = EmpleadoController::registerRaw($data); // Llama al método de registro
        Response::success(['id'=>$id], "Empleado registrado correctamente"); // Envía una respuesta de éxito
    } catch (\PDOException $e) { // Captura cualquier excepción de PDO
        if ((string)$e->getCode() === '23505') { // Verifica si el error es de clave duplicada
            Response::error("El correo ya está registrado", 409); // Envía una respuesta de error
        }
        Response::error($e->getMessage(), 500); // Envía una respuesta de error
    } catch (\Exception $e) { // Captura cualquier otra excepción
        Response::error($e->getMessage(), 500); // Envía una respuesta de error
    }
} else { // Si el rol no es válido
    Response::error("Debe indicar un campo 'role' válido", 422); // Envía una respuesta de error
}
