import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../viewmodels/phone_input_viewmodel.dart';
import 'ciudadano_home.dart';

class PhoneInputScreen extends StatelessWidget {
  const PhoneInputScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<PhoneInputViewModel>(
      create: (_) => PhoneInputViewModel(),
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
            title: const Text('Ingreso de Teléfono'),
            backgroundColor: Colors.teal.shade700,
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          body: Consumer<PhoneInputViewModel>(
            builder: (context, viewModel, child) {
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Center(
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Text(
                          'Ingrese su número de teléfono',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 30),
                        TextField(
                          controller: viewModel.phoneController,
                          keyboardType: TextInputType.phone,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Teléfono',
                            labelStyle: const TextStyle(color: Colors.white70),
                            prefixIcon: const Icon(Icons.phone, color: Colors.white70),
                            enabledBorder: OutlineInputBorder(
                              borderSide: const BorderSide(color: Colors.white70),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderSide: const BorderSide(color: Colors.tealAccent, width: 2),
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                        ),
                        const SizedBox(height: 30),
                        SizedBox(
                          height: 50,
                          child: ElevatedButton(
                            onPressed: viewModel.isLoading
                                ? null
                                : () async {
                                    final success = await viewModel.validarTelefono();

                                    if (!context.mounted) return;

                                    if (success && viewModel.ciudadanoId != null) {
                                      Navigator.pushReplacement(
                                        context,
                                        MaterialPageRoute(
                                          builder: (context) => CiudadanoHome(
                                            user: {'id_celular': viewModel.ciudadanoId!},
                                          ),
                                        ),
                                      );
                                    } else if (viewModel.errorMessage != null) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(content: Text(viewModel.errorMessage!)),
                                      );
                                    }
                                  },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.teal,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30),
                              ),
                            ),
                            child: viewModel.isLoading
                                ? const CircularProgressIndicator(color: Colors.white)
                                : const Text(
                                    'Continuar',
                                    style: TextStyle(fontSize: 18, color: Colors.white),
                                  ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }
}
