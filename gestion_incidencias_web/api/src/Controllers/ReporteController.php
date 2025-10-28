<?php

namespace App\Controllers;

use App\Services\ReporteService;
use App\Core\Response;

class ReporteController
{
    public static function estadisticas(?string $inicio = null, ?string $fin = null): void
    {
        try {
            $inicio = is_string($inicio)
                ? $inicio
                : (isset($_GET['inicio']) && is_string($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01'));

            $fin = is_string($fin)
                ? $fin
                : (isset($_GET['fin']) && is_string($_GET['fin']) ? $_GET['fin'] : date('Y-m-d'));

            $estadisticas = ReporteService::obtenerResumen($inicio, $fin);
            Response::success($estadisticas, "Resumen de incidencias");
        } catch (\Exception $e) {
            Response::error("Error al obtener reporte: " . $e->getMessage(), 500);
        }
    }

    public static function exportCsv(): void
    {
        $inicio = (isset($_GET['inicio']) && is_string($_GET['inicio'])) ? $_GET['inicio'] : date('Y-m-01');
        $fin    = (isset($_GET['fin'])    && is_string($_GET['fin']))    ? $_GET['fin']    : date('Y-m-d');

        /** @var array<int, array{id:int|string, tipo:string, estado:string, fecha_reporte:string}> $incidencias */
        $incidencias = ReporteService::obtenerPorRango($inicio, $fin);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_incidencias_' . $inicio . '_a_' . $fin . '.csv"');

        $output = fopen('php://output', 'w');
        if ($output === false) {
            Response::error('No se pudo abrir el stream de salida', 500);
            return;
        }

        fputcsv($output, ['ID', 'Tipo', 'Estado', 'Fecha']);

        foreach ($incidencias as $row) {
            // $row cumple: array{id:int|string, tipo:string, estado:string, fecha_reporte:string}
            $id     = (string) $row['id'];           // opcional castear para el CSV
            $tipo   = $row['tipo'];
            $estado = $row['estado'];
            $fecha  = $row['fecha_reporte'];

            fputcsv($output, [$id, $tipo, $estado, $fecha]);
        }

        fclose($output);
        exit;
    }
}
