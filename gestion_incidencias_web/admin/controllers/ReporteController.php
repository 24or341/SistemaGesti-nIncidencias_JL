<?php
// Configuración del archivo ReporteController.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    declare(strict_types=1); // Habilitar tipos estrictos
    class ReporteController // Controlador de reportes
    {
        public function index(): void // Método principal para manejar la vista de reportes
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01'); // Fecha de inicio por defecto: primer día del mes actual
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01'); // Validar que sea cadena
            $finRaw    = $_GET['fin']    ?? date('Y-m-d'); // Fecha de fin por defecto: día actual
            $fin       = is_string($finRaw)    ? $finRaw    : date('Y-m-d'); // Validar que sea cadena

            try { // Intentar obtener los datos del reporte
                /** @var array<string,mixed> $resp */
                $resp = apiRequest( // Hacer solicitud a la API
                    'admin_dashboard/resumen_estadistico.php' // Endpoint de la API
                    . '?inicio=' . urlencode($inicio) // Parámetro de fecha de inicio
                    . '&fin='    . urlencode($fin), // Parámetro de fecha de fin
                    'GET' // Método HTTP
                );
                if (!isset($resp['data']) || !is_array($resp['data'])) { // Validar respuesta
                    throw new \RuntimeException('Datos de reporte inválidos'); // Lanzar excepción si la respuesta es inválida
                }

                /** 
                 * @var array{
                 *   por_estado: array<int,array<string,mixed>>,
                 *   por_tipo:   array<int,array<string,mixed>>
                 * } $datos 
                 */
                $datos      = $resp['data']; // Almacenar datos del reporte
                $por_estado = $datos['por_estado']; // Datos del reporte por estado
                $por_tipo   = $datos['por_tipo']; // Datos del reporte por tipo
                $errorReport = null; // Inicializar variable de error como nula
            } catch (\Exception $e) { // Capturar excepciones
                error_log('Error cargando reportes: ' . $e->getMessage()); // Registrar error en el log
                $por_estado  = []; // Inicializar variables vacías en caso de error
                $por_tipo    = []; // Inicializar variables vacías en caso de error
                $errorReport = htmlspecialchars($e->getMessage(), ENT_QUOTES); // Almacenar mensaje de error
            }

            view('reporte', compact('por_estado','por_tipo','inicio','fin','errorReport')); // Renderizar vista con los datos
        }

        public function exportPdf(): void // Método para exportar reporte en PDF
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01'); // Fecha de inicio por defecto: primer día del mes actual
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01'); // Validar que sea cadena
            $finRaw    = $_GET['fin'] ?? date('Y-m-d'); // Fecha de fin por defecto: día actual
            $fin       = is_string($finRaw) ? $finRaw : date('Y-m-d'); // Validar que sea cadena

            $url = ADMIN_BASE . 'reporte/generar_pdf.php' // URL del script para generar PDF
                . '?inicio=' . urlencode($inicio) // Parámetro de fecha de inicio
                . '&fin='    . urlencode($fin); // Parámetro de fecha de fin

            header('Location: ' . $url); // Redirigir a la URL para generar el PDF
            exit; // Terminar la ejecución del script
        }

        public function exportExcel(): void // Método para exportar reporte en Excel
        {
            $inicioRaw = $_GET['inicio'] ?? date('Y-m-01'); // Fecha de inicio por defecto: primer día del mes actual
            $inicio    = is_string($inicioRaw) ? $inicioRaw : date('Y-m-01'); // Validar que sea cadena
            $finRaw    = $_GET['fin'] ?? date('Y-m-d'); // Fecha de fin por defecto: día actual
            $fin       = is_string($finRaw) ? $finRaw : date('Y-m-d'); // Validar que sea cadena

            $url = API_BASE . 'admin_dashboard/reporte_csv.php' // URL del script para generar Excel
                . '?inicio=' . urlencode($inicio) // Parámetro de fecha de inicio
                . '&fin='    . urlencode($fin); // Parámetro de fecha de fin

            header('Location: ' . $url); // Redirigir a la URL para generar el Excel
            exit; // Terminar la ejecución del script
        }
    }
?>