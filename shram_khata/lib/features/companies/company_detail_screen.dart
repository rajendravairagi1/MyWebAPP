import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/billing_service.dart';
import '../../data/services/company_service.dart';
import '../../state/providers.dart';
import '../billing/invoice_create_screen.dart';
import '../billing/invoices_screen.dart' show InvoiceTile;
import 'company_form_screen.dart';
import 'contract_form_screen.dart';

final _companyProvider = FutureProvider.autoDispose.family<Company?, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(companyServiceProvider).company(id);
});
final _sitesProvider = FutureProvider.autoDispose.family<List<Site>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(companyServiceProvider).sites(companyId: id, activeOnly: false);
});
final _contractsProvider = FutureProvider.autoDispose.family<List<ContractWithRates>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(companyServiceProvider).contracts(id);
});
final _companyInvoicesProvider = FutureProvider.autoDispose.family<List<InvoiceRow>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(billingServiceProvider).list(companyId: id);
});

class CompanyDetailScreen extends ConsumerWidget {
  const CompanyDetailScreen({super.key, required this.companyId});
  final String companyId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final company = ref.watch(_companyProvider(companyId));
    final session = ref.watch(sessionProvider);
    final manage = session.can(Perm.companiesManage);
    return company.when(
      loading: () => const Scaffold(body: LoadingView()),
      error: (e, _) => Scaffold(appBar: AppBar(), body: ErrorView(e)),
      data: (c) {
        if (c == null) return Scaffold(appBar: AppBar(), body: const EmptyState(icon: Icons.apartment, title: 'Company not found'));
        final sites = ref.watch(_sitesProvider(c.id)).value ?? const <Site>[];
        final contracts = ref.watch(_contractsProvider(c.id)).value ?? const <ContractWithRates>[];
        final invoices = ref.watch(_companyInvoicesProvider(c.id)).value ?? const <InvoiceRow>[];
        final due = invoices.fold<int>(0, (a, r) => a + r.outstanding);
        return Scaffold(
          appBar: AppBar(
            title: Text(c.name),
            actions: [
              if (manage)
                IconButton(
                  icon: const Icon(Icons.edit_outlined),
                  onPressed: () => Navigator.push(
                      context, MaterialPageRoute(builder: (_) => CompanyFormScreen(company: c))),
                ),
            ],
          ),
          body: ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 32), children: [
            AppCard(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                if (c.contactPerson.isNotEmpty || c.mobile.isNotEmpty)
                  _line(Icons.person_outline, [c.contactPerson, c.mobile].where((e) => e.isNotEmpty).join(' • ')),
                if (c.email.isNotEmpty) _line(Icons.mail_outline, c.email),
                if (c.address.isNotEmpty) _line(Icons.place_outlined, c.address),
                if (c.gstin.isNotEmpty) _line(Icons.badge_outlined, c.gstin),
                const Divider(height: 22),
                Row(children: [
                  Expanded(child: Stat(label: 'Outstanding', value: Money.format(due), color: due > 0 ? Palette.info : Palette.ink)),
                  Expanded(child: Stat(label: 'Sites', value: '${sites.where((s) => s.isActive).length}')),
                  Expanded(child: Stat(label: 'Invoices', value: '${invoices.length}')),
                ]),
              ]),
            ),
            if (session.can(Perm.billingManage)) ...[
              const SizedBox(height: 12),
              FilledButton.icon(
                icon: const Icon(Icons.receipt_long_outlined),
                label: const Text('Create invoice'),
                onPressed: () => Navigator.push(
                    context, MaterialPageRoute(builder: (_) => InvoiceCreateScreen(companyId: c.id))),
              ),
            ],
            SectionTitle('SITES',
                padding: const EdgeInsets.fromLTRB(4, 22, 4, 8),
                trailing: manage ? TextButton(onPressed: () => _siteDialog(context, ref, c.id), child: const Text('Add site')) : null),
            if (sites.isEmpty)
              const AppCard(child: Text('Add at least one site to take attendance.', style: TextStyle(color: Palette.muted)))
            else
              AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < sites.length; i++) ...[
                    ListTile(
                      leading: Icon(Icons.location_on_outlined, color: sites[i].isActive ? Palette.brand : Palette.muted),
                      title: Text(sites[i].name,
                          style: TextStyle(fontWeight: FontWeight.w700, color: sites[i].isActive ? null : Palette.muted)),
                      subtitle: sites[i].address.isEmpty ? null : Text(sites[i].address),
                      trailing: !sites[i].isActive
                          ? const Pill('Inactive', color: Palette.muted)
                          : (manage ? const Icon(Icons.edit_outlined, size: 18, color: Palette.muted) : null),
                      onTap: manage ? () => _siteDialog(context, ref, c.id, site: sites[i]) : null,
                    ),
                    if (i < sites.length - 1) const Divider(indent: 16),
                  ],
                ]),
              ),
            SectionTitle('CONTRACTS & BILLING RATES',
                padding: const EdgeInsets.fromLTRB(4, 22, 4, 8),
                trailing: manage
                    ? TextButton(
                        onPressed: () => Navigator.push(
                            context, MaterialPageRoute(builder: (_) => ContractFormScreen(companyId: c.id))),
                        child: const Text('Add'))
                    : null),
            if (contracts.isEmpty)
              const AppCard(
                  child: Text('No contract yet. Add one with billing rates per skill, otherwise invoices cannot be priced.',
                      style: TextStyle(color: Palette.muted)))
            else
              for (final k in contracts)
                Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: _ContractCard(c: k, companyId: c.id, manage: manage),
                ),
            SectionTitle('INVOICES', padding: const EdgeInsets.fromLTRB(4, 22, 4, 8)),
            if (invoices.isEmpty)
              const AppCard(child: Text('No invoices yet.', style: TextStyle(color: Palette.muted)))
            else
              AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < invoices.length; i++) ...[
                    InvoiceTile(row: invoices[i], showCompany: false),
                    if (i < invoices.length - 1) const Divider(indent: 16),
                  ],
                ]),
              ),
          ]),
        );
      },
    );
  }

  Widget _line(IconData icon, String text) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Icon(icon, size: 18, color: Palette.muted),
          const SizedBox(width: 10),
          Expanded(child: Text(text)),
        ]),
      );

  Future<void> _siteDialog(BuildContext context, WidgetRef ref, String companyId, {Site? site}) async {
    final name = TextEditingController(text: site?.name);
    final addr = TextEditingController(text: site?.address);
    var active = site?.isActive ?? true;
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setS) => AlertDialog(
          title: Text(site == null ? 'Add site' : 'Edit site'),
          content: Column(mainAxisSize: MainAxisSize.min, children: [
            TextField(
              controller: name,
              autofocus: true,
              textCapitalization: TextCapitalization.words,
              decoration: const InputDecoration(labelText: 'Site / plant name'),
            ),
            const SizedBox(height: 10),
            TextField(controller: addr, decoration: const InputDecoration(labelText: 'Address (optional)')),
            if (site != null)
              SwitchListTile(
                contentPadding: EdgeInsets.zero,
                title: const Text('Active'),
                value: active,
                onChanged: (v) => setS(() => active = v),
              ),
          ]),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
            TextButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Save')),
          ],
        ),
      ),
    );
    if (ok != true) return;
    try {
      final svc = ref.read(companyServiceProvider);
      if (site == null) {
        await svc.addSite(companyId: companyId, name: name.text, address: addr.text);
      } else {
        await svc.updateSite(site.copyWith(name: name.text.trim(), address: addr.text.trim(), isActive: active));
      }
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }
}

