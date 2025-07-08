import 'package:flutter/material.dart';
import 'reportar_screen.dart';
import 'historial_screen.dart';

class CiudadanoHome extends StatefulWidget {
  final Map<String, dynamic> user;

  const CiudadanoHome({super.key, required this.user});

  @override
  State<CiudadanoHome> createState() => _CiudadanoHomeState();
}

class _CiudadanoHomeState extends State<CiudadanoHome> {
  int _selectedIndex = 0;

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> screens = <Widget>[
      ReportarScreen(ciudadanoId: widget.user['id_celular']),
      HistorialScreen(ciudadanoId: widget.user['id_celular']),
      // const Center(
      //   child: Text(
      //     'Notificaciones',
      //     style: TextStyle(color: Colors.white),
      //   ),
      // ),
      // const Center(
      //   child: Text(
      //     'Perfil',
      //     style: TextStyle(color: Colors.white),
      //   ),
      // ),
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
                icon: Icon(Icons.history),
                label: 'Historial',
              ),
              // BottomNavigationBarItem(
              //   icon: Icon(Icons.notifications_none),
              //   label: 'Notificaciones',
              // ),
              // BottomNavigationBarItem(
              //   icon: Icon(Icons.person_outline),
              //   label: 'Perfil',
              // ),
            ],
          ),
        ),
      ),
    );
  }
}
