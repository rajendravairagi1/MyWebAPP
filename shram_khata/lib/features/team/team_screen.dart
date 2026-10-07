import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';
import '../settings/plan_screen.dart';
import 'role_form_screen.dart';
import 'staff_form_screen.dart';

final _staffProvider = FutureProvider.autoDispose<List<StaffMember>>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(workspaceServiceProvider).staff();
});
final _rolesProvider = FutureProvider.autoDispose<List<Role>>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(workspaceServiceProvider).roles();
});

class TeamScreen extends ConsumerWidget {
  const TeamScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final plan = ref.watch(planProvider);
    if (!plan.hasTeam) {
      return Scaffold(
        appBar: AppBar(title: const Text('Team & roles')),
        body: EmptyState(
          icon: Icons.groups_2_outlined,
          title: 'Teams are on Owner + Team',
          message:
              'On the Solo plan you work alone. Upgrade to add supervisors who mark attendance, accountants who handle payments, and custom roles.',
          action: FilledButton(
            style: FilledButton.styleFrom(minimumSize: const Size(200, 50)),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PlanSettingsScreen())),
            child: const Text('See plans'),
          ),
        ),
      );
    }
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Team & roles'),
          bottom: const TabBar(tabs: [Tab(text: 'People'), Tab(text: 'Roles')]),
        ),
        body: const TabBarView(children: [_People(), _Roles()]),
      ),
    );
  }
}

class _People extends ConsumerWidget {
  const _People();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final staff = ref.watch(_staffProvider);
    final roles = {for (final r in ref.watch(_rolesProvider).value ?? const <Role>[]) r.id: r};
    final me = ref.watch(sessionProvider);
    return Scaffold(
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const StaffFormScreen())),
        icon: const Icon(Icons.person_add_alt_1),
        label: const Text('Add member'),
      ),
      body: staff.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (list) => ListView.separated(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 96),
          itemCount: list.length,
          separatorBuilder: (_, _) => const SizedBox(height: 8),
          itemBuilder: (_, i) {
            final s = list[i];
            final role = roles[s.roleId];
            final isOwner = s.roleId == RoleIds.owner;
            return AppCard(
              onTap: isOwner ? null : () => Navigator.push(context, MaterialPageRoute(builder: (_) => StaffFormScreen(staff: s))),
              child: Row(children: [
                Avatar(name: s.name, size: 44),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text('${s.name}${s.id == me.staff?.id ? ' (you)' : ''}',
                        style: const TextStyle(fontWeight: FontWeight.w800)),
                    Text([if (s.mobile.isNotEmpty) s.mobile, if (s.email.isNotEmpty) s.email].join(' • '),
                        style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                  ]),
                ),
                Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                  Pill(role?.name ?? '—', color: isOwner ? Palette.brand : Palette.info),
                  if (!s.isActive) const Padding(padding: EdgeInsets.only(top: 4), child: Pill('Disabled', color: Palette.absent)),
                ]),
              ]),
            );
          },
        ),
      ),
    );
  }
}

class _Roles extends ConsumerWidget {
  const _Roles();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final roles = ref.watch(_rolesProvider);
    final plan = ref.watch(planProvider);
    return Scaffold(
      floatingActionButton: plan.customRoles
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const RoleFormScreen())),
              icon: const Icon(Icons.add_moderator_outlined),
              label: const Text('New role'),
            )
          : null,
      body: roles.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (list) => ListView.separated(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 96),
          itemCount: list.length,
          separatorBuilder: (_, _) => const SizedBox(height: 8),
          itemBuilder: (_, i) {
            final r = list[i];
            final count = r.permissions.split(',').where((e) => e.isNotEmpty).length;
            return AppCard(
              onTap: r.isSystem ? null : () => Navigator.push(context, MaterialPageRoute(builder: (_) => RoleFormScreen(role: r))),
              child: Row(children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(color: Palette.brandTint, borderRadius: BorderRadius.circular(13)),
                  child: Icon(r.isSystem ? Icons.verified_user_outlined : Icons.tune, color: Palette.brand),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(r.name, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15.5)),
                    Text(r.isSystem ? 'Built-in • $count permissions' : 'Custom • $count permissions',
                        style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                  ]),
                ),
                if (!r.isSystem) const Icon(Icons.chevron_right, color: Palette.muted),
              ]),
            );
          },
        ),
      ),
    );
  }
}
