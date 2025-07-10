import 'dart:convert';
import 'dart:developer' as developer;
import 'package:http/http.dart' as http;
import '../config.dart';
import '../models/usuario_model.dart';

class AuthService {
  // Método para iniciar sesión (empleados y admins vía login.php)
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final url = Uri.parse('${baseUrl}login.php');

    try {
      developer.log('Intentando iniciar sesión con: $email', name: 'AuthService');

      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );

      developer.log('Respuesta: ${response.body}', name: 'AuthService');

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return responseData;
      } else {
        return {'success': false, 'message': 'Error del servidor'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Excepción: $e'};
    }
  }

  // Método para registrar un nuevo empleado (usando register.php)
  static Future<Map<String, dynamic>> register(String dni, String name, String surname, String email, String password) async {
    final url = Uri.parse('${baseUrl}register.php');

    try {
      developer.log('Intentando registrar a: $dni $name $surname', name: 'AuthService');

      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'dni': dni,
          'nombre': name,
          'apellido': surname,
          'email': email,
          'password': password,
          'role': 'empleado'
        }),
      );

      developer.log('Respuesta: ${response.body}', name: 'AuthService');

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return responseData;
      } else {
        return {'success': false, 'message': 'Error del servidor'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Excepción: $e'};
    }
  }

  // Nuevo: Método para actualizar los datos del perfil
  static Future<Map<String, dynamic>> actualizarPerfil(Usuario usuario) async {
    final url = Uri.parse('${baseUrl}actualizar_perfil.php');

    try {
      developer.log('Actualizando perfil: ${usuario.toJson()}', name: 'AuthService');

      final response = await http.put(
        url,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': usuario.token ?? ''
        },
        body: jsonEncode(usuario.toJson()),
      );

      developer.log('Respuesta: ${response.body}', name: 'AuthService');

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return responseData;
      } else {
        return {'success': false, 'message': 'Error del servidor'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Excepción: $e'};
    }
  }

}
