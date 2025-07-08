<?php

    namespace App\Controllers;

    use App\Services\ReporteService;
    use App\Core\Response;

    class ReporteController
    {
        public static function estadisticas(?string $inicio = null, ?string $fin = null): void
        {
            try {
                $inicio = $inicio ?: ($_GET['inicio'] ?? date('Y-m-01'));
                $fin    = $fin    ?: ($_GET['fin']    ?? date('Y-m-d'));

                $estadisticas = ReporteService::obtenerResumen($inicio, $fin);
                Response::success($estadisticas, "Resumen de incidencias");
            } catch (\Exception $e) {
                Response::error("Error al obtener reporte: " . $e->getMessage(), 500);
            }
        }

        public static function exportCsv(): void
        {
            $inicio = $_GET['inicio'] ?? date('Y-m-01');
            $fin    = $_GET['fin']    ?? date('Y-m-d');

            $incidencias = ReporteService::obtenerPorRango($inicio, $fin);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_incidencias_'.$inicio.'_a_'.$fin.'.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID','Tipo','Estado','Fecha']);

            foreach ($incidencias as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['tipo'],
                    $row['estado'],
                    $row['fecha_reporte']
                ]);
            }
            fclose($output);
            exit;
        }
    }
?>