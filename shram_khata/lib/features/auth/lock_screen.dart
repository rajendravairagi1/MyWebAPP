import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../state/providers.dart';

/// Shown on opening the app when "ask password" is on.
class LockScreen extends ConsumerStatefulWidget {
  const LockScreen({super.key});

  @override
  ConsumerState<LockScreen> createState() => _LockScreenState();
}

class _LockScreenState extends ConsumerState<LockScreen> {
  final _secret = TextEditingController();
  bool _hide = true;
  bool _busy = false;
  String? _error;

  @override
  void dispose() {
    _secret.dispose();
    super.dispose();
  }

  Future<void> _open() async {
    final staff = ref.read(sessionProvider).staff;
    if (staff == null || _secret.text.isEmpty) return;
    setState(() {
      _busy = true;
      _error = null;
    });
    final ok = await ref.read(workspaceServiceProvider).verifySecret(staff.id, _secret.text);
    if (!mounted) return;
    if (ok) {
      ref.read(appLockProvider.notifier).unlock();
    } else {
      setState(() {
        _busy = false;
        _error = tr('Wrong password / PIN.');
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final staff = ref.watch(sessionProvider).staff;
    return Scaffold(
      body: SafeArea(
        child: ListView(padding: const EdgeInsets.fromLTRB(24, 72, 24, 24), children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(color: Palette.brand, borderRadius: BorderRadius.circular(18)),
            child: const Icon(Icons.lock_outline, color: Colors.white, size: 30),
          ),
          const SizedBox(height: 22),
          Text(tr('HazriBook is locked'), style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w800)),
          const SizedBox(height: 6),
          Text(
            trf('Enter the password / PIN of {name} to continue.', {'name': staff?.name ?? ''}),
            style: const TextStyle(color: Palette.muted, height: 1.4),
          ),
          const SizedBox(height: 26),
          TextField(
            controller: _secret,
            obscureText: _hide,
            autofocus: true,
            onSubmitted: (_) => _open(),
            decoration: InputDecoration(
              labelText: tr('Password / PIN'),
              prefixIcon: const Icon(Icons.lock_outline),
              suffixIcon: IconButton(
                icon: Icon(_hide ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                onPressed: () => setState(() => _hide = !_hide),
              ),
            ),
          ),
          if (_error != null) ...[
            const SizedBox(height: 10),
            Text(_error!, style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600)),
          ],
          const SizedBox(height: 20),
          FilledButton(onPressed: _busy ? null : _open, child: Text(tr('Open'))),
          const SizedBox(height: 8),
          TextButton(
            onPressed: () async {
              ref.read(appLockProvider.notifier).unlock();
              await ref.read(sessionProvider.notifier).logout();
            },
            child: Text(tr('Sign in as someone else')),
          ),
        ]),
      ),
    );
  }
}
