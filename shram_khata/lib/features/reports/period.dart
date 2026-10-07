import 'package:flutter/material.dart';

import '../../core/dates.dart';
import '../../core/theme.dart';

typedef Period = ({String from, String to});

/// Choose a reporting period: quick presets or a custom range.
Future<Period?> pickPeriod(BuildContext context, {String title = 'Choose period'}) {
  return showModalBottomSheet<Period>(
    context: context,
    builder: (ctx) {
      final now = DateTime.now();
      final thisFrom = D.firstOfMonth(now);
      final lastFrom = DateTime(now.year, now.month - 1, 1);
      final lastTo = D.lastOfMonth(lastFrom);
      Period p(DateTime a, DateTime b) => (from: D.ymd(a), to: D.ymd(b));
      Widget tile(IconData icon, String label, String sub, Period? v) => ListTile(
            leading: Icon(icon, color: Palette.brand),
            title: Text(label, style: const TextStyle(fontWeight: FontWeight.w700)),
            subtitle: Text(sub),
            onTap: () async {
              if (v != null) return Navigator.pop(ctx, v);
              final r = await showDateRangePicker(
                context: ctx,
                firstDate: DateTime(2020),
                lastDate: now,
                initialDateRange: DateTimeRange(start: thisFrom, end: now),
              );
              if (r != null && ctx.mounted) Navigator.pop(ctx, p(r.start, r.end));
            },
          );
      return SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 8),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              ),
            ),
            tile(Icons.calendar_month, 'This month', D.monthTitle(now), p(thisFrom, now)),
            tile(Icons.history, 'Last month', D.monthTitle(lastFrom), p(lastFrom, lastTo)),
            tile(Icons.date_range, 'Last 7 days',
                '${D.showShort(D.ymd(D.addDays(now, -6)))} – ${D.showShort(D.ymd(now))}',
                p(D.addDays(now, -6), now)),
            tile(Icons.edit_calendar_outlined, 'Custom range', 'Pick start and end date', null),
            const SizedBox(height: 8),
          ],
        ),
      );
    },
  );
}
