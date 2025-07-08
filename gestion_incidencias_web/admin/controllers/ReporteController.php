<?php
    declare(strict_types=1);
    class ReporteController
    {
        public function index(): void
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01');
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01');
            $finRaw    = $_GET['fin']    ?? date('Y-m-d');
            $fin       = is_string($finRaw)    ? $finRaw    : date('Y-m-d');

            try {
                /** @var array<string,mixed> $resp */
                $resp = apiRequest(
                    'admin_dashboard/resumen_estadistico.php'
                    . '?inicio=' . urlencode($inicio)
                    . '&fin='    . urlencode($fin),
                    'GET'
                );
                if (!isset($resp['data']) || !is_array($resp['data'])) {
                    throw new \RuntimeException('Datos de reporte inválidos');
                }

                /** 
                 * @var array{
                 *   por_estado: array<int,array<string,mixed>>,
                 *   por_tipo:   array<int,array<string,mixed>>
                 * } $datos 
                 */
                $datos      = $resp['data'];
                $por_estado = $datos['por_estado'];
                $por_tipo   = $datos['por_tipo'];
                $errorReport = null;
            } catch (\Exception $e) {
                error_log('Error cargando reportes: ' . $e->getMessage());
                $por_estado  = [];
                $por_tipo    = [];
                $errorReport = htmlspecialchars($e->getMessage(), ENT_QUOTES);
            }

            view('reporte', compact('por_estado','por_tipo','inicio','fin','errorReport'));
        }

        public function exportPdf(): void
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01');
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01');
            $finRaw    = $_GET['fin'] ?? date('Y-m-d');
            $fin       = is_string($finRaw) ? $finRaw : date('Y-m-d');

            $url = ADMIN_BASE . 'reporte/generar_pdf.php'
                . '?inicio=' . urlencode($inicio)
                . '&fin='    . urlencode($fin);

            header('Location: ' . $url);
            exit;
        }

        public function exportExcel(): void
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01');
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01');
            $finRaw    = $_GET['fin'] ?? date('Y-m-d');
            $fin       = is_string($finRaw) ? $finRaw : date('Y-m-d');

            $url = API_BASE . 'admin_dashboard/reporte_csv.php'
                . '?inicio=' . urlencode($inicio)
                . '&fin='    . urlencode($fin);

            header('Location: ' . $url);
            exit;
        }
    }
?>