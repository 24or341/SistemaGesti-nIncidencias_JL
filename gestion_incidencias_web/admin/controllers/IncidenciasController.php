<?php
    declare(strict_types=1);
    class IncidenciasController
    {
        public function index(): void
        {
            $errorInc = null;
            try {
                /** @var array<string,mixed> $incResp */
                $incResp = apiRequest('admin_dashboard/incidencias.php', 'GET');
                if (! isset($incResp['data']) || ! is_array($incResp['data'])) {
                    throw new \RuntimeException('Incidencias inválidas');
                }
                /** @var array<int,array<string,mixed>> $incidencias */
                $incidencias = $incResp['data'];

                /** @var array<string,mixed> $empResp */
                $empResp = apiRequest('admin_dashboard/empleados.php', 'GET');
                if (! isset($empResp['data']) || ! is_array($empResp['data'])) {
                    throw new \RuntimeException('Empleados inválidos');
                }
                /** @var array<int,array<string,mixed>> $empleados */
                $empleados = $empResp['data'];

                /** @var array<string,mixed> $prioResp */
                $prioResp = apiRequest('admin_dashboard/prioridad.php', 'GET');
                if (! isset($prioResp['data']) || ! is_array($prioResp['data'])) {
                    throw new \RuntimeException('Prioridades inválidas');
                }
                /** @var array<int,array<string,mixed>> $prioridades */
                $prioridades = $prioResp['data'];
            } catch (\Exception $e) {
                error_log('Error cargando Incidencias: ' . $e->getMessage());
                $incidencias = [];
                $empleados   = [];
                $prioridades = [];
                $errorInc    = htmlspecialchars($e->getMessage(), ENT_QUOTES);
            }
            view('incidencias', compact('incidencias','empleados','prioridades','errorInc'));
        }
    }
?>