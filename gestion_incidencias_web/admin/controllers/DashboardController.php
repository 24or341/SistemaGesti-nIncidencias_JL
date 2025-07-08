<?php
    declare(strict_types=1);
    class DashboardController
    {
        public function index(): void
        {
            try {
                /** 
                 * @var array<string,mixed> $result 
                 */
                $result = apiRequest('admin_dashboard/resumen_incidencias.php', 'GET');

                if (! isset($result['data']) || ! is_array($result['data'])) {
                    throw new \RuntimeException('Formato de datos inesperado');
                }
            } catch (\Exception $e) {
                view('error', ['message' => htmlspecialchars($e->getMessage(), ENT_QUOTES)]);
                return;
            }

            /** 
             * @var array<string,int|string> $d 
             */
            $d = $result['data'];

            /** @var int $pendientes */
            $pendientes = isset($d['Pendiente']) && is_int($d['Pendiente'])
                ? $d['Pendiente']
                : (int) ($d['Pendiente'] ?? 0);

            /** @var int $enDesarrollo */
            $enDesarrollo = isset($d['En Desarrollo']) && is_int($d['En Desarrollo'])
                ? $d['En Desarrollo']
                : (int) ($d['En Desarrollo'] ?? 0);

            /** @var int $terminadas */
            $terminadas = isset($d['Terminado']) && is_int($d['Terminado'])
                ? $d['Terminado']
                : (int) ($d['Terminado'] ?? 0);

            view('dashboard', [
                'pendientes'    => $pendientes,
                'en_desarrollo' => $enDesarrollo,
                'terminadas'    => $terminadas,
            ]);
        }
    }
?>