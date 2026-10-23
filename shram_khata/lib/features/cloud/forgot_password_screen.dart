import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../state/cloud.dart';
import 'cloud_login_screen.dart';

/// Reset a forgotten password with a code sent by email (cloud accounts).
class ForgotPasswordScreen extends ConsumerStatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  ConsumerState<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends ConsumerState<ForgotPasswordScreen> {
  final _id = TextEditingController();
  final _code = TextEditingController();
  final _pass = TextEditingController();
  final _pass2 = TextEditingController();
  bool _codeSent = false;
  bool _busy = false;
  String? _error;
  String? _info;

  @override
  void dispose() {
    for (final c in [_id, _code, _pass, _pass2]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _send() async {
    if (_id.text.trim().isEmpty) return setState(() => _error = tr('Enter your email.'));
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      await ref.read(syncServiceProvider).forgotPassword(_id.text.trim());
      setState(() {
        _codeSent = true;
        _info = tr('If this email has a cloud account, a 6 digit code was sent to it. It works for 15 minutes. Check Spam too.');
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  Future<void> _reset() async {
    if (_code.text.trim().length < 6) return setState(() => _error = tr('Enter the 6 digit code from the email.'));
    if (_pass.text.length < 8) return setState(() => _error = tr('Password must be at least 8 characters.'));
    if (_pass.text != _pass2.text) return setState(() => _error = tr('The two passwords do not match.'));
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      await ref.read(syncServiceProvider).resetPassword(_id.text.trim(), _code.text.trim(), _pass.text);
      await completeCloudLogin(ref, _id.text.trim(), _pass.text);
      if (mounted) Navigator.of(context).popUntil((r) => r.isFirst);
    } catch (e) {
      if (mounted) setState(() => _error = e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(tr('Forgot password?'))),
      body: ListView(padding: const EdgeInsets.fromLTRB(24, 8, 24, 24), children: [
        Text(
          tr('We email you a code. This works for accounts that use cloud sync. '
              'If you never turned on cloud sync, the password cannot be recovered.'),
          style: const TextStyle(color: Palette.muted, height: 1.45),
        ),
        const SizedBox(height: 20),
        TextField(
          controller: _id,
          enabled: !_codeSent,
          keyboardType: TextInputType.emailAddress,
          autocorrect: false,
          decoration: InputDecoration(labelText: tr('Your email'), prefixIcon: const Icon(Icons.email_outlined)),
        ),
        if (!_codeSent) ...[
          const SizedBox(height: 16),
          FilledButton(onPressed: _busy ? null : _send, child: Text(tr('Send code'))),
        ] else ...[
          if (_info != null) ...[
            const SizedBox(height: 12),
            Text(_info!, style: const TextStyle(color: Palette.present, fontWeight: FontWeight.w600)),
          ],
          const SizedBox(height: 14),
          TextField(
            controller: _code,
            keyboardType: TextInputType.number,
            maxLength: 6,
            decoration: InputDecoration(labelText: tr('6 digit code'), prefixIcon: const Icon(Icons.pin_outlined), counterText: ''),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _pass,
            obscureText: true,
            decoration: InputDecoration(labelText: tr('New password (8+ characters)'), prefixIcon: const Icon(Icons.lock_outline)),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _pass2,
            obscureText: true,
            decoration: InputDecoration(labelText: tr('Repeat new password'), prefixIcon: const Icon(Icons.lock_outline)),
          ),
          const SizedBox(height: 16),
          FilledButton(onPressed: _busy ? null : _reset, child: Text(tr('Reset password and sign in'))),
          TextButton(
            onPressed: _busy ? null : () => setState(() => _codeSent = false),
            child: Text(tr('Send a new code')),
          ),
        ],
        if (_error != null) ...[
          const SizedBox(height: 12),
          Text(tr(_error!), style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600)),
        ],
      ]),
    );
  }
}
