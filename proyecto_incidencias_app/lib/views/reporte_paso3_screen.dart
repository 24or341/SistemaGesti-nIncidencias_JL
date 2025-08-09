import 'dart:io';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/reportar_viewmodel.dart';
import 'ciudadano_home.dart';

/// Pantalla final para subir la foto de evidencia y enviar el reporte de incidencia.
/// Muestra estados según el progreso: subida, éxito o error.
class ReportePaso3Screen extends StatelessWidget {
  const ReportePaso3Screen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<ReportarViewModel>(
      builder: (context, reporteViewModel, _) {
        return Scaffold(
          appBar: AppBar(
            title: const Text('Subir Foto'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
          ),
          body: Padding(
            padding: const EdgeInsets.all(20),
            child: reporteViewModel.reporteExitoso
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.check_circle_outline, size: 100, color: Colors.teal),
                        const SizedBox(height: 20),
                        const Text(
                          '¡Incidencia enviada correctamente!',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 30),
                        ElevatedButton.icon(
                          onPressed: () {
                            // Regresa al home con la pestaña de "Mis Incidencias"
                            Navigator.pushAndRemoveUntil(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const CiudadanoHome(initialIndex: 2),
                              ),
                              (route) => false,
                            );
                          },
                          icon: const Icon(Icons.search),
                          label: const Text('Consulte su Incidencia'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.teal,
                            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                            shape: const RoundedRectangleBorder(
                              borderRadius: BorderRadius.all(Radius.circular(30)),
                            ),
                          ),
                        ),
                      ],
                    ),
                  )
                : Column(
                    children: [
                      // Instrucciones para subir imagen
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          color: Colors.teal.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.teal),
                        ),
                        child: const Column(
                          children: [
                            Icon(Icons.cloud_upload, size: 60, color: Colors.teal),
                            SizedBox(height: 12),
                            Text(
                              'Pulse uno de los botones para subir una imagen como evidencia.',
                              textAlign: TextAlign.center,
                              style: TextStyle(color: Colors.white70),
                            ),
                            SizedBox(height: 16),
                          ],
                        ),
                      ),
                      const SizedBox(height: 10),

                      // Botones para seleccionar imagen desde galería o tomar foto
                      Wrap(
                        spacing: 16,
                        runSpacing: 10,
                        alignment: WrapAlignment.center,
                        children: [
                          ElevatedButton.icon(
                            onPressed: reporteViewModel.seleccionarImagenDesdeGaleria,
                            icon: const Icon(Icons.photo_library),
                            label: const Text('Galería'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.teal,
                              shape: const RoundedRectangleBorder(
                                borderRadius: BorderRadius.all(Radius.circular(30)),
                              ),
                            ),
                          ),
                          ElevatedButton.icon(
                            onPressed: reporteViewModel.tomarFotoDesdeCamara,
                            icon: const Icon(Icons.camera_alt),
                            label: const Text('Cámara'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.teal,
                              shape: const RoundedRectangleBorder(
                                borderRadius: BorderRadius.all(Radius.circular(30)),
                              ),
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 20),

                      // Vista previa de imagen seleccionada con opción a remover
                      if (reporteViewModel.imagenSeleccionada != null)
                        Column(
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(12),
                              child: Image.file(
                                File(reporteViewModel.imagenSeleccionada!.path),
                                height: 200,
                                fit: BoxFit.contain,
                              ),
                            ),
                            const SizedBox(height: 10),
                            TextButton.icon(
                              onPressed: reporteViewModel.removerImagen,
                              icon: const Icon(Icons.delete, color: Colors.redAccent),
                              label: const Text(
                                'Remover Archivo',
                                style: TextStyle(color: Colors.redAccent),
                              ),
                            ),
                          ],
                        ),
                      const Spacer(),

                      // Botón para enviar el reporte con validación de carga
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.send),
                          label: reporteViewModel.isLoading
                              ? const CircularProgressIndicator(color: Colors.white)
                              : const Text('Reportar Incidencia', style: TextStyle(fontSize: 18)),
                          onPressed: reporteViewModel.isLoading
                              ? null
                              : () async {
                                  final result = await reporteViewModel.enviarReporteFinal(context);
                                  if (result['success'] == true) {
                                    reporteViewModel.reporteExitoso = true;
                                  } else {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(content: Text(result['message'] ?? 'Error al reportar')),
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
    );
  }
}
