<?php
// Configuración del archivo AuthController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Activa tipado estricto.
    require_once __DIR__ . '/../config.php'; // Carga la configuración del sistema.
    require_once __DIR__ . '/../helpers.php'; // Carga funciones helper.

    class AuthController // Controlador de autenticación
    {
        public function login(): void // Método para manejar el inicio de sesión
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Si no es una petición POST, muestra el formulario de login.
                authView('login'); // Muestra la vista de login.
                return; // Termina la ejecución si no es POST.
            }

            $emailRaw    = $_POST['email']    ?? ''; // Obtiene el email del formulario, si no existe, usa cadena vacía.
            $email       = is_string($emailRaw)    ? trim($emailRaw)    : ''; // Normaliza el email a string y elimina espacios.
            $passwordRaw = $_POST['password'] ?? ''; // Obtiene la contraseña del formulario, si no existe, usa cadena vacía.
            $password    = is_string($passwordRaw) ? trim($passwordRaw) : ''; // Normaliza la contraseña a string y elimina espacios.

            $ch = curl_init(API_BASE . 'login.php'); // Inicia cURL para petición al backend.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Configura cURL para devolver el resultado como string.

            $payload = json_encode(['email' => $email, 'password' => $password]); // Codifica el payload a JSON.
            if ($payload === false) { // Manejo de error en caso de fallo en json_encode.
                throw new \RuntimeException('Error serializando JSON de login'); // Lanza excepción si json_encode falla.
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Añade el JSON al cuerpo de la petición, enviando el payload.
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); // Configura la cabecera Content-Type para JSON.

            $response = curl_exec($ch); // Ejecuta la petición cURL.
            curl_close($ch); // Cierra la sesión cURL.
            if (!is_string($response)) { // Verifica que la respuesta sea una cadena.
                throw new \RuntimeException('Error en la petición cURL de login'); // Lanza excepción si la respuesta no es válida.
            }

            /** 
             * @var array{success: bool, data: array{id:int|string,nombre:string,apellido:string,role:string,token:string}, message?:string} $decoded
             */
            $decoded = json_decode($response, true); // Decodifica la respuesta JSON a un array asociativo.

            if (! $decoded['success']) { // Si la respuesta indica fallo en el login.
                $error = $decoded['message'] ?? 'Credenciales inválidas.'; // Obtiene el mensaje de error o usa uno genérico.
                authView('login', ['error' => $error]); // Muestra la vista de login con el error.
                return; // Termina la ejecución.
            }

            $user     = $decoded['data']; // Extrae los datos del usuario.
            $id       = is_int($user['id']) ? $user['id'] : (int)$user['id']; // Normaliza el ID del usuario a entero.
            $nombre   = (string)$user['nombre']; // Normaliza el nombre a string.
            $apellido = (string)$user['apellido']; // Normaliza el apellido a string.
            $role     = (string)$user['role']; // Normaliza el rol a string.
            $token    = (string)$user['token']; // Normaliza el token a string.

            $_SESSION['user_id']      = $id; // Guarda el ID del usuario en la sesión.
            $_SESSION['admin_nombre'] = $nombre . ' ' . $apellido; // Guarda el nombre completo en la sesión.
            $_SESSION['user_role']    = $role; // Guarda el rol del usuario en la sesión.
            $_SESSION['user_token']   = $token; // Guarda el token en la sesión.

            $dest = $role === 'administrador' ? 'dashboard' : 'incidencias'; // Redirige según el rol del usuario.
            header('Location: ' . url($dest)); // Redirige al usuario a la página correspondiente.
            exit; // Termina la ejecución.
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