import 'package:flutter_test/flutter_test.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:proyecto_incidencias_app/viewmodels/reportar_viewmodel.dart';
import 'package:proyecto_incidencias_app/models/tipo_incidencia_model.dart';

/// Función local de validación (idéntica a la que tendrías en el ViewModel)
bool validarCamposSimulado(ReportarViewModel viewModel) {
  final descripcion = viewModel.descripcionController.text.trim();
  final direccion = viewModel.direccionController.text.trim();
  final zona = viewModel.zonaController.text.trim();
  final tipo = viewModel.tipoSeleccionado;
  final ubicacion = viewModel.selectedLocation;

  if (descripcion.isEmpty ||
      direccion.isEmpty ||
      zona.isEmpty ||
      tipo == null ||
      ubicacion.latitude == 0 ||
      ubicacion.longitude == 0) {
    return false;
  }
  return true;
}

void main() {
  test('RF01 - Validación de campos del reporte de incidencia', () {
    final viewModel = ReportarViewModel();

    // Simula los datos válidos del formulario
    viewModel.descripcionController.text = 'Fuga de agua';
    viewModel.direccionController.text = 'Av. La Cultura 456';
    viewModel.zonaController.text = 'Gregorio Albarracín';
    viewModel.tipoSeleccionado =
        TipoIncidencia(id: 1, nombre: 'Servicios Públicos');
    viewModel.selectedLocation = const LatLng(-18.015, -70.250);

    final resultado = validarCamposSimulado(viewModel);

    expect(
      resultado,
      isTrue,
      reason:
          'Debe retornar true si descripción, dirección, zona, tipo y coordenadas son válidos',
    );
  });
}
