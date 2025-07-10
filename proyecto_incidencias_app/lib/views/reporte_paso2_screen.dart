import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:provider/provider.dart';
import '../viewmodels/reportar_viewmodel.dart';
import 'reporte_paso3_screen.dart';

class ReportePaso2Screen extends StatelessWidget {
  const ReportePaso2Screen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<ReportarViewModel>(
      builder: (context, viewModel, _) {
        return Scaffold(
          appBar: AppBar(
            title: const Text('Seleccione la Ubicación'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
          ),
          body: Column(
            children: [
              Expanded(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: GoogleMap(
                    initialCameraPosition: CameraPosition(
                      target: viewModel.selectedLocation,
                      zoom: 15,
                    ),
                    onTap: (LatLng location) {
                      viewModel.selectedLocation = location;
                      viewModel.notifyListeners();
                    },
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
              const SizedBox(height: 16),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    icon: const Icon(Icons.arrow_forward),
                    label: const Text('Siguiente'),
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => ChangeNotifierProvider.value(
                            value: viewModel,
                            child: const ReportePaso3Screen(),
                          ),
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
              ),
              const SizedBox(height: 16),
            ],
          ),
        );
      },
    );
  }
}
