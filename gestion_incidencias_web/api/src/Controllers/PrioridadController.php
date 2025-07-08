<?php
    namespace App\Controllers;

    use App\Core\Response;
    use App\Services\PrioridadService;

    class PrioridadController
    {
        public static function listar(): void
        {
            try {
                $todos = PrioridadService::obtenerTodas();
                Response::success($todos, "Prioridades obtenidas");
            } catch (\Exception $e) {
                Response::error("Error al obtener prioridades: " . $e->getMessage(), 500);
            }
        }
    }
?>