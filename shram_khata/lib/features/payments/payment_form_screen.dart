import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';
import '../labour/labour_pickers.dart';
import 'payment_widgets.dart';

/// Record a daily payment, advance, settlement or deduction.
class PaymentFormScreen extends ConsumerStatefulWidget {
  const PaymentFormScreen({super.key, this.labourId});
  final String? labourId;

  @override
  ConsumerState<PaymentFormScreen> createState() => _PaymentFormScreenState();
}

class _PaymentFormScreenState extends ConsumerState<PaymentFormScreen> {
  Labour? _labour;
  Ledger? _ledger;
  String _type = PayType.daily;
  String _mode = PayMode.cash;
  DateTime _date = DateTime.now();
  final _amount = TextEditingController();
  final _note = TextEditingController();
  final _ref = TextEditingController();
  bool _busy = false;

  @override
  void initState() {
    super.initState();
    if (widget.labourId != null) _setLabour(widget.labourId!);
  }

  @override
  void dispose() {
    _amount.dispose();
    _note.dispose();
    _ref.dispose();
    super.dispose();
  }

  Future<void> _setLabour(String id) async {
    final l = await ref.read(labourServiceProvider).byId(id);
    final ledger = await ref.read(paymentServiceProvider).ledger(id);
    if (!mounted) return;
    setState(() {
      _labour = l;
      _ledger = ledger;
      // Opening from the balances list: suggest settling what is owed.
      if (widget.labourId != null && ledger.balance > 0 && _amount.text.isEmpty) {
        _type = PayType.settlement;
        _amount.text = Money.toInput(ledger.balance);
      }
    });
  }

  Future<void> _save() async {
    final amount = Money.parse(_amount.text) ?? 0;
    if (_labour == null) return showError(context, 'Choose a labourer');
    if (amount <= 0) return showError(context, 'Enter the amount');
    setState(() => _busy = true);
    try {
      await ref.read(paymentServiceProvider).add(
            branchId: _labour!.branchId,
            labourId: _labour!.id,
            type: _type,
            mode: _mode,
            amount: amount,
            date: D.ymd(_date),
            note: _note.text,
            reference: _ref.text,
            staffId: ref.read(sessionProvider).staff?.id,
          );
      if (mounted) {
        showInfo(context, '${Money.format(amount)} recorded for ${_labour!.name}');
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final deduction = _type == PayType.deduction;
    final entered = Money.parse(_amount.text) ?? 0;
    final balance = _ledger?.balance;
    final after = balance == null ? null : balance - entered;
    return Scaffold(
      appBar: AppBar(title: const Text('Add payment')),
      body: FormBody(
        bottom: FilledButton(
          onPressed: _busy ? null : _save,
          child: Text(entered > 0 ? 'Save ${Money.format(entered)}' : 'Save payment'),
        ),
        children: [
          AppCard(
            onTap: widget.labourId != null
                ? null
                : () async {
                    final l = await pickLabour(context, title: 'Who is being paid?', includeInactive: true);
                    if (l != null) {
                      _amount.clear();
                      await _setLabour(l.id);
                    }
                  },
            child: Row(children: [
              Avatar(name: _labour?.name ?? '?', size: 46),
              const SizedBox(width: 12),
              Expanded(
                child: _labour == null
                    ? const Text('Choose labour', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Palette.muted))
                    : Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(_labour!.name, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                        Text(_labour!.skill, style: const TextStyle(color: Palette.muted)),
                      ]),
              ),
              if (widget.labourId == null) const Icon(Icons.unfold_more, color: Palette.muted),
            ]),
          ),
          if (_ledger != null) ...[
            const SizedBox(height: 10),
            AccountSummary(_ledger!, color: Palette.brandTint),
          ],
          const SectionTitle('TYPE', padding: EdgeInsets.fromLTRB(2, 20, 2, 8)),
          Wrap(spacing: 8, runSpacing: 8, children: [
            for (final t in PayType.all)
              ChoiceChip(
                avatar: Icon(t == PayType.deduction ? Icons.remove_circle_outline : Icons.payments_outlined,
                    size: 16, color: _type == t ? typeColor(t) : Palette.muted),
                label: Text(PayType.label(t)),
                selected: _type == t,
                onSelected: (_) => setState(() => _type = t),
              ),
          ]),
          const SizedBox(height: 4),
          Text(
            switch (_type) {
              PayType.daily => 'Wages given for working days.',
              PayType.advance => 'Money given before it is earned. It is cut from future salary.',
              PayType.settlement => 'Final / periodic clearing of the balance.',
              _ => 'A fine or recovery. Reduces what is owed, no cash is paid.',
            },
            style: const TextStyle(fontSize: 12.5, color: Palette.muted),
          ),
          const SizedBox(height: 18),
          TextField(
            controller: _amount,
            onChanged: (_) => setState(() {}),
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w800),
            decoration: InputDecoration(labelText: 'Amount', prefixText: '${Money.symbol} '),
          ),
          const SizedBox(height: 8),
          Wrap(spacing: 8, children: [
            for (final v in [100, 200, 500, 1000])
              ActionChip(
                label: Text('${Money.symbol}$v'),
                onPressed: () => setState(() => _amount.text = '$v'),
              ),
            if (balance != null && balance > 0)
              ActionChip(
                backgroundColor: Palette.brandTint,
                label: Text('Full ${Money.format(balance)}'),
                onPressed: () => setState(() => _amount.text = Money.toInput(balance)),
              ),
          ]),
          if (after != null && entered > 0) ...[
            const SizedBox(height: 8),
            Text(
              after >= 0
                  ? 'Balance after this: ${Money.format(after)} still to pay'
                  : 'This is ${Money.format(-after)} more than earned — recorded as advance',
              style: TextStyle(
                fontSize: 12.5,
                fontWeight: FontWeight.w600,
                color: after >= 0 ? Palette.muted : Palette.half,
              ),
            ),
          ],
          if (!deduction) ...[
            const SectionTitle('PAID BY', padding: EdgeInsets.fromLTRB(2, 20, 2, 8)),
            SegmentedButton<String>(
              showSelectedIcon: false,
              segments: [
                for (final m in PayMode.all)
                  ButtonSegment(value: m, label: Text(PayMode.label(m)), icon: Icon(modeIcon(m), size: 18)),
              ],
              selected: {_mode},
              onSelectionChanged: (v) => setState(() => _mode = v.first),
            ),
            if (_mode != PayMode.cash) ...[
              const SizedBox(height: 12),
              TextField(
                controller: _ref,
                decoration: InputDecoration(
                  labelText: _mode == PayMode.upi ? 'UPI reference / UTR (optional)' : 'Bank reference (optional)',
                ),
              ),
            ],
          ],
          const SizedBox(height: 14),
          InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: () async {
              final d = await showDatePicker(
                context: context,
                initialDate: _date,
                firstDate: DateTime(2020),
                lastDate: DateTime.now(),
              );
              if (d != null) setState(() => _date = d);
            },
            child: InputDecorator(
              decoration: const InputDecoration(labelText: 'Date', suffixIcon: Icon(Icons.calendar_today_outlined)),
              child: Text(D.showDt(_date)),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _note,
            decoration: InputDecoration(labelText: deduction ? 'Reason (fine, damage...)' : 'Note (optional)'),
          ),
        ],
      ),
    );
  }
}
