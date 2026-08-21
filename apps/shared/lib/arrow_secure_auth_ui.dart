import 'package:flutter/material.dart';

import 'arrow_secure_auth.dart';

/// Shared Portuguese login/lock UI used by all four Flutter apps.
abstract final class ArrowSecureAuthUi {
  static Future<void> afterPasswordLogin(
    BuildContext? context,
    ArrowSecureAuth auth, {
    required String email,
    required String password,
    required bool rememberMe,
  }) async {
    await auth.savePasswordLogin(email: email, password: password, rememberMe: rememberMe);
    if (context != null && context.mounted) {
      await promptEnableBiometrics(context, auth, email: email, password: password);
    }
  }

  static Future<void> afterFederatedLogin(
    BuildContext? context,
    ArrowSecureAuth auth, {
    String? email,
    required ArrowLoginMethod method,
  }) async {
    await auth.saveFederatedLogin(email: email, method: method);
    if (context != null && context.mounted) {
      await promptEnableBiometrics(context, auth, email: email);
    }
  }

  static Future<void> promptEnableBiometrics(
    BuildContext context,
    ArrowSecureAuth auth, {
    String? email,
    String? password,
  }) async {
    try {
      if (await auth.hasAskedEnablePrompt()) return;
      if (await auth.isBiometricsEnabled()) {
        await auth.markEnablePrompted();
        return;
      }
      if (!await auth.isBiometricAvailable()) {
        await auth.markEnablePrompted();
        return;
      }
      if (!context.mounted) return;
      final yes = await showDialog<bool>(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          title: const Text(ArrowSecureAuthStrings.enablePromptTitle),
          content: const Text(ArrowSecureAuthStrings.enablePromptBody),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(ctx).pop(false),
              child: const Text(ArrowSecureAuthStrings.enableNo),
            ),
            TextButton(
              onPressed: () => Navigator.of(ctx).pop(true),
              child: const Text(ArrowSecureAuthStrings.enableYes),
            ),
          ],
        ),
      );
      await auth.markEnablePrompted();
      if (yes == true) {
        final result = await auth.setBiometricsEnabled(true, email: email, password: password);
        if (result == ArrowBiometricToggleResult.unavailable && context.mounted) {
          ScaffoldMessenger.maybeOf(context)?.showSnackBar(
            const SnackBar(content: Text(ArrowSecureAuthStrings.unavailable)),
          );
        }
      }
    } catch (_) {}
  }

  static void toastUnavailable(BuildContext context) {
    final messenger = ScaffoldMessenger.maybeOf(context);
    if (messenger != null) {
      messenger.showSnackBar(const SnackBar(content: Text(ArrowSecureAuthStrings.unavailable)));
      return;
    }
  }
}

class ArrowRememberMeRow extends StatelessWidget {
  const ArrowRememberMeRow({
    super.key,
    required this.value,
    required this.onChanged,
  });

  final bool value;
  final ValueChanged<bool> onChanged;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () => onChanged(!value),
      child: Row(
        children: [
          SizedBox(
            width: 24,
            height: 24,
            child: Checkbox(
              value: value,
              onChanged: (next) {
                if (next != null) onChanged(next);
              },
            ),
          ),
          const SizedBox(width: 8),
          const Expanded(
            child: Text(ArrowSecureAuthStrings.rememberMe),
          ),
        ],
      ),
    );
  }
}

class ArrowBiometricLoginButton extends StatelessWidget {
  const ArrowBiometricLoginButton({super.key, required this.onPressed});

  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return OutlinedButton.icon(
      onPressed: onPressed,
      icon: const Icon(Icons.fingerprint),
      label: const Text(ArrowSecureAuthStrings.biometricToggle),
    );
  }
}

class ArrowForgetDeviceButton extends StatelessWidget {
  const ArrowForgetDeviceButton({super.key, required this.onPressed});

  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return TextButton(
      onPressed: onPressed,
      child: const Text(ArrowSecureAuthStrings.forgetDevice),
    );
  }
}

class ArrowBiometricSettingsTile extends StatefulWidget {
  const ArrowBiometricSettingsTile({super.key, required this.auth});

  final ArrowSecureAuth auth;

