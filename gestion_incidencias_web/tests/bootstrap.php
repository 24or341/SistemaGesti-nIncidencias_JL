<?php
// Autoload principal
require __DIR__ . '/../vendor/autoload.php';

// Forzamos entorno de pruebas
$_ENV['APP_ENV']    = 'testing';
$_ENV['JWT_SECRET'] = $_ENV['JWT_SECRET'] ?? 'test_secret_rf02';

// Inicializa helper JWT
\App\Core\Auth::init();
