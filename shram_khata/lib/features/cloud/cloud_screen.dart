import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/base.dart' show newId;
import '../../data/sync/sync_service.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';
import 'feedback_screen.dart';

/// Turn cloud sync on, see its status, and manage cloud logins.
class CloudScreen extends ConsumerWidget {
  const CloudScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final cloud = ref.watch(cloudProvider);
    final session = ref.watch(sessionProvider);
    return Scaffold(
      appBar: AppBar(title: Text(tr('Cloud sync'))),
      body: cloud.connected
          ? _Connected(cloud: cloud)
          : session.isOwner
              ? const _Connect()
              : Padding(
                  padding: const EdgeInsets.all(24),
                  child: Text(tr('Only the owner can turn on cloud sync.'), style: const TextStyle(color: Palette.muted)),
                ),
    );
  }
}

class _Connect extends ConsumerStatefulWidget {
  const _Connect();

  @override
  ConsumerState<_Connect> createState() => _ConnectState();
}

class _ConnectState extends ConsumerState<_Connect> {
  late final _email = TextEditingController(text: ref.read(sessionProvider).staff?.email);
  late final _mobile = TextEditingController(text: ref.read(sessionProvider).staff?.mobile);
  final _pass = TextEditingController();
  final _pass2 = TextEditingController();
  bool _busy = false;
  String? _error;

  @override
  void dispose() {
    for (final c in [_email, _mobile, _pass, _pass2]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _connect() async {
    if (_email.text.trim().isEmpty && _mobile.text.trim().isEmpty) {
      return setState(() => _error = tr('Enter an email or a mobile number.'));
    }
    if (_pass.text.length < 8) return setState(() => _error = tr('Password must be at least 8 characters.'));
    if (_pass.text != _pass2.text) return setState(() => _error = tr('The two passwords do not match.'));
    setState(() {
      _busy = true;
      _error = null;
    });
    try {
      final me = ref.read(sessionProvider).staff!;
      final profile = await ref.read(profileProvider.future);
      final svc = ref.read(syncServiceProvider);
      await svc.register(
        businessName: profile.name.isEmpty ? me.name : profile.name,
        ownerName: me.name,
        email: _email.text.trim(),
        mobile: _mobile.text.trim(),
        password: _pass.text,
        ownerId: me.id,
        agencyId: newId(),
        planId: profile.planId,
      );
      ref.read(cloudProvider.notifier).refreshSession();
      await ref.read(cloudProvider.notifier).syncNow();
    } catch (e) {
      if (mounted) setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 24), children: [
      AppCard(
        color: Palette.brandTint,
        child: Text(
          tr('Cloud sync keeps your data safe online and lets your supervisors and company managers use their own phones. '
              'Everything you have entered so far is uploaded. The app keeps working without internet.'),
          style: const TextStyle(height: 1.45),
        ),
      ),
      const SizedBox(height: 18),
      TextField(
        controller: _email,
        keyboardType: TextInputType.emailAddress,
        autocorrect: false,
        decoration: InputDecoration(labelText: tr('Email')),
      ),
      const SizedBox(height: 12),
      TextField(
        controller: _mobile,
        keyboardType: TextInputType.phone,
        decoration: InputDecoration(labelText: tr('Mobile')),
      ),
      const SizedBox(height: 12),
      TextField(
        controller: _pass,
        obscureText: true,
        decoration: InputDecoration(labelText: tr('Choose a cloud password (8+ characters)')),
      ),
      const SizedBox(height: 12),
      TextField(
        controller: _pass2,
        obscureText: true,
        decoration: InputDecoration(labelText: tr('Repeat password')),
      ),
      if (_error != null) ...[
        const SizedBox(height: 12),
        Text(tr(_error!), style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600)),
      ],
      const SizedBox(height: 20),
      FilledButton(
        onPressed: _busy ? null : _connect,
        child: _busy
            ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white))
            : Text(tr('Turn on cloud sync')),
      ),
      const SizedBox(height: 10),
      Text(
        tr('Use this email or mobile and password to sign in on another phone.'),
        textAlign: TextAlign.center,
        style: const TextStyle(color: Palette.muted, fontSize: 12.5),
      ),
    ]);
  }
}

class _Connected extends ConsumerWidget {
  const _Connected({required this.cloud});
  final CloudState cloud;

