import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/countries.dart';
import '../../core/files.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../domain/business_profile.dart';
import '../../state/providers.dart';

/// The agency's identity: printed on every statement, invoice and report.
class BusinessProfileScreen extends ConsumerStatefulWidget {
  const BusinessProfileScreen({super.key});

  @override
  ConsumerState<BusinessProfileScreen> createState() => _BusinessProfileScreenState();
}

class _BusinessProfileScreenState extends ConsumerState<BusinessProfileScreen> {
  BusinessProfile? _p;
  final _c = <String, TextEditingController>{};

  TextEditingController _ctl(String k, String v) => _c.putIfAbsent(k, () => TextEditingController(text: v));

  @override
  void initState() {
    super.initState();
    ref.read(workspaceServiceProvider).profile().then((p) {
      if (mounted) setState(() => _p = p);
    });
  }

  @override
  void dispose() {
    for (final c in _c.values) {
      c.dispose();
    }
    super.dispose();
  }

  String _t(String k) => _c[k]?.text.trim() ?? '';

  Future<void> _save() async {
    final p = _p!;
    if (_t('name').isEmpty) return showError(context, tr('Business name is required'));
    final rate = double.tryParse(_t('taxRate')) ?? p.taxRate;
    final next = p.copyWith(
      name: _t('name'),
      tagline: _t('tagline'),
      ownerName: _t('ownerName'),
      mobile: _t('mobile'),
      altMobile: _t('altMobile'),
      email: _t('email'),
      website: _t('website'),
      address: _t('address'),
      city: _t('city'),
      state: _t('state'),
      pincode: _t('pincode'),
      taxLabel: _t('taxLabel').isEmpty ? p.country.taxLabel : _t('taxLabel'),
      taxId: _t('taxId').toUpperCase(),
      taxRate: rate,
      registrationLabel: _t('registrationLabel').isEmpty ? 'Registration No.' : _t('registrationLabel'),
      registrationId: _t('registrationId'),
      upiId: _t('upiId'),
      upiName: _t('upiName'),
      bankName: _t('bankName'),
      accountName: _t('accountName'),
      accountNumber: _t('accountNumber'),
      ifsc: _t('ifsc').toUpperCase(),
      invoicePrefix: _t('invoicePrefix').isEmpty ? 'INV' : _t('invoicePrefix'),
      paymentTermsDays: int.tryParse(_t('terms')) ?? p.paymentTermsDays,
      invoiceTerms: _c['invoiceTerms']?.text.trim() ?? p.invoiceTerms,
      footerNote: _t('footerNote'),
      monthDivisor: (int.tryParse(_t('divisor')) ?? p.monthDivisor).clamp(20, 31),
    );
    await ref.read(workspaceServiceProvider).saveProfile(next);
    if (mounted) {
      showInfo(context, tr('Saved'));
      Navigator.pop(context);
    }
  }

