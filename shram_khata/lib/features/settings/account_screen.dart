import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';

class AccountScreen extends ConsumerStatefulWidget {
  const AccountScreen({super.key});

  @override
  ConsumerState<AccountScreen> createState() => _AccountScreenState();
}

class _AccountScreenState extends ConsumerState<AccountScreen> {
  final _old = TextEditingController();
  final _new = TextEditingController();
  final _confirm = TextEditingController();

  @override
  void dispose() {
    _old.dispose();
    _new.dispose();
    _confirm.dispose();
    super.dispose();
  }

  Future<void> _change() async {
    final s = ref.read(sessionProvider);
    if (_new.text != _confirm.text) return showError(context, tr('New entries do not match'));
    try {
      final ok = await ref.read(workspaceServiceProvider).changeOwnSecret(s.staff!.id, _old.text, _new.text);
      if (!mounted) return;
      if (!ok) return showError(context, tr('Current password / PIN is wrong'));
      final cloud = ref.read(cloudProvider).session;
      if (cloud != null && !cloud.isManager) {
        // Best effort: keep the cloud password the same as the one on this phone.
        try {
          await ref.read(syncServiceProvider).changePassword(_old.text, _new.text);
        } catch (_) {}
      }
      if (!mounted) return;
      _old.clear();
      _new.clear();
      _confirm.clear();
      showInfo(context, tr('Updated'));
    } catch (e) {
      if (mounted) showError(context, e);
    }
  }

  @override
  Widget build(BuildContext context) {
    final s = ref.watch(sessionProvider);
    final staff = s.staff!;
    return Scaffold(
      appBar: AppBar(title: Text(tr('Account & security'))),
      body: ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 32), children: [
        AppCard(
          child: Row(children: [
            Avatar(name: staff.name, size: 52),
            const SizedBox(width: 14),
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(staff.name, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                Text(s.role?.name ?? '', style: const TextStyle(color: Palette.muted)),
                if (staff.email.isNotEmpty) Text(staff.email, style: const TextStyle(fontSize: 13)),
                if (staff.mobile.isNotEmpty) Text(staff.mobile, style: const TextStyle(fontSize: 13)),
              ]),
            ),
          ]),
        ),
        if (staff.email.isNotEmpty) ...[
          const SizedBox(height: 10),
          AppCard(
            color: Palette.infoBg,
            padding: const EdgeInsets.all(12),
            child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Icon(Icons.mark_email_unread_outlined, color: Palette.info, size: 20),
              SizedBox(width: 10),
              Expanded(
                child: Text(
                  tr('Email verification is pending. It turns on when cloud sync is connected, which also enables password reset by email and using the app on more than one phone.'),
                  style: TextStyle(color: Palette.info, fontSize: 12.5, height: 1.4),
                ),
              ),
            ]),
          ),
        ],
        SectionTitle(tr('CHANGE PASSWORD / PIN'), padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
        TextField(controller: _old, obscureText: true, decoration: InputDecoration(labelText: tr('Current'))),
        const SizedBox(height: 10),
        TextField(controller: _new, obscureText: true, decoration: InputDecoration(labelText: tr('New'))),
        const SizedBox(height: 10),
        TextField(controller: _confirm, obscureText: true, decoration: InputDecoration(labelText: tr('Confirm new'))),
        const SizedBox(height: 16),
        FilledButton(onPressed: _change, child: Text(tr('Update'))),
      ]),
    );
  }
}
