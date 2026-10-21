import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/permissions.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';
import '../attendance/attendance_screen.dart';
import '../labour/labour_list_screen.dart';
import '../payments/payments_screen.dart';
import '../settings/more_screen.dart';
import 'dashboard_screen.dart';

class _Tab {
  const _Tab(this.label, this.icon, this.selectedIcon, this.builder);
  final String label;
  final IconData icon;
  final IconData selectedIcon;
  final Widget Function() builder;
}

/// Bottom navigation. Tabs appear only when the signed-in role may use them,
/// so a supervisor sees Attendance, Labour and More.
class Shell extends ConsumerStatefulWidget {
  const Shell({super.key});

  @override
  ConsumerState<Shell> createState() => _ShellState();
}

class _ShellState extends ConsumerState<Shell> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final s = ref.watch(sessionProvider);
    final tabs = <_Tab>[
      if (s.can(Perm.dashboard))
        _Tab('Home', Icons.space_dashboard_outlined, Icons.space_dashboard, () => const DashboardScreen()),
      if (s.can(Perm.attendanceView) || s.can(Perm.attendanceMark))
        _Tab('Attendance', Icons.fact_check_outlined, Icons.fact_check, () => const AttendanceScreen()),
      if (s.can(Perm.labourView))
        _Tab('Labour', Icons.groups_outlined, Icons.groups, () => const LabourListScreen()),
      if (s.can(Perm.paymentsView))
        _Tab('Payments', Icons.account_balance_wallet_outlined, Icons.account_balance_wallet, () => const PaymentsScreen()),
      _Tab('More', Icons.grid_view_outlined, Icons.grid_view_rounded, () => const MoreScreen()),
    ];
    final i = _index.clamp(0, tabs.length - 1);
    return Scaffold(
      body: IndexedStack(
        index: i,
        children: [
          for (var t = 0; t < tabs.length; t++)
            // Build tabs lazily, then keep them alive.
            t == i || _visited.contains(t) ? tabs[t].builder() : const SizedBox.shrink(),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: i,
        onDestinationSelected: (v) => setState(() {
          _visited.add(v);
          _index = v;
        }),
        destinations: [
          for (final t in tabs)
            NavigationDestination(
              icon: _badged(t.label, Icon(t.icon)),
              selectedIcon: _badged(t.label, Icon(t.selectedIcon)),
              label: tr(t.label),
            ),
        ],
      ),
    );
  }

  /// A red dot on "More" when company managers have written to the agency.
  Widget _badged(String label, Widget icon) {
    final unread = ref.watch(cloudProvider).unread;
    if (label != 'More' || unread <= 0) return icon;
    return Badge(label: Text('$unread'), child: icon);
  }

  final _visited = <int>{0};
}
