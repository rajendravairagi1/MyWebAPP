import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/i18n.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/billing_service.dart';
import '../home/dashboard_widgets.dart' show KpiTile;
import 'manager_data.dart';

class MonthBar extends ConsumerWidget {
  const MonthBar({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final m = ref.watch(managerMonthProvider);
    final n = ref.read(managerMonthProvider.notifier);
    final canNext = m.isBefore(D.firstOfMonth(DateTime.now()));
    return Row(children: [
      IconButton(onPressed: () => n.set(DateTime(m.year, m.month - 1)), icon: const Icon(Icons.chevron_left)),
      Expanded(
        child: Text(D.monthTitle(m), textAlign: TextAlign.center, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
      ),
      IconButton(onPressed: canNext ? () => n.set(DateTime(m.year, m.month + 1)) : null, icon: const Icon(Icons.chevron_right)),
    ]);
  }
}

/// Month so far: workers, man-days and what the company owes.
class ManagerOverview extends ConsumerWidget {
  const ManagerOverview({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final company = ref.watch(managerCompanyProvider).value;
    final bill = ref.watch(managerBillProvider);
    final invoices = ref.watch(managerInvoicesProvider).value ?? const [];
    final profile = ref.watch(managerProfileProvider).value;
    final outstanding = invoices.fold<int>(0, (a, r) => a + r.outstanding);
    return ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 24), children: [
      if (company != null)
        Padding(
          padding: const EdgeInsets.fromLTRB(4, 4, 4, 0),
          child: Text(company.name, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800)),
        ),
      const MonthBar(),
      bill.when(
        loading: () => const Padding(padding: EdgeInsets.all(40), child: LoadingView()),
        error: (e, _) => ErrorView(e),
        data: (d) {
          if (d == null || d.lines.isEmpty) {
            return EmptyState(
              icon: Icons.groups_outlined,
              title: tr('No attendance yet this month'),
              message: tr('Workers and their attendance appear here once the agency marks them.'),
            );
          }
          final fee = d.feeAmount;
          final taxRate = profile?.taxEnabled == true ? profile!.taxRate : 0.0;
          final tax = ((d.subtotal + fee) * taxRate / 100).round();
          final workers = d.lines.map((l) => l.labourId).toSet().length;
          return Column(children: [
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              mainAxisSpacing: 10,
              crossAxisSpacing: 10,
              childAspectRatio: 1.4,
              children: [
                KpiTile(label: tr('Workers'), value: '$workers', hint: tr('worked this month'), icon: Icons.groups_outlined, color: Palette.info),
                KpiTile(label: tr('Man-days'), value: num1(d.totalDays), hint: tr('full days'), icon: Icons.event_available_outlined, color: Palette.present),
                KpiTile(
                    label: tr('Overtime'),
                    value: '${num1(d.lines.fold(0.0, (a, l) => a + l.otHours))} h',
                    hint: tr('this month'),
                    icon: Icons.schedule,
                    color: Palette.half),
                KpiTile(label: tr('Bill so far'), value: Money.compact(d.subtotal + fee + tax), hint: tr('this month'), icon: Icons.receipt_long_outlined, color: Palette.brand),
              ],
            ),
            const SizedBox(height: 14),
            AppCard(
              child: Column(children: [
                _row(tr('Labour charges'), Money.format(d.subtotal)),
                if (fee > 0)
                  _row(
                      d.fee.type == ServiceFee.percentOf
                          ? trf('Service fee ({pct}%)', {'pct': num1(d.fee.percent)})
                          : tr('Service fee'),
                      Money.format(fee)),
                if (tax > 0) _row('${profile?.taxLabel ?? 'Tax'} @ ${num1(taxRate)}%', Money.format(tax)),
                const Divider(height: 18),
                _row(tr('Total so far'), Money.format(d.subtotal + fee + tax), big: true),
              ]),
            ),
          ]);
        },
      ),
      const SizedBox(height: 14),
      AppCard(
        color: outstanding > 0 ? Palette.halfBg : Palette.presentBg,
        child: Row(children: [
          Expanded(
            child: Text(tr('To pay the agency (issued bills)'), style: const TextStyle(fontWeight: FontWeight.w700)),
          ),
          Text(Money.format(outstanding), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
        ]),
      ),
    ]);
  }

  Widget _row(String k, String v, {bool big = false}) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(children: [
          Expanded(child: Text(k, style: TextStyle(color: big ? Palette.ink : Palette.muted, fontWeight: big ? FontWeight.w800 : FontWeight.w500))),
          Text(v, style: TextStyle(fontWeight: FontWeight.w800, fontSize: big ? 18 : 15)),
        ]),
      );
}
