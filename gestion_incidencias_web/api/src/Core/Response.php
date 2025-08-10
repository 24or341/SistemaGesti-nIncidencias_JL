<?php
// Configuración del archivo Response.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto

namespace App\Core; // Espacio de nombres para la lógica central de la aplicación

class Response // Clase para manejar las respuestas HTTP
{
    /**
     * @param mixed $data
     */
    public static function json($data, int $code = 200): void // Envía una respuesta JSON
    {
        http_response_code($code); // Establece el código de respuesta HTTP
        header('Content-Type: application/json'); // Establece el tipo de contenido a JSON
        try { // Intenta codificar los datos a JSON
            echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES); // Codifica los datos a JSON
        } catch (\JsonException $e) { // Captura cualquier excepción de JSON
            http_response_code(500); // Establece el código de respuesta HTTP a 500
            echo json_encode(['success' => false, 'message' => 'Error encoding JSON']); // Envía una respuesta JSON de error
        }
        exit; // Finaliza la ejecución del script
    }

    public static function error(string $message, int $code = 400): void // Envía una respuesta de error
    {
        self::json(['success' => false, 'message' => $message], $code); // Envía una respuesta JSON de error
    }

    /**
     * @param mixed $data
     */
    public static function success($data = [], string $message = '', int $code = 200): void // Envía una respuesta de éxito
    {
        $response = ['success' => true]; // Crea la estructura de respuesta
        if ($message !== '') { // Si hay un mensaje
            $response['message'] = $message; // Agrega el mensaje a la respuesta
        }
        $response['data'] = $data; // Agrega los datos a la respuesta
        self::json($response, $code); // Envía la respuesta JSON
    }
}
