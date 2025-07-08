<?php
    if (in_array($_GET['path'] ?? '', ['auth/login','auth/register'])) {
        return;
    }

    if (empty($_SESSION['user_id'])) {
        header('Location: ' . url('auth/login'));
        exit;
    }

    $role = $_SESSION['user_role'] ?? '';
    $path = explode('/', ($_GET['path'] ?? 'dashboard'))[0];

    if ($role === 'empleado' && in_array($path, ['dashboard','empleados','reporte'])) {
        header('Location: ' . url('incidencias'));
        exit;
    }
?>