  Widget _field(String key, String label, String initial,
      {TextInputType? type, int lines = 1, String? hint, String? helper, TextCapitalization cap = TextCapitalization.none}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: TextField(
        controller: _ctl(key, initial),
        keyboardType: type,
        maxLines: lines,
        textCapitalization: cap,
        onChanged: (_) => setState(() {}),
        decoration: InputDecoration(labelText: tr(label), hintText: hint, helperText: helper == null ? null : tr(helper)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final p = _p;
    if (p == null) return const Scaffold(body: LoadingView());
    return Scaffold(
      appBar: AppBar(title: Text(tr('Business profile'))),
      body: FormBody(
        bottom: FilledButton(onPressed: _save, child: Text(tr('Save profile'))),
        children: [
          _Preview(profile: p, name: _t('name').isEmpty ? p.name : _t('name'), tagline: _t('tagline'), mobile: _t('mobile'), email: _t('email'), website: _t('website'), address: [_t('address'), _t('city')].where((e) => e.isNotEmpty).join(', '), taxLine: p.taxEnabled && _t('taxId').isNotEmpty ? '${_t('taxLabel').isEmpty ? 'GST' : _t('taxLabel')}: ${_t('taxId')}' : ''),
          SectionTitle(tr('COUNTRY & CURRENCY'), padding: EdgeInsets.fromLTRB(2, 22, 2, 10)),
          DropdownButtonFormField<String>(
            key: ValueKey('country-${p.countryCode}'),
            initialValue: p.countryCode,
            isExpanded: true,
            decoration: InputDecoration(labelText: tr('Country')),
            items: [for (final c in Countries.all) DropdownMenuItem(value: c.code, child: Text(c.name))],
            onChanged: (v) {
              if (v == null) return;
              final c = Countries.byCode(v);
              _ctl('taxLabel', p.taxLabel).text = c.taxLabel;
              _ctl('taxRate', num1(p.taxRate)).text = num1(c.taxRate);
              setState(() => _p = p.copyWith(countryCode: v, currencyCode: c.currency, taxLabel: c.taxLabel));
            },
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            key: ValueKey('currency-${p.currencyCode}'),
            initialValue: p.currencyCode,
            isExpanded: true,
            decoration: InputDecoration(labelText: tr('Currency')),
            items: [
              for (final c in Currencies.all) DropdownMenuItem(value: c.code, child: Text('${c.name} (${c.symbol})')),
            ],
            onChanged: (v) => setState(() => _p = p.copyWith(currencyCode: v)),
          ),
          SectionTitle(tr('THEME COLOUR'), padding: EdgeInsets.fromLTRB(2, 22, 2, 10)),
          Wrap(spacing: 10, runSpacing: 10, children: [
            for (final c in Palette.swatches)
              GestureDetector(
                onTap: () => setState(() => _p = p.copyWith(themeColor: c.toARGB32())),
                child: Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: c,
                    shape: BoxShape.circle,
                    border: Border.all(
                        color: p.themeColor == c.toARGB32() ? Palette.ink : Colors.transparent, width: 3),
                  ),
                  child: p.themeColor == c.toARGB32() ? const Icon(Icons.check, color: Colors.white, size: 20) : null,
                ),
              ),
          ]),
          const SizedBox(height: 12),
          TextField(
            controller: _ctl('themeHex', '#${(p.themeColor & 0xFFFFFF).toRadixString(16).padLeft(6, '0').toUpperCase()}'),
            decoration: InputDecoration(labelText: tr('Custom colour (hex)'), hintText: tr('#0F766E')),
            onChanged: (v) {
              final h = v.replaceAll('#', '').trim();
              if (h.length == 6) {
                final n = int.tryParse(h, radix: 16);
                if (n != null) setState(() => _p = p.copyWith(themeColor: 0xFF000000 | n));
              }
            },
          ),
          SectionTitle(tr('LOGO & IDENTITY'), padding: EdgeInsets.fromLTRB(2, 22, 2, 10)),
          Row(children: [
            GestureDetector(
              onTap: () async {
                final path = await pickAndStoreImage(context, folder: 'brand');
                if (path != null) setState(() => _p = p.copyWith(logoPath: path));
              },
              child: Container(
                width: 84,
                height: 84,
                decoration: BoxDecoration(
                  color: Palette.surface,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Palette.line),
                  image: fileImage(p.logoPath) == null ? null : DecorationImage(image: fileImage(p.logoPath)!, fit: BoxFit.contain),
                ),
                child: fileImage(p.logoPath) == null
                    ? Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                        Icon(Icons.add_photo_alternate_outlined, color: Palette.brand),
                        SizedBox(height: 4),
                        Text(tr('Logo'), style: TextStyle(fontSize: 12, color: Palette.muted)),
                      ])
                    : null,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(tr('Your logo appears on every PDF.'), style: TextStyle(color: Palette.muted, fontSize: 13)),
                if (p.logoPath != null)
                  TextButton(onPressed: () => setState(() => _p = p.copyWith(logoPath: null)), child: Text(tr('Remove logo'))),
              ]),
            ),
          ]),
          const SizedBox(height: 16),
          _field('name', 'Business name *', p.name, cap: TextCapitalization.words),
          _field('tagline', 'Tagline (optional)', p.tagline, hint: tr('e.g. Skilled & unskilled manpower supplier')),
          _field('ownerName', 'Owner name', p.ownerName, cap: TextCapitalization.words),
          SectionTitle(tr('CONTACT'), padding: EdgeInsets.fromLTRB(2, 12, 2, 10)),
          _field('mobile', 'Mobile', p.mobile, type: TextInputType.phone),
          _field('altMobile', 'Alternate mobile', p.altMobile, type: TextInputType.phone),
          _field('email', 'Email', p.email, type: TextInputType.emailAddress),
          _field('website', 'Website', p.website, type: TextInputType.url),
          SectionTitle(tr('ADDRESS'), padding: EdgeInsets.fromLTRB(2, 12, 2, 10)),
          _field('address', 'Street / area', p.address, lines: 2, cap: TextCapitalization.sentences),
          Row(children: [
            Expanded(child: _field('city', 'City', p.city, cap: TextCapitalization.words)),
            const SizedBox(width: 10),
            Expanded(child: _field('pincode', 'PIN code', p.pincode, type: TextInputType.number)),
          ]),
          _field('state', 'State', p.state, cap: TextCapitalization.words),
          SectionTitle(tr('TAX & REGISTRATION'), padding: EdgeInsets.fromLTRB(2, 12, 2, 4)),
          SwitchListTile(
            contentPadding: EdgeInsets.zero,
            title: Text(tr('I charge tax on invoices')),
            subtitle: Text(tr('Turn off if you are not GST / tax registered.')),
            value: p.taxEnabled,
            activeThumbColor: Palette.brand,
            onChanged: (v) => setState(() => _p = p.copyWith(taxEnabled: v)),
          ),
          if (p.taxEnabled) ...[
            const SizedBox(height: 6),
            Row(children: [
              Expanded(child: _field('taxLabel', 'Tax name', p.taxLabel, hint: tr('GST'))),
              const SizedBox(width: 10),
              Expanded(child: _field('taxRate', 'Rate %', num1(p.taxRate), type: const TextInputType.numberWithOptions(decimal: true))),
            ]),
            _field('taxId', trf('{tax} registration number', {'tax': p.taxLabel}), p.taxId, cap: TextCapitalization.characters),
          ],
          Row(children: [
            Expanded(flex: 2, child: _field('registrationLabel', 'Other ID name', p.registrationLabel, hint: tr('Udyam / PAN / Labour licence'))),
            const SizedBox(width: 10),
            Expanded(flex: 3, child: _field('registrationId', 'Number', p.registrationId)),
          ]),
          SectionTitle(tr('PAYMENT DETAILS ON BILLS'), padding: EdgeInsets.fromLTRB(2, 12, 2, 10)),
          if (p.country.hasUpi) ...[
            _field('upiId', 'UPI ID', p.upiId, hint: tr('name@bank'), helper: 'A payment QR code is generated from this on every invoice.'),
            _field('upiName', 'Name shown on UPI (optional)', p.upiName),
          ],
          Row(children: [
            OutlinedButton.icon(
              style: OutlinedButton.styleFrom(minimumSize: const Size(0, 48)),
              onPressed: () async {
                final path = await pickAndStoreImage(context, folder: 'brand');
                if (path != null) setState(() => _p = p.copyWith(qrImagePath: path));
              },
              icon: Icon(p.qrImagePath == null ? Icons.qr_code_2 : Icons.check_circle, color: p.qrImagePath == null ? null : Palette.present),
              label: Text(p.qrImagePath == null ? tr('Upload your own QR image') : tr('QR image set')),
            ),
            if (p.qrImagePath != null)
              TextButton(onPressed: () => setState(() => _p = p.copyWith(qrImagePath: null)), child: Text(tr('Remove'))),
          ]),
          const SizedBox(height: 14),
          _field('bankName', 'Bank name', p.bankName, cap: TextCapitalization.words),
          _field('accountName', 'Account holder name', p.accountName, cap: TextCapitalization.words),
          _field('accountNumber', 'Account number', p.accountNumber, type: TextInputType.number),
          _field('ifsc', p.country.bankCodeLabel, p.ifsc, cap: TextCapitalization.characters),
          SectionTitle(tr('INVOICE SETTINGS'), padding: EdgeInsets.fromLTRB(2, 12, 2, 10)),
          Row(children: [
            Expanded(child: _field('invoicePrefix', 'Invoice prefix', p.invoicePrefix)),
            const SizedBox(width: 10),
            Expanded(child: _field('terms', 'Payment terms (days)', '${p.paymentTermsDays}', type: TextInputType.number)),
          ]),
          _field('invoiceTerms', 'Terms & conditions', p.invoiceTerms, lines: 4, helper: 'Printed at the bottom of every invoice.'),
          _field('footerNote', 'Footer message', p.footerNote, helper: 'Printed at the bottom of every page.'),
          SectionTitle(tr('PAYROLL'), padding: EdgeInsets.fromLTRB(2, 12, 2, 10)),
          _field('divisor', 'Days in a month for monthly salaries', '${p.monthDivisor}',
              type: TextInputType.number, helper: 'Monthly salary ÷ this number = daily rate. Usually 26 or 30.'),
        ],
      ),
    );
  }
}

