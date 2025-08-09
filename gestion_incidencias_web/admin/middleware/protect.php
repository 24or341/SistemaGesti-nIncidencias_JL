<?php
// Configuración del archivo protect.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    if (in_array($_GET['path'] ?? '', ['auth/login','auth/register'])) { // Rutas públicas
        return; // Retorno
    } 

    if (empty($_SESSION['user_id'])) { // Verificar si el usuario no está autenticado
        header('Location: ' . url('auth/login')); // Redirigir a la página de inicio de sesión
        exit; // Salir del script
    }

    $role = $_SESSION['user_role'] ?? ''; // Obtener el rol del usuario desde la sesión
    $pathRaw = $_GET['path'] ?? 'dashboard'; // Obtener la ruta actual
    $path = is_string($pathRaw) ? explode('/', $pathRaw)[0] : 'dashboard'; // Extraer la primera parte de la ruta
    $path = explode('/', $path)[0]; // Asegurarse de que es una cadena

    if ($role === 'empleado' && in_array($path, ['dashboard','empleados','reporte'])) { // Verificar si el rol es 'empleado' y la ruta no está permitida
        header('Location: ' . url('incidencias')); // Redirigir a la página de incidencias
        exit; // Salir del script
    }
?>