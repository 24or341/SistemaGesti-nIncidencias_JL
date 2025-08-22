import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:image_picker/image_picker.dart';
import 'package:geocoding/geocoding.dart';

import '../models/tipo_incidencia_model.dart';
import '../services/incidencia_service.dart';

class ReportarViewModel extends ChangeNotifier {
  final descripcionController = TextEditingController();
  final direccionController = TextEditingController();
  final zonaController = TextEditingController();
  final celularController = TextEditingController();

  LatLng selectedLocation = const LatLng(-18.03727, -70.25357);
  XFile? imagenSeleccionada;
  bool isLoading = false;
  bool reporteExitoso = false; // Nueva bandera para mostrar pantalla de éxito

  List<TipoIncidencia> tipos = [];
  TipoIncidencia? tipoSeleccionado;

  int? ciudadanoId;

  /// Paso 1: Cargar tipos de incidencia
  Future<void> cargarTipos() async {
    final data = await IncidenciaService.obtenerTiposIncidencia();
    tipos = data.map((e) => TipoIncidencia.fromJson(e)).toList();
    if (tipos.isNotEmpty) tipoSeleccionado = tipos.first;
    notifyListeners();
  }

  /// Paso 1: Seleccionar tipo de incidencia
  void seleccionarTipoPorId(int? id) {
    if (id == null) return;
    tipoSeleccionado = tipos.firstWhere((t) => t.id == id);
    notifyListeners();
  }

  /// Paso 1 extra: Validar o crear ciudadano por celular
  Future<bool> validarOCrearCiudadano() async {
    final celular = celularController.text.trim();
    if (celular.length < 9) return false;

    final response = await IncidenciaService.validarTelefono(celular);

    if (response != null && response['success'] == true && response['data']?['id'] != null) {
      ciudadanoId = response['data']['id'];
      return true;
    }

    return false;
  }

  /// Paso 2: Actualizar ubicación con geocoding inverso
  Future<void> actualizarUbicacion(LatLng nuevaUbicacion) async {
    selectedLocation = nuevaUbicacion;
    try {
      final placemarks = await placemarkFromCoordinates(
        nuevaUbicacion.latitude,
        nuevaUbicacion.longitude,
      );
      if (placemarks.isNotEmpty) {
        final place = placemarks[0];
        direccionController.text =
            '${place.name}, ${place.street}, ${place.locality}, ${place.country}';
        zonaController.text = place.subAdministrativeArea ?? "Zona no disponible";
      }
    } catch (_) {
      // Geocoding fallido, mantener valores actuales.
    }
    notifyListeners();
  }

  /// Paso 3: Seleccionar imagen desde galería
  Future<void> seleccionarImagenDesdeGaleria() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.gallery);
    if (picked != null) {
      imagenSeleccionada = picked;
      notifyListeners();
    }
  }

  /// Paso 3: Tomar foto desde cámara
  Future<void> tomarFotoDesdeCamara() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.camera);
    if (picked != null) {
      imagenSeleccionada = picked;
      notifyListeners();
    }
  }

  /// Paso 3: Remover imagen
  void removerImagen() {
    imagenSeleccionada = null;
    notifyListeners();
  }

  /// Mostrar pantalla de éxito
  void marcarReporteExitoso() {
    reporteExitoso = true;
    notifyListeners();
  }

  /// Enviar reporte final completo
  Future<Map<String, dynamic>> enviarReporteFinal(BuildContext context) async {
    isLoading = true;
    notifyListeners();

    try {
      if (ciudadanoId == null) {
        final exito = await validarOCrearCiudadano();
        if (!exito) {
          isLoading = false;
          notifyListeners();
          return {
            'success': false,
            'message': 'Número de celular inválido o no se pudo registrar'
          };
        }
      }

      final response = await IncidenciaService.registrarIncidenciaConFoto(
        descripcion: descripcionController.text.trim(),
        latitud: selectedLocation.latitude,
        longitud: selectedLocation.longitude,
        direccion: direccionController.text.trim(),
        zona: zonaController.text.trim(),
        tipoId: tipoSeleccionado?.id ?? 0,
        foto: imagenSeleccionada != null ? File(imagenSeleccionada!.path) : null,
        ciudadanoId: ciudadanoId!,
      );

      isLoading = false;
      notifyListeners();
      return response;
    } catch (e) {
      isLoading = false;
      notifyListeners();
      return {'success': false, 'message': 'Error al enviar reporte: $e'};
    }
  }

  @override
  void dispose() {
    descripcionController.dispose();
    direccionController.dispose();
    zonaController.dispose();
    celularController.dispose();
    super.dispose();
  }
}
