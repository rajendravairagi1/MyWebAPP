import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/countries.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';

/// First-run business setup: country & currency, then the first company and
/// its sites. Everything is kept locally until "Finish" so Back never leaves
/// half-saved data.
class SetupWizardScreen extends ConsumerStatefulWidget {
  const SetupWizardScreen({super.key});

  @override
  ConsumerState<SetupWizardScreen> createState() => _SetupWizardScreenState();
}

class _SetupWizardScreenState extends ConsumerState<SetupWizardScreen> {
  int _step = 0;
  bool _busy = false;

  Country _country = Countries.india;
  String _currency = 'INR';
  bool _taxEnabled = false;
  late final _taxLabel = TextEditingController(text: _country.taxLabel);
  late final _taxRate = TextEditingController(text: _fmtRate(_country.taxRate));

  final _company = TextEditingController();
  final _contact = TextEditingController();
  final _siteCtrls = <TextEditingController>[TextEditingController()];
  final _form = GlobalKey<FormState>();

  static String _fmtRate(double r) => r == r.roundToDouble() ? r.toInt().toString() : r.toString();

  @override
  void dispose() {
    _taxLabel.dispose();
    _taxRate.dispose();
    _company.dispose();
    _contact.dispose();
    for (final c in _siteCtrls) {
      c.dispose();
    }
    super.dispose();
  }

  void _pickCountry(Country c) {
    setState(() {
      _country = c;
      _currency = c.currency;
      _taxLabel.text = c.taxLabel;
      _taxRate.text = _fmtRate(c.taxRate);
    });
  }

  Future<void> _finish({required bool withCompany}) async {
    if (withCompany && !_form.currentState!.validate()) return;
    setState(() => _busy = true);
    try {
      final ws = ref.read(workspaceServiceProvider);
      final profile = await ws.profile();
      await ws.saveProfile(profile.copyWith(
        countryCode: _country.code,
        currencyCode: _currency,
        taxEnabled: _taxEnabled,
        taxLabel: _taxLabel.text.trim().isEmpty ? _country.taxLabel : _taxLabel.text.trim(),
        taxRate: double.tryParse(_taxRate.text.trim()) ?? _country.taxRate,
        setupDone: true,
      ));
      if (withCompany) {
        final svc = ref.read(companyServiceProvider);
        var branch = ref.read(writeBranchProvider);
        branch ??= (await ws.branches()).first.id;
        final cid = await svc.addCompany(
          branchId: branch,
          name: _company.text,
          contactPerson: _contact.text,
        );
        for (final c in _siteCtrls) {
          if (c.text.trim().isNotEmpty) await svc.addSite(companyId: cid, name: c.text);
        }
        final sites = await svc.sites(companyId: cid);
        if (sites.isEmpty) await svc.addSite(companyId: cid, name: 'Main site');
      }
    } catch (e) {
      if (mounted) {
        setState(() => _busy = false);
        showError(context, e);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final titles = ['Your business', 'Your first company'];
    return Scaffold(
      appBar: AppBar(
        title: Text(titles[_step]),
        automaticallyImplyLeading: false,
        leading: _step > 0
            ? IconButton(icon: const Icon(Icons.arrow_back), onPressed: () => setState(() => _step--))
            : null,
        actions: [
          TextButton(
              onPressed: _busy ? null : () => _finish(withCompany: false),
              child: const Text('Skip'),
            ),
        ],
      ),
      body: Column(
        children: [
          LinearProgressIndicator(
            value: (_step + 1) / 2,
            minHeight: 3,
            backgroundColor: Palette.line,
          ),
          Expanded(
            child: _step == 0 ? _countryStep() : _companyStep(),
          ),
        ],
      ),
    );
  }

  Widget _countryStep() {
    return FormBody(
      bottom: FilledButton(
        onPressed: () => setState(() => _step = 1),
        child: const Text('Next'),
      ),
      children: [
        const Text(
          'Where is your business? We set your currency, phone code and tax name from this.',
          style: TextStyle(color: Palette.muted),
        ),
        const SizedBox(height: 16),
        DropdownButtonFormField<String>(
          key: ValueKey('country-${_country.code}'),
          initialValue: _country.code,
          isExpanded: true,
          decoration: const InputDecoration(labelText: 'Country'),
          items: [
            for (final c in Countries.all) DropdownMenuItem(value: c.code, child: Text(c.name)),
          ],
          onChanged: (v) {
            if (v != null) _pickCountry(Countries.byCode(v));
          },
        ),
        const SizedBox(height: 12),
        DropdownButtonFormField<String>(
          key: ValueKey('currency-$_currency'),
          initialValue: _currency,
          isExpanded: true,
          decoration: const InputDecoration(labelText: 'Currency'),
          items: [
            for (final c in Currencies.all)
              DropdownMenuItem(value: c.code, child: Text('${c.name} (${c.symbol})')),
          ],
          onChanged: (v) => setState(() => _currency = v ?? _currency),
        ),
        const SizedBox(height: 8),
        SwitchListTile(
          contentPadding: EdgeInsets.zero,
          value: _taxEnabled,
          onChanged: (v) => setState(() => _taxEnabled = v),
          title: const Text('Add tax on invoices'),
          subtitle: const Text('Turn on if you are registered for tax'),
        ),
        if (_taxEnabled) ...[
          TextFormField(
            controller: _taxLabel,
            decoration: const InputDecoration(labelText: 'Tax name (GST, VAT ...)'),
          ),
          const SizedBox(height: 12),
          TextFormField(
            controller: _taxRate,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: const InputDecoration(labelText: 'Tax rate %'),
          ),
        ],
      ],
    );
  }

  Widget _companyStep() {
    return Form(
      key: _form,
      child: FormBody(
        bottom: FilledButton(
          onPressed: _busy ? null : () => _finish(withCompany: true),
          child: const Text('Finish setup'),
        ),
        children: [
          const Text(
            'Which company do your workers work for? You can add more companies later. '
            'Each worker is attached to a company and a site.',
            style: TextStyle(color: Palette.muted),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _company,
            textCapitalization: TextCapitalization.words,
            decoration: const InputDecoration(labelText: 'Company name *'),
            validator: (v) => (v ?? '').trim().isEmpty ? 'Enter the company name' : null,
          ),
          const SizedBox(height: 12),
          TextFormField(
            controller: _contact,
            textCapitalization: TextCapitalization.words,
            decoration: const InputDecoration(labelText: 'Contact person (optional)'),
          ),
          const SizedBox(height: 20),
          const SectionTitle('Sites / locations'),
          for (var i = 0; i < _siteCtrls.length; i++)
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Row(children: [
                Expanded(
                  child: TextFormField(
                    controller: _siteCtrls[i],
                    textCapitalization: TextCapitalization.words,
                    decoration: InputDecoration(labelText: 'Site ${i + 1} (e.g. Plant 1)'),
                  ),
                ),
                if (_siteCtrls.length > 1)
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => setState(() => _siteCtrls.removeAt(i).dispose()),
                  ),
              ]),
            ),
          Align(
            alignment: Alignment.centerLeft,
            child: TextButton.icon(
              onPressed: () => setState(() => _siteCtrls.add(TextEditingController())),
              icon: const Icon(Icons.add),
              label: const Text('Add another site'),
            ),
          ),
        ],
      ),
    );
  }
}
