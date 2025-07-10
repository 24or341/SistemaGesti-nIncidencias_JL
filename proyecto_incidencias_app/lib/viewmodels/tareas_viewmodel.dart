import 'package:flutter/material.dart';
import '../models/incidencia_model.dart';
import '../services/incidencias_empleado_service.dart';

class TareasViewModel extends ChangeNotifier {
  final int usuarioId;
  final String token;
  final String rol; // nuevo campo

  TareasViewModel(this.usuarioId, this.token, this.rol);

  List<Incidencia> _incidencias = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Incidencia> get incidencias => _incidencias;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> cargarIncidencias() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      List<Map<String, dynamic>> data = [];

      if (rol == 'administrador') {
        data = await IncidenciasEmpleadoService.obtenerTodasLasIncidencias(token: token);
      } else {
        data = await IncidenciasEmpleadoService.obtenerIncidenciasAsignadas(
          usuarioId,
          token: token,
        );
      }

      _incidencias = data.map((e) => Incidencia.fromJson(e)).toList();
    } catch (e) {
      _errorMessage = 'Error al cargar incidencias.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> actualizarEstado({
    required int incidenciaId,
    required int nuevoEstadoId,
    required String token,
    VoidCallback? onSuccess,
    VoidCallback? onError,
  }) async {
    final exito = await IncidenciasEmpleadoService.actualizarEstado(
      incidenciaId,
      nuevoEstadoId,
      token: token,
    );

    if (exito) {
      onSuccess?.call();
      await cargarIncidencias();
    } else {
      onError?.call();
    }
  }
}
