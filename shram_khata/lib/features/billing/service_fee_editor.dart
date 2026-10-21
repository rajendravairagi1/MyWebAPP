import 'package:flutter/material.dart';

import '../../core/i18n.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../data/services/billing_service.dart';

/// Pick how the agency's service fee is charged: none, a percentage of the
/// labour charges, or one fixed amount per invoice.
class ServiceFeeEditor extends StatefulWidget {
  const ServiceFeeEditor({super.key, required this.fee, required this.onChanged});
  final ServiceFee fee;
  final ValueChanged<ServiceFee> onChanged;

  @override
  State<ServiceFeeEditor> createState() => _ServiceFeeEditorState();
}

class _ServiceFeeEditorState extends State<ServiceFeeEditor> {
  late final _value = TextEditingController(text: _initialText());

  String _initialText() {
    final f = widget.fee;
    if (f.type == ServiceFee.percentOf && f.percent > 0) return num1(f.percent);
    if (f.type == ServiceFee.fixedAmount && f.fixed > 0) return Money.toInput(f.fixed);
    return '';
  }

  @override
  void dispose() {
    _value.dispose();
    super.dispose();
  }

  void _emit(String type) {
    final text = _value.text.trim();
    final f = ServiceFee(type: type);
    if (type == ServiceFee.percentOf) f.percent = double.tryParse(text) ?? 0;
    if (type == ServiceFee.fixedAmount) f.fixed = Money.parse(text) ?? 0;
    widget.onChanged(f);
  }

  @override
  Widget build(BuildContext context) {
    final type = widget.fee.type;
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Wrap(spacing: 8, runSpacing: 8, children: [
        for (final e in [
          (ServiceFee.none, tr('No fee')),
          (ServiceFee.percentOf, tr('Percentage %')),
          (ServiceFee.fixedAmount, tr('Fixed amount')),
        ])
          ChoiceChip(
            label: Text(e.$2),
            selected: type == e.$1,
            onSelected: (_) {
              _value.clear();
              _emit(e.$1);
            },
          ),
      ]),
      if (type != ServiceFee.none) ...[
        const SizedBox(height: 10),
        TextField(
          controller: _value,
          keyboardType: const TextInputType.numberWithOptions(decimal: true),
          onChanged: (_) => _emit(type),
          decoration: InputDecoration(
            labelText: type == ServiceFee.percentOf ? tr('Service fee %') : tr('Service fee amount'),
            prefixText: type == ServiceFee.fixedAmount ? '${Money.symbol} ' : null,
            suffixText: type == ServiceFee.percentOf ? '%' : null,
            isDense: true,
          ),
        ),
        const SizedBox(height: 6),
        Text(
          type == ServiceFee.percentOf
              ? tr('Added as a share of the labour charges on every invoice.')
              : tr('Added once on every invoice.'),
          style: const TextStyle(color: Palette.muted, fontSize: 12.5),
        ),
      ],
    ]);
  }
}
