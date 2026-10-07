import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/plans.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';
import '../auth/plan_screen.dart' show PlanCard;

/// Switch between Solo / Owner + Team / Company.
class PlanSettingsScreen extends ConsumerStatefulWidget {
  const PlanSettingsScreen({super.key});

  @override
  ConsumerState<PlanSettingsScreen> createState() => _PlanSettingsScreenState();
}

class _PlanSettingsScreenState extends ConsumerState<PlanSettingsScreen> {
  Plan? _picked;

  @override
  Widget build(BuildContext context) {
    final current = ref.watch(planProvider);
    final sel = _picked ?? current;
    return Scaffold(
      appBar: AppBar(title: const Text('Plan')),
      body: FormBody(
        bottom: FilledButton(
          onPressed: sel == current
              ? null
              : () async {
                  try {
                    await ref.read(workspaceServiceProvider).changePlan(sel, staffId: ref.read(sessionProvider).staff?.id);
                    if (context.mounted) {
                      showInfo(context, 'Switched to ${sel.title}');
                      setState(() => _picked = null);
                    }
                  } catch (e) {
                    if (context.mounted) showError(context, e);
                  }
                },
          child: Text(sel == current ? 'Current plan' : 'Switch to ${sel.title}'),
        ),
        children: [
          for (final p in Plan.values)
            Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: PlanCard(
                plan: p,
                selected: sel == p,
                current: current == p,
                onTap: () => setState(() => _picked = p),
              ),
            ),
        ],
      ),
    );
  }
}
