import 'package:flutter_test/flutter_test.dart';
import 'package:proyecto_incidencias_app/models/incidencia_model.dart';

bool validarIncidenciaAsignada(Incidencia incidencia) {
  const estadosValidos = ['Pendiente', 'En desarrollo', 'Terminado'];

  final lat = incidencia.latitud;
  final lon = incidencia.longitud;

  final coordenadasValidas = (lat == null || lon == null)
      ? true
      : (lat >= -90 && lat <= 90 && lon >= -180 && lon <= 180);

  return incidencia.descripcion.isNotEmpty &&
      (incidencia.tipo != null && incidencia.tipo!.isNotEmpty) &&
      estadosValidos.contains(incidencia.estado) &&
      coordenadasValidas;
}

void main() {
  test('RF11 - Validar datos de una incidencia asignada al empleado', () {
    final incidencia = Incidencia(
      id: 50,
      descripcion: 'Bache en avenida principal',
      estado: 'En desarrollo',
      fechaReporte: '2025-06-10',
      direccion: 'Av. Leguía',
      zona: 'Gregorio Albarracín',
      latitud: -18.015,
      longitud: -70.255,
      tipo: 'Infraestructura vial',
    );

    final esValida = validarIncidenciaAsignada(incidencia);

    expect(
      esValida,
      isTrue,
      reason:
          'La incidencia asignada debe tener tipo, estado, descripción y coordenadas válidas para ser mostrada al empleado.',
    );
  });
}
