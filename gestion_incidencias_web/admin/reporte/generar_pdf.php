<?php
// Configuración del archivo generar_pdf.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
session_start(); // Iniciar sesión para acceder al token de usuario
require_once __DIR__ . '/../config.php'; // Incluir archivo de configuración
require_once __DIR__ . '/fpdf/fpdf.php'; // Incluir la librería FPDF para generar PDFs

$token = $_SESSION['user_token'] ?? ''; // Obtener el token de sesión del usuario
if (!$token) { // Verificar si el token existe
    exit('Token no encontrado'); // Salir si no hay token
}

function fetchApiData($url, $token) // Función para obtener datos de la API
{
    $ch = curl_init($url); // Inicializar cURL
    curl_setopt_array($ch, [ // Configurar opciones de cURL
        CURLOPT_RETURNTRANSFER => true, // Retornar la transferencia como cadena
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"] // Incluir el token de autorización en el encabezado
    ]);
    $response = curl_exec($ch); // Ejecutar la solicitud cURL
    curl_close($ch); // Cerrar la sesión cURL
    return json_decode($response, true)['data'] ?? []; // Decodificar la respuesta JSON y retornar los datos
}

$url = API_BASE . 'admin_dashboard/incidencias.php'; // URL de la API para obtener las incidencias
$incidencias = fetchApiData($url, $token); // Obtener las incidencias desde la API

class PDF extends FPDF // Clase personalizada que extiende FPDF
{
    function Header() // Método para el encabezado del PDF
    {
        if ($this->PageNo() === 1) return; // No mostrar encabezado en la primera página
        $this->SetFont('Times', 'B', 12); // Establecer la fuente para el encabezado
        $this->Cell(0, 10, utf8_decode('Listado de Incidencias'), 0, 1, 'C'); // Título del encabezado
        $this->Ln(2); // Espacio después del encabezado
    }

    function Footer() // Método para el pie de página del PDF
    {
        if ($this->PageNo() === 1) return; // No mostrar pie de página en la primera página
        $this->SetY(-15); // Posicionar el pie de página a 15 mm del final
        $this->SetFont('Times', 'I', 9); // Establecer la fuente para el pie de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C'); // Mostrar el número de página
    }
}

$pdf = new PDF(); // Crear una nueva instancia de PDF
$pdf->AddPage(); // Añadir una página al PDF

$pdf->SetFont('Times', 'B', 20); // Establecer la fuente para el título
$pdf->Ln(60); // Espacio antes del título
$pdf->Cell(0, 10, utf8_decode('Sistema Web de Gestión de Incidencias'), 0, 1, 'C'); // Título del PDF
$pdf->Ln(10); // Espacio después del título
$pdf->SetFont('Times', '', 16); // Establecer la fuente para el subtítulo
$pdf->Cell(0, 10, utf8_decode('Reporte General de Incidencias'), 0, 1, 'C'); // Subtítulo del PDF
$pdf->Ln(80); // Espacio antes de la fecha
$pdf->SetFont('Times', 'I', 12); // Establecer la fuente para la fecha
$pdf->Cell(0, 10, utf8_decode('Fecha de generación: ') . date('d/m/Y H:i'), 0, 1, 'C'); // Mostrar la fecha de generación del reporte

$pdf->AddPage(); // Añadir una nueva página para el contenido del reporte
$pdf->SetFont('Times', 'B', 11); // Establecer la fuente para el encabezado de la tabla
$pdf->Cell(0, 10, utf8_decode('Listado de Incidencias'), 0, 1, 'C'); // Título de la tabla
$pdf->Ln(2); // Espacio después del título de la tabla

$headers = ['ID', 'Tipo', 'Estado', 'Prioridad', 'Descripción', 'Ubicación']; // Encabezados de la tabla
$widths = [10, 35, 28, 25, 60, 35]; // Anchos de las columnas
foreach ($headers as $i => $col) { // Iterar sobre los encabezados
    $pdf->Cell($widths[$i], 10, utf8_decode($col), 1, 0, 'C'); // Crear la celda del encabezado
}
$pdf->Ln(); // Nueva línea después de los encabezados

$pdf->SetFont('Times', '', 10); // Establecer la fuente para el contenido de la tabla

function getMaxHeight($pdf, $row, $widths) // Función para calcular la altura máxima de una fila
{
    $max = 0; // Inicializar la altura máxima
    foreach ($row as $i => $text) { // Iterar sobre cada celda de la fila
        $nb = $pdf->GetStringWidth($text) / ($widths[$i] - 2); // Calcular el número de líneas necesarias para el texto
        $height = ceil($nb) * 5; // Calcular la altura de la celda
        $max = max($max, $height); // Actualizar la altura máxima si es necesario
    }
    return max($max, 8); // Retornar la altura máxima, asegurando un mínimo de 8
}

foreach ($incidencias as $inc) { // Iterar sobre cada incidencia
    $row = [ // Crear una fila con los datos de la incidencia
        $inc['id'], // ID de la incidencia
        utf8_decode($inc['tipo']), // Tipo de incidencia
        utf8_decode($inc['estado']), // Estado de la incidencia
        $inc['prioridad'] ? utf8_decode($inc['prioridad']) : '—', // Prioridad de la incidencia, mostrando '—' si no hay prioridad
        utf8_decode($inc['descripcion']), // Descripción de la incidencia
        $inc['latitud'] . ', ' . $inc['longitud'] // Ubicación de la incidencia (latitud y longitud)
    ];

    $maxHeight = getMaxHeight($pdf, $row, $widths); // Calcular la altura máxima de la fila
    $x = $pdf->GetX(); // Obtener la posición X actual
    $y = $pdf->GetY(); // Obtener la posición Y actual

    for ($i = 0; $i < count($row); $i++) { // Iterar sobre cada celda de la fila
        $pdf->SetXY($x, $y); // Establecer la posición para la celda
        $pdf->MultiCell($widths[$i], 5, $row[$i], 1, 'L'); // Crear la celda con el texto, permitiendo múltiples líneas
        $x += $widths[$i]; // Actualizar la posición X para la siguiente celda
        $pdf->SetXY($x, $y); // Establecer la posición X para la siguiente celda
    }
    $pdf->Ln($maxHeight); // Mover a la siguiente línea después de completar la fila
}

if (empty($incidencias)) { // Verificar si no hay incidencias
    $pdf->Ln(10); // Espacio si no hay incidencias
    $pdf->SetFont('Times', 'I', 11); // Establecer la fuente para el mensaje de no encontrado
    $pdf->Cell(0, 10, utf8_decode('No se encontraron incidencias.'), 0, 1); // Mensaje indicando que no se encontraron incidencias
}

$pdf->Output('I', 'reporte_incidencias.pdf'); // Generar el PDF y enviarlo al navegador
exit; // Finalizar el script para evitar cualquier salida adicional