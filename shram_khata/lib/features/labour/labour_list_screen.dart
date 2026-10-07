import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/files.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/labour_service.dart';
import '../../state/providers.dart';
import 'labour_detail_screen.dart';
import 'labour_form_screen.dart';

class LabourListScreen extends ConsumerStatefulWidget {
  const LabourListScreen({super.key, this.standalone = false});
  final bool standalone;

  @override
  ConsumerState<LabourListScreen> createState() => _LabourListScreenState();
}

class _LabourListScreenState extends ConsumerState<LabourListScreen> {
  String _q = '';
  String _status = LabourStatus.active;
  String? _skill;

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionProvider);
    final list = ref.watch(labourListProvider((query: _q, statuses: _status)));
    final balances = session.can(Perm.paymentsView) ? ref.watch(balancesProvider).value : null;
    return Scaffold(
      appBar: AppBar(title: const Text('Labour')),
      floatingActionButton: session.can(Perm.labourManage)
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.push(
                  context, MaterialPageRoute(builder: (_) => const LabourFormScreen())),
              icon: const Icon(Icons.person_add_alt_1),
              label: const Text('Add labour'),
            )
          : null,
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
            child: TextField(
              onChanged: (v) => setState(() => _q = v),
              decoration: const InputDecoration(
                hintText: 'Search name, mobile or skill',
                prefixIcon: Icon(Icons.search),
              ),
            ),
          ),
          SizedBox(
            height: 42,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                for (final s in LabourStatus.all)
                  Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                      label: Text(LabourStatus.label(s)),
                      selected: _status == s,
                      onSelected: (_) => setState(() => _status = s),
                    ),
                  ),
                if (_skill != null)
                  InputChip(
                    label: Text(_skill!),
                    onDeleted: () => setState(() => _skill = null),
                  ),
              ],
            ),
          ),
          Expanded(
            child: list.when(
              loading: () => const LoadingView(),
              error: (e, _) => ErrorView(e),
              data: (all) {
                final rows = _skill == null ? all : all.where((l) => l.skill == _skill).toList();
                final skills = {for (final l in all) l.skill}.toList()..sort();
                if (all.isEmpty) {
                  return EmptyState(
                    icon: Icons.groups_outlined,
                    title: _q.isEmpty ? 'No ${LabourStatus.label(_status).toLowerCase()} labour' : 'No match',
                    message: _q.isEmpty && _status == LabourStatus.active
                        ? 'Add your first labourer to start taking attendance.'
                        : null,
                  );
                }
                return ListView(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 96),
                  children: [
                    if (skills.length > 1)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: Wrap(spacing: 6, runSpacing: 6, children: [
                          for (final s in skills)
                            ActionChip(
                              label: Text(s, style: const TextStyle(fontSize: 12)),
                              visualDensity: VisualDensity.compact,
                              backgroundColor: _skill == s ? Palette.brandTint : null,
                              onPressed: () => setState(() => _skill = _skill == s ? null : s),
                            ),
                        ]),
                      ),
                    Padding(
                      padding: const EdgeInsets.only(bottom: 6, left: 4),
                      child: Text('${rows.length} ${rows.length == 1 ? 'person' : 'people'}',
                          style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                    ),
                    for (final l in rows)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: AppCard(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                          onTap: () => Navigator.push(
                            context,
                            MaterialPageRoute(builder: (_) => LabourDetailScreen(labourId: l.id)),
                          ),
                          child: Row(children: [
                            Avatar(name: l.name, image: fileImage(l.photoPath), size: 46),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Text(l.name,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15.5)),
                                Text([l.skill, if (l.mobile.isNotEmpty) l.mobile].join(' • '),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(color: Palette.muted, fontSize: 13)),
                              ]),
                            ),
                            if (balances != null && balances[l.id] != null && balances[l.id] != 0)
                              Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                BalanceText(balances[l.id]!),
                                Text(balances[l.id]! > 0 ? 'to pay' : 'advance',
                                    style: const TextStyle(fontSize: 11, color: Palette.muted)),
                              ])
                            else if (balances != null)
                              const Text('settled',
                                  style: TextStyle(fontSize: 12, color: Palette.present, fontWeight: FontWeight.w600)),
                          ]),
                        ),
                      ),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