  String _ago(DateTime? t) {
    if (t == null) return tr('Not yet');
    final d = DateTime.now().difference(t);
    if (d.inMinutes < 1) return tr('Just now');
    if (d.inHours < 1) return trf('{n} min ago', {'n': d.inMinutes});
    if (d.inDays < 1) return trf('{n} h ago', {'n': d.inHours});
    return trf('{n} days ago', {'n': d.inDays});
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final s = cloud.session!;
    final notifier = ref.read(cloudProvider.notifier);
    return ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 24), children: [
      AppCard(
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Icon(cloud.error == null ? Icons.cloud_done_outlined : Icons.cloud_off_outlined,
                color: cloud.error == null ? Palette.present : Palette.absent),
            const SizedBox(width: 10),
            Expanded(
              child: Text(cloud.error == null ? tr('Connected') : tr('Not connected right now'),
                  style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
            ),
          ]),
          const SizedBox(height: 10),
          Text(trf('Signed in as {name}', {'name': s.name}), style: const TextStyle(color: Palette.muted)),
          const SizedBox(height: 4),
          Text(trf('Last sync: {when}', {'when': _ago(cloud.lastSync)}), style: const TextStyle(color: Palette.muted)),
          if (cloud.unsent > 0) ...[
            const SizedBox(height: 4),
            Text(trf('{n} changes waiting to upload', {'n': cloud.unsent}), style: const TextStyle(color: Palette.half)),
          ],
          if (cloud.error != null) ...[
            const SizedBox(height: 8),
            Text(tr(cloud.error!), style: const TextStyle(color: Palette.absent)),
          ],
          const SizedBox(height: 14),
          OutlinedButton.icon(
            onPressed: cloud.syncing ? null : notifier.syncNow,
            icon: cloud.syncing
                ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2))
                : const Icon(Icons.sync),
            label: Text(cloud.syncing ? tr('Syncing...') : tr('Sync now')),
          ),
        ]),
      ),
      if (!s.isManager) ...[
        const SizedBox(height: 14),
        GroupCard(children: [
          MenuTile(
            icon: Icons.forum_outlined,
            title: tr('Messages from companies'),
            subtitle: cloud.unread > 0 ? trf('{n} new', {'n': cloud.unread}) : tr('Feedback from company managers'),
            color: cloud.unread > 0 ? Palette.absent : null,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FeedbackScreen())),
          ),
        ]),
      ],
      if (s.isOwner) ...[
        SectionTitle(tr('CLOUD LOGINS'), padding: const EdgeInsets.fromLTRB(4, 22, 4, 8)),
        const _People(),
      ],
      const SizedBox(height: 22),
      TextButton.icon(
        onPressed: () async {
          final ok = await confirm(
            context,
            title: tr('Sign out of cloud?'),
            message: tr('This phone stops syncing. Your data stays on this phone. You can sign in again later.'),
            confirmLabel: tr('Sign out'),
            danger: true,
          );
          if (!ok) return;
          await ref.read(syncServiceProvider).signOut();
          notifier.refreshSession();
        },
        icon: const Icon(Icons.logout, color: Palette.absent),
        label: Text(tr('Sign out of cloud on this phone'), style: const TextStyle(color: Palette.absent)),
      ),
      if (s.isOwner)
        TextButton.icon(
          onPressed: () => _deleteAccount(context, ref),
          icon: const Icon(Icons.delete_forever_outlined, color: Palette.absent),
          label: Text(tr('Delete my cloud account and data'), style: const TextStyle(color: Palette.absent)),
        ),
    ]);
  }
}

final cloudPeopleProvider = FutureProvider.autoDispose<List<CloudPerson>>((ref) async {
  return ref.watch(syncServiceProvider).people();
});

class _People extends ConsumerWidget {
  const _People();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final people = ref.watch(cloudPeopleProvider);
    return people.when(
      loading: () => const Padding(padding: EdgeInsets.all(20), child: LoadingView()),
      error: (e, _) => AppCard(child: Text(tr(e.toString()), style: const TextStyle(color: Palette.muted))),
      data: (list) => Column(children: [
        AppCard(
          padding: EdgeInsets.zero,
          child: Column(children: [
            for (var i = 0; i < list.length; i++) ...[
              ListTile(
                title: Text(list[i].name, style: const TextStyle(fontWeight: FontWeight.w700)),
                subtitle: Text([
                  tr(switch (list[i].kind) { 'owner' => 'Owner', 'manager' => 'Company manager', _ => 'Team member' }),
                  list[i].email ?? list[i].mobile ?? '',
                ].where((e) => e.isNotEmpty).join(' • ')),
                trailing: list[i].kind == 'owner'
                    ? null
                    : Switch(
                        value: list[i].isActive,
                        onChanged: (v) async {
                          try {
                            await ref.read(syncServiceProvider).updatePerson(list[i].id, isActive: v);
                            ref.invalidate(cloudPeopleProvider);
                          } catch (e) {
                            if (context.mounted) showError(context, e);
                          }
                        },
                      ),
              ),
              if (i < list.length - 1) const Divider(indent: 16),
            ],
          ]),
        ),
        const SizedBox(height: 10),
        OutlinedButton.icon(
          onPressed: () => addManager(context, ref),
          icon: const Icon(Icons.add_business_outlined),
          label: Text(tr('Add company manager login')),
        ),
      ]),
    );
  }
}

