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

  /// Wipes the phone after the owner types ERASE.
  Future<void> _erase(BuildContext context) async {
    final word = TextEditingController();
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(tr('Erase all data on this phone?')),
        content: Column(mainAxisSize: MainAxisSize.min, children: [
          Text(tr('Every worker, attendance, payment and bill on this phone is deleted and you are signed out. '
              'If cloud sync is on, the online copy stays and you can sign in again to get it back. '
              'To delete the online copy too, use More → Cloud sync first.')),
          const SizedBox(height: 12),
          TextField(controller: word, decoration: InputDecoration(labelText: tr('Type ERASE to confirm'))),
        ]),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: Text(tr('Cancel'))),
          TextButton(
            onPressed: () => Navigator.pop(ctx, word.text.trim().toUpperCase() == 'ERASE'),
            style: TextButton.styleFrom(foregroundColor: Palette.absent),
            child: Text(tr('Erase everything')),
          ),
        ],
      ),
    );
    word.dispose();
    if (ok != true) return;
    final cloud = ref.read(syncServiceProvider);
    if (cloud.session != null) {
      try {
        await cloud.signOut();
      } catch (_) {}
      ref.read(cloudProvider.notifier).refreshSession();
    }
    await ref.read(sessionProvider.notifier).eraseEverything();
    if (context.mounted) Navigator.of(context).popUntil((r) => r.isFirst);
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
        SectionTitle(tr('APP LOCK'), padding: const EdgeInsets.fromLTRB(4, 22, 4, 4)),
        SwitchListTile(
          contentPadding: EdgeInsets.zero,
          title: Text(tr('Ask password when the app opens')),
          subtitle: Text(tr('Also after the app was left for 2 minutes. Keeps your data private if someone picks up your phone.')),
          value: ref.watch(appLockProvider).enabled,
          onChanged: (v) => ref.read(appLockProvider.notifier).setEnabled(v),
        ),
        SectionTitle(tr('CHANGE PASSWORD / PIN'), padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
        TextField(controller: _old, obscureText: true, decoration: InputDecoration(labelText: tr('Current'))),
        const SizedBox(height: 10),
        TextField(controller: _new, obscureText: true, decoration: InputDecoration(labelText: tr('New'))),
        const SizedBox(height: 10),
        TextField(controller: _confirm, obscureText: true, decoration: InputDecoration(labelText: tr('Confirm new'))),
        const SizedBox(height: 16),
        FilledButton(onPressed: _change, child: Text(tr('Update'))),
        SectionTitle(tr('SIGN OUT'), padding: const EdgeInsets.fromLTRB(4, 28, 4, 8)),
        OutlinedButton.icon(
          onPressed: () => ref.read(sessionProvider.notifier).logout().then((_) {
            if (context.mounted) Navigator.of(context).popUntil((r) => r.isFirst);
          }),
          icon: const Icon(Icons.logout),
          label: Text(tr('Sign out of this phone')),
        ),
        if (s.isOwner) ...[
          const SizedBox(height: 10),
          TextButton.icon(
            onPressed: () => _erase(context),
            icon: const Icon(Icons.delete_forever_outlined, color: Palette.absent),
            label: Text(tr('Erase all data on this phone'), style: const TextStyle(color: Palette.absent)),
          ),
        ],
      ]),
    );
  }
}
