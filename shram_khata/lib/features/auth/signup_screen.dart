import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/plans.dart';
import '../../core/security.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';

class SignUpScreen extends ConsumerStatefulWidget {
  const SignUpScreen({super.key, required this.plan});
  final Plan plan;

  @override
  ConsumerState<SignUpScreen> createState() => _SignUpScreenState();
}

class _SignUpScreenState extends ConsumerState<SignUpScreen> {
  final _form = GlobalKey<FormState>();
  final _business = TextEditingController();
  final _owner = TextEditingController();
  final _email = TextEditingController();
  final _mobile = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  bool _hide = true;
  bool _busy = false;

  @override
  void dispose() {
    for (final c in [_business, _owner, _email, _mobile, _password, _confirm]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _busy = true);
    try {
      await ref.read(sessionProvider.notifier).signUp(
            plan: widget.plan,
            businessName: _business.text,
            ownerName: _owner.text,
            email: _email.text,
            mobile: _mobile.text,
            password: _password.text,
          );
      if (mounted) Navigator.popUntil(context, (r) => r.isFirst);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(tr('Create your account'))),
      body: Form(
        key: _form,
        child: FormBody(
          children: [
            Row(children: [
              Pill(tr('Plan'), color: Palette.muted),
              const SizedBox(width: 8),
              Text(tr(widget.plan.title), style: const TextStyle(fontWeight: FontWeight.w700)),
            ]),
            const SizedBox(height: 16),
            TextFormField(
              controller: _business,
              textCapitalization: TextCapitalization.words,
              decoration: InputDecoration(
                labelText: tr('Agency / business name'),
                prefixIcon: Icon(Icons.storefront_outlined),
              ),
              validator: (v) => (v ?? '').trim().isEmpty ? tr('Enter your business name') : null,
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _owner,
              textCapitalization: TextCapitalization.words,
              decoration: InputDecoration(
                labelText: tr('Your name'),
                prefixIcon: Icon(Icons.person_outline),
              ),
              validator: (v) => (v ?? '').trim().isEmpty ? tr('Enter your name') : null,
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              autocorrect: false,
              decoration: InputDecoration(
                labelText: tr('Email'),
                prefixIcon: Icon(Icons.mail_outline),
              ),
              validator: (v) => Security.isEmail(v ?? '') ? null : tr('Enter a valid email'),
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _mobile,
              keyboardType: TextInputType.phone,
              inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(15)],
              decoration: InputDecoration(
                labelText: tr('Mobile number'),
                prefixIcon: Icon(Icons.phone_outlined),
              ),
              validator: (v) => (v ?? '').length >= 7 ? null : tr('Enter your mobile number'),
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _password,
              obscureText: _hide,
              decoration: InputDecoration(
                labelText: tr('Password'),
                prefixIcon: const Icon(Icons.lock_outline),
                suffixIcon: IconButton(
                  icon: Icon(_hide ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                  onPressed: () => setState(() => _hide = !_hide),
                ),
              ),
              validator: (v) => (v ?? '').length < 6 ? tr('Use at least 6 characters') : null,
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _confirm,
              obscureText: _hide,
              decoration: InputDecoration(
                labelText: tr('Confirm password'),
                prefixIcon: Icon(Icons.lock_outline),
              ),
              validator: (v) => v == _password.text ? null : tr('Passwords do not match'),
            ),
            const SizedBox(height: 18),
            AppCard(
              color: Palette.infoBg,
              padding: const EdgeInsets.all(12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.info_outline, size: 18, color: Palette.info),
                  SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      tr('Your data is stored on this phone. Email verification, password recovery and backup across phones come with cloud sync. Please remember your password.'),
                      style: TextStyle(fontSize: 12.5, height: 1.4, color: Palette.info),
                    ),
                  ),
                ],
              ),
            ),
          ],
          bottom: FilledButton(
            onPressed: _busy ? null : _submit,
            child: _busy
                ? const SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white),
                  )
                : Text(tr('Create account')),
          ),
        ),
      ),
    );
  }
}
