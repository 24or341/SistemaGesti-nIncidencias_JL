import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test('RF05 - Validar estabilidad de actualizar_estado.php (ID=41)', () async {
    final String endpoint =
        'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/api_empleados/actualizar_estado.php';

    const String token =
        'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjE4Nzk3OTUsImV4cCI6MTc2MTk2NjE5NSwidXNlcl9pZCI6MTAzLCJyb2xlIjoiZW1wbGVhZG8iLCJlbWFpbCI6InVzZXJ0ZXN0MjAyNUBnbWFpbC5jb20iLCJub21icmUiOiJ1c2VydGVzdDIwMjUifQ.VfscuUT1ZUUbqN0cmlQKGFl41TARdGT4BF6ambLcePE';

    final data = {
      'incidencia_id': 41,
      'nuevo_estado': 2, // ID numérico del estado
    };

    const int solicitudes = 1;
    final peticiones = List.generate(
      solicitudes,
      (_) => http.post(
        Uri.parse(endpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(data),
      ),
    );

    final respuestas = await Future.wait(peticiones);

    for (final res in respuestas) {
      expect(res.statusCode, 200, reason: 'Debe responder HTTP 200.');
      final json = jsonDecode(res.body);
      expect(json['success'], isTrue, reason: 'Debe devolver success=true.');
    }

    print('Total de solicitudes: $solicitudes');
    for (var i = 0; i < respuestas.length; i++) {
      print('Solicitud #${i + 1}: ${respuestas[i].statusCode} - ${respuestas[i].reasonPhrase}');
    }
  });
}
