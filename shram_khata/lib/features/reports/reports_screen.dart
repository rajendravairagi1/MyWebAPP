import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../pdf/register_pdf.dart';
import '../../pdf/statement_pdf.dart';
import '../../state/providers.dart';
import '../labour/labour_pickers.dart';
import 'pdf_screen.dart';
import 'period.dart';

class ReportsScreen extends ConsumerWidget {
  const ReportsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final branch = ref.watch(scopeBranchProvider);

    Widget card(IconData icon, Color color, String title, String sub, VoidCallback onTap) => Padding(
          padding: const EdgeInsets.only(bottom: 10),
          child: AppCard(
            onTap: onTap,
            child: Row(children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(14)),
                child: Icon(icon, color: color),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15.5)),
                  const SizedBox(height: 2),
                  Text(sub, style: const TextStyle(color: Palette.muted, fontSize: 12.5, height: 1.3)),
                ]),
              ),
              const Icon(Icons.chevron_right, color: Palette.muted),
            ]),
          ),
        );

    return Scaffold(
      appBar: AppBar(title: const Text('Reports')),
      body: ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 32), children: [
        card(Icons.person_outline, Palette.brand, 'Labour statement',
            'One person: P / H / A calendar, earnings, payments and balance.', () async {
          final l = await pickLabour(context, title: 'Statement for', includeInactive: true);
          if (l == null || !context.mounted) return;
          final p = await pickPeriod(context, title: 'Statement period');
          if (p == null || !context.mounted) return;
          await openPdf(context,
              title: 'Statement',
              fileName: 'statement_${l.name.replaceAll(' ', '_')}_${p.from}_${p.to}',
              build: () async => StatementPdf.build(await ref.read(reportServiceProvider).statement(l.id, p.from, p.to)));
        }),
        card(Icons.grid_on_outlined, Palette.info, 'Attendance register',
            'Everyone × every day for a company or site. Good to share with the company.', () async {
          final pick = await showModalBottomSheet<({String companyId, String? siteId})>(
            context: context,
            isScrollControlled: true,
            builder: (_) => const _CompanySitePicker(),
          );
          if (pick == null || !context.mounted) return;
          final p = await pickPeriod(context, title: 'Register period');
          if (p == null || !context.mounted) return;
          await openPdf(context, title: 'Attendance register', fileName: 'attendance_${p.from}_${p.to}', build: () async {
            final cs = ref.read(companyServiceProvider);
            final reports = ref.read(reportServiceProvider);
            final company = (await cs.company(pick.companyId))!;
            final sites = await cs.sites(companyId: pick.companyId, activeOnly: false);
            final siteIds = pick.siteId != null ? [pick.siteId!] : sites.map((s) => s.id).toList();
            final matrix = await reports.matrix(siteIds: siteIds, from: p.from, to: p.to);
            final siteName = pick.siteId == null ? 'All sites' : sites.firstWhere((s) => s.id == pick.siteId).name;
            return RegisterPdf.attendance(
              profile: await ref.read(workspaceServiceProvider).profile(),
              heading: '${company.name} • $siteName',
              matrix: matrix,
            );
          });
        }),
        card(Icons.receipt_long_outlined, Palette.present, 'Payment register',
            'Every payment made in a period, split by cash / UPI / bank.', () async {
          final p = await pickPeriod(context, title: 'Payment period');
          if (p == null || !context.mounted) return;
          await openPdf(context, title: 'Payment register', fileName: 'payments_${p.from}_${p.to}', build: () async {
            final reports = ref.read(reportServiceProvider);
            return RegisterPdf.payments(
              profile: await ref.read(workspaceServiceProvider).profile(),
              rows: await reports.paymentRegister(branchId: branch, from: p.from, to: p.to),
              from: p.from,
              to: p.to,
            );
          });
        }),
        card(Icons.account_balance_wallet_outlined, Palette.absent, 'Labour balances',
            'Who is owed money and who has taken advances, with totals.', () async {
          final p = await pickPeriod(context, title: 'Balances for');
          if (p == null || !context.mounted) return;
          await openPdf(context, title: 'Labour balances', fileName: 'balances_${p.from}_${p.to}', build: () async {
            final reports = ref.read(reportServiceProvider);
            return RegisterPdf.balances(
              profile: await ref.read(workspaceServiceProvider).profile(),
              rows: await reports.balances(branchId: branch, from: p.from, to: p.to),
              from: p.from,
              to: p.to,
            );
          });
        }),
      ]),
    );
  }
}

class _CompanySitePicker extends ConsumerStatefulWidget {
  const _CompanySitePicker();

  @override
  ConsumerState<_CompanySitePicker> createState() => _CompanySitePickerState();
}

class _CompanySitePickerState extends ConsumerState<_CompanySitePicker> {
  String? _company;
  String? _site;
  List<Site> _sites = [];

  @override
  Widget build(BuildContext context) {
    final companies = ref.watch(companiesProvider).value ?? const <Company>[];
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(context).viewInsets.bottom + 20),
      child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Which company?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
        const SizedBox(height: 14),
        DropdownButtonFormField<String>(
          initialValue: _company,
          decoration: const InputDecoration(labelText: 'Company'),
          items: [for (final c in companies) DropdownMenuItem(value: c.id, child: Text(c.name))],
          onChanged: (v) async {
            final s = await ref.read(companyServiceProvider).sites(companyId: v, activeOnly: false);
            setState(() {
              _company = v;
              _site = null;
              _sites = s;
            });
          },
        ),
        if (_sites.length > 1) ...[
          const SizedBox(height: 12),
          DropdownButtonFormField<String?>(
            initialValue: _site,
            decoration: const InputDecoration(labelText: 'Site'),
            items: [
              const DropdownMenuItem(value: null, child: Text('All sites')),
              for (final s in _sites) DropdownMenuItem(value: s.id, child: Text(s.name)),
            ],
            onChanged: (v) => setState(() => _site = v),
          ),
        ],
        const SizedBox(height: 18),
        FilledButton(
          onPressed: _company == null ? null : () => Navigator.pop(context, (companyId: _company!, siteId: _site)),
          child: const Text('Next: choose period'),
        ),
      ]),
    );
  }
}
