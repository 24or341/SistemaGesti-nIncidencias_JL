// Realizado por: Leandro Hurtado Ortiz
// Fecha de creación: 2025-05-10
// Requerimiento: RF03 - Autenticación de Empleados
/// Modelo de datos (Entidad) que representa a un usuario autenticado en el sistema.
/// Esta clase es fundamental para:
/// 1. Deserializar el objeto JSON devuelto por la API de login exitosa.
/// 2. Almacenar los datos del perfil del usuario (Empleado/Administrador), incluido el JWT.
/// Ejecución: Utilizado por LoginViewModel para mapear la respuesta del AuthService.
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
