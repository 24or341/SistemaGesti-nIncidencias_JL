import 'package:flutter_test/flutter_test.dart';
import 'package:flutter/material.dart';
import 'package:proyecto_incidencias_app/models/incidencia_model.dart';

/// Función auxiliar que retorna el color e ícono según el estado de la incidencia
Map<String, dynamic> obtenerEstiloPorEstado(String estado) {
  switch (estado) {
    case 'Pendiente':
      return {'color': Colors.orange, 'icono': Icons.access_time};
    case 'En desarrollo':
      return {'color': Colors.blue, 'icono': Icons.build_circle};
    case 'Terminado':
      return {'color': Colors.green, 'icono': Icons.check_circle};
    default:
      return {'color': Colors.grey, 'icono': Icons.help_outline};
  }
}

/// Validación integral de una incidencia visualizada
bool validarIncidenciaVisualizada(Incidencia incidencia) {
  return incidencia.descripcion.isNotEmpty &&
      incidencia.estado.isNotEmpty &&
      incidencia.tipo != null &&
      incidencia.fechaReporte.isNotEmpty;
}

void main() {
  test('RF08 - Validar presentación y datos de incidencias visualizadas', () {
    // Incidencia simulada (modelo real)
    final incidencia = Incidencia(
      id: 22,
      descripcion: 'Bache en la avenida principal',
      estado: 'En desarrollo',
      fechaReporte: '2025-06-08',
      direccion: 'Av. Leguía',
      zona: 'Gregorio Albarracín',
      latitud: -18.015,
      longitud: -70.255,
      tipo: 'Infraestructura vial',
    );

    // Validar integridad de datos mostrados
    final datosValidos = validarIncidenciaVisualizada(incidencia);
    expect(
      datosValidos,
      isTrue,
      reason:
          'Debe retornar true si los datos (estado, tipo, descripción, fecha) son válidos y completos.',
    );

    // Validar estilo visual según estado
    final estilo = obtenerEstiloPorEstado(incidencia.estado);

    expect(estilo['color'], equals(Colors.blue),
        reason: 'El color azul debe asignarse a incidencias en desarrollo.');
    expect(estilo['icono'], equals(Icons.build_circle),
        reason: 'El ícono de herramientas debe asignarse a incidencias en desarrollo.');
  });
}
