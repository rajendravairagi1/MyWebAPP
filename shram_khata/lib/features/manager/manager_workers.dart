import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/i18n.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';
import 'manager_data.dart';
import 'manager_overview.dart' show MonthBar;

/// Who came this month, how many days, and what it adds up to.
class ManagerWorkers extends ConsumerWidget {
  const ManagerWorkers({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final bill = ref.watch(managerBillProvider);
    return Column(children: [
      const Padding(padding: EdgeInsets.fromLTRB(16, 4, 16, 0), child: MonthBar()),
      Expanded(
        child: bill.when(
          loading: () => const LoadingView(),
          error: (e, _) => ErrorView(e),
          data: (d) {
            if (d == null || d.lines.isEmpty) {
              return EmptyState(icon: Icons.groups_outlined, title: tr('No workers this month'));
            }
            return ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              itemCount: d.lines.length,
              separatorBuilder: (_, _) => const SizedBox(height: 8),
              itemBuilder: (_, i) {
                final l = d.lines[i];
                return AppCard(
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => ManagerWorkerScreen(labourId: l.labourId, name: l.name, skill: l.skill)),
                  ),
                  child: Row(children: [
                    Avatar(name: l.name, size: 42),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(l.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15.5)),
                        Text(
                          trf('{skill} • {days} days{ot}', {
                            'skill': tr(l.skill),
                            'days': num1(l.days),
                            'ot': l.otHours > 0 ? ' + ${num1(l.otHours)}h OT' : '',
                          }),
                          style: const TextStyle(color: Palette.muted, fontSize: 12.5),
                        ),
                      ]),
                    ),
                    Text(l.missingRate ? '—' : Money.format(l.amount), style: const TextStyle(fontWeight: FontWeight.w800)),
                  ]),
                );
              },
            );
          },
        ),
      ),
    ]);
  }
}

final _workerMonthProvider = FutureProvider.autoDispose.family<List<AttendanceData>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  final m = ref.watch(managerMonthProvider);
  return ref.watch(attendanceServiceProvider).entries(labourId: id, from: D.ymd(m), to: D.ymd(D.lastOfMonth(m)));
});

/// One worker's month: a day-by-day calendar, read only.
class ManagerWorkerScreen extends ConsumerWidget {
  const ManagerWorkerScreen({super.key, required this.labourId, required this.name, required this.skill});
  final String labourId;
  final String name;
  final String skill;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final m = ref.watch(managerMonthProvider);
    final rows = ref.watch(_workerMonthProvider(labourId)).value ?? const <AttendanceData>[];
    final byDate = <String, List<AttendanceData>>{};
    for (final e in rows) {
      (byDate[e.date] ??= []).add(e);
    }
    var days = 0.0, ot = 0.0;
    var absent = 0;
    for (final list in byDate.values) {
      final v = list.fold<double>(0, (a, e) => a + dayValue(e.status));
      days += v;
      if (v == 0) absent++;
      ot += list.fold<double>(0, (a, e) => a + (e.status == 'A' ? 0 : e.otHours));
    }
    final lead = m.weekday - 1;
    final count = D.daysInMonth(m);
    return Scaffold(
      appBar: AppBar(title: Text(name)),
      body: ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 24), children: [
        Text(tr(skill), style: const TextStyle(color: Palette.muted)),
        const MonthBar(),
        AppCard(
          padding: const EdgeInsets.all(10),
          child: Column(children: [
            Row(children: [
              for (final w in ['M', 'T', 'W', 'T', 'F', 'S', 'S'])
                Expanded(child: Center(child: Text(w, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Palette.muted)))),
            ]),
            const SizedBox(height: 6),
            GridView.count(
              crossAxisCount: 7,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              mainAxisSpacing: 4,
              crossAxisSpacing: 4,
              children: [
                for (var i = 0; i < lead; i++) const SizedBox(),
                for (var d = 1; d <= count; d++) _cell(DateTime(m.year, m.month, d), byDate),
              ],
            ),
          ]),
        ),
        const SizedBox(height: 14),
        Row(children: [
          Expanded(child: Stat(label: tr('Days worked'), value: num1(days))),
          Expanded(child: Stat(label: tr('Absent'), value: '$absent')),
          Expanded(child: Stat(label: tr('Overtime'), value: '${num1(ot)} h')),
        ]),
      ]),
    );
  }

  Widget _cell(DateTime date, Map<String, List<AttendanceData>> byDate) {
    final list = byDate[D.ymd(date)];
    String? letter;
    double ot = 0;
    if (list != null) {
      final v = list.fold<double>(0, (a, e) => a + dayValue(e.status));
      letter = v >= 1 ? 'P' : (v > 0 ? 'H' : 'A');
      ot = list.fold<double>(0, (a, e) => a + (e.status == 'A' ? 0 : e.otHours));
    }
    final future = date.isAfter(DateTime.now());
    return Container(
      decoration: BoxDecoration(
        color: letter == null ? (future ? Colors.transparent : const Color(0xFFF6F8F8)) : Palette.statusBg(letter),
        borderRadius: BorderRadius.circular(10),
        border: D.isToday(date) ? Border.all(color: Palette.brand, width: 1.6) : null,
      ),
      child: Stack(children: [
        Positioned(left: 5, top: 3, child: Text('${date.day}', style: TextStyle(fontSize: 10, color: future ? Palette.line : Palette.muted, fontWeight: FontWeight.w600))),
        if (letter != null)
          Center(child: Padding(padding: const EdgeInsets.only(top: 6), child: Text(letter, style: TextStyle(fontSize: 17, fontWeight: FontWeight.w900, color: Palette.status(letter))))),
        if (ot > 0) Positioned(right: 4, bottom: 2, child: Text('+${num1(ot)}', style: const TextStyle(fontSize: 9, color: Palette.info, fontWeight: FontWeight.w800))),
      ]),
    );
  }
}
