<?php
// Configuración del archivo protect.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08

// Normaliza el path actual
$rawPath  = $_GET['path'] ?? 'dashboard';
$current  = is_string($rawPath) ? trim($rawPath, '/') : 'dashboard';

// Rutas públicas (si index.php definió $PUBLIC_ROUTES, úsalo; si no, usa estas por defecto)
$PUBLIC_ROUTES = $PUBLIC_ROUTES ?? ['auth/login','auth/register','auth/mfa-setup','auth/mfa-verify'];

// 1) Permitir siempre rutas públicas
if (in_array($current, $PUBLIC_ROUTES, true)) {
    return;
}

// 2) Si hay MFA pendiente (hay mfa_user_id pero aún no hay user_id):
//    solo permitir mfa-setup y mfa-verify; forzar redirección al setup si intenta otra ruta
if (empty($_SESSION['user_id']) && !empty($_SESSION['mfa_user_id'])) {
    if (!in_array($current, ['auth/mfa-setup','auth/mfa-verify'], true)) {
        header('Location: ' . url('auth/mfa-setup'));
        exit;
    }
    return;
}

// 3) Si no hay sesión (ni user_id ni MFA pendiente) → login
if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('auth/login'));
    exit;
}

// 4) Control de acceso por rol (mantiene tu restricción actual para empleados)
$role   = $_SESSION['user_role'] ?? '';
$first  = explode('/', $current)[0]; // módulo raíz

if ($role === 'empleado' && in_array($first, ['dashboard','empleados','reporte'], true)) {
    header('Location: ' . url('incidencias'));
    exit;
}
