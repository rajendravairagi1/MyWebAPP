import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/payment_service.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';

IconData modeIcon(String mode) => switch (mode) {
      PayMode.upi => Icons.qr_code_2,
      PayMode.bank => Icons.account_balance_outlined,
      _ => Icons.payments_outlined,
    };

Color typeColor(String type) => switch (type) {
      PayType.daily => Palette.present,
      PayType.advance => Palette.half,
      PayType.settlement => Palette.info,
      _ => Palette.absent,
    };

class PaymentTile extends ConsumerWidget {
  const PaymentTile({super.key, required this.row, this.showName = true, this.canVoid = false});
  final PaymentRow row;
  final bool showName;
  final bool canVoid;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final p = row.payment;
    final voided = p.voidedAt != null;
    final color = voided ? Palette.muted : typeColor(p.type);
    final deduction = p.type == PayType.deduction;
    return ListTile(
      onLongPress: canVoid && !voided ? () => _void(context, ref) : null,
      onTap: () => _details(context, ref),
      leading: Container(
        width: 40,
        height: 40,
        decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
        child: Icon(deduction ? Icons.remove_circle_outline : modeIcon(p.mode), size: 20, color: color),
      ),
      title: Text(
        showName ? row.labourName : PayType.label(p.type),
        style: TextStyle(
          fontWeight: FontWeight.w700,
          decoration: voided ? TextDecoration.lineThrough : null,
          color: voided ? Palette.muted : null,
        ),
      ),
      subtitle: Text([
        if (showName) PayType.label(p.type),
        if (!deduction) PayMode.label(p.mode),
        D.showShort(p.date),
        if (p.note.isNotEmpty) p.note,
      ].join(' • ')),
      trailing: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(
            '${deduction ? '− ' : ''}${Money.format(p.amount)}',
            style: TextStyle(
              fontWeight: FontWeight.w800,
              fontSize: 15.5,
              color: voided ? Palette.muted : Palette.ink,
              decoration: voided ? TextDecoration.lineThrough : null,
            ),
          ),
          if (voided) const Text('VOID', style: TextStyle(fontSize: 10.5, fontWeight: FontWeight.w800, color: Palette.absent)),
        ],
      ),
    );
  }

  void _details(BuildContext context, WidgetRef ref) {
    final p = row.payment;
    showModalBottomSheet<void>(
      context: context,
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
          child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(row.labourName, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
            const SizedBox(height: 4),
            Text('${PayType.label(p.type)} • ${Money.format(p.amount)}',
                style: const TextStyle(fontSize: 15, color: Palette.muted)),
            const SizedBox(height: 12),
            _kv('Date', D.show(p.date)),
            if (p.type != PayType.deduction) _kv('Mode', PayMode.label(p.mode)),
            if (p.reference.isNotEmpty) _kv('Reference', p.reference),
            if (p.note.isNotEmpty) _kv('Note', p.note),
            _kv('Entered', '${D.showDt(p.createdAt)} ${TimeOfDay.fromDateTime(p.createdAt).format(ctx)}'),
            if (p.voidedAt != null) ...[
              _kv('Voided', D.showDt(p.voidedAt!)),
              _kv('Reason', p.voidReason ?? ''),
            ],
            if (canVoid && p.voidedAt == null) ...[
              const SizedBox(height: 14),
              OutlinedButton.icon(
                style: OutlinedButton.styleFrom(foregroundColor: Palette.absent),
                icon: const Icon(Icons.block),
                label: const Text('Void this payment'),
                onPressed: () {
                  Navigator.pop(ctx);
                  _void(context, ref);
                },
              ),
            ],
          ]),
        ),
      ),
    );
  }

  Widget _kv(String k, String v) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          SizedBox(width: 84, child: Text(k, style: const TextStyle(color: Palette.muted))),
          Expanded(child: Text(v, style: const TextStyle(fontWeight: FontWeight.w600))),
        ]),
      );

  Future<void> _void(BuildContext context, WidgetRef ref) async {
    final c = TextEditingController();
    final reason = await showDialog<String>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Void payment?'),
        content: Column(mainAxisSize: MainAxisSize.min, children: [
          const Text('The entry stays in history but stops counting toward the balance.'),
          const SizedBox(height: 12),
          TextField(controller: c, autofocus: true, decoration: const InputDecoration(labelText: 'Reason')),
        ]),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, c.text),
            style: TextButton.styleFrom(foregroundColor: Palette.absent),
            child: const Text('Void'),
          ),
        ],
      ),
    );
    if (reason == null || !context.mounted) return;
    try {
      await ref.read(paymentServiceProvider).voidPayment(row.payment.id, reason, staffId: ref.read(sessionProvider).staff?.id);
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }
}
