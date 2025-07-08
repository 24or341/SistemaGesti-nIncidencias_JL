<?php
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || $_SERVER['SERVER_PORT'] == 443)
                ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];

    $docRoot    = realpath($_SERVER['DOCUMENT_ROOT']);
    $currentDir = realpath(__DIR__);
    $adminUrl   = str_replace(
        '\\',
        '/',
        substr($currentDir, strlen($docRoot))
    );

    define('BASE_URL', $protocol . $host . $adminUrl . '/index.php?path=');

    define('ADMIN_BASE', $protocol . $host . $adminUrl . '/');

    $projectUrl = dirname($adminUrl);
    define('API_BASE', $protocol . $host . $projectUrl . '/api/public/');

    function apiRequest(string $endpoint, string $method = 'GET', array $payload = []): array
    {
        $token = $_SESSION['user_token'] ?? '';
        if (!$token) {
            throw new Exception("No hay token en sesión");
        }

        $url = API_BASE . ltrim($endpoint, '/');
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "Authorization: Bearer {$token}",
            "Accept: application/json"
        ];

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code >= 400) {
            throw new Exception("API {$endpoint} respondió HTTP {$code}: {$resp}");
        }

        return json_decode($resp, true);
    }
?>