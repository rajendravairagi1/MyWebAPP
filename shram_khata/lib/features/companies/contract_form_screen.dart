import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/files.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/company_service.dart';
import '../../data/services/labour_service.dart';
import '../../state/providers.dart';

class _RateRow {
  _RateRow({String skill = '', String perDay = '', String ot = ''})
      : skill = TextEditingController(text: skill),
        perDay = TextEditingController(text: perDay),
        ot = TextEditingController(text: ot);
  final TextEditingController skill;
  final TextEditingController perDay;
  final TextEditingController ot;
  void dispose() {
    skill.dispose();
    perDay.dispose();
    ot.dispose();
  }
}

/// A contract: validity, payment terms and the billing rate per skill.
class ContractFormScreen extends ConsumerStatefulWidget {
  const ContractFormScreen({super.key, required this.companyId, this.existing});
  final String companyId;
  final ContractWithRates? existing;

  @override
  ConsumerState<ContractFormScreen> createState() => _ContractFormScreenState();
}

class _ContractFormScreenState extends ConsumerState<ContractFormScreen> {
  late final _title = TextEditingController(text: widget.existing?.contract.title);
  late final _notes = TextEditingController(text: widget.existing?.contract.notes);
  late final _terms = TextEditingController(
      text: (widget.existing?.contract.paymentTermsDays ?? 30).toString());
  late DateTime _start = widget.existing == null
      ? DateTime.now()
      : D.parse(widget.existing!.contract.startDate);
  DateTime? _end;
  String? _doc;
  bool _active = true;
  final _rows = <_RateRow>[];
  bool _busy = false;

  @override
  void initState() {
    super.initState();
    final c = widget.existing;
    if (c != null) {
      _end = D.tryParse(c.contract.endDate);
      _doc = c.contract.docPath;
      _active = c.contract.isActive;
      for (final r in c.rates) {
        _rows.add(_RateRow(
          skill: r.skill,
          perDay: Money.toInput(r.perDay),
          ot: r.otPerHour == 0 ? '' : Money.toInput(r.otPerHour),
        ));
      }
    }
    if (_rows.isEmpty) _rows.add(_RateRow());
  }

