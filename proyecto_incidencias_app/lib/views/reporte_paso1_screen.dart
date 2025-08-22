import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/reportar_viewmodel.dart';
import 'reporte_paso2_screen.dart';
import '../main.dart';

class ReportePaso1Screen extends StatelessWidget {
  const ReportePaso1Screen({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<ReportarViewModel>(
      create: (_) => ReportarViewModel()..cargarTipos(),
      child: Consumer<ReportarViewModel>(
        builder: (context, viewModel, _) {
          return Scaffold(
            appBar: AppBar(
              title: const Text('Reportar Incidencia'),
              backgroundColor: Colors.teal.shade700,
              foregroundColor: Colors.white,
              actions: [
                IconButton(
                  icon: const Icon(Icons.logout),
                  tooltip: 'Salir al inicio',
                  onPressed: () {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (_) => const WelcomeScreen()),
                      (route) => false,
                    );
                  },
                ),
              ],
            ),

            body: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  _buildTextField(viewModel.descripcionController, 'Descripción', maxLines: 3),
                  const SizedBox(height: 16),

                  // Tipo de Incidencia
                  if (viewModel.tipos.isNotEmpty)
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: DropdownButton<int>(
                        isExpanded: true,
                        value: viewModel.tipoSeleccionado?.id,
                        dropdownColor: Colors.white,
                        iconEnabledColor: Colors.teal,
                        underline: Container(),
                        style: const TextStyle(color: Colors.black, fontSize: 16),
                        items: viewModel.tipos
                            .map((tipo) => DropdownMenuItem<int>(
                                  value: tipo.id,
                                  child: Text(tipo.nombre),
                                ))
                            .toList(),
                        onChanged: (id) => viewModel.seleccionarTipoPorId(id),
                      ),
                    ),
                  const SizedBox(height: 16),

                  // Fecha de Reporte (solo visual)
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Fecha de reporte: ${DateTime.now().toLocal().toString().split('.')[0]}",
                      style: const TextStyle(color: Colors.white70),
                    ),
                  ),
                  const SizedBox(height: 16),

                  _buildTextField(viewModel.celularController, 'Número de Teléfono', keyboardType: TextInputType.phone),
                  const SizedBox(height: 16),

                  _buildTextField(viewModel.direccionController, 'Dirección'),
                  const SizedBox(height: 16),

                  _buildTextField(viewModel.zonaController, 'Zona', initialValue: 'Tacna'),
                  const SizedBox(height: 24),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      icon: const Icon(Icons.arrow_forward),
                      label: const Text('Siguiente'),
                      onPressed: () async {
                        if (viewModel.descripcionController.text.isEmpty ||
                            viewModel.celularController.text.length < 9) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Complete los campos correctamente.')),
                          );
                          return;
                        }

                        final exito = await viewModel.validarOCrearCiudadano();
                        if (!exito) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Error al validar número de teléfono.')),
                          );
                          return;
                        }

                        if (context.mounted) {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => ChangeNotifierProvider.value(
                                value: viewModel,
                                child: const ReportePaso2Screen(),
                              ),
                            ),
                          );
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.teal,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(30),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label,
      {int maxLines = 1, TextInputType keyboardType = TextInputType.text, String? initialValue}) {
    if (initialValue != null && controller.text.isEmpty) {
      controller.text = initialValue;
    }

    return TextField(
      controller: controller,
      maxLines: maxLines,
      keyboardType: keyboardType,
      style: const TextStyle(color: Colors.white),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.white70),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        enabledBorder: OutlineInputBorder(
          borderSide: const BorderSide(color: Colors.white54),
          borderRadius: BorderRadius.circular(12),
        ),
        focusedBorder: OutlineInputBorder(
          borderSide: const BorderSide(color: Colors.tealAccent),
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    );
  }
}
