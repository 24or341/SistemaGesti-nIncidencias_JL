import 'package:flutter/material.dart';
import 'views/login_screen.dart';
import 'views/register_screen.dart';
import 'views/phone_input_screen.dart';

void main() {
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Gestión de Incidencias',
      debugShowCheckedModeBanner: false,
      theme: ThemeData.dark().copyWith(
        useMaterial3: true,
        scaffoldBackgroundColor: const Color(0xFF0E0E2C),
        colorScheme: ColorScheme.dark(
          primary: Colors.tealAccent.shade400,
          secondary: Colors.purpleAccent.shade100,
          surface: const Color(0xFF1F1F3C),
          background: const Color(0xFF0E0E2C),
          onPrimary: Colors.white,
          onSecondary: Colors.white70,
          onSurface: Colors.white70,
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF1F1F3C),
          foregroundColor: Colors.white,
          elevation: 2,
        ),
        cardColor: const Color(0xFF1C1C38),
        textTheme: const TextTheme(
          bodyMedium: TextStyle(color: Colors.white70),
          bodyLarge: TextStyle(color: Colors.white),
          titleLarge: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        ),
      ),
      home: const WelcomeScreen(),
    );
  }
}

class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0f2027), Color(0xFF203a43), Color(0xFF2c5364)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        width: double.infinity,
        height: double.infinity,
        child: SafeArea(
          child: Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.report_problem, size: 100, color: Colors.amberAccent),
                  const SizedBox(height: 24),
                  const Text(
                    'Gestión de Incidencias',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Reporta incidencias en infraestructuras públicas y contribuye a una ciudad mejor.',
                    style: TextStyle(fontSize: 16, color: Colors.white70),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 40),
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      minimumSize: const Size.fromHeight(50),
                      backgroundColor: Colors.tealAccent.shade400,
                      foregroundColor: Colors.black87,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    ),
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const PhoneInputScreen()));
                    },
                    child: const Text('Reportar Incidencia', style: TextStyle(fontSize: 18)),
                  ),
                  const SizedBox(height: 16),
                  OutlinedButton(
                    style: OutlinedButton.styleFrom(
                      minimumSize: const Size.fromHeight(50),
                      side: const BorderSide(color: Colors.white70),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    ),
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => LoginScreen()));
                    },
                    child: const Text('Iniciar Sesión', style: TextStyle(fontSize: 18)),
                  ),
                  const SizedBox(height: 16),
                  TextButton(
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => RegisterScreen()));
                    },
                    child: const Text('¿No tienes cuenta? Regístrate', style: TextStyle(color: Colors.white70)),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
