import 'package:flutter/material.dart';
import '../models/incidencia_model.dart';
import '../viewmodels/tareas_viewmodel.dart';
import 'detalle_incidencia_screen.dart';
// Realizado por: Leandro Hurtado Ortiz
/// Pantalla que muestra la lista de incidencias asignadas al empleado o
/// todas las incidencias si el usuario es administrador.
/// Permite actualizar el estado de cada incidencia y navegar a su detalle.
class TareasScreen extends StatefulWidget {
  final Map<String, dynamic> user;

  const TareasScreen({super.key, required this.user});

  @override
  State<TareasScreen> createState() => _TareasScreenState();
}

class _TareasScreenState extends State<TareasScreen> {
  late final TareasViewModel _tareasViewModel;

  @override
  void initState() {
    super.initState();
    // Inicializa ViewModel con datos del usuario para cargar incidencias según rol
    _tareasViewModel = TareasViewModel(
      widget.user['id'],
      widget.user['token'],
      widget.user['role'],
    );
    _tareasViewModel.cargarIncidencias();
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
          title: Text(
            widget.user['role'] == 'administrador'
                ? 'Todas las Incidencias'
                : 'Tareas Asignadas',
          ),
          backgroundColor: Colors.teal.shade700,
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        body: SafeArea(
          child: AnimatedBuilder(
            animation: _tareasViewModel,
            builder: (context, _) {
              if (_tareasViewModel.isLoading) {
                return const Center(
                  child: CircularProgressIndicator(color: Colors.tealAccent),
                );
              }

              if (_tareasViewModel.errorMessage != null) {
                return Center(
                  child: Text(
                    _tareasViewModel.errorMessage!,
                    style: const TextStyle(color: Colors.white70),
                  ),
                );
              }

              if (_tareasViewModel.incidencias.isEmpty) {
                return Center(
                  child: Text(
                    widget.user['role'] == 'administrador'
                        ? 'No hay incidencias registradas.'
                        : 'No tienes incidencias asignadas.',
                    style: const TextStyle(color: Colors.white70),
                  ),
                );
              }

              return ListView.builder(
                padding: const EdgeInsets.all(12),
                itemCount: _tareasViewModel.incidencias.length,
                itemBuilder: (context, index) {
                  final incidencia = _tareasViewModel.incidencias[index];
                  return _buildIncidenciaCard(incidencia);
                },
              );
            },
          ),
        ),
      ),
    );
  }

  /// Construye la tarjeta para cada incidencia con detalles básicos y selector de estado
  Widget _buildIncidenciaCard(Incidencia incidencia) {
    final estado = incidencia.estado;
    final descripcion = incidencia.descripcion.isNotEmpty
        ? incidencia.descripcion
        : 'Sin descripción';
    final direccion = incidencia.direccion ?? '';
    final estadoId = _obtenerEstadoId(estado);

    return Card(
      color: Colors.white10,
      elevation: 2,
      margin: const EdgeInsets.symmetric(vertical: 8),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        leading: const Icon(Icons.assignment_outlined, color: Colors.tealAccent),
        title: Text(
          descripcion,
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (direccion.isNotEmpty)
              Text("Dirección: $direccion", style: const TextStyle(color: Colors.white70)),
            Text("Estado actual: $estado", style: const TextStyle(color: Colors.white70)),
          ],
        ),
        trailing: DropdownButton<int>(
          dropdownColor: Colors.grey[900],
          value: estadoId,
          iconEnabledColor: Colors.tealAccent,
          style: const TextStyle(color: Colors.white),
          underline: Container(height: 0),
          items: const [
            DropdownMenuItem(value: 1, child: Text('Pendiente')),
            DropdownMenuItem(value: 2, child: Text('En Desarrollo')),
            DropdownMenuItem(value: 3, child: Text('Terminado')),
          ],
          onChanged: (nuevoEstadoId) {
            if (nuevoEstadoId != null && nuevoEstadoId != estadoId) {
              _tareasViewModel.actualizarEstado(
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
                usuarioId: widget.user['id'],
                token: widget.user['token'],
                role: widget.user['role'],
              ),
            ),
          );
        },
      ),
    );
  }

  /// Convierte el nombre del estado a un ID para el Dropdown
  int _obtenerEstadoId(String estado) {
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
