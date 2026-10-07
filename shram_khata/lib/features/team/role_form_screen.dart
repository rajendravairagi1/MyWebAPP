import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';

/// Create or edit a custom role by switching permissions on and off.
class RoleFormScreen extends ConsumerStatefulWidget {
  const RoleFormScreen({super.key, this.role});
  final Role? role;

  @override
  ConsumerState<RoleFormScreen> createState() => _RoleFormScreenState();
}

class _RoleFormScreenState extends ConsumerState<RoleFormScreen> {
  late final _name = TextEditingController(text: widget.role?.name);
  late final Set<String> _perms =
      (widget.role?.permissions ?? Perm.supervisor.join(',')).split(',').where((e) => e.isNotEmpty).toSet();

  @override
  void dispose() {
    _name.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    try {
      await ref.read(workspaceServiceProvider).saveRole(
            id: widget.role?.id,
            name: _name.text,
            permissions: _perms.toList(),
          );
      await ref.read(sessionProvider.notifier).reload();
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    }
  }

  void _toggle(String key, bool on) {
    setState(() {
      on ? _perms.add(key) : _perms.remove(key);
      // Marking requires viewing; keep dependent permissions consistent.
      if (on && key == Perm.attendanceMark) _perms.add(Perm.attendanceView);
      if (on && key == Perm.paymentsManage) _perms.add(Perm.paymentsView);
      if (on && key == Perm.labourManage) _perms.add(Perm.labourView);
      if (on && key == Perm.companiesManage) _perms.add(Perm.companiesView);
      if (on && key == Perm.billingManage) _perms.add(Perm.billingView);
      if (!on && key == Perm.attendanceView) _perms.removeAll([Perm.attendanceMark, Perm.attendanceEditLocked]);
      if (!on && key == Perm.paymentsView) _perms.remove(Perm.paymentsManage);
      if (!on && key == Perm.labourView) _perms.removeAll([Perm.labourManage, Perm.labourDocs]);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.role == null ? 'New role' : 'Edit role'),
        actions: [
          if (widget.role != null)
            IconButton(
              icon: const Icon(Icons.delete_outline),
              onPressed: () async {
                final ok = await confirm(context,
                    title: 'Delete ${widget.role!.name}?',
                    message: 'Only possible when nobody has this role.',
                    confirmLabel: 'Delete',
                    danger: true);
                if (!ok) return;
                try {
                  await ref.read(workspaceServiceProvider).deleteRole(widget.role!.id);
                  if (context.mounted) Navigator.pop(context);
                } catch (e) {
                  if (context.mounted) showError(context, e);
                }
              },
            ),
        ],
      ),
      body: FormBody(
        bottom: FilledButton(onPressed: _save, child: const Text('Save role')),
        children: [
          TextField(
            controller: _name,
            textCapitalization: TextCapitalization.words,
            decoration: const InputDecoration(labelText: 'Role name', hintText: 'e.g. Site manager'),
          ),
          for (final g in Perm.groups.entries) ...[
            SectionTitle(g.key.toUpperCase(), padding: const EdgeInsets.fromLTRB(2, 20, 2, 6)),
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(children: [
                for (var i = 0; i < g.value.length; i++) ...[
                  SwitchListTile(
                    title: Text(g.value[i].$2),
                    value: _perms.contains(g.value[i].$1),
                    activeThumbColor: Palette.brand,
                    onChanged: (v) => _toggle(g.value[i].$1, v),
                  ),
                  if (i < g.value.length - 1) const Divider(indent: 16),
                ],
              ]),
            ),
          ],
        ],
      ),
    );
  }
}
