import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/login_viewmodel.dart';
import 'register_screen.dart';
import 'empleado_screen.dart';

class LoginScreen extends StatelessWidget {
  LoginScreen({super.key});

  final _formKey = GlobalKey<FormState>();
  bool _captchaChecked = false;

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<LoginViewModel>(
      create: (_) => LoginViewModel(),
      child: Scaffold(
        body: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF0f2027), Color(0xFF203a43), Color(0xFF2c5364)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: SafeArea(
            child: Stack(
              children: [
                // Botón de retroceso
                Positioned(
                  top: 8,
                  left: 8,
                  child: IconButton(
                    icon: const Icon(Icons.arrow_back, color: Colors.white),
                    onPressed: () {
                      Navigator.pop(context);
                    },
                  ),
                ),
                // Contenido principal
                Center(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(24),
                    child: Consumer<LoginViewModel>(
                      builder: (context, viewModel, child) {
                        return Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.lock_outline,
                                size: 80, color: Colors.cyanAccent),
                            const SizedBox(height: 20),
                            const Text(
                              'Iniciar Sesión',
                              style: TextStyle(
                                  fontSize: 26,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white),
                            ),
                            const SizedBox(height: 30),
                            Form(
                              key: _formKey,
                              child: Column(
                                children: [
                                  if (viewModel.errorMessage != null)
                                    Padding(
                                      padding:
                                          const EdgeInsets.only(bottom: 10),
                                      child: Text(
                                        viewModel.errorMessage!,
                                        style: const TextStyle(
                                            color: Colors.redAccent,
                                            fontSize: 14),
                                      ),
                                    ),

                                  // Campo correo
                                  TextFormField(
                                    style:
                                        const TextStyle(color: Colors.white),
                                    decoration: InputDecoration(
                                      filled: true,
                                      fillColor: Colors.white10,
                                      labelText: 'Correo',
                                      labelStyle:
                                          const TextStyle(color: Colors.white70),
                                      border: OutlineInputBorder(
                                          borderRadius:
                                              BorderRadius.circular(12)),
                                      prefixIcon: const Icon(Icons.email,
                                          color: Colors.white70),
                                    ),
                                    keyboardType: TextInputType.emailAddress,
                                    validator: (value) {
                                      if (value == null || value.isEmpty) {
                                        return 'Por favor ingrese su correo';
                                      }
                                      if (!RegExp(r'^[^@]+@[^@]+\.[^@]+')
                                          .hasMatch(value)) {
                                        return 'Correo inválido';
                                      }
                                      return null;
                                    },
                                    onChanged: viewModel.setEmail,
                                  ),
                                  const SizedBox(height: 16),

                                  // Campo contraseña
                                  TextFormField(
                                    style:
                                        const TextStyle(color: Colors.white),
                                    decoration: InputDecoration(
                                      filled: true,
                                      fillColor: Colors.white10,
                                      labelText: 'Contraseña',
                                      labelStyle:
                                          const TextStyle(color: Colors.white70),
                                      border: OutlineInputBorder(
                                          borderRadius:
                                              BorderRadius.circular(12)),
                                      prefixIcon: const Icon(Icons.lock,
                                          color: Colors.white70),
                                    ),
                                    obscureText: true,
                                    validator: (value) {
                                      if (value == null || value.isEmpty) {
                                        return 'Por favor ingrese su contraseña';
                                      }
                                      return null;
                                    },
                                    onChanged: viewModel.setPassword,
                                  ),

                                  const SizedBox(height: 24),

                                  // --- CAPTCHA ---
                                  StatefulBuilder(
                                    builder: (context, setState) {
                                      bool captchaLoading = false;
                                      bool captchaChecked = _captchaChecked;

                                      return Container(
                                        padding: const EdgeInsets.all(10),
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(8),
                                          boxShadow: [
                                            BoxShadow(
                                              color:
                                                  Colors.black.withOpacity(0.2),
                                              blurRadius: 4,
                                            )
                                          ],
                                        ),
                                        child: Row(
                                          children: [
                                            GestureDetector(
                                              onTap: () async {
                                                if (!captchaChecked &&
                                                    !captchaLoading) {
                                                  setState(() {
                                                    captchaLoading = true;
                                                  });
                                                  await Future.delayed(
                                                      const Duration(
                                                          milliseconds: 1200));
                                                  setState(() {
                                                    captchaLoading = false;
                                                    captchaChecked = true;
                                                    _captchaChecked = true;
                                                  });
                                                }
                                              },
                                              child: AnimatedContainer(
                                                duration: const Duration(
                                                    milliseconds: 400),
                                                width: 28,
                                                height: 28,
                                                decoration: BoxDecoration(
                                                  border: Border.all(
                                                      color: captchaChecked
                                                          ? Colors.green
                                                          : captchaLoading
                                                              ? Colors.blue
                                                              : Colors
                                                                  .grey.shade600,
                                                      width: 2),
                                                  borderRadius:
                                                      BorderRadius.circular(4),
                                                  color: captchaChecked
                                                      ? Colors.green
                                                      : Colors.white,
                                                ),
                                                child: Center(
                                                  child: AnimatedSwitcher(
                                                    duration: const Duration(
                                                        milliseconds: 400),
                                                    transitionBuilder:
                                                        (child, anim) =>
                                                            RotationTransition(
                                                      turns: child.key ==
                                                              const ValueKey(
                                                                  'check')
                                                          ? anim
                                                          : Tween<double>(
                                                                  begin: 0.75,
                                                                  end: 1.0)
                                                              .animate(anim),
                                                      child: child,
                                                    ),
                                                    child: captchaLoading
                                                        ? SizedBox(
                                                            key: const ValueKey(
                                                                'loading'),
                                                            width: 16,
                                                            height: 16,
                                                            child:
                                                                CircularProgressIndicator(
                                                              strokeWidth: 2,
                                                              valueColor:
                                                                  AlwaysStoppedAnimation<
                                                                          Color>(
                                                                      Colors
                                                                          .blue),
                                                            ),
                                                          )
                                                        : captchaChecked
                                                            ? const Icon(
                                                                Icons.check,
                                                                key: ValueKey(
                                                                    'check'),
                                                                color: Colors
                                                                    .white,
                                                                size: 18)
                                                            : null,
                                                  ),
                                                ),
                                              ),
                                            ),
                                            const SizedBox(width: 10),
                                            const Text(
                                              'No soy un robot',
                                              style: TextStyle(
                                                  fontSize: 16,
                                                  color: Colors.black87),
                                            ),
                                            const Spacer(),
                                            Image.network(
                                              'https://www.gstatic.com/recaptcha/api2/logo_48.png',
                                              width: 24,
                                              height: 24,
                                            ),
                                          ],
                                        ),
                                      );
                                    },
                                  ),
                                  const SizedBox(height: 24),

                                  // Botón de inicio de sesión
                                  SizedBox(
                                    width: double.infinity,
                                    child: viewModel.isLoading
                                        ? const Center(
                                            child: CircularProgressIndicator(
                                                color: Colors.white))
                                        : ElevatedButton(
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor:
                                                  Colors.cyanAccent.shade700,
                                              foregroundColor: Colors.black,
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      vertical: 16),
                                              shape: RoundedRectangleBorder(
                                                  borderRadius:
                                                      BorderRadius.circular(
                                                          12)),
                                            ),
                                            onPressed: () async {
                                              if (_formKey.currentState!
                                                  .validate()) {
                                                if (!_captchaChecked) {
                                                  ScaffoldMessenger.of(context)
                                                      .showSnackBar(
                                                    const SnackBar(
                                                      content: Text(
                                                          'Por favor verifica el CAPTCHA'),
                                                    ),
                                                  );
                                                  return;
                                                }

                                                bool success =
                                                    await viewModel.login();

                                                if (!context.mounted) return;

                                                if (success &&
                                                    viewModel.usuario !=
                                                        null) {
                                                  Navigator.pushReplacement(
                                                    context,
                                                    MaterialPageRoute(
                                                      builder: (_) =>
                                                          EmpleadoScreen(
                                                              user: viewModel
                                                                  .usuario!
                                                                  .toJson()),
                                                    ),
                                                  );
                                                }
                                              }
                                            },
                                            child: const Text('Iniciar Sesión',
                                                style:
                                                    TextStyle(fontSize: 18)),
                                          ),
                                  ),
                                  const SizedBox(height: 16),

                                  TextButton(
                                    onPressed: () {
                                      Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                            builder: (context) =>
                                                RegisterScreen()),
                                      );
                                    },
                                    child: const Text(
                                      '¿No tienes cuenta? Regístrate',
                                      style:
                                          TextStyle(color: Colors.white70),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
