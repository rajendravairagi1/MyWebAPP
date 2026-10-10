import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';
import 'company_detail_screen.dart';
import 'company_form_screen.dart';

class CompaniesScreen extends ConsumerWidget {
  const CompaniesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final companies = ref.watch(companiesProvider);
    final invoices = ref.watch(invoicesProvider).value ?? const [];
    final session = ref.watch(sessionProvider);
    final outstanding = <String, int>{};
    for (final i in invoices) {
      outstanding[i.invoice.companyId] = (outstanding[i.invoice.companyId] ?? 0) + i.outstanding;
    }
    return Scaffold(
      appBar: AppBar(title: const Text('Companies')),
      floatingActionButton: session.can(Perm.companiesManage)
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CompanyFormScreen())),
              icon: const Icon(Icons.add_business_outlined),
              label: const Text('Add company'),
            )
          : null,
      body: companies.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (list) {
          if (list.isEmpty) {
            return const EmptyState(
              icon: Icons.apartment_outlined,
              title: 'No companies yet',
              message: 'Add the companies you supply labour to. Each company can have several sites and a contract with billing rates.',
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 4, 16, 96),
            itemCount: list.length,
            separatorBuilder: (_, _) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final Company c = list[i];
              final due = outstanding[c.id] ?? 0;
              return AppCard(
                onTap: () => Navigator.push(
                    context, MaterialPageRoute(builder: (_) => CompanyDetailScreen(companyId: c.id))),
                child: Row(children: [
                  Container(
                    width: 46,
                    height: 46,
                    decoration: BoxDecoration(color: Palette.brandTint, borderRadius: BorderRadius.circular(14)),
                    child: Icon(Icons.apartment, color: Palette.brand),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(c.name, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15.5)),
                      Text(
                        [if (c.contactPerson.isNotEmpty) c.contactPerson, if (c.mobile.isNotEmpty) c.mobile].join(' • '),
                        style: const TextStyle(color: Palette.muted, fontSize: 13),
                      ),
                    ]),
                  ),
                  if (due > 0)
                    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                      Text(Money.compact(due), style: const TextStyle(fontWeight: FontWeight.w800, color: Palette.info)),
                      const Text('due', style: TextStyle(fontSize: 11, color: Palette.muted)),
                    ]),
                ]),
              );
            },
          );
        },
      ),
    );
  }
}
