import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/cloud.dart';
import '../language/language_screen.dart';
import 'manager_feedback.dart';
import 'manager_invoices.dart';
import 'manager_overview.dart';
import 'manager_workers.dart';

/// The whole app for a company manager: read-only view of one company.
class ManagerShell extends ConsumerStatefulWidget {
  const ManagerShell({super.key});

  @override
  ConsumerState<ManagerShell> createState() => _ManagerShellState();
}

class _ManagerShellState extends ConsumerState<ManagerShell> {
  int _i = 0;

  @override
  Widget build(BuildContext context) {
    final cloud = ref.watch(cloudProvider);
    final pages = const [ManagerOverview(), ManagerWorkers(), ManagerInvoices(), ManagerFeedback()];
    return Scaffold(
      appBar: AppBar(
        title: Text(cloud.session?.agencyName.isNotEmpty == true ? cloud.session!.agencyName : 'HazriBook'),
        actions: [
          IconButton(
            tooltip: tr('Sync now'),
            onPressed: cloud.syncing ? null : () => ref.read(cloudProvider.notifier).syncNow(),
            icon: cloud.syncing
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                : const Icon(Icons.sync),
          ),
          PopupMenuButton<String>(
            onSelected: (v) async {
              if (v == 'lang') {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const LanguageScreen()));
              } else if (v == 'out') {
                final ok = await confirm(
                  context,
                  title: tr('Sign out?'),
                  message: tr('You will need your email / mobile and password to sign in again.'),
                  confirmLabel: tr('Sign out'),
                  danger: true,
                );
                if (!ok) return;
                await ref.read(syncServiceProvider).signOut();
                ref.read(cloudProvider.notifier).refreshSession();
              }
            },
            itemBuilder: (_) => [
              PopupMenuItem(value: 'lang', child: Text(tr('Language'))),
              PopupMenuItem(value: 'out', child: Text(tr('Sign out'))),
            ],
          ),
        ],
      ),
      body: Column(children: [
        if (cloud.error != null)
          Container(
            width: double.infinity,
            color: Palette.halfBg,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Text(tr(cloud.error!), style: const TextStyle(color: Palette.half, fontSize: 12.5)),
          ),
        Expanded(child: pages[_i]),
      ]),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _i,
        onDestinationSelected: (v) => setState(() => _i = v),
        destinations: [
          NavigationDestination(icon: const Icon(Icons.space_dashboard_outlined), selectedIcon: const Icon(Icons.space_dashboard), label: tr('Overview')),
          NavigationDestination(icon: const Icon(Icons.groups_outlined), selectedIcon: const Icon(Icons.groups), label: tr('Workers')),
          NavigationDestination(icon: const Icon(Icons.receipt_long_outlined), selectedIcon: const Icon(Icons.receipt_long), label: tr('Bills')),
          NavigationDestination(icon: const Icon(Icons.forum_outlined), selectedIcon: const Icon(Icons.forum), label: tr('Feedback')),
        ],
      ),
    );
  }
}
