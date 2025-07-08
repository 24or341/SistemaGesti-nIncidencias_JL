<?php

    namespace App\Services;

    use App\Repositories\ReporteRepository;

    class ReporteService
    {
        public static function obtenerResumen(string $inicio, string $fin): array
        {
            return [
                'por_estado' => ReporteRepository::contarPorEstado($inicio, $fin),
                'por_tipo'   => ReporteRepository::contarPorTipo($inicio, $fin)
            ];
        }

        public static function obtenerPorRango(string $inicio, string $fin): array
        {
            return ReporteRepository::obtenerPorRango($inicio, $fin);
        }
    }
?>