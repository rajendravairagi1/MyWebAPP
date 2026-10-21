import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';

/// Sign in to an existing cloud account: owner on a new phone, a supervisor,
/// or a company manager.
class CloudLoginScreen extends ConsumerStatefulWidget {
  const CloudLoginScreen({super.key});

  @override
  ConsumerState<CloudLoginScreen> createState() => _CloudLoginScreenState();
}

class _CloudLoginScreenState extends ConsumerState<CloudLoginScreen> {
  final _id = TextEditingController();
  final _secret = TextEditingController();
  bool _hide = true;
  bool _busy = false;
  String? _error;
  String? _step;

  @override
  void dispose() {
    _id.dispose();
    _secret.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (_id.text.trim().isEmpty || _secret.text.isEmpty) {
      setState(() => _error = tr('Enter your email or mobile and password.'));
      return;
    }
    setState(() {
      _busy = true;
      _error = null;
      _step = tr('Signing in...');
    });
    final svc = ref.read(syncServiceProvider);
    try {
      final s = await svc.login(_id.text.trim(), _secret.text);
      if (mounted) setState(() => _step = tr('Downloading your data...'));
      await svc.sync();
      ref.read(cloudProvider.notifier).refreshSession();
      if (s.isManager) {
        if (mounted) Navigator.of(context).popUntil((r) => r.isFirst);
        return;
      }
      final ok = await ref.read(sessionProvider.notifier).enterCloud(s.userId);
      if (!ok) {
        await svc.signOut();
        ref.read(cloudProvider.notifier).refreshSession();
        throw Exception(tr('This phone already has a different workspace, or your account is not set up yet.'));
      }
      if (mounted) Navigator.of(context).popUntil((r) => r.isFirst);
    } catch (e) {
      if (mounted) setState(() => _error = e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) {
        setState(() {
          _busy = false;
          _step = null;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(tr('Cloud account'))),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(24, 8, 24, 24),
        children: [
          Text(
            tr('Sign in with the account you made on your first phone. Your workspace is downloaded to this phone. Supervisors and company managers use the login the owner created for them.'),
            style: const TextStyle(color: Palette.muted, height: 1.45),
          ),
          const SizedBox(height: 24),
          TextField(
            controller: _id,
            keyboardType: TextInputType.emailAddress,
            autocorrect: false,
            textInputAction: TextInputAction.next,
            decoration: InputDecoration(labelText: tr('Email or mobile'), prefixIcon: const Icon(Icons.person_outline)),
          ),
          const SizedBox(height: 14),
          TextField(
            controller: _secret,
            obscureText: _hide,
            onSubmitted: (_) => _login(),
            decoration: InputDecoration(
              labelText: tr('Password'),
              prefixIcon: const Icon(Icons.lock_outline),
              suffixIcon: IconButton(
                icon: Icon(_hide ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                onPressed: () => setState(() => _hide = !_hide),
              ),
            ),
          ),
          if (_error != null) ...[
            const SizedBox(height: 12),
            Text(tr(_error!), style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600)),
          ],
          if (_step != null) ...[
            const SizedBox(height: 12),
            Text(_step!, style: const TextStyle(color: Palette.muted)),
          ],
          const SizedBox(height: 22),
          FilledButton(
            onPressed: _busy ? null : _login,
            child: _busy
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white))
                : Text(tr('Sign in')),
          ),
          const SizedBox(height: 12),
          Text(
            tr('You need an internet connection for this step.'),
            textAlign: TextAlign.center,
            style: const TextStyle(color: Palette.muted, fontSize: 12.5),
          ),
        ],
      ),
    );
  }
}
