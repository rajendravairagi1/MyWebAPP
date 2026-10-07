import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _id = TextEditingController();
  final _secret = TextEditingController();
  bool _hide = true;
  bool _busy = false;
  String? _error;

  @override
  void dispose() {
    _id.dispose();
    _secret.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (_id.text.trim().isEmpty || _secret.text.isEmpty) {
      setState(() => _error = 'Enter your email or mobile and password / PIN.');
      return;
    }
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      final ok = await ref.read(sessionProvider.notifier).login(_id.text, _secret.text);
      if (!ok && mounted) setState(() => _error = 'Wrong email / mobile or password / PIN.');
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(24, 48, 24, 24),
          children: [
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: Palette.brand,
                borderRadius: BorderRadius.circular(18),
              ),
              child: const Icon(Icons.groups_rounded, color: Colors.white, size: 32),
            ),
            const SizedBox(height: 22),
            const Text('Welcome back', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800)),
            const SizedBox(height: 6),
            const Text(
              'Sign in with your email or mobile number.\nOwners use a password, team members use a PIN.',
              style: TextStyle(color: Palette.muted, height: 1.45),
            ),
            const SizedBox(height: 28),
            TextField(
              controller: _id,
              keyboardType: TextInputType.emailAddress,
              autocorrect: false,
              textInputAction: TextInputAction.next,
              decoration: const InputDecoration(
                labelText: 'Email or mobile',
                prefixIcon: Icon(Icons.person_outline),
              ),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: _secret,
              obscureText: _hide,
              onSubmitted: (_) => _login(),
              decoration: InputDecoration(
                labelText: 'Password / PIN',
                prefixIcon: const Icon(Icons.lock_outline),
                suffixIcon: IconButton(
                  icon: Icon(_hide ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                  onPressed: () => setState(() => _hide = !_hide),
                ),
              ),
            ),
            if (_error != null) ...[
              const SizedBox(height: 12),
              Text(_error!, style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600)),
            ],
            const SizedBox(height: 22),
            FilledButton(
              onPressed: _busy ? null : _login,
              child: _busy
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white),
                    )
                  : const Text('Sign in'),
            ),
            const SizedBox(height: 8),
            TextButton(
              onPressed: () => showDialog<void>(
                context: context,
                builder: (ctx) => AlertDialog(
                  title: const Text('Forgot password?'),
                  content: const Text(
                    'Team members: ask the owner to set a new PIN for you (Team & roles).\n\n'
                    'Owner: password recovery by email works once cloud sync is connected. Until then, keep your password safe.',
                  ),
                  actions: [
                    TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK')),
                  ],
                ),
              ),
              child: const Text('Forgot password?'),
            ),
          ],
        ),
      ),
    );
  }
}
