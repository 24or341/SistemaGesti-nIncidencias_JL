<?php
// Configuración del archivo EmpleadosController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Activa tipado estricto.
    class EmpleadosController // Controlador de empleados
    {
        public function index(): void // Método para mostrar la lista de empleados
        {
            try {
                /** 
                 * @var array<string,mixed> $resp  
                 */
                $resp = apiRequest('admin_dashboard/empleados.php', 'GET'); // Realiza una petición a la API para obtener la lista de empleados.

                if (! isset($resp['data']) || ! is_array($resp['data'])) { // Verifica que la respuesta tenga la estructura esperada.
                    throw new \RuntimeException('No se recibieron empleados válidos'); // Lanza excepción si el formato es incorrecto.
                }

                /** @var array<int,array<string,mixed>> $empleados */
                $empleados = $resp['data']; // Extrae la lista de empleados.
                $errorEmp  = null; // Inicializa la variable de error como nula.
            } catch (\Exception $e) { // Captura cualquier excepción durante la petición o procesamiento.
                error_log('Error cargando empleados: ' . $e->getMessage()); // Registra el error en el log del servidor.
                $empleados = []; // Usa una lista vacía de empleados en caso de error.
                $errorEmp  = htmlspecialchars($e->getMessage(), ENT_QUOTES); // Prepara el mensaje de error para mostrar en la vista.
            }

            view('empleados', compact('empleados', 'errorEmp')); // Renderiza la vista de empleados con los datos obtenidos.
        }
    }
?>