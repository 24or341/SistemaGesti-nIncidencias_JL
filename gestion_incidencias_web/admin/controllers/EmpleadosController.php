<?php
    declare(strict_types=1);
    class EmpleadosController
    {
        public function index(): void
        {
            try {
                /** 
                 * @var array<string,mixed> $resp  
                 */
                $resp = apiRequest('admin_dashboard/empleados.php', 'GET');

                if (! isset($resp['data']) || ! is_array($resp['data'])) {
                    throw new \RuntimeException('No se recibieron empleados válidos');
                }

                /** @var array<int,array<string,mixed>> $empleados */
                $empleados = $resp['data'];
                $errorEmp  = null;
            } catch (\Exception $e) {
                error_log('Error cargando empleados: ' . $e->getMessage());
                $empleados = [];
                $errorEmp  = htmlspecialchars($e->getMessage(), ENT_QUOTES);
            }

            view('empleados', compact('empleados', 'errorEmp'));
        }
    }
?>