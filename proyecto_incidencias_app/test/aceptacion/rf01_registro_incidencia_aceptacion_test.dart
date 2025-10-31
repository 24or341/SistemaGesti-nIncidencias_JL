import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;

void main() {
  test('RF01 - Validar estabilidad del endpoint registrar_incidencia.php bajo carga concurrente', () async {
    final String endpoint =
        'http://localhost/SistemaGesti-nIncidencias_JL/gestion_incidencias_web/api/public/api_ciudadano/registrar_incidencia.php';

    const int solicitudes = 5;
    final List<Future<http.Response>> peticiones = [];

    for (int i = 0; i < solicitudes; i++) {
      peticiones.add(http.post(
        Uri.parse(endpoint),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: {
          'descripcion': 'Bache en la vía principal',
          'latitud': '-18.015',
          'longitud': '-70.250',
          'direccion': 'Av. Municipal 123',
          'zona': 'Gregorio Albarracín',
          'tipo_id': '1',
          'ciudadano_id': '10',
        },
      ));
    }

    final respuestas = await Future.wait(peticiones);

    for (final res in respuestas) {
      expect(res.statusCode, 200, reason: 'El endpoint debe responder con código 200.');
      expect(res.body.contains('success'), isTrue, reason: 'Debe devolver respuesta con clave success.');
    }

    print('Total de solicitudes: $solicitudes');
    for (int i = 0; i < respuestas.length; i++) {
      print('Solicitud #${i + 1}: ${respuestas[i].statusCode} - ${respuestas[i].reasonPhrase}');
    }
  });
}
