import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/billing_service.dart';
import '../../domain/calc.dart';
import '../../pdf/invoice_pdf.dart';
import '../../state/providers.dart';
import '../payments/payment_widgets.dart' show modeIcon;
import '../reports/pdf_screen.dart';
import 'invoices_screen.dart' show stateColor;

final _detailProvider = FutureProvider.autoDispose.family<InvoiceDetail, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(billingServiceProvider).detail(id);
});

class InvoiceDetailScreen extends ConsumerWidget {
  const InvoiceDetailScreen({super.key, required this.invoiceId});
  final String invoiceId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(_detailProvider(invoiceId));
    final session = ref.watch(sessionProvider);
    return detail.when(
      loading: () => const Scaffold(body: LoadingView()),
      error: (e, _) => Scaffold(appBar: AppBar(), body: ErrorView(e)),
      data: (d) {
        final inv = d.invoice;
        final row = InvoiceRow(inv, d.company.name, d.paid);
        final s = row.state;
        final canManage = session.can(Perm.billingManage);
        return Scaffold(
          appBar: AppBar(
            title: Text(inv.number),
            actions: [
              IconButton(
                tooltip: 'PDF / Share',
                icon: const Icon(Icons.picture_as_pdf_outlined),
                onPressed: () => openPdf(
                  context,
                  title: inv.number,
                  fileName: inv.number.replaceAll('/', '-'),
                  build: () async => InvoicePdf.build(await ref.read(reportServiceProvider).invoice(inv.id)),
                ),
              ),
            ],
          ),
          body: ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 32), children: [
            AppCard(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Row(children: [
                  Expanded(
                    child: Text(d.company.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                  ),
                  Pill(InvoiceState.label(s), color: stateColor(s)),
                ]),
                const SizedBox(height: 2),
                Text(
                  '${d.site?.name ?? 'All sites'} • ${D.showShort(inv.periodFrom)} – ${D.show(inv.periodTo)}',
                  style: const TextStyle(color: Palette.muted),
                ),
                const Divider(height: 24),
                Row(children: [
                  Expanded(child: Stat(label: 'Invoice date', value: D.showShort(inv.issueDate))),
                  Expanded(child: Stat(label: 'Due date', value: D.showShort(inv.dueDate), color: s == InvoiceState.overdue ? Palette.absent : null)),
                ]),
                const Divider(height: 24),
                _row('Subtotal', Money.format(inv.subtotal)),
                if (inv.taxAmount > 0) _row('${inv.taxLabel} @ ${num1(inv.taxRate)}%', Money.format(inv.taxAmount)),
                _row('Total', Money.format(inv.total), bold: true),
                if (d.paid > 0) _row('Received', '− ${Money.format(d.paid)}'),
                if (s != InvoiceState.cancelled)
                  _row('Balance due', Money.format(d.outstanding), bold: true, color: d.outstanding > 0 ? Palette.absent : Palette.present),
              ]),
            ),
            if (canManage && d.outstanding > 0 && s != InvoiceState.cancelled) ...[
              const SizedBox(height: 12),
              FilledButton.icon(
                icon: const Icon(Icons.add_card),
                label: const Text('Record payment received'),
                onPressed: () => _receipt(context, ref, d),
              ),
            ],
            const SectionTitle('RECEIPTS', padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
            if (d.payments.isEmpty)
              const AppCard(child: Text('No payments received yet.', style: TextStyle(color: Palette.muted)))
            else
              AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < d.payments.length; i++) ...[
                    ListTile(
                      leading: Icon(modeIcon(d.payments[i].mode), color: Palette.present),
                      title: Text(Money.format(d.payments[i].amount), style: const TextStyle(fontWeight: FontWeight.w800)),
                      subtitle: Text([
                        D.show(d.payments[i].date),
                        PayMode.label(d.payments[i].mode),
                        if (d.payments[i].reference.isNotEmpty) d.payments[i].reference,
                      ].join(' • ')),
                      trailing: canManage
                          ? IconButton(
                              icon: const Icon(Icons.delete_outline, color: Palette.muted),
                              onPressed: () async {
                                final ok = await confirm(context,
                                    title: 'Remove this receipt?',
                                    message: 'The invoice balance will go back up by ${Money.format(d.payments[i].amount)}.',
                                    confirmLabel: 'Remove',
                                    danger: true);
                                if (ok) await ref.read(billingServiceProvider).removeReceipt(d.payments[i].id, staffId: session.staff?.id);
                              },
                            )
                          : null,
                    ),
                    if (i < d.payments.length - 1) const Divider(indent: 16),
                  ],
                ]),
              ),
            SectionTitle('LINES (${d.lines.length})', padding: const EdgeInsets.fromLTRB(4, 22, 4, 8)),
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(children: [
                for (var i = 0; i < d.lines.length; i++) ...[
                  ListTile(
                    dense: true,
                    title: Text(d.lines[i].description, style: const TextStyle(fontWeight: FontWeight.w700)),
                    subtitle: Text('${d.lines[i].skill} • ${num1(d.lines[i].days)} d × ${Money.format(d.lines[i].rate)}'
                        '${d.lines[i].otHours > 0 ? ' + ${num1(d.lines[i].otHours)}h OT' : ''}'),
                    trailing: Text(Money.format(d.lines[i].amount), style: const TextStyle(fontWeight: FontWeight.w800)),
                  ),
                  if (i < d.lines.length - 1) const Divider(indent: 16),
                ],
              ]),
            ),
            if (canManage && s != InvoiceState.cancelled && d.payments.isEmpty) ...[
              const SizedBox(height: 18),
              TextButton.icon(
                style: TextButton.styleFrom(foregroundColor: Palette.absent),
                icon: const Icon(Icons.cancel_outlined),
                label: const Text('Cancel this invoice'),
                onPressed: () async {
                  final ok = await confirm(context,
                      title: 'Cancel ${inv.number}?',
                      message: 'The invoice number is kept and marked cancelled. You can then create a corrected invoice for the same period.',
                      confirmLabel: 'Cancel invoice',
                      danger: true);
                  if (!ok) return;
                  try {
                    await ref.read(billingServiceProvider).cancel(inv.id, staffId: session.staff?.id);
                  } catch (e) {
                    if (context.mounted) showError(context, e);
                  }
                },
              ),
            ],
          ]),
        );
      },
    );
  }

  Widget _row(String k, String v, {bool bold = false, Color? color}) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(children: [
          Text(k, style: TextStyle(color: bold ? Palette.ink : Palette.muted, fontWeight: bold ? FontWeight.w800 : FontWeight.w500)),
          const Spacer(),
          Text(v, style: TextStyle(fontWeight: FontWeight.w800, fontSize: bold ? 17 : 14.5, color: color)),
        ]),
      );

  Future<void> _receipt(BuildContext context, WidgetRef ref, InvoiceDetail d) async {
    final amount = TextEditingController(text: Money.toInput(d.outstanding));
    final reference = TextEditingController();
    var mode = PayMode.bank;
    var date = DateTime.now();
    final ok = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setS) => Padding(
          padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(ctx).viewInsets.bottom + 20),
          child: SingleChildScrollView(
            child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Payment received', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 14),
              TextField(
                controller: amount,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800),
                decoration: InputDecoration(labelText: 'Amount', prefixText: '₹ ', helperText: 'Outstanding ${Money.format(d.outstanding)}'),
              ),
              const SizedBox(height: 12),
              SegmentedButton<String>(
                showSelectedIcon: false,
                segments: [
                  for (final m in PayMode.all) ButtonSegment(value: m, label: Text(PayMode.label(m)), icon: Icon(modeIcon(m), size: 18)),
                ],
                selected: {mode},
                onSelectionChanged: (v) => setS(() => mode = v.first),
              ),
              const SizedBox(height: 12),
              TextField(controller: reference, decoration: const InputDecoration(labelText: 'Reference / UTR / cheque no. (optional)')),
              const SizedBox(height: 12),
              InkWell(
                borderRadius: BorderRadius.circular(14),
                onTap: () async {
                  final p = await showDatePicker(context: ctx, initialDate: date, firstDate: DateTime(2020), lastDate: DateTime.now());
                  if (p != null) setS(() => date = p);
                },
                child: InputDecorator(
                  decoration: const InputDecoration(labelText: 'Received on', suffixIcon: Icon(Icons.calendar_today_outlined)),
                  child: Text(D.showDt(date)),
                ),
              ),
              const SizedBox(height: 16),
              FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Save receipt')),
            ]),
          ),
        ),
      ),
    );
    if (ok != true) return;
    try {
      await ref.read(billingServiceProvider).addReceipt(
            invoiceId: d.invoice.id,
            amount: Money.parse(amount.text) ?? 0,
            date: D.ymd(date),
            mode: mode,
            reference: reference.text,
            staffId: ref.read(sessionProvider).staff?.id,
          );
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }
}
