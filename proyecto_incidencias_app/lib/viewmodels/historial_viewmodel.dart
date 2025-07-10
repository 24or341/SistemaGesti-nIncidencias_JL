import 'package:flutter/material.dart';
import '../models/incidencia_model.dart';
import '../services/incidencia_service.dart';

class HistorialViewModel extends ChangeNotifier {
  List<Incidencia> _incidencias = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Incidencia> get incidencias => _incidencias;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  // Cargar incidencias filtradas por ciudadano
  Future<void> cargarHistorial(int ciudadanoId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final data = await IncidenciaService.obtenerTodasLasIncidencias(ciudadanoId);
      _incidencias = data.map((json) => Incidencia.fromJson(json)).toList();
    } catch (e) {
      _errorMessage = 'Error al cargar incidencias.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Cargar todas las incidencias (sin filtro)
  Future<void> cargarHistorialSinFiltro() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final data = await IncidenciaService.obtenerTodasLasIncidencias(); // sin parámetro
      _incidencias = data.map((json) => Incidencia.fromJson(json)).toList();
    } catch (e) {
      _errorMessage = 'Error al cargar todas las incidencias.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
