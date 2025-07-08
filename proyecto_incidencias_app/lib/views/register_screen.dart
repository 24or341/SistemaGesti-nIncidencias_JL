import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/register_viewmodel.dart';
import 'login_screen.dart';

class RegisterScreen extends StatelessWidget {
  RegisterScreen({super.key});

  final _formKey = GlobalKey<FormState>();

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<RegisterViewModel>(
      create: (_) => RegisterViewModel(),
      child: Scaffold(
        body: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF0f2027), Color(0xFF203a43), Color(0xFF2c5364)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: Consumer<RegisterViewModel>(
                  builder: (context, viewModel, child) {
                    return Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.person_add_alt_1, size: 80, color: Colors.cyanAccent),
                        const SizedBox(height: 20),
                        const Text(
                          'Registro de Empleado',
                          style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Colors.white),
                        ),
                        const SizedBox(height: 20),
                        Form(
                          key: _formKey,
                          child: Column(
                            children: [
                              if (viewModel.errorMessage != null)
                                Padding(
                                  padding: const EdgeInsets.only(bottom: 10),
                                  child: Text(
                                    viewModel.errorMessage!,
                                    style: const TextStyle(color: Colors.redAccent, fontSize: 14),
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                              if (viewModel.successMessage != null)
                                Padding(
                                  padding: const EdgeInsets.only(bottom: 10),
                                  child: Text(
                                    viewModel.successMessage!,
                                    style: const TextStyle(color: Colors.greenAccent, fontSize: 14),
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                              _buildInput(
                                label: 'DNI',
                                icon: Icons.badge,
                                validator: (v) => v == null || v.isEmpty ? 'Ingrese su DNI' : null,
                                onChanged: viewModel.setDni,
                              ),
                              _buildInput(
                                label: 'Nombre',
                                icon: Icons.person,
                                validator: (v) => v == null || v.isEmpty ? 'Ingrese su nombre' : null,
                                onChanged: viewModel.setNombre,
                              ),
                              _buildInput(
                                label: 'Apellido',
                                icon: Icons.person_outline,
                                validator: (v) => v == null || v.isEmpty ? 'Ingrese su apellido' : null,
                                onChanged: viewModel.setApellido,
                              ),
                              _buildInput(
                                label: 'Correo',
                                icon: Icons.email,
                                keyboardType: TextInputType.emailAddress,
                                validator: (v) {
                                  if (v == null || v.isEmpty) return 'Ingrese su correo';
                                  if (!RegExp(r'^[^@]+@[^@]+\.[^@]+').hasMatch(v)) return 'Correo inválido';
                                  return null;
                                },
                                onChanged: viewModel.setCorreo,
                              ),
                              _buildInput(
                                label: 'Contraseña',
                                icon: Icons.lock,
                                obscureText: true,
                                validator: (v) {
                                  if (v == null || v.isEmpty) return 'Ingrese su contraseña';
                                  if (v.length < 8) return 'Mínimo 8 caracteres';
                                  return null;
                                },
                                onChanged: viewModel.setPassword,
                              ),
                              const SizedBox(height: 24),
                              SizedBox(
                                width: double.infinity,
                                child: viewModel.isLoading
                                    ? const Center(child: CircularProgressIndicator(color: Colors.white))
                                    : ElevatedButton(
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: Colors.cyanAccent.shade700,
                                          foregroundColor: Colors.black,
                                          padding: const EdgeInsets.symmetric(vertical: 16),
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                        ),
                                        onPressed: () async {
                                          if (_formKey.currentState!.validate()) {
                                            await viewModel.registrar();
                                          }
                                        },
                                        child: const Text('Registrarse', style: TextStyle(fontSize: 18)),
                                      ),
                              ),
                              const SizedBox(height: 16),
                              TextButton(
                                onPressed: () {
                                  Navigator.pushReplacement(
                                    context,
                                    MaterialPageRoute(builder: (_) => LoginScreen()),
                                  );
                                },
                                child: const Text(
                                  '¿Ya tienes cuenta? Inicia sesión',
                                  style: TextStyle(color: Colors.white70),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInput({
    required String label,
    required IconData icon,
    TextInputType keyboardType = TextInputType.text,
    bool obscureText = false,
    required String? Function(String?) validator,
    required Function(String) onChanged,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: TextFormField(
        style: const TextStyle(color: Colors.white),
        decoration: InputDecoration(
          filled: true,
          fillColor: Colors.white10,
          labelText: label,
          labelStyle: const TextStyle(color: Colors.white70),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
          prefixIcon: Icon(icon, color: Colors.white70),
        ),
        keyboardType: keyboardType,
        obscureText: obscureText,
        validator: validator,
        onChanged: onChanged,
      ),
    );
  }
}
