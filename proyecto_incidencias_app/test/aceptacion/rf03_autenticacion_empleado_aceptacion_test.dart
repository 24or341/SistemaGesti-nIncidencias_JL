import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test('RF03 - Validar estabilidad del endpoint login.php bajo carga concurrente', () async {
    const String endpoint =
        'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/login.php';

    // Credenciales válidas del empleado real
    final Map<String, dynamic> credenciales = {
      'email': 'usertest2025@gmail.com',
      'password': '12345678',
    };

    const int solicitudes = 5;
    final List<Future<Map<String, dynamic>>> peticiones = [];

    for (int i = 0; i < solicitudes; i++) {
      final peticion = (() async {
        final stopwatch = Stopwatch()..start();
        final res = await http.post(
          Uri.parse(endpoint),
          headers: {'Content-Type': 'application/json'},
          body: jsonEncode(credenciales),
        );
        stopwatch.stop();

        return {
          'statusCode': res.statusCode,
          'body': res.body,
          'tiempo': stopwatch.elapsedMilliseconds,
        };
      })();

      peticiones.add(peticion);
    }

    final respuestas = await Future.wait(peticiones);

    for (int i = 0; i < respuestas.length; i++) {
      final r = respuestas[i];
      final status = r['statusCode'];
      final body = jsonDecode(r['body']);
      final tiempo = r['tiempo'];

      expect(status, 200, reason: 'El endpoint debe responder HTTP 200.');
      expect(body['success'], isTrue,
          reason: 'La autenticación debe ser exitosa con las credenciales válidas.');
      expect(body['data']?['token'], isNotNull,
          reason: 'La respuesta debe contener un token JWT.');
      expect(tiempo < 100, isTrue,
          reason: 'El tiempo de respuesta debe ser menor a 100 ms (actual: $tiempo ms).');

      print(
          'Solicitud #${i + 1}: ${status} | Tiempo: ${tiempo} ms | Usuario: ${body['data']?['email'] ?? 'N/A'}');
    }

    print('✅ Total de solicitudes concurrentes: $solicitudes');
  });
}
