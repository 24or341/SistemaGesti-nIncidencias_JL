<?php
// Configuración del archivo AuthController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Activa tipado estricto.
    require_once __DIR__ . '/../config.php'; // Carga la configuración del sistema.
    require_once __DIR__ . '/../helpers.php'; // Carga funciones helper.

    class AuthController // Controlador de autenticación
    {
        public function login(): void
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                authView('login');
                return;
            }

            $emailRaw    = $_POST['email']    ?? '';
            $passwordRaw = $_POST['password'] ?? '';
            $otpRaw      = $_POST['otp']      ?? ''; // <- NUEVO (puede venir desde la vista MFA)

            $email    = is_string($emailRaw)    ? trim($emailRaw)    : '';
            $password = is_string($passwordRaw) ? trim($passwordRaw) : '';
            $otp      = is_string($otpRaw)      ? trim($otpRaw)      : '';

            // Si estamos en fase MFA, recupera email/password guardados en sesión
            if ($otp !== '' && isset($_SESSION['mfa_email'], $_SESSION['mfa_password'])) {
                $email    = (string)$_SESSION['mfa_email'];
                $password = (string)$_SESSION['mfa_password'];
            }

            $ch = curl_init(API_BASE . 'login.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $payloadArr = ['email' => $email, 'password' => $password];
            if ($otp !== '') {
                $payloadArr['otp'] = $otp; // enviar OTP en el segundo paso
            }

            $payload = json_encode($payloadArr);
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

            /** @var array{success: bool, data?: array<string,mixed>, message?:string} $decoded */
            $decoded = json_decode($response, true);

            if (empty($decoded['success'])) {
                $error = $decoded['message'] ?? 'Credenciales inválidas.';
                authView('login', ['error' => $error]);
                return;
            }

            // Si el backend pide OTP:
            if (!empty($decoded['data']['mfa_required'])) {
                // Guardar credenciales temporalmente para el segundo paso
                $_SESSION['mfa_email']    = $email;
                $_SESSION['mfa_password'] = $password;
                authView('login_mfa', ['info' => 'Ingresa el código de tu app Authenticator.']);
                return;
            }

            // Login final exitoso:
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

            // Limpiar credenciales MFA temporales si existían
            unset($_SESSION['mfa_email'], $_SESSION['mfa_password']);

            $dest = $role === 'administrador' ? 'dashboard' : 'incidencias';
            header('Location: ' . url($dest));
            exit;
        }

        public function mfaSetup(): void
        {
            // Fija el email que se mostrará en el autenticador (mejor si guardas el que inició sesión)
            $email = $_SESSION['pending_login_email'] ?? ($_SESSION['admin_email'] ?? 'admin@example.com');

            // 1) Secreto Base32 persistido en sesión mientras dura el setup
            if (empty($_SESSION['mfa_secret'])) {
                $_SESSION['mfa_secret'] = self::generateBase32Secret(20);
            }
            $secret = $_SESSION['mfa_secret'];

            // 2) otpauth URL (lo que codifica el QR)
            $issuer   = rawurlencode('Sistema Incidencias');
            $account  = rawurlencode($email);
            $otpauth  = "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}&digits=6&period=30&algorithm=SHA1";

            // 3) URL del QR (servicio público)
            $qr = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode($otpauth);

            // 4) Renderiza vista pasando las variables EXACTAS
            authView('mfa_setup', [
                'qr'      => $qr,
                'secret'  => $secret,
                'otpauth' => $otpauth,
                'error'   => null,
            ]);
        }

        // Helper simple para secreto Base32
        private static function generateBase32Secret(int $len = 20): string
        {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
            $res = '';
            for ($i = 0; $i < $len; $i++) {
                $res .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            return $res;
        }



        public function mfaVerify(): void
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: '.url('auth/mfa-setup')); exit;
            }
            $code = isset($_POST['code']) ? trim((string)$_POST['code']) : '';

            $ch = curl_init(API_BASE.'admin_mfa/verify.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.($_SESSION['user_token'] ?? ''),
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['code' => $code]));
            $resp = curl_exec($ch);
            curl_close($ch);

            $json = is_string($resp) ? json_decode($resp, true) : null;
            if (is_array($json) && !empty($json['success'])) {
                // MFA activado; redirige al dashboard
                header('Location: '.url('dashboard')); exit;
            }

            $error = $json['message'] ?? 'Código inválido. Inténtalo de nuevo.';
            authView('mfa_setup', ['error' => $error, 'qr' => null, 'secret' => null, 'email' => null]);
        }



        public function register(): void // Método para manejar el registro de usuarios
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Si no es una petición POST, muestra el formulario de registro.
                authView('register'); // Muestra la vista de registro.
                return; // Termina la ejecución si no es POST.
            }

            $nombreRaw    = $_POST['nombre']   ?? ''; // Obtiene el nombre del formulario, si no existe, usa cadena vacía.
            $nombre       = is_string($nombreRaw)   ? trim($nombreRaw)   : ''; // Normaliza el nombre a string y elimina espacios.
            $apellidoRaw  = $_POST['apellido'] ?? ''; // Obtiene el apellido del formulario, si no existe, usa cadena vacía.
            $apellido     = is_string($apellidoRaw) ? trim($apellidoRaw) : ''; // Normaliza el apellido a string y elimina espacios.
            $emailRaw     = $_POST['email']    ?? ''; // Obtiene el email del formulario, si no existe, usa cadena vacía.
            $email        = is_string($emailRaw)    ? trim($emailRaw)    : ''; // Normaliza el email a string y elimina espacios.
            $passwordRaw  = $_POST['password'] ?? ''; // Obtiene la contraseña del formulario, si no existe, usa cadena vacía.
            $password     = is_string($passwordRaw) ? trim($passwordRaw) : ''; // Normaliza la contraseña a string y elimina espacios.
            $roleRaw      = $_POST['role']     ?? 'administrador'; // Obtiene el rol del formulario, si no existe, usa 'administrador'.
            $role         = is_string($roleRaw)     ? trim($roleRaw)     : 'administrador'; // Normaliza el rol a string y elimina espacios.

            $ch = curl_init(API_BASE . 'register.php'); // Inicia cURL para petición al backend.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Configura cURL para devolver el resultado como string.

            $payload = json_encode(compact('nombre','apellido','email','password','role')); // Codifica el payload a JSON.
            if ($payload === false) { // Manejo de error en caso de fallo en json_encode.
                throw new \RuntimeException('Error serializando JSON de registro'); // Lanza excepción si json_encode falla.
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Añade el JSON al cuerpo de la petición, enviando el payload.
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); // Configura la cabecera Content-Type para JSON.

            $response = curl_exec($ch); // Ejecuta la petición cURL.
            curl_close($ch); // Cierra la sesión cURL.
            if (!is_string($response)) { // Verifica que la respuesta sea una cadena.
                throw new \RuntimeException('Error en la petición cURL de registro'); // Lanza excepción si la respuesta no es válida.
            }

            /** @var array{success: bool, message?: string} $decoded */
            $decoded = json_decode($response, true); // Decodifica la respuesta JSON a un array asociativo.

            if (! $decoded['success']) { // Si la respuesta indica fallo en el registro.
                $error = $decoded['message'] ?? 'Error en el registro.'; // Obtiene el mensaje de error o usa uno genérico.
                authView('register', ['error' => $error]); // Muestra la vista de registro con el error.
                return; // Termina la ejecución.
            }

            header('Location: ' . url('auth/login')); // Redirige al usuario a la página de login tras registro exitoso.
            exit; // Termina la ejecución.
        }

        public function logout(): void // Método para manejar el cierre de sesión
        {
            $_SESSION = []; // Limpia todas las variables de sesión.
            if (ini_get('session.use_cookies')) { // Si las cookies de sesión están habilitadas, elimina la cookie de sesión.
                $params = session_get_cookie_params(); // Obtiene los parámetros de la cookie de sesión.

                $cookieNameRaw = session_name(); // Obtiene el nombre de la cookie de sesión.
                $cookieName    = is_string($cookieNameRaw) ? $cookieNameRaw : ''; // Normaliza el nombre de la cookie a string.

                setcookie(  // Elimina la cookie de sesión.
                    $cookieName, // Nombre de la cookie
                    '',
                    time() - 42000, // Tiempo de expiración en el pasado
                    $params['path'], // Ruta de la cookie
                    $params['domain'], // Dominio de la cookie
                    $params['secure'], // Solo se envía por HTTPS si es seguro
                    $params['httponly'] // No accesible vía JavaScript
                );
            }
            session_destroy(); // Destruye la sesión en el servidor.
            header('Location: ' . url('auth/login')); // Redirige al usuario a la página de login tras cerrar sesión.
            exit; // Termina la ejecución.
        }
    }
?>