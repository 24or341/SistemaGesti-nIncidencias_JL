import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:provider/provider.dart';
import '../viewmodels/reportar_viewmodel.dart';

class ReportarScreen extends StatelessWidget {
  final int ciudadanoId;

  const ReportarScreen({super.key, required this.ciudadanoId});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<ReportarViewModel>(
      create: (_) => ReportarViewModel()..cargarTipos(),
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
            title: const Text('Reportar Incidencia'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          body: Consumer<ReportarViewModel>(
            builder: (context, viewModel, _) {
              return SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  children: [
                    _buildTextField(viewModel.descripcionController, 'Descripción', maxLines: 3),
                    const SizedBox(height: 16),
                    _buildTextField(viewModel.direccionController, 'Dirección'),
                    const SizedBox(height: 16),
                    _buildTextField(viewModel.zonaController, 'Zona'),
                    const SizedBox(height: 16),

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
                                    child: Text(
                                      tipo.nombre,
                                      style: const TextStyle(color: Colors.black),
                                    ),
                                  ))
                              .toList(),
                          onChanged: (id) => viewModel.seleccionarTipoPorId(id),
                        ),
                      ),
                    const SizedBox(height: 16),

                    ElevatedButton.icon(
                      onPressed: viewModel.seleccionarImagen,
                      icon: const Icon(Icons.image),
                      label: const Text("Seleccionar Imagen"),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.teal.shade600,
                      ),
                    ),
                    const SizedBox(height: 10),

                    if (viewModel.imagenSeleccionada != null)
                      ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: Image.file(
                          viewModel.imagenSeleccionada!,
                          height: 200,
                          fit: BoxFit.contain,
                        ),
                      ),

                    const SizedBox(height: 16),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: SizedBox(
                        height: 250,
                        child: GoogleMap(
                          initialCameraPosition: CameraPosition(
                            target: viewModel.selectedLocation,
                            zoom: 15,
                          ),
                          onTap: viewModel.actualizarUbicacion,
                          markers: {
                            Marker(
                              markerId: const MarkerId('selectedLocation'),
                              position: viewModel.selectedLocation,
                              infoWindow: const InfoWindow(title: 'Ubicación seleccionada'),
                            ),
                          },
                          zoomControlsEnabled: true,
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.send),
                        label: viewModel.isLoading
                            ? const CircularProgressIndicator(color: Colors.white)
                            : const Text('Enviar', style: TextStyle(fontSize: 18)),
                        onPressed: viewModel.isLoading
                            ? null
                            : () async {
                                final response = await viewModel.enviarReporte(ciudadanoId);
                                if (!context.mounted) return;

                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text(response['success'] == true
                                        ? 'Incidencia enviada'
                                        : 'Error: ${response['message']}'),
                                  ),
                                );
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
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, {int maxLines = 1}) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
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
