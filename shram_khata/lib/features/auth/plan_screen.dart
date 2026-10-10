import 'package:flutter/material.dart';

import '../../core/plans.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import 'signup_screen.dart';

/// Choose Solo / Owner + Team / Company while signing up.
class PlanScreen extends StatefulWidget {
  const PlanScreen({super.key});

  @override
  State<PlanScreen> createState() => _PlanScreenState();
}

class _PlanScreenState extends State<PlanScreen> {
  Plan _plan = Plan.team;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Choose your plan')),
      body: FormBody(
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(4, 0, 4, 14),
            child: Text(
              'Pick what fits how you work. You can change it later from Settings.',
              style: TextStyle(color: Palette.muted, height: 1.4),
            ),
          ),
          for (final p in Plan.values)
            Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: PlanCard(
                plan: p,
                selected: _plan == p,
                recommended: p == Plan.team,
                onTap: () => setState(() => _plan = p),
              ),
            ),
        ],
        bottom: FilledButton(
          onPressed: () => Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => SignUpScreen(plan: _plan)),
          ),
          child: Text('Continue with ${_plan.title}'),
        ),
      ),
    );
  }
}

class PlanCard extends StatelessWidget {
  const PlanCard({
    super.key,
    required this.plan,
    required this.selected,
    required this.onTap,
    this.recommended = false,
    this.current = false,
  });

  final Plan plan;
  final bool selected;
  final bool recommended;
  final bool current;
  final VoidCallback onTap;

  IconData get _icon => switch (plan) {
        Plan.solo => Icons.person_outline,
        Plan.team => Icons.groups_2_outlined,
        Plan.company => Icons.apartment_outlined,
      };

  @override
  Widget build(BuildContext context) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 180),
      decoration: BoxDecoration(
        color: selected ? Palette.brandTint : Palette.surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: selected ? Palette.brand : Palette.line,
          width: selected ? 2 : 1,
        ),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(18),
          onTap: onTap,
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: selected ? Palette.brand : Palette.brandTint,
                        borderRadius: BorderRadius.circular(13),
                      ),
                      child: Icon(_icon, color: selected ? Colors.white : Palette.brand),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Wrap(
                            spacing: 8,
                            runSpacing: 4,
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              Text(plan.title,
                                  style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                              if (recommended) const Pill('Popular', color: Palette.half),
                              if (current) const Pill('Current'),
                            ],
                          ),
                          Text(plan.tagline,
                              style: const TextStyle(color: Palette.muted, fontSize: 13)),
                        ],
                      ),
                    ),
                    Icon(
                      selected ? Icons.check_circle : Icons.circle_outlined,
                      color: selected ? Palette.brand : Palette.line,
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                for (final f in plan.features)
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 3),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.only(top: 2),
                          child: Icon(Icons.check, size: 16, color: Palette.brand),
                        ),
                        const SizedBox(width: 8),
                        Expanded(child: Text(f, style: const TextStyle(fontSize: 13.5, height: 1.3))),
                      ],
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
