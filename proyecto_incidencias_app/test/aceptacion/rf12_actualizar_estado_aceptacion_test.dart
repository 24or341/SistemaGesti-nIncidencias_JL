import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test('RF12 - Validar actualización de estado vía actualizar_estado.php', () async {
    const String endpoint =
        'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/api_empleados/actualizar_estado.php';

    // Token de empleado válido
    const String token =
        'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjI2OTg2NDQsImV4cCI6MTc2Mjc4NTA0NCwidXNlcl9pZCI6MTAzLCJyb2xlIjoiZW1wbGVhZG8iLCJlbWFpbCI6InVzZXJ0ZXN0MjAyNUBnbWFpbC5jb20iLCJub21icmUiOiJ1c2VydGVzdDIwMjUifQ.jzhhZCAYnQVpT-OqOvpNfTQ5kLC9xGS1mviyDJjkamk';

    // Incidencia de prueba
    final Map<String, dynamic> data = {
      'incidencia_id': 41,
      'nuevo_estado': 3, // id 3 = "Terminado"
    };

    final response = await http.post(
      Uri.parse(endpoint),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: jsonEncode(data),
    );

    expect(
      response.statusCode,
      200,
      reason: 'El endpoint debe responder con HTTP 200 al actualizar el estado.',
    );

    final json = jsonDecode(response.body);

    expect(
      json['success'],
      isTrue,
      reason: 'La respuesta debe indicar success=true tras actualizar el estado.',
    );

    print('✅ RF12 - actualizar_estado.php respondió 200 y success=true para la incidencia 41.');
  });
}
