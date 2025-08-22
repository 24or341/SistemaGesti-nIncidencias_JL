import 'package:flutter/material.dart';
import 'reporte_paso1_screen.dart';
import 'historial_todo_screen.dart';
import 'phone_input_screen.dart';

// Realizado por: Leandro Hurtado Ortiz
/// Pantalla principal para ciudadanos.
/// Contiene navegación inferior para acceder a:
/// - Reportar incidencia
/// - Ver todas las incidencias
/// - Ver incidencias propias
class CiudadanoHome extends StatefulWidget {
  final int initialIndex;

  const CiudadanoHome({super.key, this.initialIndex = 0});

  @override
  State<CiudadanoHome> createState() => _CiudadanoHomeState();
}

class _CiudadanoHomeState extends State<CiudadanoHome> {
  late int _selectedIndex;

  @override
  void initState() {
    super.initState();
    _selectedIndex = widget.initialIndex;
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> screens = <Widget>[
      const ReportePaso1Screen(),
      const HistorialTodoScreen(),
      const PhoneInputScreen(),
    ];

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
        body: SafeArea(child: screens[_selectedIndex]),
        bottomNavigationBar: Container(
          decoration: const BoxDecoration(
            color: Color(0xFF1F1F3C),
            border: Border(top: BorderSide(color: Colors.white12, width: 0.5)),
          ),
          child: BottomNavigationBar(
            backgroundColor: Colors.transparent,
            selectedItemColor: Colors.tealAccent.shade400,
            unselectedItemColor: Colors.white60,
            showUnselectedLabels: true,
            currentIndex: _selectedIndex,
            onTap: _onItemTapped,
            type: BottomNavigationBarType.fixed,
            selectedLabelStyle: const TextStyle(fontWeight: FontWeight.bold),
            items: const [
              BottomNavigationBarItem(
                icon: Icon(Icons.add_location_alt_outlined),
                label: 'Reportar',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.public),
                label: 'Todas',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.search),
                label: 'Mis Incidencias',
              ),
            ],
          ),
        ),
      ),
    );
  }
}
