<?php
// Configuración del archivo generar_excel.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
    require_once("../../inc/db.php"); // Incluir archivo de conexión a la base de datos

    $inicio = $_GET['inicio'] ?? date('Y-m-01'); // Fecha de inicio por defecto al primer día del mes actual
    $fin = $_GET['fin'] ?? date('Y-m-d'); // Fecha de fin por defecto al día actual

    header('Content-Type: text/csv'); // Establecer el tipo de contenido a CSV
    header('Content-Disposition: attachment; filename="reporte_incidencias.csv"'); // Nombre del archivo a descargar

    $output = fopen("php://output", "w"); // Abrir el flujo de salida para escribir el CSV
    fputcsv($output, ['ID', 'Tipo', 'Estado', 'Fecha']); // Encabezados del CSV

    $stmt = $pdo->prepare("
        SELECT i.id, ti.nombre AS tipo, ei.nombre AS estado, i.fecha_reporte
        FROM incidencia i
        JOIN tipo_incidencia ti ON i.tipo_id = ti.id
        JOIN estado_incidencia ei ON i.estado_id = ei.id
        WHERE i.fecha_reporte BETWEEN :inicio AND :fin
        ORDER BY i.fecha_reporte DESC
    "); // Preparar la consulta SQL para obtener las incidencias
    $stmt->execute(['inicio' => $inicio, 'fin' => $fin]); // Ejecutar la consulta con los parámetros de fecha

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // Iterar sobre los resultados
        fputcsv($output, [$row['id'], $row['tipo'], $row['estado'], $row['fecha_reporte']]); // Escribir cada fila en el CSV
    }

    fclose($output); // Cerrar el flujo de salida
    exit(); // Finalizar el script para evitar cualquier salida adicional
?>