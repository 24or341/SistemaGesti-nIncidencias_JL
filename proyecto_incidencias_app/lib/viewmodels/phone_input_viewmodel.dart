import 'package:flutter/material.dart';
import '../services/incidencia_service.dart';

class PhoneInputViewModel extends ChangeNotifier {
  final TextEditingController phoneController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;
  int? _ciudadanoId;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  int? get ciudadanoId => _ciudadanoId;

  Future<bool> validarTelefono() async {
    final celular = phoneController.text.trim();

    if (celular.isEmpty || celular.length < 9) {
      _errorMessage = 'Por favor ingrese un número de teléfono válido.';
      notifyListeners();
      return false;
    }

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await IncidenciaService.validarTelefono(celular);

      if (response != null && response['success'] == true) {
        _ciudadanoId = response['data']['id'];
        return true;
      } else {
        _errorMessage = response?['message'] ?? 'Error desconocido';
        return false;
      }
    } catch (_) {
      _errorMessage = 'Error al verificar el teléfono.';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  @override
  void dispose() {
    phoneController.dispose();
    super.dispose();
  }
}
