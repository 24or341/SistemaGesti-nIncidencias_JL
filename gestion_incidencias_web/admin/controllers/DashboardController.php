<?php
// Configuración del archivo DashboardController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Activa tipado estricto.
    class DashboardController // Controlador del dashboard
    {
        public function index(): void // Método para mostrar el dashboard
        {
            try {
                /** 
                 * @var array<string,mixed> $result 
                 */
                $result = apiRequest('admin_dashboard/resumen_incidencias.php', 'GET'); // Realiza una petición a la API para obtener el resumen de incidencias.

                if (! isset($result['data']) || ! is_array($result['data'])) { // Verifica que la respuesta tenga la estructura esperada.
                    throw new \RuntimeException('Formato de datos inesperado'); // Lanza excepción si el formato es incorrecto.
                }
            } catch (\Exception $e) { // Captura cualquier excepción durante la petición o procesamiento.
                view('error', ['message' => htmlspecialchars($e->getMessage(), ENT_QUOTES)]); // Muestra una vista de error con el mensaje de la excepción.
                return; // Termina la ejecución del método si hay un error.
            }

            /** 
             * @var array<string,int|string> $d 
             */
            $d = $result['data']; // Extrae los datos del resumen de incidencias.

            /** @var int $pendientes */
            $pendientes = isset($d['Pendiente']) && is_int($d['Pendiente']) // Verifica y normaliza el valor de incidencias pendientes.
                ? $d['Pendiente'] // Valor válido
                : (int) ($d['Pendiente'] ?? 0); // Valor por defecto si no está definido o no es entero.

            /** @var int $enDesarrollo */
            $enDesarrollo = isset($d['En Desarrollo']) && is_int($d['En Desarrollo']) // Verifica y normaliza el valor de incidencias en desarrollo.
                ? $d['En Desarrollo'] // Valor válido
                : (int) ($d['En Desarrollo'] ?? 0); // Valor por defecto si no está definido o no es entero.

            /** @var int $terminadas */
            $terminadas = isset($d['Terminado']) && is_int($d['Terminado']) // Verifica y normaliza el valor de incidencias terminadas.
                ? $d['Terminado'] // Valor válido
                : (int) ($d['Terminado'] ?? 0); // Valor por defecto si no está definido o no es entero.

            view('dashboard', [ // Renderiza la vista del dashboard con los datos obtenidos.
                'pendientes'    => $pendientes, // Datos de incidencias pendientes
                'en_desarrollo' => $enDesarrollo, // Datos de incidencias en desarrollo
                'terminadas'    => $terminadas, // Datos de incidencias terminadas
            ]);
        }
    }
?>