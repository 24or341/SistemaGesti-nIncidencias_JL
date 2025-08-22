class TipoIncidencia {
  final int id;
  final String nombre;

  TipoIncidencia({
    required this.id,
    required this.nombre,
  });

  factory TipoIncidencia.fromJson(Map<String, dynamic> json) {
    return TipoIncidencia(
      id: json['id'],
      nombre: json['nombre'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nombre': nombre,
    };
  }
}
