import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/files.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/payment_service.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';
import 'payment_form_screen.dart';
import 'payment_widgets.dart';

enum _Range { today, week, month }

typedef _PayKey = ({String from, String to});

final _paymentsProvider = FutureProvider.autoDispose.family<List<PaymentRow>, _PayKey>((ref, k) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  return ref.watch(paymentServiceProvider).list(branchId: branch, from: k.from, to: k.to);
});

class PaymentsScreen extends ConsumerStatefulWidget {
  const PaymentsScreen({super.key});

  @override
  ConsumerState<PaymentsScreen> createState() => _PaymentsScreenState();
}

class _PaymentsScreenState extends ConsumerState<PaymentsScreen> {
  int _view = 0; // 0 payments, 1 balances
  _Range _range = _Range.today;

  _PayKey get _key {
    final now = DateTime.now();
    return switch (_range) {
      _Range.today => (from: D.ymd(now), to: D.ymd(now)),
      _Range.week => (from: D.ymd(D.addDays(now, -6)), to: D.ymd(now)),
      _Range.month => (from: D.ymd(D.firstOfMonth(now)), to: D.ymd(now)),
    };
  }

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Payments')),
      floatingActionButton: session.can(Perm.paymentsManage)
          ? FloatingActionButton.extended(
              onPressed: () =>
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const PaymentFormScreen())),
              icon: const Icon(Icons.add),
              label: const Text('Add payment'),
            )
          : null,
      body: Column(children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
          child: SegmentedButton<int>(
            showSelectedIcon: false,
            segments: const [
              ButtonSegment(value: 0, label: Text('Payments made'), icon: Icon(Icons.receipt_long_outlined)),
              ButtonSegment(value: 1, label: Text('Who to pay'), icon: Icon(Icons.pending_actions)),
            ],
            selected: {_view},
            onSelectionChanged: (v) => setState(() => _view = v.first),
          ),
        ),
        Expanded(child: _view == 0 ? _list() : const _Balances()),
      ]),
    );
  }

  Widget _list() {
    final rows = ref.watch(_paymentsProvider(_key));
    final canVoid = ref.watch(sessionProvider).can(Perm.paymentsManage);
    return Column(children: [
      SizedBox(
        height: 42,
        child: ListView(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          children: [
            for (final e in const [(_Range.today, 'Today'), (_Range.week, 'Last 7 days'), (_Range.month, 'This month')])
              Padding(
                padding: const EdgeInsets.only(right: 8),
                child: ChoiceChip(
                  label: Text(e.$2),
                  selected: _range == e.$1,
                  onSelected: (_) => setState(() => _range = e.$1),
                ),
              ),
          ],
        ),
      ),
      Expanded(
        child: rows.when(
          loading: () => const LoadingView(),
          error: (e, _) => ErrorView(e),
          data: (list) {
            if (list.isEmpty) {
              return const EmptyState(
                icon: Icons.account_balance_wallet_outlined,
                title: 'No payments in this period',
                message: 'Daily payments, advances and settlements you record will show up here.',
              );
            }
            final active = list.where((r) => r.payment.voidedAt == null && PayType.isCashOut(r.payment.type));
            final byMode = <String, int>{};
            for (final r in active) {
              byMode[r.payment.mode] = (byMode[r.payment.mode] ?? 0) + r.payment.amount;
            }
            final total = byMode.values.fold(0, (a, b) => a + b);
            final byDate = <String, List<PaymentRow>>{};
            for (final r in list) {
              (byDate[r.payment.date] ??= []).add(r);
            }
            return ListView(padding: const EdgeInsets.fromLTRB(16, 8, 16, 96), children: [
              AppCard(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Total paid', style: TextStyle(color: Palette.muted, fontWeight: FontWeight.w600)),
                  Text(Money.format(total), style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 10),
                  Row(children: [
                    for (final m in PayMode.all)
                      Expanded(
                        child: Row(children: [
                          Icon(modeIcon(m), size: 16, color: Palette.muted),
                          const SizedBox(width: 5),
                          Flexible(
                            child: Text('${PayMode.label(m)} ${Money.compact(byMode[m] ?? 0)}',
                                style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600),
                                overflow: TextOverflow.ellipsis),
                          ),
                        ]),
                      ),
                  ]),
                ]),
              ),
              for (final e in byDate.entries) ...[
                SectionTitle(
                  D.isToday(D.parse(e.key)) ? 'TODAY' : D.show(e.key).toUpperCase(),
                  padding: const EdgeInsets.fromLTRB(4, 18, 4, 8),
                ),
                AppCard(
                  padding: EdgeInsets.zero,
                  child: Column(children: [
                    for (var i = 0; i < e.value.length; i++) ...[
                      PaymentTile(row: e.value[i], canVoid: canVoid),
                      if (i < e.value.length - 1) const Divider(indent: 16, endIndent: 16),
                    ],
                  ]),
                ),
              ],
            ]);
          },
        ),
      ),
    ]);
  }
}

