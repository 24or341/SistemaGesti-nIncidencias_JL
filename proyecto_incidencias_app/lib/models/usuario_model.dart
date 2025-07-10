class Usuario {
  final int id;
  final String nombre;
  final String apellido;
  final String correo;
  final String? token;
  final String? dni;
  final String? role;

  Usuario({
    required this.id,
    required this.nombre,
    required this.apellido,
    required this.correo,
    this.token,
    this.dni,
    this.role,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'],
      nombre: json['nombre'] ?? '',
      apellido: json['apellido'] ?? '',
      correo: json['email'] ?? '',
      token: json['token'],
      dni: json['dni'],
      role: json['role'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nombre': nombre,
      'apellido': apellido,
      'email': correo,
      'token': token,
      'dni': dni,
      'role': role,
    };
  }
}