  @override
  State<ArrowBiometricSettingsTile> createState() => _ArrowBiometricSettingsTileState();
}

class _ArrowBiometricSettingsTileState extends State<ArrowBiometricSettingsTile> {
  bool _available = false;
  bool _enabled = false;
  ArrowLoginMethod _method = ArrowLoginMethod.password;
  bool _loaded = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final available = await widget.auth.isBiometricAvailable();
    final enabled = await widget.auth.isBiometricsEnabled();
    final method = await widget.auth.loginMethod();
    if (!mounted) return;
    setState(() {
      _available = available;
      _enabled = enabled;
      _method = method;
      _loaded = true;
    });
  }

  Future<void> _onChanged(bool value) async {
    if (!_available) {
      ArrowSecureAuthUi.toastUnavailable(context);
      return;
    }
    final result = await widget.auth.setBiometricsEnabled(value);
    if (!mounted) return;
    if (result == ArrowBiometricToggleResult.unavailable) {
      ArrowSecureAuthUi.toastUnavailable(context);
      setState(() => _enabled = false);
      return;
    }
    setState(() => _enabled = result == ArrowBiometricToggleResult.enabled);
  }

  @override
  Widget build(BuildContext context) {
    if (!_loaded || !_available) return const SizedBox.shrink();
    return SwitchListTile(
      secondary: const Icon(Icons.fingerprint),
      title: const Text(ArrowSecureAuthStrings.biometricToggle),
      subtitle: Text(ArrowSecureAuthStrings.lockSubtitleFor(_method)),
      value: _enabled,
      onChanged: _onChanged,
    );
  }
}

class ArrowForgetDeviceTile extends StatelessWidget {
  const ArrowForgetDeviceTile({super.key, required this.auth, this.onForgot});

  final ArrowSecureAuth auth;
  final VoidCallback? onForgot;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: const Icon(Icons.phonelink_erase_outlined),
      title: const Text(ArrowSecureAuthStrings.forgetDevice),
      onTap: () async {
        await auth.forgetDevice();
        onForgot?.call();
      },
    );
  }
}

class ArrowBiometricLockPage extends StatefulWidget {
  const ArrowBiometricLockPage({
    super.key,
    required this.auth,
    required this.onUnlocked,
    required this.onUsePassword,
    this.subtitle,
  });

  final ArrowSecureAuth auth;
  final Future<void> Function() onUnlocked;
  final VoidCallback onUsePassword;
  final String? subtitle;

  @override
  State<ArrowBiometricLockPage> createState() => _ArrowBiometricLockPageState();
}

class _ArrowBiometricLockPageState extends State<ArrowBiometricLockPage> {
  bool _busy = false;
  String _subtitle = ArrowSecureAuthStrings.lockPassword;

  @override
  void initState() {
    super.initState();
    _subtitle = widget.subtitle ?? ArrowSecureAuthStrings.lockPassword;
    WidgetsBinding.instance.addPostFrameCallback((_) => _unlock());
  }

  Future<void> _unlock() async {
    if (_busy) return;
    setState(() => _busy = true);
    try {
      _subtitle = await widget.auth.lockSubtitleAsync();
      final ok = await widget.auth.authenticate();
      if (!ok) {
        if (mounted) setState(() => _busy = false);
        return;
      }
      await widget.onUnlocked();
    } catch (_) {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF111827),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 28),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.fingerprint, size: 72, color: Colors.white),
              const SizedBox(height: 20),
              const Text(
                ArrowSecureAuthStrings.unlockTitle,
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 10),
              Text(
                _subtitle,
                textAlign: TextAlign.center,
                style: const TextStyle(color: Color(0xFF9CA3AF), fontSize: 15),
              ),
              const SizedBox(height: 28),
              if (_busy) const CircularProgressIndicator(color: Colors.white),
              if (!_busy)
                FilledButton(
                  onPressed: _unlock,
                  child: const Text(ArrowSecureAuthStrings.tryAgain),
                ),
              const SizedBox(height: 8),
              TextButton(
                onPressed: () {
                  widget.auth.skipNextBiometricPrompt();
                  widget.onUsePassword();
                },
                child: const Text(
                  ArrowSecureAuthStrings.usePassword,
                  style: TextStyle(color: Colors.white),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
