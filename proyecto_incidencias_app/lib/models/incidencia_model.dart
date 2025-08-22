import 'dart:typed_data';

class Incidencia {
  final int id;
  final String descripcion;
  final String estado;
  final String fechaReporte;
  final String? direccion;
  final String? zona;
  final double? latitud;
  final double? longitud;
  final Uint8List? foto;
  final String? tipo;

  Incidencia({
    required this.id,
    required this.descripcion,
    required this.estado,
    required this.fechaReporte,
    this.direccion,
    this.zona,
    this.latitud,
    this.longitud,
    this.foto,
    this.tipo,
  });

  factory Incidencia.fromJson(Map<String, dynamic> json) {
    Uint8List? fotoBytes;
    if (json['foto'] != null && json['foto'] is String) {
      try {
        fotoBytes = Uint8List.fromList(
          List<int>.from(json['foto'].codeUnits),
        );
      } catch (_) {
        fotoBytes = null;
      }
    }

    return Incidencia(
      id: json['id'],
      descripcion: json['descripcion'] ?? '',
      estado: json['estado'] ?? '',
      fechaReporte: json['fecha_reporte'] ?? '',
      direccion: json['direccion'],
      zona: json['zona'],
      latitud: json['latitud'] != null ? double.tryParse(json['latitud'].toString()) : null,
      longitud: json['longitud'] != null ? double.tryParse(json['longitud'].toString()) : null,
      foto: fotoBytes,
      tipo: json['tipo'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'descripcion': descripcion,
      'estado': estado,
      'fecha_reporte': fechaReporte,
      'direccion': direccion,
      'zona': zona,
      'latitud': latitud,
      'longitud': longitud,
      'foto': foto,
      'tipo': tipo,
    };
  }
}