class _ContractCard extends StatelessWidget {
  const _ContractCard({required this.c, required this.companyId, required this.manage});
  final ContractWithRates c;
  final String companyId;
  final bool manage;

  @override
  Widget build(BuildContext context) {
    final k = c.contract;
    final today = D.today();
    final ended = k.endDate != null && k.endDate!.compareTo(today) < 0;
    final soon = k.endDate != null && !ended && k.endDate!.compareTo(D.ymd(D.addDays(DateTime.now(), 30))) <= 0;
    return AppCard(
      onTap: manage
          ? () => Navigator.push(context, MaterialPageRoute(builder: (_) => ContractFormScreen(companyId: companyId, existing: c)))
          : null,
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Expanded(child: Text(k.title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15.5))),
          if (!k.isActive)
            const Pill('Inactive', color: Palette.muted)
          else if (ended)
            const Pill('Ended', color: Palette.absent)
          else if (soon)
            const Pill('Ending soon', color: Palette.half)
          else
            const Pill('Active', color: Palette.present),
        ]),
        const SizedBox(height: 2),
        Text(
          '${D.show(k.startDate)} – ${k.endDate == null ? 'open ended' : D.show(k.endDate!)} • ${k.paymentTermsDays} day terms',
          style: const TextStyle(color: Palette.muted, fontSize: 12.5),
        ),
        const SizedBox(height: 10),
        if (c.rates.isEmpty)
          const Text('No rates set', style: TextStyle(color: Palette.absent))
        else
          Wrap(spacing: 8, runSpacing: 8, children: [
            for (final r in c.rates)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(color: Palette.brandTint, borderRadius: BorderRadius.circular(10)),
                child: Text('${r.skill}  ${Money.format(r.perDay)}/day',
                    style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12.5, color: Palette.brandDark)),
              ),
          ]),
        if (k.docPath != null && File(k.docPath!).existsSync())
          Padding(
            padding: const EdgeInsets.only(top: 10),
            child: InkWell(
              onTap: () => showDialog<void>(
                context: context,
                builder: (_) => Dialog(clipBehavior: Clip.antiAlias, child: InteractiveViewer(child: Image.file(File(k.docPath!)))),
              ),
              child: Row(children: [
                Icon(Icons.attach_file, size: 16, color: Palette.brand),
                SizedBox(width: 4),
                Text('View contract copy', style: TextStyle(color: Palette.brand, fontWeight: FontWeight.w700, fontSize: 13)),
              ]),
            ),
          ),
      ]),
    );
  }
}
