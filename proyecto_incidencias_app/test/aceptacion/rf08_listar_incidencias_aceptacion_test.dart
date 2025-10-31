import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test('RF08 - Validar eficiencia del endpoint listar_incidencias.php', () async {
    final String endpoint =
        'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/api_ciudadano/listar_incidencias.php?ciudadano_id=10';

    final stopwatch = Stopwatch()..start();

    final response = await http.get(
      Uri.parse(endpoint),
      headers: {'Accept': 'application/json'},
    );

    stopwatch.stop();
    final elapsed = stopwatch.elapsedMilliseconds;

    expect(response.statusCode, 200,
        reason: 'El endpoint debe responder con HTTP 200.');

    final json = jsonDecode(response.body);
    expect(json['success'], isTrue, reason: 'Debe devolver success=true.');

    expect(
      elapsed < 100,
      isTrue,
      reason:
          'El tiempo de respuesta debe ser menor a 100 ms (actual: $elapsed ms).',
    );

    print('✅ Tiempo de respuesta: ${elapsed} ms');
    print('Total de incidencias encontradas: ${json['data']?.length ?? 0}');
  });
}
