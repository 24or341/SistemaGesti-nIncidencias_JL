<?php
    declare(strict_types=1);
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../helpers.php';

    class AuthController
    {
        public function login(): void
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                authView('login');
                return;
            }

            $emailRaw    = $_POST['email']    ?? '';
            $email       = is_string($emailRaw)    ? trim($emailRaw)    : '';
            $passwordRaw = $_POST['password'] ?? '';
            $password    = is_string($passwordRaw) ? trim($passwordRaw) : '';

            $ch = curl_init(API_BASE . 'login.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $payload = json_encode(['email' => $email, 'password' => $password]);
            if ($payload === false) {
                throw new \RuntimeException('Error serializando JSON de login');
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            curl_close($ch);
            if (!is_string($response)) {
                throw new \RuntimeException('Error en la petición cURL de login');
            }

            /** 
             * @var array{success: bool, data: array{id:int|string,nombre:string,apellido:string,role:string,token:string}, message?:string} $decoded
             */
            $decoded = json_decode($response, true);

            if (! $decoded['success']) {
                $error = $decoded['message'] ?? 'Credenciales inválidas.';
                authView('login', ['error' => $error]);
                return;
            }

            $user     = $decoded['data'];
            $id       = is_int($user['id']) ? $user['id'] : (int)$user['id'];
            $nombre   = (string)$user['nombre'];
            $apellido = (string)$user['apellido'];
            $role     = (string)$user['role'];
            $token    = (string)$user['token'];

            $_SESSION['user_id']      = $id;
            $_SESSION['admin_nombre'] = $nombre . ' ' . $apellido;
            $_SESSION['user_role']    = $role;
            $_SESSION['user_token']   = $token;

            $dest = $role === 'administrador' ? 'dashboard' : 'incidencias';
            header('Location: ' . url($dest));
            exit;
        }

        public function register(): void
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                authView('register');
                return;
            }

            $nombreRaw    = $_POST['nombre']   ?? '';
            $nombre       = is_string($nombreRaw)   ? trim($nombreRaw)   : '';
            $apellidoRaw  = $_POST['apellido'] ?? '';
            $apellido     = is_string($apellidoRaw) ? trim($apellidoRaw) : '';
            $emailRaw     = $_POST['email']    ?? '';
            $email        = is_string($emailRaw)    ? trim($emailRaw)    : '';
            $passwordRaw  = $_POST['password'] ?? '';
            $password     = is_string($passwordRaw) ? trim($passwordRaw) : '';
            $roleRaw      = $_POST['role']     ?? 'administrador';
            $role         = is_string($roleRaw)     ? trim($roleRaw)     : 'administrador';

            $ch = curl_init(API_BASE . 'register.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $payload = json_encode(compact('nombre','apellido','email','password','role'));
            if ($payload === false) {
                throw new \RuntimeException('Error serializando JSON de registro');
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            curl_close($ch);
            if (!is_string($response)) {
                throw new \RuntimeException('Error en la petición cURL de registro');
            }

            /** @var array{success: bool, message?: string} $decoded */
            $decoded = json_decode($response, true);

            if (! $decoded['success']) {
                $error = $decoded['message'] ?? 'Error en el registro.';
                authView('register', ['error' => $error]);
                return;
            }

            header('Location: ' . url('auth/login'));
            exit;
        }

        public function logout(): void
        {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                $cookieNameRaw = session_name();
                $cookieName    = is_string($cookieNameRaw) ? $cookieNameRaw : '';

                setcookie(
                    $cookieName,
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            session_destroy();
            header('Location: ' . url('auth/login'));
            exit;
        }
    }
?>