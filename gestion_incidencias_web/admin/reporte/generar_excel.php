<?php

    require_once("../../inc/db.php");

    $inicio = $_GET['inicio'] ?? date('Y-m-01');
    $fin = $_GET['fin'] ?? date('Y-m-d');

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte_incidencias.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Tipo', 'Estado', 'Fecha']);

    $stmt = $pdo->prepare("
        SELECT i.id, ti.nombre AS tipo, ei.nombre AS estado, i.fecha_reporte
        FROM incidencia i
        JOIN tipo_incidencia ti ON i.tipo_id = ti.id
        JOIN estado_incidencia ei ON i.estado_id = ei.id
        WHERE i.fecha_reporte BETWEEN :inicio AND :fin
        ORDER BY i.fecha_reporte DESC
    ");
    $stmt->execute(['inicio' => $inicio, 'fin' => $fin]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [$row['id'], $row['tipo'], $row['estado'], $row['fecha_reporte']]);
    }

    fclose($output);
    exit();

?>