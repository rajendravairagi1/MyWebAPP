import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../data/database.dart';
import '../../data/services/billing_service.dart';
import '../../domain/business_profile.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';

/// Month shown on the manager's screens.
class ManagerMonth extends Notifier<DateTime> {
  @override
  DateTime build() => D.firstOfMonth(DateTime.now());
  void set(DateTime d) => state = D.firstOfMonth(d);
}

final managerMonthProvider = NotifierProvider<ManagerMonth, DateTime>(ManagerMonth.new);

String? _company(Ref ref) => ref.watch(cloudProvider).session?.scopeCompanyId;

final managerCompanyProvider = FutureProvider.autoDispose<Company?>((ref) async {
  ref.watch(dbTickProvider);
  final id = _company(ref);
  if (id == null) return null;
  return ref.watch(companyServiceProvider).company(id);
});

/// What the month so far is worth at the contract rates, per worker.
final managerBillProvider = FutureProvider.autoDispose<InvoiceDraft?>((ref) async {
  ref.watch(dbTickProvider);
  final id = _company(ref);
  if (id == null) return null;
  final m = ref.watch(managerMonthProvider);
  final end = D.lastOfMonth(m);
  final to = end.isAfter(DateTime.now()) ? DateTime.now() : end;
  if (to.isBefore(m)) return null;
  return ref.watch(billingServiceProvider).preview(companyId: id, from: D.ymd(m), to: D.ymd(to));
});

final managerInvoicesProvider = FutureProvider.autoDispose<List<InvoiceRow>>((ref) async {
  ref.watch(dbTickProvider);
  final id = _company(ref);
  if (id == null) return const [];
  return ref.watch(billingServiceProvider).list(companyId: id);
});

final managerProfileProvider = FutureProvider.autoDispose<BusinessProfile>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(workspaceServiceProvider).profile();
});
