import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'service_fee_editor.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/billing_service.dart';
import '../../domain/business_profile.dart';
import '../../state/providers.dart';
import 'invoice_detail_screen.dart';

/// Build an invoice: pick company, site and period, review the lines, save.
class InvoiceCreateScreen extends ConsumerStatefulWidget {
  const InvoiceCreateScreen({super.key, this.companyId});
  final String? companyId;

  @override
  ConsumerState<InvoiceCreateScreen> createState() => _InvoiceCreateScreenState();
}

class _InvoiceCreateScreenState extends ConsumerState<InvoiceCreateScreen> {
  String? _companyId;
  String? _siteId;
  late DateTime _from = D.firstOfMonth(DateTime.now());
  late DateTime _to = DateTime.now();
  InvoiceDraft? _draft;
  List<Site> _sites = [];
  final _notes = TextEditingController();
  bool _busy = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _companyId = widget.companyId;
    if (_companyId != null) _loadSites().then((_) => _preview());
  }

  @override
  void dispose() {
    _notes.dispose();
    super.dispose();
  }

  Future<void> _loadSites() async {
    final s = await ref.read(companyServiceProvider).sites(companyId: _companyId);
    if (mounted) setState(() => _sites = s);
  }

  Future<void> _preview() async {
    if (_companyId == null) return;
    try {
      final d = await ref.read(billingServiceProvider).preview(
            companyId: _companyId!,
            siteId: _siteId,
            from: D.ymd(_from),
            to: D.ymd(_to),
          );
      if (mounted) {
        setState(() {
          _draft = d;
          _error = null;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _error = e.toString());
    }
  }

  Future<void> _save(BusinessProfile profile) async {
    final d = _draft;
    if (d == null) return;
    setState(() => _busy = true);
    try {
      final company = (await ref.read(companyServiceProvider).company(d.companyId))!;
      final id = await ref.read(billingServiceProvider).create(
            draft: d,
            branchId: company.branchId,
            issueDate: D.today(),
            paymentTermsDays: profile.paymentTermsDays,
            notes: _notes.text,
            staffId: ref.read(sessionProvider).staff?.id,
          );
      if (!mounted) return;
      Navigator.pushReplacement(
          context, MaterialPageRoute(builder: (_) => InvoiceDetailScreen(invoiceId: id)));
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  Future<void> _editRate(DraftLine l) async {
    final c = TextEditingController(text: l.rate > 0 ? Money.toInput(l.rate) : '');
    final o = TextEditingController(text: l.otRate > 0 ? Money.toInput(l.otRate) : '');
    final ok = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(ctx).viewInsets.bottom + 20),
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(trf('Billing rate for {name}', {'name': l.name}), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
          const SizedBox(height: 4),
          Text(l.otHours > 0
              ? trf('{skill} • {days} days • {ot}h OT', {'skill': l.skill, 'days': num1(l.days), 'ot': num1(l.otHours)})
              : trf('{skill} • {days} days', {'skill': l.skill, 'days': num1(l.days)}),
              style: const TextStyle(color: Palette.muted)),
          const SizedBox(height: 4),
          Text(tr('This changes only this invoice. To fix it for good, add the skill to the company contract.'),
              style: TextStyle(color: Palette.muted, fontSize: 12.5)),
          const SizedBox(height: 14),
          TextField(
            controller: c,
            autofocus: true,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: InputDecoration(labelText: tr('Rate per day'), prefixText: '${Money.symbol} '),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: o,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: InputDecoration(labelText: tr('Overtime per hour'), prefixText: '${Money.symbol} '),
          ),
          const SizedBox(height: 16),
          FilledButton(onPressed: () => Navigator.pop(ctx, true), child: Text(tr('Apply'))),
        ]),
      ),
    );
    if (ok == true) {
      setState(() {
        l.rate = Money.parse(c.text) ?? 0;
        l.otRate = Money.parse(o.text) ?? 0;
      });
    }
  }

  Future<void> _range() async {
    final r = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDateRange: DateTimeRange(start: _from, end: _to),
    );
    if (r != null) {
      setState(() {
        _from = r.start;
        _to = r.end;
      });
      _preview();
    }
  }

  @override
  Widget build(BuildContext context) {
    final companies = ref.watch(companiesProvider).value ?? const <Company>[];
    final profile = ref.watch(profileProvider).value ?? const BusinessProfile();
    final d = _draft;
    final taxRate = profile.taxEnabled ? profile.taxRate : 0.0;
    final fee = d?.feeAmount ?? 0;
    final tax = d == null ? 0 : ((d.subtotal + fee) * taxRate / 100).round();
    final now = DateTime.now();
    final thisMonth = D.firstOfMonth(now);
    final lastMonth = DateTime(now.year, now.month - 1, 1);
    return Scaffold(
      appBar: AppBar(title: Text(tr('New invoice'))),
      body: FormBody(
        bottom: d == null
            ? null
            : FilledButton(
                onPressed: _busy || d.lines.isEmpty ? null : () => _save(profile),
                child: Text(trf('Create invoice • {amount}', {'amount': Money.format(d.subtotal + fee + tax)})),
              ),
        children: [
          DropdownButtonFormField<String>(
            initialValue: _companyId,
            decoration: InputDecoration(labelText: tr('Company')),
            items: [for (final c in companies) DropdownMenuItem(value: c.id, child: Text(c.name))],
            onChanged: (v) async {
              setState(() {
                _companyId = v;
                _siteId = null;
                _draft = null;
              });
              await _loadSites();
              _preview();
            },
          ),
          if (_sites.length > 1) ...[
            const SizedBox(height: 12),
            DropdownButtonFormField<String?>(
              initialValue: _siteId,
              decoration: InputDecoration(labelText: tr('Site')),
              items: [
                DropdownMenuItem(value: null, child: Text(tr('All sites'))),
                for (final s in _sites) DropdownMenuItem(value: s.id, child: Text(s.name)),
              ],
              onChanged: (v) {
                setState(() => _siteId = v);
                _preview();
              },
            ),
          ],
          SectionTitle(tr('PERIOD'), padding: EdgeInsets.fromLTRB(2, 20, 2, 8)),
          Wrap(spacing: 8, runSpacing: 8, children: [
            ChoiceChip(
              label: Text(tr('This month')),
              selected: D.ymd(_from) == D.ymd(thisMonth) && D.ymd(_to) == D.ymd(now),
              onSelected: (_) {
                setState(() {
                  _from = thisMonth;
                  _to = now;
                });
                _preview();
              },
            ),
            ChoiceChip(
              label: Text(tr('Last month')),
              selected: D.ymd(_from) == D.ymd(lastMonth) && D.ymd(_to) == D.ymd(D.lastOfMonth(lastMonth)),
              onSelected: (_) {
                setState(() {
                  _from = lastMonth;
                  _to = D.lastOfMonth(lastMonth);
                });
                _preview();
              },
            ),
            ActionChip(
              avatar: const Icon(Icons.edit_calendar_outlined, size: 16),
              label: Text(trf('{from} – {to}', {'from': D.showShort(D.ymd(_from)), 'to': D.show(D.ymd(_to))})),
              onPressed: _range,
            ),
          ]),
          if (_error != null)
            Padding(
              padding: const EdgeInsets.only(top: 12),
              child: Text(tr(_error!), style: const TextStyle(color: Palette.absent)),
            ),
          if (d != null) ...[
            if (d.overlapping != null)
              Padding(
                padding: const EdgeInsets.only(top: 14),
                child: AppCard(
                  color: Palette.halfBg,
                  padding: const EdgeInsets.all(12),
                  child: Row(children: [
                    const Icon(Icons.warning_amber_rounded, color: Palette.half),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        trf('Invoice {number} already covers part of this period. Check you are not billing the same days twice.', {'number': d.overlapping!.number}),
                        style: const TextStyle(color: Palette.half, fontWeight: FontWeight.w600, fontSize: 13),
                      ),
                    ),
                  ]),
                ),
              ),
            SectionTitle(tr('LINES'), padding: EdgeInsets.fromLTRB(2, 20, 2, 8)),
            if (d.lines.isEmpty)
              AppCard(
                child: Text(tr('No attendance found for this company in this period.'), style: TextStyle(color: Palette.muted)),
              )
            else ...[
              if (d.missingRates > 0)
                Padding(
                  padding: const EdgeInsets.only(bottom: 10),
                  child: AppCard(
                    color: Palette.absentBg,
                    padding: const EdgeInsets.all(12),
                    child: Text(
                      d.missingRates == 1
                          ? trf('{n} line has no billing rate. Tap a line to set it, or add the skill to the contract.', {'n': d.missingRates})
                          : trf('{n} lines have no billing rate. Tap a line to set it, or add the skill to the contract.', {'n': d.missingRates}),
                      style: const TextStyle(color: Palette.absent, fontWeight: FontWeight.w600, fontSize: 13),
                    ),
                  ),
                ),
              AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < d.lines.length; i++) ...[
                    ListTile(
                      onTap: () => _editRate(d.lines[i]),
                      title: Text(d.lines[i].name, style: const TextStyle(fontWeight: FontWeight.w700)),
                      subtitle: Text(
                        d.lines[i].missingRate
                            ? trf('{skill} • {days} days • rate missing',
                                {'skill': d.lines[i].skill, 'days': num1(d.lines[i].days)})
                            : d.lines[i].otHours > 0
                                ? trf('{skill} • {days} d × {rate} + {ot}h OT', {
                                    'skill': d.lines[i].skill,
                                    'days': num1(d.lines[i].days),
                                    'rate': Money.format(d.lines[i].rate),
                                    'ot': num1(d.lines[i].otHours)
                                  })
                                : trf('{skill} • {days} d × {rate}', {
                                    'skill': d.lines[i].skill,
                                    'days': num1(d.lines[i].days),
                                    'rate': Money.format(d.lines[i].rate)
                                  }),
                        style: TextStyle(color: d.lines[i].missingRate ? Palette.absent : null),
                      ),
                      trailing: Text(Money.format(d.lines[i].amount),
                          style: const TextStyle(fontWeight: FontWeight.w800)),
                    ),
                    if (i < d.lines.length - 1) const Divider(indent: 16),
                  ],
                ]),
              ),
              const SizedBox(height: 12),
              SectionTitle(tr('SERVICE FEE (YOUR CHARGE ON TOP)'), padding: const EdgeInsets.fromLTRB(2, 8, 2, 10)),
              AppCard(
                child: ServiceFeeEditor(
                  key: ValueKey('fee-$_companyId-${d.from}-${d.to}-$_siteId'),
                  fee: d.fee,
                  onChanged: (f) => setState(() => d.fee = f),
                ),
              ),
              const SizedBox(height: 12),
              AppCard(
                child: Column(children: [
                  _total(tr('Man-days'), num1(d.totalDays)),
                  _total(tr('Labour charges'), Money.format(d.subtotal)),
                  if (fee > 0)
                    _total(
                        d.fee.type == ServiceFee.percentOf
                            ? trf('Service fee ({pct}%)', {'pct': num1(d.fee.percent)})
                            : tr('Service fee'),
                        Money.format(fee)),
                  if (taxRate > 0) _total(trf('{tax} @ {rate}%', {'tax': profile.taxLabel, 'rate': num1(taxRate)}), Money.format(tax)),
                  const Divider(height: 18),
                  _total(tr('Total'), Money.format(d.subtotal + fee + tax), big: true),
                ]),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: _notes,
                maxLines: 2,
                decoration: InputDecoration(labelText: tr('Note on invoice (optional)')),
              ),
            ],
          ],
        ],
      ),
    );
  }

  Widget _total(String k, String v, {bool big = false}) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(children: [
          Text(k, style: TextStyle(color: big ? Palette.ink : Palette.muted, fontWeight: big ? FontWeight.w800 : FontWeight.w500, fontSize: big ? 16 : 14)),
          const Spacer(),
          Text(v, style: TextStyle(fontWeight: FontWeight.w800, fontSize: big ? 20 : 15)),
        ]),
      );
}
