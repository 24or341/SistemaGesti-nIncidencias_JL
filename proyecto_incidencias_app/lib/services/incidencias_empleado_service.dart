import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../config.dart';

class IncidenciasEmpleadoService {
  // Obtener incidencias asignadas a un usuario con rol empleado
  static Future<List<Map<String, dynamic>>> obtenerIncidenciasAsignadas(int usuarioId, {String? token}) async {
    final url = Uri.parse('${baseUrl}api_empleados/incidencias_asignadas.php?usuario_id=$usuarioId');

    try {
      final response = await http.get(
        url,
        headers: token != null ? {'Authorization': 'Bearer $token'} : {},
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true && responseData['data'] is List) {
          List<Map<String, dynamic>> incidencias = List<Map<String, dynamic>>.from(responseData['data']);
          for (var incidencia in incidencias) {
            if (incidencia.containsKey('foto') && incidencia['foto'] != null) {
              try {
                Uint8List fotoBytes = base64Decode(incidencia['foto']);
                incidencia['foto'] = fotoBytes;
              } catch (e) {
                incidencia['foto'] = null;
              }
            }
          }
          return incidencias;
        } else {
          debugPrint("Error en los datos recibidos: ${responseData['message']}");
          return [];
        }
      } else {
        debugPrint("Error HTTP ${response.statusCode}");
        return [];
      }
    } catch (e) {
      debugPrint("Error al obtener incidencias: $e");
      return [];
    }
  }

  // Actualizar el estado de una incidencia
  static Future<bool> actualizarEstado(int incidenciaId, int nuevoEstadoId, {String? token}) async {
    final url = Uri.parse('${baseUrl}api_empleados/actualizar_estado.php');

    try {
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'incidencia_id': incidenciaId,
          'nuevo_estado': nuevoEstadoId,
        }),
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return responseData['success'] == true;
      } else {
        debugPrint("Error HTTP al actualizar estado: ${response.statusCode}");
        return false;
      }
    } catch (e) {
      debugPrint("Error al actualizar estado: $e");
      return false;
    }
  }

  // Obtener detalles de una incidencia asignada a un usuario con rol empleado
  static Future<Map<String, dynamic>?> obtenerIncidenciaPorId(int usuarioId, int incidenciaId, {String? token}) async {
    final url = Uri.parse('${baseUrl}api_empleados/incidencias_asignadas.php?usuario_id=$usuarioId');

    try {
      final response = await http.get(
        url,
        headers: token != null ? {'Authorization': 'Bearer $token'} : {},
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true && responseData['data'] is List) {
          var incidencia = List<Map<String, dynamic>>.from(responseData['data'])
              .firstWhere((element) => element['id'] == incidenciaId, orElse: () => {});

          if (incidencia.containsKey('foto') && incidencia['foto'] != null) {
            try {
              Uint8List fotoBytes = base64Decode(incidencia['foto']);
              incidencia['foto'] = fotoBytes;
            } catch (e) {
              incidencia['foto'] = null;
            }
          }
          return incidencia;
        } else {
          debugPrint("Error en los datos: ${responseData['message']}");
          return null;
        }
      } else {
        debugPrint("Error HTTP al obtener incidencia: ${response.statusCode}");
        return null;
      }
    } catch (e) {
      debugPrint("Excepción al obtener detalle de incidencia: $e");
      return null;
    }
  }

  // Obtener todas las incidencias (para administradores)
  static Future<List<Map<String, dynamic>>> obtenerTodasLasIncidencias({String? token}) async {
    final url = Uri.parse('${baseUrl}api_empleados/todas_incidencias.php');

    try {
      final response = await http.get(
        url,
        headers: token != null ? {'Authorization': 'Bearer $token'} : {},
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true && responseData['data'] is List) {
          List<Map<String, dynamic>> incidencias = List<Map<String, dynamic>>.from(responseData['data']);
          for (var incidencia in incidencias) {
            if (incidencia.containsKey('foto') && incidencia['foto'] != null) {
              try {
                Uint8List fotoBytes = base64Decode(incidencia['foto']);
                incidencia['foto'] = fotoBytes;
              } catch (e) {
                incidencia['foto'] = null;
              }
            }
          }
          return incidencias;
        } else {
          debugPrint("Error en los datos recibidos (admin): ${responseData['message']}");
          return [];
        }
      } else {
        debugPrint("Error HTTP (admin) ${response.statusCode}");
        return [];
      }
    } catch (e) {
      debugPrint("Error al obtener incidencias (admin): $e");
      return [];
    }
  }

  // Obtener detalles de una incidencia específica (modo administrador)
  static Future<Map<String, dynamic>?> obtenerIncidenciaPorIdAdmin(int incidenciaId, {String? token}) async {
    final url = Uri.parse('${baseUrl}api_empleados/todas_incidencias.php');

    try {
      final response = await http.get(
        url,
        headers: token != null ? {'Authorization': 'Bearer $token'} : {},
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true && responseData['data'] is List) {
          var incidencia = List<Map<String, dynamic>>.from(responseData['data'])
              .firstWhere((element) => element['id'] == incidenciaId, orElse: () => {});

          if (incidencia.containsKey('foto') && incidencia['foto'] != null) {
            try {
              Uint8List fotoBytes = base64Decode(incidencia['foto']);
              incidencia['foto'] = fotoBytes;
            } catch (e) {
              incidencia['foto'] = null;
            }
          }
          return incidencia;
        } else {
          debugPrint("Error en los datos (admin): ${responseData['message']}");
          return null;
        }
      } else {
        debugPrint("Error HTTP al obtener incidencia (admin): ${response.statusCode}");
        return null;
      }
    } catch (e) {
      debugPrint("Excepción al obtener detalle de incidencia (admin): $e");
      return null;
    }
  }
}
