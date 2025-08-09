<?php
// Configuración del archivo IncidenciasController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Habilitar tipos estrictos
    class IncidenciasController // Controlador de incidencias
    {
        public function index(): void // Método principal para manejar la vista de incidencias
        {
            $errorInc = null; // Variable para almacenar errores
            try {
                /** @var array<string,mixed> $incResp */
                $incResp = apiRequest('admin_dashboard/incidencias.php', 'GET'); // Solicitar datos de incidencias
                if (! isset($incResp['data']) || ! is_array($incResp['data'])) { // Validar respuesta
                    throw new \RuntimeException('Incidencias inválidas'); // Lanzar excepción si la respuesta es inválida
                }
                /** @var array<int,array<string,mixed>> $incidencias */
                $incidencias = $incResp['data']; // Almacenar datos de incidencias

                /** @var array<string,mixed> $empResp */
                $empResp = apiRequest('admin_dashboard/empleados.php', 'GET'); // Solicitar datos de empleados
                if (! isset($empResp['data']) || ! is_array($empResp['data'])) { // Validar respuesta
                    throw new \RuntimeException('Empleados inválidos'); // Lanzar excepción si la respuesta es inválida
                }
                /** @var array<int,array<string,mixed>> $empleados */
                $empleados = $empResp['data']; // Almacenar datos de empleados

                /** @var array<string,mixed> $prioResp */
                $prioResp = apiRequest('admin_dashboard/prioridad.php', 'GET'); // Solicitar datos de prioridades
                if (! isset($prioResp['data']) || ! is_array($prioResp['data'])) { // Validar respuesta
                    throw new \RuntimeException('Prioridades inválidas'); // Lanzar excepción si la respuesta es inválida
                }
                /** @var array<int,array<string,mixed>> $prioridades */
                $prioridades = $prioResp['data']; // Almacenar datos de prioridades
            } catch (\Exception $e) { // Capturar excepciones
                error_log('Error cargando Incidencias: ' . $e->getMessage()); // Registrar error en el log
                $incidencias = []; // Inicializar variables vacías en caso de error
                $empleados   = []; // Inicializar variables vacías en caso de error
                $prioridades = []; // Inicializar variables vacías en caso de error
                $errorInc    = htmlspecialchars($e->getMessage(), ENT_QUOTES); // Almacenar mensaje de error
            }
            view('incidencias', compact('incidencias','empleados','prioridades','errorInc')); // Renderizar vista con los datos
        }
    }
?>