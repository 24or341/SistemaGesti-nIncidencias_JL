import 'package:flutter/material.dart';
import '../services/incidencias_empleado_service.dart';

class DetalleIncidenciaViewModel extends ChangeNotifier {
  Map<String, dynamic>? _incidencia;
  bool _isLoading = true;

  Map<String, dynamic>? get incidencia => _incidencia;
  bool get isLoading => _isLoading;

  Future<void> cargarDetalle(int usuarioId, int incidenciaId, String token, String rol) async {
    _isLoading = true;
    notifyListeners();

    try {
      Map<String, dynamic>? resultado;

      if (rol == 'administrador') {
        resultado = await IncidenciasEmpleadoService.obtenerIncidenciaPorIdAdmin(
          incidenciaId,
          token: token,
        );
      } else {
        resultado = await IncidenciasEmpleadoService.obtenerIncidenciaPorId(
          usuarioId,
          incidenciaId,
          token: token,
        );
      }

      _incidencia = resultado;
    } catch (e) {
      _incidencia = null;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
