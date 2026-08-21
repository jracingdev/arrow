import 'package:arrow_shared/arrow_auth_errors.dart';
import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/screens/home_shell.dart';
import 'package:provider/screens/phone_screen.dart';
import 'package:provider/service/auth_service.dart';
import 'package:provider/themes/app_theme.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  bool obscure = true;

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  Future<void> _goHomeIfAdmitted() async {
    if (await AuthService.admitCurrentUser()) {
      Get.offAll(() => const HomeShell());
    }
  }

  Future<void> _loginEmail() async {
    final email = emailController.text.trim().toLowerCase();
    final password = passwordController.text.trim();
    if (email.isEmpty || !email.contains('@')) {
      ShowToastDialog.showToast('Informe um e-mail válido.');
      return;
    }
    if (password.isEmpty) {
      ShowToastDialog.showToast('Informe a senha.');
      return;
    }
    ShowToastDialog.showLoader('Entrando...');
    try {
      await FirebaseAuth.instance.signInWithEmailAndPassword(email: email, password: password);
      ShowToastDialog.closeLoader();
      await _goHomeIfAdmitted();
    } on FirebaseAuthException catch (e) {
      ShowToastDialog.closeLoader();
      final mfa = ArrowAuthErrors.messageFor(e);
      if (mfa != null) {
        ShowToastDialog.showToast(mfa);
      } else if (e.code == 'user-not-found') {
        ShowToastDialog.showToast('Nenhum usuário com este e-mail.');
      } else if (e.code == 'wrong-password' || e.code == 'invalid-credential' || e.code == 'INVALID_LOGIN_CREDENTIALS') {
        ShowToastDialog.showToast('Senha incorreta.');
      } else if (e.code == 'invalid-email') {
        ShowToastDialog.showToast('E-mail inválido.');
      } else {
        ShowToastDialog.showToast(e.message ?? e.code);
      }
    } catch (_) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Não foi possível entrar. Tente de novo.');
    }
  }

  Future<void> _loginGoogle() async {
    ShowToastDialog.showLoader('Entrando com Google...');
    try {
      await ArrowGoogleAuth.signIn();
      ShowToastDialog.closeLoader();
      await _goHomeIfAdmitted();
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(ArrowGoogleAuth.userMessage(e));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 12),
              Image.asset('assets/provider-logo.png'),
              const SizedBox(height: 20),
              const Text('Bem-vindo de volta', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w700, color: AppTheme.grey900)),
              const SizedBox(height: 8),
              const Text(
                'Entre para gerenciar pedidos, equipe e serviços on-demand.',
                style: TextStyle(fontSize: 15, color: AppTheme.grey500),
              ),
              const SizedBox(height: 32),
              TextField(
                controller: emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: const InputDecoration(
                  labelText: 'E-mail',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 14),
              TextField(
                controller: passwordController,
                obscureText: obscure,
                decoration: InputDecoration(
                  labelText: 'Senha',
                  border: const OutlineInputBorder(),
                  suffixIcon: IconButton(
                    onPressed: () => setState(() => obscure = !obscure),
                    icon: Icon(obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              ElevatedButton(onPressed: _loginEmail, child: const Text('Entrar')),
              const SizedBox(height: 12),
              OutlinedButton.icon(
                onPressed: () => Get.to(() => const PhoneScreen()),
                icon: const Icon(Icons.phone_android),
                label: const Text('Entrar com telefone'),
                style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(48)),
              ),
              const SizedBox(height: 12),
              OutlinedButton.icon(
                onPressed: _loginGoogle,
                icon: const Icon(Icons.g_mobiledata, size: 28),
                label: const Text('Entrar com Google'),
                style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(48)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
