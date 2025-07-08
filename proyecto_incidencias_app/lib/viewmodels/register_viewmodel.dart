import 'package:flutter/material.dart';
import '../services/auth_service.dart';

class RegisterViewModel extends ChangeNotifier {
  String _dni = '';
  String _nombre = '';
  String _apellido = '';
  String _correo = '';
  String _password = '';

  bool _isLoading = false;
  String? _errorMessage;
  String? _successMessage;

  // Getters
  String get dni => _dni;
  String get nombre => _nombre;
  String get apellido => _apellido;
  String get correo => _correo;
  String get password => _password;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;

  // Setters
  void setDni(String value) {
    _dni = value;
    notifyListeners();
  }

  void setNombre(String value) {
    _nombre = value;
    notifyListeners();
  }

  void setApellido(String value) {
    _apellido = value;
    notifyListeners();
  }

  void setCorreo(String value) {
    _correo = value;
    notifyListeners();
  }

  void setPassword(String value) {
    _password = value;
    notifyListeners();
  }

  Future<bool> registrar() async {
    _isLoading = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    final response = await AuthService.register(_dni, _nombre, _apellido, _correo, _password);

    _isLoading = false;

    if (response['success'] == true) {
      _successMessage = response['message'];
      notifyListeners();
      return true;
    } else {
      _errorMessage = response['message'] ?? 'Error desconocido';
      notifyListeners();
      return false;
    }
  }
}