/// Mini letterhead so the owner sees how documents will look.
class _Preview extends StatelessWidget {
  const _Preview({
    required this.profile,
    required this.name,
    required this.tagline,
    required this.mobile,
    required this.email,
    required this.website,
    required this.address,
    required this.taxLine,
  });

  final BusinessProfile profile;
  final String name, tagline, mobile, email, website, address, taxLine;

  @override
  Widget build(BuildContext context) {
    final contact = [mobile, email, website].where((e) => e.isNotEmpty).join('  •  ');
    final logo = fileImage(profile.logoPath);
    return AppCard(
      padding: const EdgeInsets.all(14),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(tr('HOW YOUR BILLS WILL LOOK'),
            style: TextStyle(fontSize: 10.5, fontWeight: FontWeight.w800, color: Palette.muted, letterSpacing: 0.8)),
        const SizedBox(height: 10),
        Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          if (logo != null) ...[
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Palette.line),
                image: DecorationImage(image: logo, fit: BoxFit.contain),
              ),
            ),
            const SizedBox(width: 10),
          ],
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(name.isEmpty ? tr('Your business name') : name,
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Palette.brandDark)),
              if (tagline.isNotEmpty) Text(tagline, style: const TextStyle(fontSize: 11.5, color: Palette.muted)),
              if (address.isNotEmpty) Text(address, style: const TextStyle(fontSize: 11.5)),
              if (taxLine.isNotEmpty) Text(taxLine, style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700)),
              if (contact.isNotEmpty) Text(contact, style: const TextStyle(fontSize: 11.5, color: Palette.muted)),
            ]),
          ),
          Text(tr('INVOICE'), style: TextStyle(fontWeight: FontWeight.w800, color: Palette.brand, letterSpacing: 1)),
        ]),
        const SizedBox(height: 10),
        Container(height: 2, color: Palette.brand),
      ]),
    );
  }
}
