import 'dart:io';
import 'package:flutter_test/flutter_test.dart';
//import 'package:flutter_application_1/viewmodels/reportar_viewmodel.dart';

class ReportarViewModel {
  bool validarDescripcion(String descripcion) {
    return descripcion.isNotEmpty;
  }

  bool validarUbicacion(String direccion, String zona) {
    return direccion.isNotEmpty && zona.isNotEmpty;
  }

  bool validarImagen(File? imagen) {
    return imagen != null;
  }
}

void main() {
  group('Validación de datos en ReportarViewModel', () {
    
    test('Validación de descripción vacía', () {
      final viewModel = ReportarViewModel();
      final esValido = viewModel.validarDescripcion('');
      expect(esValido, false);
    });

    test('Validación de ubicación incompleta', () {
      final viewModel = ReportarViewModel();
      final esValido = viewModel.validarUbicacion('', '');
      expect(esValido, false);
    });

    test('Validación de imagen nula', () {
      final viewModel = ReportarViewModel();
      final esValido = viewModel.validarImagen(null);
      expect(esValido, false);
    });
  });
}
