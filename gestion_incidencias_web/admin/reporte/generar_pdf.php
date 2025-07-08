<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/fpdf/fpdf.php';

$token = $_SESSION['user_token'] ?? '';
if (!$token) {
    exit('Token no encontrado');
}

function fetchApiData($url, $token)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true)['data'] ?? [];
}

$url = API_BASE . 'admin_dashboard/incidencias.php';
$incidencias = fetchApiData($url, $token);

class PDF extends FPDF
{
    function Header()
    {
        if ($this->PageNo() === 1) return;
        $this->SetFont('Times', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Listado de Incidencias'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer()
    {
        if ($this->PageNo() === 1) return;
        $this->SetY(-15);
        $this->SetFont('Times', 'I', 9);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Times', 'B', 20);
$pdf->Ln(60);
$pdf->Cell(0, 10, utf8_decode('Sistema Web de Gestión de Incidencias'), 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Times', '', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Incidencias'), 0, 1, 'C');
$pdf->Ln(80);
$pdf->SetFont('Times', 'I', 12);
$pdf->Cell(0, 10, utf8_decode('Fecha de generación: ') . date('d/m/Y H:i'), 0, 1, 'C');

$pdf->AddPage();
$pdf->SetFont('Times', 'B', 11);
$pdf->Cell(0, 10, utf8_decode('Listado de Incidencias'), 0, 1, 'C');
$pdf->Ln(2);

$headers = ['ID', 'Tipo', 'Estado', 'Prioridad', 'Descripción', 'Ubicación'];
$widths = [10, 35, 28, 25, 60, 35];
foreach ($headers as $i => $col) {
    $pdf->Cell($widths[$i], 10, utf8_decode($col), 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Times', '', 10);

function getMaxHeight($pdf, $row, $widths)
{
    $max = 0;
    foreach ($row as $i => $text) {
        $nb = $pdf->GetStringWidth($text) / ($widths[$i] - 2);
        $height = ceil($nb) * 5;
        $max = max($max, $height);
    }
    return max($max, 8);
}

foreach ($incidencias as $inc) {
    $row = [
        $inc['id'],
        utf8_decode($inc['tipo']),
        utf8_decode($inc['estado']),
        $inc['prioridad'] ? utf8_decode($inc['prioridad']) : '—',
        utf8_decode($inc['descripcion']),
        $inc['latitud'] . ', ' . $inc['longitud']
    ];

    $maxHeight = getMaxHeight($pdf, $row, $widths);
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    for ($i = 0; $i < count($row); $i++) {
        $pdf->SetXY($x, $y);
        $pdf->MultiCell($widths[$i], 5, $row[$i], 1, 'L');
        $x += $widths[$i];
        $pdf->SetXY($x, $y);
    }
    $pdf->Ln($maxHeight);
}

if (empty($incidencias)) {
    $pdf->Ln(10);
    $pdf->SetFont('Times', 'I', 11);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron incidencias.'), 0, 1);
}

$pdf->Output('I', 'reporte_incidencias.pdf');
exit;