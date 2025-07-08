import 'package:flutter/material.dart';
import 'tareas_screen.dart';

class EmpleadoScreen extends StatefulWidget {
  final Map<String, dynamic> user;

  const EmpleadoScreen({super.key, required this.user});

  @override
  State<EmpleadoScreen> createState() => _EmpleadoScreenState();
}

class _EmpleadoScreenState extends State<EmpleadoScreen> {
  int _selectedIndex = 0;

  late final List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _screens = [
      TareasScreen(user: widget.user),
      const Center(child: Text('Próximamente', style: TextStyle(color: Colors.white))),
      // const Center(child: Text('Completadas', style: TextStyle(color: Colors.white))),
      // const Center(child: Text('Notificaciones', style: TextStyle(color: Colors.white))),
      // const Center(child: Text('Perfil', style: TextStyle(color: Colors.white))),
    ];
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
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
        body: SafeArea(child: _screens[_selectedIndex]),
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
            items: const [
              BottomNavigationBarItem(
                icon: Icon(Icons.assignment),
                label: 'Tareas',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.lock_outline),
                label: 'Próximamente',
              ),
              // BottomNavigationBarItem(
              //   icon: Icon(Icons.check_circle),
              //   label: 'Completadas',
              // ),
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
