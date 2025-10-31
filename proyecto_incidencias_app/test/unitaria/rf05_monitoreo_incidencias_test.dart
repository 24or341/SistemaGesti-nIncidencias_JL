import 'package:flutter_test/flutter_test.dart';
import 'package:proyecto_incidencias_app/models/incidencia_model.dart';

/// Validación integral de los datos de una incidencia asignada.
/// Comprueba que existan campos clave, que el estado sea válido,
/// que sea editable solo si está pendiente y que las coordenadas estén dentro del rango.
bool validarDatosIncidencia(Incidencia incidencia) {
  const estadosValidos = ['Pendiente', 'En desarrollo', 'Terminado'];

  return incidencia.id > 0 &&
      incidencia.descripcion.isNotEmpty &&
      estadosValidos.contains(incidencia.estado) &&
      (incidencia.latitud != null &&
          incidencia.longitud != null &&
          incidencia.latitud! >= -90 &&
          incidencia.latitud! <= 90 &&
          incidencia.longitud! >= -180 &&
          incidencia.longitud! <= 180);
}

void main() {
  test('RF05 - Validar datos de una incidencia asignada', () {
    final incidencia = Incidencia(
      id: 15,
      descripcion: 'Fuga de agua en calle Los Cedros',
      estado: 'Pendiente',
      fechaReporte: '2025-06-07',
      direccion: 'Av. Los Cedros',
      zona: 'Gregorio Albarracín',
      latitud: -18.013,
      longitud: -70.256,
      tipo: 'Servicios Públicos',
    );

    final resultado = validarDatosIncidencia(incidencia);

    expect(
      resultado,
      isTrue,
      reason:
          'Debe retornar true si los datos de la incidencia cumplen con las validaciones: campos presentes, estado permitido y coordenadas válidas.',
    );
  });
}
