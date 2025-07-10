import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/usuario_model.dart';
import '../viewmodels/perfil_viewmodel.dart';

class PerfilScreen extends StatelessWidget {
  final Usuario usuario;

  const PerfilScreen({super.key, required this.usuario});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => PerfilViewModel(usuario),
      child: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0f2027), Color(0xFF203a43), Color(0xFF2c5364)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Scaffold(
          backgroundColor: Colors.transparent,
          appBar: AppBar(
            title: const Text('Perfil'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          body: SafeArea(
            child: Padding(
              padding: const EdgeInsets.all(24),
              child: Consumer<PerfilViewModel>(
                builder: (context, viewModel, _) {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Perfil del Usuario',
                        style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: Colors.white),
                      ),
                      const SizedBox(height: 24),

                      _buildInputField(
                        label: 'Nombre',
                        initialValue: viewModel.usuario.nombre,
                        enabled: viewModel.isEditing,
                        onChanged: viewModel.setNombre,
                      ),
                      _buildInputField(
                        label: 'Apellido',
                        initialValue: viewModel.usuario.apellido,
                        enabled: viewModel.isEditing,
                        onChanged: viewModel.setApellido,
                      ),
                      _buildInputField(
                        label: 'Correo',
                        initialValue: viewModel.usuario.correo,
                        enabled: viewModel.isEditing,
                        onChanged: viewModel.setCorreo,
                      ),
                      _buildInputField(
                        label: 'DNI',
                        initialValue: viewModel.usuario.dni ?? '',
                        enabled: viewModel.isEditing,
                        onChanged: viewModel.setDni,
                      ),

                      const SizedBox(height: 20),

                      if (viewModel.errorMessage != null)
                        Text(viewModel.errorMessage!,
                            style: const TextStyle(color: Colors.redAccent)),
                      if (viewModel.successMessage != null)
                        Text(viewModel.successMessage!,
                            style: const TextStyle(color: Colors.greenAccent)),

                      const Spacer(),

                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.tealAccent.shade400,
                              foregroundColor: Colors.black,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 12),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12)),
                            ),
                            icon: Icon(viewModel.isEditing
                                ? Icons.cancel
                                : Icons.edit),
                            label: Text(viewModel.isEditing
                                ? 'Cancelar'
                                : 'Editar'),
                            onPressed: viewModel.toggleEditing,
                          ),
                          ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.cyanAccent.shade700,
                              foregroundColor: Colors.black,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 12),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12)),
                            ),
                            icon: const Icon(Icons.save),
                            label: const Text('Guardar'),
                            onPressed: viewModel.isEditing &&
                                    !viewModel.isLoading
                                ? () => viewModel.guardarCambios()
                                : null,
                          ),
                        ],
                      ),
                    ],
                  );
                },
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInputField({
    required String label,
    required String initialValue,
    required bool enabled,
    required Function(String) onChanged,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: TextFormField(
        initialValue: initialValue,
        enabled: enabled,
        style: const TextStyle(color: Colors.white),
        decoration: InputDecoration(
          labelText: label,
          labelStyle: const TextStyle(color: Colors.white70),
          filled: true,
          fillColor: Colors.white10,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        ),
        onChanged: onChanged,
      ),
    );
  }
}
