<?php
// Configuración del archivo config.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Activa tipado estricto.

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || ($_SERVER['SERVER_PORT'] ?? '') == 443)
            ? "https://" : "http://"; // Detecta protocolo.

$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST'])
    ? $_SERVER['HTTP_HOST']
    : 'localhost'; //Normaliza host evitando un mixed.

$docRootRaw    = $_SERVER['DOCUMENT_ROOT'] ?? ''; //Ruta raíz del documento 
$docRoot       = is_string($docRootRaw) ? realpath($docRootRaw) : false; // El realpath puede devolver false
$currentDir = realpath(__DIR__) ?: ''; //Ruta actual del directorio del admin.
$adminUrl      = ''; //Valor por defecto.

if ($docRoot !== false && $docRoot !== '' && is_string($currentDir)) {
    $adminUrl = str_replace('\\', '/', substr($currentDir, strlen($docRoot)));
} //Calcula la porción de URL al DocumentRoot.

define('BASE_URL', $protocol . $host . $adminUrl . '/index.php?path='); //URL base para links internos.
define('ADMIN_BASE', $protocol . $host . $adminUrl . '/'); //Base para recursos del admin.

$projectUrl = dirname($adminUrl); //Directorio padre que sirve para formar el API_BASE
define('API_BASE', $protocol . $host . $projectUrl . '/api/public/'); //Endpoint base de la API pública.

/**
 * @param string $endpoint
 * @param string $method
 * @param array<string,mixed> $payload
 * @return array<string,mixed>
 * @throws Exception
 */
function apiRequest(string $endpoint, string $method = 'GET', array $payload = []): array
{
    $tokenRaw = $_SESSION['user_token'] ?? ''; // El token se espera en sesión.
    $token = is_string($tokenRaw) ? $tokenRaw : ''; // Se normaliza a string.
    if ($token === '') {
        throw new Exception("No hay token en sesión"); // Si no hay token, se lanza excepción y se bloquea la petición.
    }

    $url = API_BASE . ltrim($endpoint, '/'); //Forma URL para el endpoint.
    $ch  = curl_init($url); // Inicia cURL para petición al backend.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devuelve el resultado como string.
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos.


    $headers = [
        "Authorization: Bearer {$token}", // Añade el token en la cabecera Authorization - Parte central del Requerimiento 02
        "Accept: application/json"
    ];

    if (strtoupper($method) === 'POST') {
        $json = json_encode($payload); // Codifica el payload a JSON.
        if ($json === false) {
            throw new \RuntimeException('Error serializando JSON en apiRequest'); // Manejo de error en caso de fallo en json_encode.
        }
        curl_setopt($ch, CURLOPT_POST, true);
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json); // Añade el JSON al cuerpo de la petición, enviando el payload.
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Añade las cabeceras a la petición.
    $resp = curl_exec($ch); //Ejcuta la petición.
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Código HTTP de respuesta.
    $curlErr = curl_error($ch); // Error de cURL si lo hay.
    curl_close($ch); //Cierra cURL.

    if ($resp === false) {
        throw new \RuntimeException('cURL error: ' . $curlErr); // Manejo de error en caso de fallo en curl_exec.
    }

    if ($code >= 400) {
        throw new Exception("API {$endpoint} respondió HTTP {$code}: {$resp}"); // Manejo de error en caso de código HTTP.
    }

    $decoded = json_decode($resp, true); // Decodifica la respuesta JSON.
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException('JSON inválido en respuesta API: ' . json_last_error_msg()); // Manejo de error en caso de fallo en json_decode.
    }

    if (!is_array($decoded)) {
        return []; // Si la respuesta no es un array, devuelve array vacío.
    }

    return $decoded; // Devuelve la respuesta decodificada como array asociativo.
}
