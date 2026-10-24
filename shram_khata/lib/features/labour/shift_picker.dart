import 'package:flutter/material.dart';

import '../../core/i18n.dart';
import '../../domain/calc.dart';

/// Day / Night choice. Same pay and company rate either way; it only tells
/// which sheet a worker is marked on.
class ShiftPicker extends StatelessWidget {
  const ShiftPicker({super.key, required this.shift, required this.onChanged});
  final String shift;
  final ValueChanged<String> onChanged;

  @override
  Widget build(BuildContext context) => SegmentedButton<String>(
        segments: [
          ButtonSegment(
              value: Shift.day, icon: const Icon(Icons.wb_sunny_outlined), label: Text(tr('Day shift'))),
          ButtonSegment(
              value: Shift.night, icon: const Icon(Icons.nightlight_outlined), label: Text(tr('Night shift'))),
        ],
        selected: {shift},
        onSelectionChanged: (v) => onChanged(v.first),
      );
}
