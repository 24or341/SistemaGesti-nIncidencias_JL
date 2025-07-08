import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import '../config.dart';

class IncidenciaService {
  // Registrar incidencia con imagen (multipart/form-data)
  static Future<Map<String, dynamic>> registrarIncidenciaConFoto({
    required String descripcion,
    required double latitud,
    required double longitud,
    required String direccion,
    required String zona,
    required int tipoId,
    required int ciudadanoId,
    File? foto,
  }) async {
    final url = Uri.parse('${baseUrl}api_ciudadano/registrar_incidencia.php');

    try {
      var request = http.MultipartRequest('POST', url);

      request.fields['descripcion'] = descripcion;
      request.fields['latitud'] = latitud.toString();
      request.fields['longitud'] = longitud.toString();
      request.fields['direccion'] = direccion;
      request.fields['zona'] = zona;
      request.fields['tipo_id'] = tipoId.toString();
      request.fields['ciudadano_id'] = ciudadanoId.toString();

      // Si hay foto, la añadimos como archivo
      if (foto != null) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'foto',
            foto.path,
            contentType: MediaType('image', 'jpeg'),
          ),
        );
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'success': false, 'message': 'Error del servidor'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Excepción: $e'};
    }
  }

  // Obtener lista de tipos de incidencia
  static Future<List<Map<String, dynamic>>> obtenerTiposIncidencia() async {
    final url = Uri.parse('${baseUrl}api_ciudadano/tipos_incidencia.php');

    try {
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((item) => {
              'id': item['id'],
              'nombre': item['nombre'],
            }).toList();
      } else {
        return [];
      }
    } catch (e) {
      return [];
    }
  }

  // Obtener todas las incidencias (para el historial del ciudadano)
  static Future<List<Map<String, dynamic>>> obtenerTodasLasIncidencias(int ciudadanoId) async {
    final url = Uri.parse('${baseUrl}api_ciudadano/listar_incidencias.php?ciudadano_id=$ciudadanoId');

    try {
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true && responseData['data'] is List) {
          return List<Map<String, dynamic>>.from(responseData['data']);
        } else {
          return [];
        }
      } else {
        return [];
      }
    } catch (e) {
      debugPrint("Error al obtener todas las incidencias: $e");
      return [];
    }
  }

  // Validar o registrar ciudadano por número de celular
  static Future<Map<String, dynamic>?> validarTelefono(String celular) async {
    final url = Uri.parse('${baseUrl}api_ciudadano/validar_telefono.php');

    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'celular': celular}),
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['success'] == true) {
          return responseData;
        } else {
          return {'success': false, 'message': responseData['message']};
        }
      } else {
        return {'success': false, 'message': 'Error en la conexión al servidor'};
      }
    } catch (e) {
      debugPrint("Error al validar teléfono: $e");
      return {'success': false, 'message': 'Excepción: $e'};
    }
  }
}