/// Everyone with an amount to pay (or an advance to recover), largest first.
class _Balances extends ConsumerStatefulWidget {
  const _Balances();

  @override
  ConsumerState<_Balances> createState() => _BalancesState();
}

class _BalancesState extends ConsumerState<_Balances> {
  String _q = '';

  @override
  Widget build(BuildContext context) {
    final balances = ref.watch(balancesProvider);
    final labours = ref.watch(labourListProvider((query: _q, statuses: 'active,inactive,left')));
    final canAdd = ref.watch(sessionProvider).can(Perm.paymentsManage);
    return Column(children: [
      Padding(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
        child: TextField(
          onChanged: (v) => setState(() => _q = v),
          decoration: const InputDecoration(hintText: 'Search labour', prefixIcon: Icon(Icons.search)),
        ),
      ),
      Expanded(
        child: balances.when(
          loading: () => const LoadingView(),
          error: (e, _) => ErrorView(e),
          data: (bal) => labours.when(
            loading: () => const LoadingView(),
            error: (e, _) => ErrorView(e),
            data: (list) {
              final rows = list.where((l) => (bal[l.id] ?? 0) != 0).toList()
                ..sort((a, b) => (bal[b.id] ?? 0).compareTo(bal[a.id] ?? 0));
              if (rows.isEmpty) {
                return const EmptyState(
                  icon: Icons.verified_outlined,
                  title: 'All settled',
                  message: 'Nobody has a pending balance right now.',
                );
              }
              final owed = rows.fold<int>(0, (a, l) => a + ((bal[l.id] ?? 0) > 0 ? bal[l.id]! : 0));
              final adv = rows.fold<int>(0, (a, l) => a + ((bal[l.id] ?? 0) < 0 ? -bal[l.id]! : 0));
              return ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 96), children: [
                Row(children: [
                  Expanded(child: AppCard(child: Stat(label: 'To pay labour', value: Money.format(owed), color: Palette.absent))),
                  const SizedBox(width: 10),
                  Expanded(child: AppCard(child: Stat(label: 'Advances to recover', value: Money.format(adv), color: Palette.half))),
                ]),
                const SizedBox(height: 12),
                for (final Labour l in rows)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 8),
                    child: AppCard(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      onTap: canAdd
                          ? () => Navigator.push(
                                context,
                                MaterialPageRoute(builder: (_) => PaymentFormScreen(labourId: l.id)),
                              )
                          : null,
                      child: Row(children: [
                        Avatar(name: l.name, image: fileImage(l.photoPath), size: 42),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(l.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                            Text(l.skill, style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                          ]),
                        ),
                        Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                          BalanceText(bal[l.id]!, size: 16),
                          Text(bal[l.id]! > 0 ? 'to pay' : 'advance',
                              style: const TextStyle(fontSize: 11, color: Palette.muted)),
                        ]),
                      ]),
                    ),
                  ),
              ]);
            },
          ),
        ),
      ),
    ]);
  }
}
