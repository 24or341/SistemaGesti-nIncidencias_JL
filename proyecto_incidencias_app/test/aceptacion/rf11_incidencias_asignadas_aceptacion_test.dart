import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test(
    'RF11 - Validar endpoint incidencias_asignadas.php para empleado autenticado',
    () async {
      const String endpoint =
          'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/api_empleados/incidencias_asignadas.php?usuario_id=103';

      const String token =
          'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjI2OTg2NDQsImV4cCI6MTc2Mjc4NTA0NCwidXNlcl9pZCI6MTAzLCJyb2xlIjoiZW1wbGVhZG8iLCJlbWFpbCI6InVzZXJ0ZXN0MjAyNUBnbWFpbC5jb20iLCJub21icmUiOiJ1c2VydGVzdDIwMjUifQ.jzhhZCAYnQVpT-OqOvpNfTQ5kLC9xGS1mviyDJjkamk';

      final stopwatch = Stopwatch()..start();

      final response = await http.get(
        Uri.parse(endpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      stopwatch.stop();
      final elapsed = stopwatch.elapsedMilliseconds;

      // 1) Código HTTP correcto
      expect(
        response.statusCode,
        200,
        reason:
            'El endpoint debe responder con código HTTP 200 para un empleado autenticado.',
      );

      final json = jsonDecode(response.body);

      // 2) Estructura y bandera de éxito
      expect(
        json['success'],
        isTrue,
        reason: 'La respuesta debe contener success=true.',
      );

      expect(
        json['data'],
        isA<List>(),
        reason: 'El endpoint debe devolver una lista de incidencias asignadas.',
      );

      // 3) Tiempo de respuesta razonable
      expect(
        elapsed < 200,
        isTrue,
        reason:
            'El tiempo de respuesta debe ser adecuado (< 200 ms). Actual: $elapsed ms.',
      );

      print(
          '✅ RF11 - incidencias_asignadas.php respondió en ${elapsed} ms con ${json['data']?.length ?? 0} incidencias.');
    },
  );
}
