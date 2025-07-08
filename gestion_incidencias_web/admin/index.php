<?php
    session_start();
    require __DIR__ . '/config.php';
    require __DIR__ . '/helpers.php';
    require __DIR__ . '/middleware/protect.php';

    $path    = trim($_GET['path'] ?? 'dashboard', '/');
    $parts   = explode('/', $path);
    $module  = ucfirst($parts[0]) . 'Controller';
    $action  = $parts[1] ?? 'index';

    $ctrlFile = __DIR__ . "/controllers/{$module}.php";
    if (!file_exists($ctrlFile)) {
        http_response_code(404);
        echo "Página no encontrada";
        exit;
    }
    require $ctrlFile;

    $ctrl = new $module();
    if (!method_exists($ctrl, $action)) {
        http_response_code(404);
        echo "Acción inválida";
        exit;
    }
    $ctrl->$action();
?>