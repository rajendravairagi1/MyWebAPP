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
    return FutureBuilder(
      future: ref.watch(companyServiceProvider).sites(branchId: branch),
      builder: (context, snap) {
        final sites = snap.data ?? const <Site>[];
        final comps = {for (final c in companies.value ?? const <Company>[]) c.id: c};
        if (sites.isEmpty) {
          return const Padding(
            padding: EdgeInsets.symmetric(vertical: 8),
            child: Text('No sites yet. Add companies and sites from More → Companies.',
                style: TextStyle(color: Palette.muted)),
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
                    avatar: selected.contains(s.id) ? const Icon(Icons.check, size: 16, color: Palette.brandDark) : null,
                    onSelected: (v) {
                      final next = {...selected};
                      v ? next.add(s.id) : next.remove(s.id);
                      onChanged(next);
                    },
                  ),
              ]),
            ],
          ],
        );
      },
    );
  }
}
