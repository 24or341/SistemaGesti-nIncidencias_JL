import 'package:flutter_test/flutter_test.dart';
import 'package:proyecto_incidencias_app/models/incidencia_model.dart';

bool esTransicionEstadoValida(Incidencia incidencia, String nuevoEstado) {
  final estadoActual = incidencia.estado;

  if (estadoActual == nuevoEstado) return true;

  if (estadoActual == 'Pendiente' && nuevoEstado == 'En desarrollo') {
    return true;
  }

  if (estadoActual == 'En desarrollo' && nuevoEstado == 'Terminado') {
    return true;
  }

  return false;
}

void main() {
  test('RF12 - Validar flujo de cambio de estado de una incidencia', () {
    final incidenciaPendiente = Incidencia(
      id: 41,
      descripcion: 'Bache en la vía principal',
      estado: 'Pendiente',
      fechaReporte: '2025-06-10',
      direccion: 'Av. Municipal 123',
      zona: 'Gregorio Albarracín',
      latitud: -18.015,
      longitud: -70.250,
      tipo: 'Infraestructura vial',
    );

    final incidenciaEnDesarrollo = Incidencia(
      id: 41,
      descripcion: 'Bache en la vía principal',
      estado: 'En desarrollo',
      fechaReporte: '2025-06-10',
      direccion: 'Av. Municipal 123',
      zona: 'Gregorio Albarracín',
      latitud: -18.015,
      longitud: -70.250,
      tipo: 'Infraestructura vial',
    );

    // Transiciones válidas
    expect(
      esTransicionEstadoValida(incidenciaPendiente, 'En desarrollo'),
      isTrue,
      reason: 'Debe permitir pasar de Pendiente a En desarrollo.',
    );

    expect(
      esTransicionEstadoValida(incidenciaEnDesarrollo, 'Terminado'),
      isTrue,
      reason: 'Debe permitir pasar de En desarrollo a Terminado.',
    );

    // Mantener el mismo estado
    expect(
      esTransicionEstadoValida(incidenciaPendiente, 'Pendiente'),
      isTrue,
      reason: 'Debe permitir mantener el mismo estado.',
    );

    // Transiciones inválidas
    expect(
      esTransicionEstadoValida(incidenciaPendiente, 'Terminado'),
      isFalse,
      reason: 'No debe permitir ir de Pendiente directamente a Terminado.',
    );

    expect(
      esTransicionEstadoValida(incidenciaEnDesarrollo, 'Pendiente'),
      isFalse,
      reason: 'No debe permitir regresar de En desarrollo a Pendiente.',
    );
  });
}