  @override
  void dispose() {
    _title.dispose();
    _notes.dispose();
    _terms.dispose();
    for (final r in _rows) {
      r.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    setState(() => _busy = true);
    try {
      await ref.read(companyServiceProvider).saveContract(
            id: widget.existing?.contract.id,
            companyId: widget.companyId,
            title: _title.text,
            startDate: D.ymd(_start),
            endDate: _end == null ? null : D.ymd(_end!),
            paymentTermsDays: int.tryParse(_terms.text) ?? 30,
            notes: _notes.text,
            docPath: _doc,
            isActive: _active,
            rates: [
              for (final r in _rows)
                (
                  skill: r.skill.text,
                  perDay: Money.parse(r.perDay.text) ?? 0,
                  otPerHour: Money.parse(r.ot.text) ?? 0,
                )
            ].where((r) => r.skill.trim().isNotEmpty).toList(),
          );
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  Future<DateTime?> _pick(DateTime initial, {DateTime? first}) => showDatePicker(
        context: context,
        initialDate: initial,
        firstDate: first ?? DateTime(2020),
        lastDate: DateTime(2050),
      );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.existing == null ? 'New contract' : 'Edit contract')),
      body: FormBody(
        bottom: FilledButton(onPressed: _busy ? null : _save, child: const Text('Save contract')),
        children: [
          TextField(
            controller: _title,
            textCapitalization: TextCapitalization.words,
            decoration: const InputDecoration(labelText: 'Contract name *', hintText: 'e.g. Manpower supply FY 2026-27'),
          ),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(
              child: InkWell(
                borderRadius: BorderRadius.circular(14),
                onTap: () async {
                  final d = await _pick(_start);
                  if (d != null) setState(() => _start = d);
                },
                child: InputDecorator(
                  decoration: const InputDecoration(labelText: 'Starts'),
                  child: Text(D.showDt(_start)),
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: InkWell(
                borderRadius: BorderRadius.circular(14),
                onTap: () async {
                  final d = await _pick(_end ?? _start.add(const Duration(days: 365)), first: _start);
                  if (d != null) setState(() => _end = d);
                },
                child: InputDecorator(
                  decoration: InputDecoration(
                    labelText: 'Ends',
                    suffixIcon: _end == null
                        ? null
                        : IconButton(icon: const Icon(Icons.close, size: 18), onPressed: () => setState(() => _end = null)),
                  ),
                  child: Text(_end == null ? 'Open ended' : D.showDt(_end!)),
                ),
              ),
            ),
          ]),
          const SizedBox(height: 12),
          TextField(
            controller: _terms,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(labelText: 'Payment terms', suffixText: 'days'),
          ),
          SectionTitle('BILLING RATES (WHAT THE COMPANY PAYS YOU)',
              padding: const EdgeInsets.fromLTRB(2, 22, 2, 4),
              trailing: null),
          const Padding(
            padding: EdgeInsets.only(bottom: 10),
            child: Text(
              'Per skill, per day. Invoices use these rates, so every skill you supply should be listed.',
              style: TextStyle(color: Palette.muted, fontSize: 12.5),
            ),
          ),
          for (var i = 0; i < _rows.length; i++)
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: AppCard(
                padding: const EdgeInsets.all(12),
                child: Column(children: [
                  Row(children: [
                    Expanded(
                      child: Autocomplete<String>(
                        initialValue: TextEditingValue(text: _rows[i].skill.text),
                        optionsBuilder: (v) => LabourService.defaultSkills.where(
                            (s) => s.toLowerCase().contains(v.text.toLowerCase())),
                        onSelected: (v) => _rows[i].skill.text = v,
                        fieldViewBuilder: (context, c, f, _) => TextField(
                          controller: c,
                          focusNode: f,
                          textCapitalization: TextCapitalization.words,
                          onChanged: (v) => _rows[i].skill.text = v,
                          decoration: const InputDecoration(labelText: 'Skill', isDense: true),
                        ),
                      ),
                    ),
                    if (_rows.length > 1)
                      IconButton(
                        icon: const Icon(Icons.delete_outline, color: Palette.absent),
                        onPressed: () => setState(() => _rows.removeAt(i).dispose()),
                      ),
                  ]),
                  const SizedBox(height: 10),
                  Row(children: [
                    Expanded(
                      child: TextField(
                        controller: _rows[i].perDay,
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        decoration: const InputDecoration(labelText: 'Per day', prefixText: '₹ ', isDense: true),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: TextField(
                        controller: _rows[i].ot,
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        decoration: const InputDecoration(labelText: 'OT / hour', prefixText: '₹ ', isDense: true),
                      ),
                    ),
                  ]),
                ]),
              ),
            ),
          OutlinedButton.icon(
            onPressed: () => setState(() => _rows.add(_RateRow())),
            icon: const Icon(Icons.add),
            label: const Text('Add skill rate'),
          ),
          const SizedBox(height: 18),
          TextField(
            controller: _notes,
            maxLines: 3,
            decoration: const InputDecoration(labelText: 'Notes / special terms'),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: () async {
              final p = await pickAndStoreImage(context, folder: 'contracts');
              if (p != null) setState(() => _doc = p);
            },
            icon: Icon(_doc == null ? Icons.document_scanner_outlined : Icons.check_circle,
                color: _doc == null ? null : Palette.present),
            label: Text(_doc == null ? 'Scan / attach contract copy' : 'Contract copy attached'),
          ),
          if (widget.existing != null)
            SwitchListTile(
              contentPadding: EdgeInsets.zero,
              title: const Text('Contract is active'),
              subtitle: const Text('Inactive contracts are not used for billing rates.'),
              value: _active,
              onChanged: (v) => setState(() => _active = v),
            ),
        ],
      ),
    );
  }
}
