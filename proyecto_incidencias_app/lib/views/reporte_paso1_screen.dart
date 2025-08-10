import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/reportar_viewmodel.dart';
import 'reporte_paso2_screen.dart';
import '../main.dart';
// Realizado por: Leandro Hurtado Ortiz
/// Pantalla inicial para reportar incidencias.
/// Permite ingresar descripción, tipo, fecha, teléfono, dirección y zona.
/// Incluye validación y navegación al segundo paso del reporte.
class ReportePaso1Screen extends StatelessWidget {
  const ReportePaso1Screen({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<ReportarViewModel>(
      create: (_) => ReportarViewModel()..cargarTipos(),
      child: Consumer<ReportarViewModel>(
        builder: (context, reporteVM, _) {
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
                    // Regresa a la pantalla de bienvenida eliminando historial
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
                  _buildTextField(
                    controller: reporteVM.descripcionController,
                    label: 'Descripción',
                    maxLines: 3,
                  ),
                  const SizedBox(height: 16),

                  // Selector de tipo de incidencia
                  if (reporteVM.tipos.isNotEmpty)
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: DropdownButton<int>(
                        isExpanded: true,
                        value: reporteVM.tipoSeleccionado?.id,
                        dropdownColor: Colors.white,
                        iconEnabledColor: Colors.teal,
                        underline: const SizedBox(),
                        style:
                            const TextStyle(color: Colors.black, fontSize: 16),
                        items: reporteVM.tipos
                            .map(
                              (tipo) => DropdownMenuItem<int>(
                                value: tipo.id,
                                child: Text(tipo.nombre),
                              ),
                            )
                            .toList(),
                        onChanged: reporteVM.seleccionarTipoPorId,
                      ),
                    ),
                  const SizedBox(height: 16),

                  // Muestra fecha actual (solo visual)
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Fecha de reporte: ${DateTime.now().toLocal().toString().split('.')[0]}",
                      style: const TextStyle(color: Colors.white70),
                    ),
                  ),
                  const SizedBox(height: 16),

                  _buildTextField(
                    controller: reporteVM.celularController,
                    label: 'Número de Teléfono',
                    keyboardType: TextInputType.phone,
                  ),
                  const SizedBox(height: 16),

                  _buildTextField(
                    controller: reporteVM.direccionController,
                    label: 'Dirección',
                  ),
                  const SizedBox(height: 16),

                  _buildTextField(
                    controller: reporteVM.zonaController,
                    label: 'Zona',
                    initialValue: 'Tacna',
                  ),
                  const SizedBox(height: 24),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      icon: const Icon(Icons.arrow_forward),
                      label: const Text('Siguiente'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.teal,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(30),
                        ),
                      ),
                      onPressed: () async {
                        // Validar campos obligatorios
                        if (!_camposValidos(reporteVM, context)) return;

                        // Validar o crear ciudadano
                        final exito = await reporteVM.validarOCrearCiudadano();
                        if (!exito) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content:
                                  Text('Error al validar número de teléfono.'),
                            ),
                          );
                          return;
                        }

                        // Navegar al segundo paso
                        if (context.mounted) {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => ChangeNotifierProvider.value(
                                value: reporteVM,
                                child: const ReportePaso2Screen(),
                              ),
                            ),
                          );
                        }
                      },
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

  /// Construye un TextField estilizado con opciones personalizables.
  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    int maxLines = 1,
    TextInputType keyboardType = TextInputType.text,
    String? initialValue,
  }) {
    // Asigna valor inicial solo si el campo está vacío
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

  /// Valida que los campos obligatorios estén completos correctamente.
  bool _camposValidos(ReportarViewModel vm, BuildContext context) {
    if (vm.descripcionController.text.isEmpty ||
        vm.celularController.text.length < 9) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Complete los campos correctamente.')),
      );
      return false;
    }
    return true;
  }
}
