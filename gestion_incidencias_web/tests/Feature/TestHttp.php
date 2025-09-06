<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TestHttp extends TestCase
{
    protected static function base(): string
    {
        // Base del proyecto (raíz web que contiene /api y /admin)
        $base = getenv('TEST_BASE') ?: 'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/';
        return rtrim($base, '/');
    }

    protected static function api(string $path): string
    {
        return self::base() . '/api/public/' . ltrim($path, '/');
    }

    protected static function admin(string $path): string
    {
        return self::base() . '/admin/' . ltrim($path, '/');
    }

    /** @return array{code:int,headers:array<string,string[]>,body:string, json?:mixed} */
    protected static function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
        bool $followRedirects = false,
        ?string &$cookieJar = null
    ): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => $followRedirects,
        ]);

        // Cookies (para pruebas de frontend)
        if ($cookieJar !== null) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieJar);
        }

        // Headers
        $h = [];
        foreach ($headers as $k => $v) {
            if (is_int($k)) { $h[] = $v; }
            else { $h[] = $k . ': ' . $v; }
        }
        if (!empty($h)) curl_setopt($ch, CURLOPT_HTTPHEADER, $h);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $resp = curl_exec($ch);
        if ($resp === false) {
            $err = curl_error($ch);
            curl_close($ch);
            self::fail("cURL error: $err");
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr($resp, 0, $headerSize);
        $bodyStr    = substr($resp, $headerSize);

        // Parse headers
        $headersOut = [];
        foreach (explode("\r\n", $rawHeaders) as $line) {
            if (strpos($line, ':') !== false) {
                [$k,$v] = array_map('trim', explode(':', $line, 2));
                $headersOut[$k][] = $v;
            }
        }

        $result = ['code' => (int)$status, 'headers' => $headersOut, 'body' => $bodyStr];
        if (stripos($headersOut['Content-Type'][0] ?? '', 'application/json') !== false) {
            $result['json'] = json_decode($bodyStr, true);
        }
        return $result;
    }

    /** Decodifica el payload del JWT sin verificar firma (para asserts de claims). */
    protected static function jwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) return [];
        $payload = $parts[1];
        $payload .= str_repeat('=', (-strlen($payload)) % 4);
        $json = base64_decode(strtr($payload, '-_', '+/'));
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }

    /** Genera un email único por prueba. */
    protected static function uniqueEmail(string $prefix): string
    {
        return sprintf('%s_%d@test.local', $prefix, time() . rand(1000,9999));
    }
}
