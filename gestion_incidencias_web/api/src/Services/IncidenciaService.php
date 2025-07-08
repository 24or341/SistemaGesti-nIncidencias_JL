<?php
    namespace App\Services;

    use App\Repositories\IncidenciaRepository;

    class IncidenciaService
    {
        public static function obtenerTodas(): array
        {
            return IncidenciaRepository::obtenerTodas();
        }

        public static function asignarEmpleado(int $incidenciaId, int $empleadoId, int $prioridadId, ?string $fechaProgramada = null): bool
        {
            return IncidenciaRepository::asignarEmpleado($incidenciaId, $empleadoId, $prioridadId, $fechaProgramada);
        }

        public static function obtenerPorEmpleado(int $empleadoId): array
        {
            return IncidenciaRepository::obtenerPorEmpleado($empleadoId);
        }

        public static function actualizarEstado(int $incidenciaId, int $estadoId): bool
        {
            return IncidenciaRepository::actualizarEstado($incidenciaId, $estadoId);
        }

        public static function validarTelefono(string $celular): array
        {
            return IncidenciaRepository::validarTelefono($celular);
        }

        public static function registrarCiudadano(string $celular): int
        {
            return IncidenciaRepository::registrarCiudadano($celular);
        }
    }
?>