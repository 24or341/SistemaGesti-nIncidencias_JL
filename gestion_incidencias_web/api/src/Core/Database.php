<?php
// Configuración del archivo Database.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
declare(strict_types=1); // Habilita el modo estricto

namespace App\Core; // Espacio de nombres para la lógica central de la aplicación

use PDO; // Uso de conexión
use PDOException; // Uso de excepción
use RuntimeException;

class Database // Clase para la conexión a la base de datos
{
    private static ?PDO $instance = null; // Instancia de conexión a la base de datos

    private function __construct() // Constructor privado para evitar instanciación
    {
    }

    public static function getInstance(): PDO // Obtiene la instancia de conexión a la base de datos
    {
        if (self::$instance === null) { // Si no hay una instancia existente
            try { // Intenta crear una nueva conexión
                $host = $_ENV['DB_HOST'] ?? ''; // Host de la base de datos
                $port = $_ENV['DB_PORT'] ?? ''; // Puerto de la base de datos
                $dbname = $_ENV['DB_NAME'] ?? ''; // Nombre de la base de datos
                $user = $_ENV['DB_USER'] ?? null; // Usuario de la base de datos
                $pass = $_ENV['DB_PASS'] ?? null; // Contraseña de la base de datos

                if ($host === '' || $dbname === '') { // Verifica que las variables de entorno estén configuradas
                    throw new RuntimeException('Database env variables no configuradas'); // Lanza una excepción si faltan variables
                }

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}"; // DSN de la conexión

                self::$instance = new PDO($dsn, $user, $pass); // Crea la instancia de PDO
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura el modo de error
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Configura el modo de recuperación por defecto
            } catch (PDOException $e) { // Captura cualquier excepción de PDO
                throw new RuntimeException("Error de conexión a la base de datos: " . $e->getMessage()); // Lanza una excepción si hay un error de conexión
            }
        }
        return self::$instance; // Retorna la instancia de conexión a la base de datos
    }
}
