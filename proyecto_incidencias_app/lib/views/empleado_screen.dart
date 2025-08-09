import 'package:flutter/material.dart';
import 'tareas_screen.dart';
import 'perfil_screen.dart';
import '../models/usuario_model.dart';

/// Pantalla principal para empleados con navegación inferior.
/// Permite alternar entre la vista de tareas asignadas y el perfil del usuario.
class EmpleadoScreen extends StatefulWidget {
  final Map<String, dynamic> user;

  const EmpleadoScreen({super.key, required this.user});

  @override
  State<EmpleadoScreen> createState() => _EmpleadoScreenState();
}

class _EmpleadoScreenState extends State<EmpleadoScreen> {
  // Índice actual de la pestaña seleccionada
  int _currentTabIndex = 0;

  // Objeto Usuario creado a partir del mapa recibido
  late final Usuario _usuario;

  // Lista estática de pantallas para evitar recreación en cada build
  late final List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _usuario = Usuario.fromJson(widget.user);
    _screens = [
      TareasScreen(user: widget.user),
      PerfilScreen(usuario: _usuario),
    ];
  }

  /// Cambia la pestaña activa y actualiza la UI
  void _onTabSelected(int index) {
    setState(() {
      _currentTabIndex = index;
    });
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
        // Muestra la pantalla correspondiente a la pestaña activa
        body: SafeArea(child: _screens[_currentTabIndex]),
        bottomNavigationBar: Container(
          decoration: const BoxDecoration(
            color: Color(0xFF1F1F3C),
            border: Border(top: BorderSide(color: Colors.white12, width: 0.5)),
          ),
          child: BottomNavigationBar(
            backgroundColor: Colors.transparent,
            selectedItemColor: Colors.tealAccent,
            unselectedItemColor: Colors.white60,
            showUnselectedLabels: true,
            currentIndex: _currentTabIndex,
            onTap: _onTabSelected,
            type: BottomNavigationBarType.fixed,
            items: const [
              BottomNavigationBarItem(
                icon: Icon(Icons.assignment),
                label: 'Tareas',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.person_outline),
                label: 'Perfil',
              ),
            ],
          ),
        ),
      ),
    );
  }
}
