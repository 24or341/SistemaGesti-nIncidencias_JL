import 'package:flutter_test/flutter_test.dart';
import 'package:proyecto_incidencias_app/viewmodels/login_viewmodel.dart';

void main() {
  test('RF03 - Validar campos del formulario de autenticación', () {
    final vm = LoginViewModel();

    // Simulación de datos ingresados por el usuario
    vm.setEmail('empleado@test.com');
    vm.setPassword('123456');

    // Reglas de validación
    final correoValido = RegExp(r'^[\w\.-]+@[\w\.-]+\.\w+$').hasMatch(vm.email);
    final contrasenaValida = vm.password.length >= 6;

    // Resultado final de la validación
    final formularioValido = correoValido && contrasenaValida;

    expect(formularioValido, isTrue,
        reason:
            'El correo debe tener formato válido y la contraseña al menos 6 caracteres.');
  });
}
