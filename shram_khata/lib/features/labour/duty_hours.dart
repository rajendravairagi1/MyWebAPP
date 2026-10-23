import 'package:flutter/material.dart';

import '../../core/i18n.dart';
import '../../core/money.dart';
import '../../core/theme.dart';

/// Which duty a day's pay covers (6 / 8 / 10 / 12 hours). Overtime is the day
/// rate divided by these hours, so nobody has to type an overtime rate.
class DutyHoursPicker extends StatelessWidget {
  const DutyHoursPicker({super.key, required this.hours, required this.onChanged, this.dailyPaise});

  static const options = [6, 8, 10, 12];

  final int hours;
  final ValueChanged<int> onChanged;

  /// The day rate typed so far, to show what an overtime hour is worth.
  final int? dailyPaise;

  @override
  Widget build(BuildContext context) {
    final ot = (dailyPaise ?? 0) > 0 ? (dailyPaise! / hours).round() : null;
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(tr('Duty hours per day'), style: const TextStyle(fontWeight: FontWeight.w700)),
      const SizedBox(height: 8),
      Wrap(spacing: 8, runSpacing: 8, children: [
        for (final h in options)
          ChoiceChip(
            label: Text(trf('{n} hours', {'n': h})),
            selected: hours == h,
            onSelected: (_) => onChanged(h),
          ),
      ]),
      const SizedBox(height: 6),
      Text(
        ot == null
            ? tr('Overtime is counted after these hours, at day rate ÷ duty hours.')
            : trf('Overtime: {amount} per hour (day rate ÷ {n} hours)', {'amount': Money.format(ot), 'n': hours}),
        style: const TextStyle(color: Palette.muted, fontSize: 12.5),
      ),
    ]);
  }
}
