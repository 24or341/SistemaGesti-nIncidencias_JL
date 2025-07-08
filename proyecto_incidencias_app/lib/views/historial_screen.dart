import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/historial_viewmodel.dart';
import '../models/incidencia_model.dart';

class HistorialScreen extends StatelessWidget {
  final int ciudadanoId;

  const HistorialScreen({super.key, required this.ciudadanoId});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<HistorialViewModel>(
      create: (_) => HistorialViewModel()..cargarHistorial(ciudadanoId),
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
            title: const Text('Historial de Incidencias'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          body: Consumer<HistorialViewModel>(
            builder: (context, viewModel, _) {
              if (viewModel.isLoading) {
                return const Center(
                    child: CircularProgressIndicator(color: Colors.tealAccent));
              } else if (viewModel.errorMessage != null) {
                return Center(
                    child: Text(viewModel.errorMessage!,
                        style: const TextStyle(color: Colors.white70)));
              } else if (viewModel.incidencias.isEmpty) {
                return const Center(
                    child: Text('No hay incidencias registradas.',
                        style: TextStyle(color: Colors.white70)));
              }

              return ListView.builder(
                itemCount: viewModel.incidencias.length,
                itemBuilder: (context, index) {
                  return _buildIncidenciaCard(viewModel.incidencias[index]);
                },
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildIncidenciaCard(Incidencia incidencia) {
    String estado = incidencia.estado;
    IconData iconoEstado;
    Color colorIcono;

    switch (estado) {
      case 'Terminado':
        iconoEstado = Icons.check_circle;
        colorIcono = Colors.greenAccent;
        break;
      case 'Pendiente':
        iconoEstado = Icons.pending;
        colorIcono = Colors.orangeAccent;
        break;
      case 'En Desarrollo':
        iconoEstado = Icons.loop;
        colorIcono = Colors.blueAccent;
        break;
      default:
        iconoEstado = Icons.error_outline;
        colorIcono = Colors.redAccent;
    }

    return Card(
      color: Colors.white.withOpacity(0.1),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 12),
      child: ListTile(
        leading: Icon(iconoEstado, color: colorIcono, size: 32),
        title: Text(
          incidencia.tipo ?? 'Tipo desconocido',
          style: const TextStyle(
            fontWeight: FontWeight.bold,
            color: Colors.white,
            fontSize: 16,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 6),
            Text('Descripción: ${incidencia.descripcion}',
                style: const TextStyle(color: Colors.white70)),
            Text('Estado: $estado',
                style: const TextStyle(color: Colors.white70)),
            Text('Fecha: ${incidencia.fechaReporte}',
                style: const TextStyle(color: Colors.white70)),
            const SizedBox(height: 6),
          ],
        ),
        enabled: false,
      ),
    );
  }
}
