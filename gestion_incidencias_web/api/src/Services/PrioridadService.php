<?php
    namespace App\Services;

    use App\Repositories\PrioridadRepository;

    class PrioridadService
    {
        public static function obtenerTodas(): array
        {
            return PrioridadRepository::obtenerTodos();
        }
    }
?>