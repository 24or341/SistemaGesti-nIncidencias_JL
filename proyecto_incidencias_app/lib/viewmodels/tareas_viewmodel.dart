import 'package:flutter/material.dart';
import '../models/incidencia_model.dart';
import '../services/incidencias_empleado_service.dart';

// Realizado por: Leandro Hurtado Ortiz
// Fecha de creación: 2025-06-07
// Requerimiento: RF05 – Monitoreo y Actualización de Incidencias
/// ViewModel que gestiona la lógica de tareas asignadas y todas las incidencias.
/// Funciones principales:
/// - Cargar incidencias según el rol (empleado → asignadas, administrador → todas).
/// - Exponer estados de carga y mensajes de error para la vista.
/// - Actualizar el estado de una incidencia y refrescar la lista.
/// Ejecución: utilizado por `TareasScreen` para mostrar y gestionar incidencias.

class TareasViewModel extends ChangeNotifier {
  final int usuarioId;
  final String token;
  final String rol;

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
