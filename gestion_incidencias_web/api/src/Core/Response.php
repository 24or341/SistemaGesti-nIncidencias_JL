<?php

    namespace App\Core;

    class Response
    {
        public static function json($data, int $code = 200): void
        {
            http_response_code($code);
            header('Content-Type: application/json');
            echo json_encode($data, JSON_UNESCAPED_SLASHES);
            exit;
        }

        public static function error(string $message, int $code = 400): void
        {
            self::json(['success' => false, 'message' => $message], $code);
        }

        public static function success($data = [], string $message = '', int $code = 200): void
        {
            $response = ['success' => true];
            if (!empty($message)) {
                $response['message'] = $message;
            }
            $response['data'] = $data;

            self::json($response, $code);
        }
    }

?>