import 'package:flutter/material.dart';
import '../models/usuario_model.dart';
import '../services/auth_service.dart';

class PerfilViewModel extends ChangeNotifier {
  late Usuario _usuario;
  bool _isEditing = false;
  bool _isLoading = false;
  String? _successMessage;
  String? _errorMessage;

  // Constructor
  PerfilViewModel(Usuario usuario) {
    _usuario = usuario;
  }

  // Getters
  Usuario get usuario => _usuario;
  bool get isEditing => _isEditing;
  bool get isLoading => _isLoading;
  String? get successMessage => _successMessage;
  String? get errorMessage => _errorMessage;

  // Setters para campos editables
  void setNombre(String value) {
    _usuario = Usuario(
      id: _usuario.id,
      nombre: value,
      apellido: _usuario.apellido,
      correo: _usuario.correo,
      dni: _usuario.dni,
      token: _usuario.token,
    );
    notifyListeners();
  }

  void setApellido(String value) {
    _usuario = Usuario(
      id: _usuario.id,
      nombre: _usuario.nombre,
      apellido: value,
      correo: _usuario.correo,
      dni: _usuario.dni,
      token: _usuario.token,
    );
    notifyListeners();
  }

  void setCorreo(String value) {
    _usuario = Usuario(
      id: _usuario.id,
      nombre: _usuario.nombre,
      apellido: _usuario.apellido,
      correo: value,
      dni: _usuario.dni,
      token: _usuario.token,
    );
    notifyListeners();
  }

  void setDni(String value) {
    _usuario = Usuario(
      id: _usuario.id,
      nombre: _usuario.nombre,
      apellido: _usuario.apellido,
      correo: _usuario.correo,
      dni: value,
      token: _usuario.token,
    );
    notifyListeners();
  }

  void toggleEditing() {
    _isEditing = !_isEditing;
    notifyListeners();
  }

  // Guardar cambios en el backend
  Future<void> guardarCambios() async {
    _isLoading = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      final response = await AuthService.actualizarPerfil(_usuario);

      if (response['success'] == true) {
        _successMessage = response['message'];
        _isEditing = false;
      } else {
        _errorMessage = response['message'] ?? 'Error desconocido.';
      }
    } catch (e) {
      _errorMessage = 'Excepción: $e';
    }

    _isLoading = false;
    notifyListeners();
  }
}
