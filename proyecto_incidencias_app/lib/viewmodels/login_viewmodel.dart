import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../models/usuario_model.dart';
// Realizado por: Leandro Hurtado Ortiz
// Fecha de creación: 2025-05-10
// Requerimiento: RF03 - Autenticación de Empleados
/// ViewModel (Patrón MVVM con Provider) encargado de gestionar el estado del formulario de Login.
/// Contiene los datos ingresados por el usuario, el estado de carga (isLoading),
/// mensajes de error, y la lógica para comunicarse con el AuthService.
/// Ejecución: Es provisto a la LoginScreen y desencadena la autenticación al llamar a login().
class LoginViewModel extends ChangeNotifier {
  String _email = '';
  String _password = '';
  bool _isLoading = false;
  String? _errorMessage;
  Usuario? _usuario;

  // Getters
  String get email => _email;
  String get password => _password;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  Usuario? get usuario => _usuario;

  // Setters
  void setEmail(String value) {
    _email = value;
    notifyListeners();
  }

  void setPassword(String value) {
    _password = value;
    notifyListeners();
  }

  Future<bool> login() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final response = await AuthService.login(_email, _password);

    _isLoading = false;

    if (response['success'] == true && response['data'] != null) {
      final userData = response['data'];
      if (userData is Map<String, dynamic>) {
        try {
          _usuario = Usuario.fromJson(userData);
        } catch (e) {
          _errorMessage = 'Error al interpretar los datos del usuario.';
          notifyListeners();
          return false;
        }
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Respuesta inesperada del servidor.';
        notifyListeners();
        return false;
      }
    } else {
      _errorMessage = response['message'] ?? 'Error desconocido';
      notifyListeners();
      return false;
    }
  }
}
