<?php

namespace App\Services;

use App\Repositories\ReporteRepository;

class ReporteService
{
    /**
     * @return array{
     *   por_estado: array<int, array{estado:string, total:int}>,
     *   por_tipo:   array<int, array{tipo:string, total:int}>
     * }
     */
    public static function obtenerResumen(string $inicio, string $fin): array
    {
        return [
            'por_estado' => ReporteRepository::contarPorEstado($inicio, $fin),
            'por_tipo'   => ReporteRepository::contarPorTipo($inicio, $fin),
        ];
    }

    /**
     * @return array<int, array{id:int|string, tipo:string, estado:string, fecha_reporte:string}>
     */
    public static function obtenerPorRango(string $inicio, string $fin): array
    {
        return ReporteRepository::obtenerPorRango($inicio, $fin);
    }
}
