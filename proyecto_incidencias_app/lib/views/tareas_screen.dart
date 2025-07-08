import 'package:flutter/material.dart';
import '../models/incidencia_model.dart';
import '../viewmodels/tareas_viewmodel.dart';
import 'detalle_incidencia_screen.dart';

class TareasScreen extends StatefulWidget {
  final Map<String, dynamic> user;

  const TareasScreen({super.key, required this.user});

  @override
  State<TareasScreen> createState() => _TareasScreenState();
}

class _TareasScreenState extends State<TareasScreen> {
  late TareasViewModel _viewModel;

  @override
  void initState() {
    super.initState();
    _viewModel = TareasViewModel(widget.user['id'], widget.user['token']);
    _viewModel.cargarIncidencias();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
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
          title: const Text('Tareas Asignadas'),
          backgroundColor: Colors.teal.shade700,
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        body: SafeArea(
          child: AnimatedBuilder(
            animation: _viewModel,
            builder: (context, _) {
              if (_viewModel.isLoading) {
                return const Center(child: CircularProgressIndicator(color: Colors.tealAccent));
              }

              if (_viewModel.errorMessage != null) {
                return Center(
                  child: Text(
                    _viewModel.errorMessage!,
                    style: const TextStyle(color: Colors.white70),
                  ),
                );
              }

              if (_viewModel.incidencias.isEmpty) {
                return const Center(
                  child: Text(
                    'No tienes incidencias asignadas.',
                    style: TextStyle(color: Colors.white70),
                  ),
                );
              }

              return ListView.builder(
                itemCount: _viewModel.incidencias.length,
                padding: const EdgeInsets.all(12),
                itemBuilder: (context, index) {
                  final incidencia = _viewModel.incidencias[index];
                  return _buildTareaCard(incidencia);
                },
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildTareaCard(Incidencia incidencia) {
    final estadoNombre = incidencia.estado;
    final descripcion = incidencia.descripcion.isNotEmpty
        ? incidencia.descripcion
        : 'Sin descripción';
    final direccion = incidencia.direccion ?? '';
    final estadoActualId = _estadoIdDesdeTexto(estadoNombre);

    return Card(
      color: Colors.white10,
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      margin: const EdgeInsets.symmetric(vertical: 8),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        leading: const Icon(Icons.assignment_outlined, color: Colors.tealAccent),
        title: Text(
          descripcion,
          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (direccion.isNotEmpty)
              Text("Dirección: $direccion", style: const TextStyle(color: Colors.white70)),
            Text("Estado actual: $estadoNombre", style: const TextStyle(color: Colors.white70)),
          ],
        ),
        trailing: DropdownButton<int>(
          dropdownColor: Colors.grey[900],
          value: estadoActualId,
          iconEnabledColor: Colors.tealAccent,
          style: const TextStyle(color: Colors.white),
          underline: Container(height: 0),
          items: const [
            DropdownMenuItem(value: 1, child: Text('Pendiente')),
            DropdownMenuItem(value: 2, child: Text('En Desarrollo')),
            DropdownMenuItem(value: 3, child: Text('Terminado')),
          ],
          onChanged: (nuevoEstadoId) {
            if (nuevoEstadoId != null && nuevoEstadoId != estadoActualId) {
              _viewModel.actualizarEstado(
                incidenciaId: incidencia.id,
                nuevoEstadoId: nuevoEstadoId,
                token: widget.user['token'],
                onSuccess: () {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Estado actualizado')),
                  );
                },
                onError: () {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Error al actualizar estado')),
                  );
                },
              );
            }
          },
        ),
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => DetalleIncidenciaScreen(
                incidenciaId: incidencia.id,
                empleadoId: widget.user['id'],
                token: widget.user['token'],
              ),
            ),
          );
        },
      ),
    );
  }

  int _estadoIdDesdeTexto(String estado) {
    switch (estado) {
      case 'Pendiente':
        return 1;
      case 'En Desarrollo':
        return 2;
      case 'Terminado':
        return 3;
      default:
        return 1;
    }
  }
}
