<?php
// tests/bootstrap.php

require __DIR__ . '/../vendor/autoload.php';

// === BASE URL ===
$base = getenv('TEST_BASE') ?: 'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web';
define('TEST_BASE', rtrim($base, '/'));

// === UTILIDADES BAJO NIVEL (cURL) ===
function _http_request(string $method, string $url, array $headers = [], $body = null, bool $follow = true, ?string $cookieFile = null): array
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_FOLLOWLOCATION => $follow,
        CURLOPT_HEADER         => true,
        CURLOPT_TIMEOUT        => 25,
    ]);

    if (!empty($headers)) {
        $h = [];
        foreach ($headers as $k => $v) {
            $h[] = is_int($k) ? $v : "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
    }

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('cURL: ' . $err);
    }

    $code       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $rawHeaders = substr($raw, 0, $headerSize);
    $bodyStr    = substr($raw, $headerSize);
    $json       = json_decode($bodyStr, true);

    // Parsear headers a array asociativo con arrays de valores
    $headersArr = [];
    foreach (preg_split("/\r\n|\n|\r/", trim($rawHeaders)) as $line) {
        if ($line === '' || stripos($line, 'HTTP/') === 0) {
            continue;
        }
        $p = strpos($line, ':');
        if ($p === false) continue;
        $name  = trim(substr($line, 0, $p));
        $value = trim(substr($line, $p + 1));
        $headersArr[$name][] = $value;
    }

    return [
        'code'    => $code,
        'headers' => $headersArr,
        'body'    => $bodyStr,
        'json'    => is_array($json) ? $json : null,
    ];
}

function _b64url_decode(string $s): string {
    $s = strtr($s, '-_', '+/');
    $pad = strlen($s) % 4;
    if ($pad) $s .= str_repeat('=', 4 - $pad);
    return base64_decode($s) ?: '';
}

// Helpers globales para valores únicos
function unique_email(string $prefix = 'user', string $domain = 'example.test'): string {
    try { $suf = bin2hex(random_bytes(4)); } catch (\Throwable $e) { $suf = (string)mt_rand(100000,999999); }
    return sprintf('%s_%s@%s', $prefix, $suf, $domain);
}
function unique_dni(): string {
    try { return (string)(10000000 + random_int(0, 89999999)); }
    catch (\Throwable $e) { return (string)mt_rand(10000000, 99999999); }
}

// === CLASE BASE QUE USAN TUS TESTS ===
if (!class_exists('TestHttp')) {
    class TestHttp extends \PHPUnit\Framework\TestCase
    {
        // Construcción de URLs
        public static function base(): string  { return TEST_BASE; }
        public static function api(string $path = ''): string   { return self::base() . '/api/public/' . ltrim($path, '/'); }
        public static function admin(string $path = ''): string { return self::base() . '/admin/' . ltrim($path, '/'); }

        // Cliente HTTP genérico que tus tests ya usan (con followRedirects y cookie jar)
        public static function request(
            string $method,
            string $url,
            array $headers = [],
            $body = null,
            bool $followRedirects = true,
            ?string $cookieFile = null
        ): array {
            // Si el parámetro $url es relativo, lo convertimos a absoluto contra TEST_BASE
            if (stripos($url, 'http') !== 0) {
                $url = rtrim(TEST_BASE, '/') . '/' . ltrim($url, '/');
            }
            // Cuerpo por defecto para JSON si viene como array
            if (is_array($body) && (($headers['Content-Type'] ?? '') === 'application/json')) {
                $body = json_encode($body, JSON_UNESCAPED_SLASHES);
            }
            return _http_request($method, $url, $headers, $body, $followRedirects, $cookieFile);
        }

        // Atajos (por si los necesitas)
        protected function httpGet(string $path, array $headers = []): array {
            return self::request('GET', $path, $headers);
        }
        protected function httpPostJson(string $path, array $payload, array $headers = []): array {
            $headers['Content-Type'] = 'application/json';
            return self::request('POST', $path, $headers, json_encode($payload, JSON_UNESCAPED_SLASHES));
        }
        protected function httpPutJson(string $path, array $payload, array $headers = []): array {
            $headers['Content-Type'] = 'application/json';
            return self::request('PUT', $path, $headers, json_encode($payload, JSON_UNESCAPED_SLASHES));
        }

        // JWT: decodificar payload sin verificar firma (solo para asserts)
        public static function jwtPayload(string $jwt): array {
            $parts = explode('.', $jwt);
            if (count($parts) < 2) return [];
            $payloadJson = _b64url_decode($parts[1]);
            $arr = json_decode($payloadJson, true);
            return is_array($arr) ? $arr : [];
        }

        // Valores únicos que te faltaban
        public static function uniqueEmail(string $prefix = 'user', string $domain = 'example.test'): string {
            return unique_email($prefix, $domain);
        }
        public static function uniqueDni(): string {
            return unique_dni();
        }
    }
}
