// Realizado por: Leandro Hurtado Ortiz
// Fecha de creación: 2025-06-07
// Requerimiento: RF01 – Reporte de incidencias
// Modelo de datos para representar el tipo de incidencia.
// Contiene identificador y nombre.
// Se utiliza en el flujo de reporte (Paso 1) para mostrar las opciones disponibles
// y enviar el tipo seleccionado al servicio de incidencias.
// Ejecución: instanciado desde `ReportarViewModel` al consumir la API.

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
