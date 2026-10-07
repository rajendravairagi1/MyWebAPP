import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';

class BranchesScreen extends ConsumerWidget {
  const BranchesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final branches = ref.watch(branchesProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Branches')),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _edit(context, ref),
        icon: const Icon(Icons.add),
        label: const Text('Add branch'),
      ),
      body: branches.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (list) => ListView.separated(
          padding: const EdgeInsets.fromLTRB(16, 4, 16, 96),
          itemCount: list.length,
          separatorBuilder: (_, _) => const SizedBox(height: 8),
          itemBuilder: (_, i) {
            final b = list[i];
            return AppCard(
              onTap: () => _edit(context, ref, branch: b),
              child: Row(children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(color: Palette.brandTint, borderRadius: BorderRadius.circular(13)),
                  child: const Icon(Icons.account_tree_outlined, color: Palette.brand),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(b.name, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15.5)),
                    Text([b.city, b.mobile].where((e) => e.isNotEmpty).join(' • '),
                        style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                  ]),
                ),
                const Icon(Icons.edit_outlined, size: 18, color: Palette.muted),
              ]),
            );
          },
        ),
      ),
    );
  }

  Future<void> _edit(BuildContext context, WidgetRef ref, {Branch? branch}) async {
    final name = TextEditingController(text: branch?.name);
    final city = TextEditingController(text: branch?.city);
    final address = TextEditingController(text: branch?.address);
    final mobile = TextEditingController(text: branch?.mobile);
    final ok = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(ctx).viewInsets.bottom + 20),
        child: SingleChildScrollView(
          child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(branch == null ? 'Add branch' : 'Edit branch', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
            const SizedBox(height: 14),
            TextField(controller: name, textCapitalization: TextCapitalization.words, decoration: const InputDecoration(labelText: 'Branch name')),
            const SizedBox(height: 10),
            TextField(controller: city, textCapitalization: TextCapitalization.words, decoration: const InputDecoration(labelText: 'City')),
            const SizedBox(height: 10),
            TextField(controller: address, decoration: const InputDecoration(labelText: 'Address')),
            const SizedBox(height: 10),
            TextField(controller: mobile, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Mobile')),
            const SizedBox(height: 16),
            FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Save')),
          ]),
        ),
      ),
    );
    if (ok != true) return;
    try {
      final svc = ref.read(workspaceServiceProvider);
      if (branch == null) {
        await svc.addBranch(name: name.text, city: city.text, address: address.text, mobile: mobile.text);
      } else {
        await svc.updateBranch(branch.copyWith(
          name: name.text.trim(),
          city: city.text.trim(),
          address: address.text.trim(),
          mobile: mobile.text.trim(),
        ));
      }
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }
}
