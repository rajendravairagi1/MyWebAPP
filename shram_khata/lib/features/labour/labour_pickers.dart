import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/files.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/labour_service.dart';
import '../../state/providers.dart';

/// Searchable bottom sheet to choose one labourer.
Future<Labour?> pickLabour(
  BuildContext context, {
  Set<String> exclude = const {},
  String title = 'Choose labour',
  bool includeInactive = false,
}) {
  return showModalBottomSheet<Labour>(
    context: context,
    isScrollControlled: true,
    builder: (_) => _LabourPicker(exclude: exclude, title: title, includeInactive: includeInactive),
  );
}

class _LabourPicker extends ConsumerStatefulWidget {
  const _LabourPicker({required this.exclude, required this.title, required this.includeInactive});
  final Set<String> exclude;
  final String title;
  final bool includeInactive;

  @override
  ConsumerState<_LabourPicker> createState() => _LabourPickerState();
}

class _LabourPickerState extends ConsumerState<_LabourPicker> {
  String _q = '';

  @override
  Widget build(BuildContext context) {
    final statuses = widget.includeInactive
        ? '${LabourStatus.active},${LabourStatus.inactive},${LabourStatus.left}'
        : LabourStatus.active;
    final list = ref.watch(labourListProvider((query: _q, statuses: statuses)));
    final height = MediaQuery.of(context).size.height * 0.78;
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SizedBox(
        height: height,
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 10),
              child: Row(children: [
                Expanded(
                  child: Text(widget.title,
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                ),
              ]),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: TextField(
                autofocus: false,
                onChanged: (v) => setState(() => _q = v),
                decoration: const InputDecoration(
                  hintText: 'Search name, mobile or skill',
                  prefixIcon: Icon(Icons.search),
                ),
              ),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: list.when(
                loading: () => const LoadingView(),
                error: (e, _) => ErrorView(e),
                data: (all) {
                  final rows = all.where((l) => !widget.exclude.contains(l.id)).toList();
                  if (rows.isEmpty) {
                    return const EmptyState(icon: Icons.search_off, title: 'No labour found');
                  }
                  return ListView.builder(
                    itemCount: rows.length,
                    itemBuilder: (_, i) {
                      final l = rows[i];
                      return ListTile(
                        leading: Avatar(name: l.name, image: fileImage(l.photoPath)),
                        title: Text(l.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                        subtitle: Text([l.skill, if (l.mobile.isNotEmpty) l.mobile].join(' • ')),
                        onTap: () => Navigator.pop(context, l),
                      );
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Checklist of every site, grouped by company, for assigning labour.
class SiteChecklist extends ConsumerWidget {
  const SiteChecklist({super.key, required this.selected, required this.onChanged});
  final Set<String> selected;
  final ValueChanged<Set<String>> onChanged;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final branch = ref.watch(scopeBranchProvider);
    final companies = ref.watch(companiesProvider);
    ref.watch(dbTickProvider);
    return FutureBuilder(
      future: ref.watch(companyServiceProvider).sites(branchId: branch),
      builder: (context, snap) {
        final sites = snap.data ?? const <Site>[];
        final comps = {for (final c in companies.value ?? const <Company>[]) c.id: c};
        if (sites.isEmpty) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 8),
                child: Text('Add the company this worker works for, then pick a site.',
                    style: TextStyle(color: Palette.muted)),
              ),
              _addButton(context, ref, null),
            ],
          );
        }
        final byCompany = <String, List<Site>>{};
        for (final s in sites) {
          (byCompany[s.companyId] ??= []).add(s);
        }
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            for (final e in byCompany.entries) ...[
              Padding(
                padding: const EdgeInsets.only(top: 8, bottom: 4),
                child: Text(comps[e.key]?.name ?? '',
                    style: const TextStyle(fontWeight: FontWeight.w700, color: Palette.muted, fontSize: 13)),
              ),
              Wrap(spacing: 8, runSpacing: 8, children: [
                for (final s in e.value)
                  FilterChip(
                    label: Text(s.name),
                    selected: selected.contains(s.id),
                    avatar: selected.contains(s.id) ? Icon(Icons.check, size: 16, color: Palette.brandDark) : null,
                    onSelected: (v) {
                      final next = {...selected};
                      v ? next.add(s.id) : next.remove(s.id);
                      onChanged(next);
                    },
                  ),
              ]),
            ],
            _addButton(context, ref, null),
          ],
        );
      },
    );
  }

  Widget _addButton(BuildContext context, WidgetRef ref, String? companyId) => Align(
        alignment: Alignment.centerLeft,
        child: TextButton.icon(
          onPressed: () async {
            final id = await quickAddCompanySite(context, ref);
            if (id != null) onChanged({...selected, id});
          },
          icon: const Icon(Icons.add),
          label: const Text('Add company / site'),
        ),
      );
}

/// Small dialog to add a site to an existing company, or a new company with
/// its first site. Returns the id of the new site.
Future<String?> quickAddCompanySite(BuildContext context, WidgetRef ref) async {
  final branch = ref.read(writeBranchProvider);
  if (branch == null) return null;
  final svc = ref.read(companyServiceProvider);
  final companies = await svc.companies(branchId: ref.read(scopeBranchProvider));
  if (!context.mounted) return null;
  final company = TextEditingController();
  final site = TextEditingController();
  String? existing = companies.isEmpty ? null : companies.first.id;
  var isNew = companies.isEmpty;
  final key = GlobalKey<FormState>();
  final result = await showDialog<String>(
    context: context,
    builder: (ctx) => StatefulBuilder(
      builder: (ctx, setS) => AlertDialog(
        title: const Text('Add company / site'),
        content: Form(
          key: key,
          child: SingleChildScrollView(
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              if (!isNew)
                DropdownButtonFormField<String>(
                  initialValue: existing,
                  isExpanded: true,
                  decoration: const InputDecoration(labelText: 'Company'),
                  items: [for (final c in companies) DropdownMenuItem(value: c.id, child: Text(c.name))],
                  onChanged: (v) => existing = v,
                )
              else
                TextFormField(
                  controller: company,
                  textCapitalization: TextCapitalization.words,
                  decoration: const InputDecoration(labelText: 'Company name *'),
                  validator: (v) => (v ?? '').trim().isEmpty ? 'Enter the company name' : null,
                ),
              if (companies.isNotEmpty)
                Align(
                  alignment: Alignment.centerLeft,
                  child: TextButton(
                    onPressed: () => setS(() => isNew = !isNew),
                    child: Text(isNew ? 'Use existing company' : '+ New company'),
                  ),
                ),
              TextFormField(
                controller: site,
                textCapitalization: TextCapitalization.words,
                decoration: const InputDecoration(labelText: 'Site / location *'),
                validator: (v) => (v ?? '').trim().isEmpty ? 'Enter the site name' : null,
              ),
            ]),
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          FilledButton(
            onPressed: () async {
              if (!key.currentState!.validate()) return;
              try {
                final cid = isNew
                    ? await svc.addCompany(branchId: branch, name: company.text)
                    : existing!;
                final sid = await svc.addSite(companyId: cid, name: site.text);
                if (ctx.mounted) Navigator.pop(ctx, sid);
              } catch (e) {
                if (ctx.mounted) showError(ctx, e);
              }
            },
            child: const Text('Add'),
          ),
        ],
      ),
    ),
  );
  company.dispose();
  site.dispose();
  return result;
}
