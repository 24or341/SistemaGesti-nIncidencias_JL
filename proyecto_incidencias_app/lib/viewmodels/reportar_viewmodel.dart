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

  LatLng selectedLocation = const LatLng(-18.03727, -70.25357);
  File? imagenSeleccionada;
  bool isLoading = false;

  List<TipoIncidencia> tipos = [];
  TipoIncidencia? tipoSeleccionado;

  // Cargar tipos de incidencia
  Future<void> cargarTipos() async {
    final data = await IncidenciaService.obtenerTiposIncidencia();
    tipos = data.map((e) => TipoIncidencia.fromJson(e)).toList();
    if (tipos.isNotEmpty) tipoSeleccionado = tipos.first;
    notifyListeners();
  }

  // Seleccionar tipo por ID y notificar cambios
  void seleccionarTipoPorId(int? id) {
    if (id == null) return;
    tipoSeleccionado = tipos.firstWhere((t) => t.id == id);
    notifyListeners();
  }

  // Seleccionar imagen
  Future<void> seleccionarImagen() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      imagenSeleccionada = File(pickedFile.path);
      notifyListeners();
    }
  }

  // Cambiar ubicación seleccionada
  Future<void> actualizarUbicacion(LatLng nuevaUbicacion) async {
    selectedLocation = nuevaUbicacion;
    try {
      List<Placemark> placemarks = await placemarkFromCoordinates(
        nuevaUbicacion.latitude,
        nuevaUbicacion.longitude,
      );
      if (placemarks.isNotEmpty) {
        final place = placemarks[0];
        direccionController.text =
            '${place.name}, ${place.street}, ${place.locality}, ${place.country}';
        zonaController.text = place.subAdministrativeArea ?? "Zona no disponible";
      }
    } catch (e) {
      // .
    }
    notifyListeners();
  }

  // Enviar reporte
  Future<Map<String, dynamic>> enviarReporte(int ciudadanoId) async {
    isLoading = true;
    notifyListeners();

    final response = await IncidenciaService.registrarIncidenciaConFoto(
      descripcion: descripcionController.text,
      latitud: selectedLocation.latitude,
      longitud: selectedLocation.longitude,
      direccion: direccionController.text,
      zona: zonaController.text,
      tipoId: tipoSeleccionado!.id,
      foto: imagenSeleccionada,
      ciudadanoId: ciudadanoId,
    );

    isLoading = false;
    notifyListeners();

    return response;
  }

  @override
  void dispose() {
    descripcionController.dispose();
    direccionController.dispose();
    zonaController.dispose();
    super.dispose();
  }
}
