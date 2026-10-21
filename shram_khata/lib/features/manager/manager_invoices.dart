import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/i18n.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/billing_service.dart';
import 'manager_data.dart';

/// Bills the agency has issued to this company and what is still due.
class ManagerInvoices extends ConsumerWidget {
  const ManagerInvoices({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final list = ref.watch(managerInvoicesProvider);
    return list.when(
      loading: () => const LoadingView(),
      error: (e, _) => ErrorView(e),
      data: (rows) {
        if (rows.isEmpty) {
          return EmptyState(
            icon: Icons.receipt_long_outlined,
            title: tr('No bills yet'),
            message: tr('Bills issued by the agency appear here.'),
          );
        }
        final due = rows.fold<int>(0, (a, r) => a + r.outstanding);
        return ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 24), children: [
          AppCard(
            color: due > 0 ? Palette.halfBg : Palette.presentBg,
            child: Row(children: [
              Expanded(child: Text(tr('Total to pay'), style: const TextStyle(fontWeight: FontWeight.w700))),
              Text(Money.format(due), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
            ]),
          ),
          const SizedBox(height: 10),
          for (final r in rows)
            Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: AppCard(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Row(children: [
                    Expanded(child: Text(r.invoice.number, style: const TextStyle(fontWeight: FontWeight.w800))),
                    Pill(tr(InvoiceState.label(r.state)), color: _color(r.state)),
                  ]),
                  const SizedBox(height: 4),
                  Text('${D.showShort(r.invoice.periodFrom)} – ${D.show(r.invoice.periodTo)}', style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                  const SizedBox(height: 8),
                  Row(children: [
                    Expanded(child: Text(trf('Total {amount}', {'amount': Money.format(r.invoice.total)}))),
                    if (r.outstanding > 0)
                      Text(trf('Due {amount}', {'amount': Money.format(r.outstanding)}), style: const TextStyle(fontWeight: FontWeight.w800, color: Palette.absent)),
                  ]),
                  if (r.invoice.serviceFeeAmount > 0)
                    Padding(
                      padding: const EdgeInsets.only(top: 4),
                      child: Text(
                        trf('Labour {labour} + service fee {fee}', {
                          'labour': Money.format(r.invoice.subtotal),
                          'fee': Money.format(r.invoice.serviceFeeAmount),
                        }),
                        style: const TextStyle(color: Palette.muted, fontSize: 12.5),
                      ),
                    ),
                ]),
              ),
            ),
        ]);
      },
    );
  }

  Color _color(String s) => switch (s) {
        InvoiceState.paid => Palette.present,
        InvoiceState.overdue => Palette.absent,
        InvoiceState.partial => Palette.half,
        InvoiceState.cancelled => Palette.muted,
        _ => Palette.info,
      };
}
