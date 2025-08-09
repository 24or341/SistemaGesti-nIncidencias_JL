<?php
// Configuración del archivo index.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    session_start(); // Inicia la sesión para manejar autenticación y datos de usuario.
    require __DIR__ . '/config.php'; // Carga la configuración del sistema.
    require __DIR__ . '/helpers.php'; // Carga funciones helper.
    require __DIR__ . '/middleware/protect.php'; // Protege las rutas que requieren autenticación.

    $pathRaw = $_GET['path'] ?? 'dashboard'; // Obtiene el path de la URL, por defecto 'dashboard' si no se especifica.
    $path = is_string($pathRaw) ? trim($pathRaw, '/') : 'dashboard'; // Normaliza el path eliminando barras al inicio y fin.
    $parts   = explode('/', $path); // Divide el path en partes para determinar el módulo y la acción.
    $module  = ucfirst($parts[0]) . 'Controller'; // Convierte la primera parte del path en el nombre del controlador.
    $action  = $parts[1] ?? 'index'; // La segunda parte del path es la acción, por defecto 'index' si no se especifica.

    $ctrlFile = __DIR__ . "/controllers/{$module}.php"; // Construye la ruta completa al archivo del controlador.
    if (!file_exists($ctrlFile)) { // Verifica si el archivo del controlador existe.
        http_response_code(404); // Si no existe, devuelve un error 404.
        echo "Página no encontrada"; // Mensaje de error para el usuario.
        exit; // Termina la ejecución del script si el controlador no se encuentra.
    }
    require $ctrlFile; // Incluye el archivo del controlador.

    $ctrl = new $module(); // Crea una instancia del controlador.
    if (!method_exists($ctrl, $action)) { // Verifica si la acción existe en el controlador.
        http_response_code(404); // Si la acción no existe, devuelve un error 404.
        echo "Acción inválida"; // Mensaje de error para el usuario.
        exit; // Termina la ejecución del script si la acción no se encuentra.
    }
    $ctrl->$action(); // Llama a la acción del controlador correspondiente.
?>