/// Dialog: give a company's manager a login that shows only that company.
Future<void> addManager(BuildContext context, WidgetRef ref) async {
  final companies = await ref.read(companiesProvider.future);
  if (!context.mounted) return;
  if (companies.isEmpty) {
    showInfo(context, tr('Add a company first.'));
    return;
  }
  final name = TextEditingController();
  final email = TextEditingController();
  final mobile = TextEditingController();
  final pass = TextEditingController();
  var company = companies.first.id;
  String? error;
  final created = await showDialog<bool>(
    context: context,
    builder: (ctx) => StatefulBuilder(
      builder: (ctx, setS) => AlertDialog(
        title: Text(tr('Company manager login')),
        content: SingleChildScrollView(
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Text(tr('The manager sees only this company: workers, attendance and the bill. Not what you pay your workers.'),
                style: const TextStyle(color: Palette.muted, fontSize: 13)),
            const SizedBox(height: 12),
            DropdownButtonFormField<String>(
              initialValue: company,
              isExpanded: true,
              decoration: InputDecoration(labelText: tr('Company')),
              items: [for (final c in companies) DropdownMenuItem(value: c.id, child: Text(c.name))],
              onChanged: (v) => company = v ?? company,
            ),
            const SizedBox(height: 10),
            TextField(controller: name, textCapitalization: TextCapitalization.words, decoration: InputDecoration(labelText: tr('Manager name'))),
            const SizedBox(height: 10),
            TextField(controller: email, keyboardType: TextInputType.emailAddress, decoration: InputDecoration(labelText: tr('Email'))),
            const SizedBox(height: 10),
            TextField(controller: mobile, keyboardType: TextInputType.phone, decoration: InputDecoration(labelText: tr('Mobile'))),
            const SizedBox(height: 10),
            TextField(controller: pass, decoration: InputDecoration(labelText: tr('Password (8+ characters)'))),
            if (error != null) ...[
              const SizedBox(height: 8),
              Text(tr(error!), style: const TextStyle(color: Palette.absent)),
            ],
          ]),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: Text(tr('Cancel'))),
          FilledButton(
            onPressed: () async {
              if (name.text.trim().isEmpty) return setS(() => error = tr('Enter the manager name.'));
              if (email.text.trim().isEmpty && mobile.text.trim().isEmpty) {
                return setS(() => error = tr('Enter an email or a mobile number.'));
              }
              if (pass.text.length < 8) return setS(() => error = tr('Password must be at least 8 characters.'));
              try {
                await ref.read(syncServiceProvider).createPerson(
                      id: newId(),
                      name: name.text.trim(),
                      kind: 'manager',
                      password: pass.text,
                      email: email.text.trim(),
                      mobile: mobile.text.trim(),
                      scopeCompanyId: company,
                    );
                if (ctx.mounted) Navigator.pop(ctx, true);
              } catch (e) {
                setS(() => error = e.toString());
              }
            },
            child: Text(tr('Create login')),
          ),
        ],
      ),
    ),
  );
  for (final c in [name, email, mobile, pass]) {
    c.dispose();
  }
  if (created == true) {
    ref.invalidate(cloudPeopleProvider);
    if (context.mounted) showInfo(context, tr('Login created. Share the email / mobile and password with the manager.'));
  }
}

/// Owner: erases everything stored online. The data on the phones stays.
Future<void> _deleteAccount(BuildContext context, WidgetRef ref) async {
  final pass = TextEditingController();
  final ok = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      title: Text(tr('Delete cloud account?')),
      content: Column(mainAxisSize: MainAxisSize.min, children: [
        Text(tr('Your data stored online, all cloud logins and company manager messages are erased for good. '
            'The data on your phones stays, but phones stop syncing with each other.')),
        const SizedBox(height: 12),
        TextField(controller: pass, obscureText: true, decoration: InputDecoration(labelText: tr('Your cloud password'))),
      ]),
      actions: [
        TextButton(onPressed: () => Navigator.pop(ctx, false), child: Text(tr('Cancel'))),
        TextButton(
          onPressed: () => Navigator.pop(ctx, true),
          style: TextButton.styleFrom(foregroundColor: Palette.absent),
          child: Text(tr('Delete everything online')),
        ),
      ],
    ),
  );
  final password = pass.text;
  pass.dispose();
  if (ok != true) return;
  try {
    await ref.read(syncServiceProvider).deleteCloudAccount(password);
    ref.read(cloudProvider.notifier).refreshSession();
    if (context.mounted) showInfo(context, tr('Your cloud account was deleted.'));
  } catch (e) {
    if (context.mounted) showError(context, e);
  }
}
