class Usuario {
  final int id;
  final String nombre;
  final String apellido;
  final String correo;
  final String? token;

  Usuario({
    required this.id,
    required this.nombre,
    required this.apellido,
    required this.correo,
    this.token,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'],
      nombre: json['nombre'] ?? '',
      apellido: json['apellido'] ?? '',
      correo: json['correo'] ?? '',
      token: json['token'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nombre': nombre,
      'apellido': apellido,
      'correo': correo,
      'token': token,
    };
  }
}
