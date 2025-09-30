import 'package:flutter/material.dart';
import '../services/incidencias_empleado_service.dart';

// Realizado por: Leandro Hurtado Ortiz
// Fecha de creación: 2025-06-07
// Requerimiento: RF05 – Monitoreo y Actualización de Incidencias
/// ViewModel para la gestión del detalle de una incidencia.
/// Funciones principales:
/// - Obtener el detalle de una incidencia según el rol:
///   • Administrador: puede consultar cualquier incidencia.
///   • Empleado: solo puede consultar incidencias asignadas.
/// - Exponer el estado de carga (`isLoading`) y los datos de la incidencia.
/// Ejecución: utilizado por `DetalleIncidenciaScreen` al abrir una incidencia.

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
