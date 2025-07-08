import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../models/usuario_model.dart';

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
        _usuario = Usuario.fromJson(userData);
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
