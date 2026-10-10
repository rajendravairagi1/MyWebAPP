import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/countries.dart';
import '../../core/permissions.dart';
import '../../core/phone.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';
import '../labour/labour_pickers.dart';

class StaffFormScreen extends ConsumerStatefulWidget {
  const StaffFormScreen({super.key, this.staff});
  final StaffMember? staff;

  @override
  ConsumerState<StaffFormScreen> createState() => _StaffFormScreenState();
}

class _StaffFormScreenState extends ConsumerState<StaffFormScreen> {
  late final _name = TextEditingController(text: widget.staff?.name);
  late final _mobile = TextEditingController(text: widget.staff?.mobile);
  late final _email = TextEditingController(text: widget.staff?.email);
  final _pin = TextEditingController();
  String? _roleId;
  late final Set<String> _branches = (widget.staff?.branchIds ?? '').split(',').where((e) => e.isNotEmpty).toSet();
  late Set<String> _sites = (widget.staff?.siteIds ?? '').split(',').where((e) => e.isNotEmpty).toSet();
  late bool _active = widget.staff?.isActive ?? true;
  List<Role> _roles = [];
  bool _busy = false;

  bool get _editing => widget.staff != null;

  @override
  void initState() {
    super.initState();
    _roleId = widget.staff?.roleId;
    ref.read(workspaceServiceProvider).roles().then((r) {
      if (!mounted) return;
      setState(() {
        _roles = r.where((x) => x.id != RoleIds.owner).toList();
        _roleId ??= RoleIds.supervisor;
      });
    });
  }

  @override
  void dispose() {
    for (final c in [_name, _mobile, _email, _pin]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    setState(() => _busy = true);
    try {
      final svc = ref.read(workspaceServiceProvider);
      if (_editing) {
        await svc.updateStaff(
          widget.staff!.id,
          name: _name.text,
          mobile: _mobile.text,
          roleId: _roleId,
          pin: _pin.text,
          isActive: _active,
          branchIds: _branches.toList(),
          siteIds: _sites.toList(),
        );
      } else {
        await svc.addStaff(
          name: _name.text,
          mobile: _mobile.text,
          email: _email.text,
          roleId: _roleId!,
          pin: _pin.text,
          branchIds: _branches.toList(),
          siteIds: _sites.toList(),
        );
      }
      await ref.read(sessionProvider.notifier).reload();
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final country = ref.watch(profileProvider).value?.country ?? Countries.india;
    final plan = ref.watch(planProvider);
    final branches = ref.watch(branchesProvider).value ?? const <Branch>[];
    return Scaffold(
      appBar: AppBar(title: Text(_editing ? 'Edit member' : 'Add member')),
      body: FormBody(
        bottom: FilledButton(onPressed: _busy ? null : _save, child: const Text('Save')),
        children: [
          TextField(
            controller: _name,
            textCapitalization: TextCapitalization.words,
            decoration: const InputDecoration(labelText: 'Name'),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _mobile,
            keyboardType: TextInputType.phone,
            inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(Phone.maxLength(country))],
            decoration: InputDecoration(labelText: 'Mobile (used to sign in)', prefixText: Phone.prefix(country)),
          ),
          if (!_editing) ...[
            const SizedBox(height: 12),
            TextField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: 'Email (optional)'),
            ),
          ],
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            initialValue: _roles.any((r) => r.id == _roleId) ? _roleId : null,
            decoration: const InputDecoration(labelText: 'Role'),
            items: [for (final r in _roles) DropdownMenuItem(value: r.id, child: Text(r.name))],
            onChanged: (v) => setState(() => _roleId = v),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _pin,
            keyboardType: TextInputType.number,
            obscureText: true,
            inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(8)],
            decoration: InputDecoration(
              labelText: _editing ? 'New PIN (leave empty to keep)' : 'PIN (4–8 digits)',
              helperText: 'They sign in with their mobile number and this PIN.',
            ),
          ),
          if (plan.multiBranch && branches.length > 1) ...[
            const SectionTitle('BRANCHES', padding: EdgeInsets.fromLTRB(2, 20, 2, 4)),
            const Text('Leave all unselected for access to every branch.',
                style: TextStyle(color: Palette.muted, fontSize: 12.5)),
            const SizedBox(height: 8),
            Wrap(spacing: 8, runSpacing: 8, children: [
              for (final b in branches)
                FilterChip(
                  label: Text(b.name),
                  selected: _branches.contains(b.id),
                  onSelected: (v) => setState(() => v ? _branches.add(b.id) : _branches.remove(b.id)),
                ),
            ]),
          ],
          const SectionTitle('LIMIT TO SITES', padding: EdgeInsets.fromLTRB(2, 20, 2, 4)),
          const Text('For supervisors: they only see these sites. Leave empty for all sites.',
              style: TextStyle(color: Palette.muted, fontSize: 12.5)),
          SiteChecklist(selected: _sites, onChanged: (v) => setState(() => _sites = v)),
          if (_editing) ...[
            const SizedBox(height: 12),
            SwitchListTile(
              contentPadding: EdgeInsets.zero,
              title: const Text('Can sign in'),
              subtitle: const Text('Turn off when someone leaves. Their past entries stay.'),
              value: _active,
              activeThumbColor: Palette.brand,
              onChanged: (v) => setState(() => _active = v),
            ),
          ],
        ],
      ),
    );
  }
}
