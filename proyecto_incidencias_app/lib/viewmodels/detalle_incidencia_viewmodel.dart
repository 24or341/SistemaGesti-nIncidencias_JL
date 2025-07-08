import 'package:flutter/material.dart';
import '../services/incidencias_empleado_service.dart';

class DetalleIncidenciaViewModel extends ChangeNotifier {
  Map<String, dynamic>? _incidencia;
  bool _isLoading = true;

  Map<String, dynamic>? get incidencia => _incidencia;
  bool get isLoading => _isLoading;

  Future<void> cargarDetalle(int empleadoId, int incidenciaId, String token) async {
    _isLoading = true;
    notifyListeners();

    try {
      final resultado = await IncidenciasEmpleadoService.obtenerIncidenciaPorId(
        empleadoId,
        incidenciaId,
        token: token,
      );
      _incidencia = resultado;
    } catch (e) {
      _incidencia = null;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
