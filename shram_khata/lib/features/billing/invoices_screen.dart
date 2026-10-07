import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/billing_service.dart';
import '../../state/providers.dart';
import 'invoice_create_screen.dart';
import 'invoice_detail_screen.dart';

Color stateColor(String s) => switch (s) {
      InvoiceState.paid => Palette.present,
      InvoiceState.partial => Palette.info,
      InvoiceState.overdue => Palette.absent,
      InvoiceState.cancelled => Palette.muted,
      _ => Palette.half,
    };

class InvoiceTile extends StatelessWidget {
  const InvoiceTile({super.key, required this.row, this.showCompany = true});
  final InvoiceRow row;
  final bool showCompany;

  @override
  Widget build(BuildContext context) {
    final inv = row.invoice;
    final s = row.state;
    return ListTile(
      onTap: () => Navigator.push(
          context, MaterialPageRoute(builder: (_) => InvoiceDetailScreen(invoiceId: inv.id))),
      title: Text(showCompany ? row.companyName : inv.number,
          style: TextStyle(
            fontWeight: FontWeight.w700,
            decoration: s == InvoiceState.cancelled ? TextDecoration.lineThrough : null,
          )),
      subtitle: Text([
        if (showCompany) inv.number,
        '${D.showShort(inv.periodFrom)} – ${D.showShort(inv.periodTo)}',
      ].join(' • ')),
      trailing: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(Money.format(inv.total), style: const TextStyle(fontWeight: FontWeight.w800)),
          const SizedBox(height: 3),
          Pill(InvoiceState.label(s), color: stateColor(s)),
        ],
      ),
    );
  }
}

class InvoicesScreen extends ConsumerStatefulWidget {
  const InvoicesScreen({super.key});

  @override
  ConsumerState<InvoicesScreen> createState() => _InvoicesScreenState();
}

class _InvoicesScreenState extends ConsumerState<InvoicesScreen> {
  String _filter = 'all';

  @override
  Widget build(BuildContext context) {
    final invoices = ref.watch(invoicesProvider);
    final session = ref.watch(sessionProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Invoices')),
      floatingActionButton: session.can(Perm.billingManage)
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const InvoiceCreateScreen())),
              icon: const Icon(Icons.add),
              label: const Text('New invoice'),
            )
          : null,
      body: invoices.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (all) {
          if (all.isEmpty) {
            return const EmptyState(
              icon: Icons.receipt_long_outlined,
              title: 'No invoices yet',
              message: 'Bill a company for the attendance of a period. The invoice PDF includes an attendance sheet for the company to verify.',
            );
          }
          final rows = all.where((r) {
            return switch (_filter) {
              'unpaid' => r.state == InvoiceState.unpaid || r.state == InvoiceState.partial,
              'overdue' => r.state == InvoiceState.overdue,
              'paid' => r.state == InvoiceState.paid,
              _ => true,
            };
          }).toList();
          final due = all.fold<int>(0, (a, r) => a + r.outstanding);
          final overdue = all.where((r) => r.state == InvoiceState.overdue).fold<int>(0, (a, r) => a + r.outstanding);
          return Column(children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
              child: Row(children: [
                Expanded(child: AppCard(child: Stat(label: 'To collect', value: Money.format(due), color: Palette.info))),
                const SizedBox(width: 10),
                Expanded(child: AppCard(child: Stat(label: 'Overdue', value: Money.format(overdue), color: overdue > 0 ? Palette.absent : Palette.ink))),
              ]),
            ),
            SizedBox(
              height: 42,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                children: [
                  for (final f in const [('all', 'All'), ('unpaid', 'Unpaid'), ('overdue', 'Overdue'), ('paid', 'Paid')])
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(f.$2),
                        selected: _filter == f.$1,
                        onSelected: (_) => setState(() => _filter = f.$1),
                      ),
                    ),
                ],
              ),
            ),
            Expanded(
              child: rows.isEmpty
                  ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Nothing here')
                  : ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 96), children: [
                      AppCard(
                        padding: EdgeInsets.zero,
                        child: Column(children: [
                          for (var i = 0; i < rows.length; i++) ...[
                            InvoiceTile(row: rows[i]),
                            if (i < rows.length - 1) const Divider(indent: 16, endIndent: 16),
                          ],
                        ]),
                      ),
                    ]),
            ),
          ]);
        },
      ),
    );
  }
}
