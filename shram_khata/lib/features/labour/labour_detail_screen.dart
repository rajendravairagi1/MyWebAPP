import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/files.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/labour_service.dart';
import '../../data/services/payment_service.dart';
import '../../domain/calc.dart';
import '../../pdf/statement_pdf.dart';
import '../../state/providers.dart';
import '../payments/payment_form_screen.dart';
import '../payments/payment_widgets.dart';
import '../reports/pdf_screen.dart';
import '../reports/period.dart';
import 'day_edit_sheet.dart';
import 'labour_form_screen.dart';
import 'labour_pickers.dart';

final _labourProvider = FutureProvider.autoDispose.family<Labour?, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(labourServiceProvider).byId(id);
});

class LabourDetailScreen extends ConsumerWidget {
  const LabourDetailScreen({super.key, required this.labourId});
  final String labourId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final labour = ref.watch(_labourProvider(labourId));
    final session = ref.watch(sessionProvider);
    return labour.when(
      loading: () => const Scaffold(body: LoadingView()),
      error: (e, _) => Scaffold(appBar: AppBar(), body: ErrorView(e)),
      data: (l) {
        if (l == null) return Scaffold(appBar: AppBar(), body: const EmptyState(icon: Icons.person_off_outlined, title: 'Labour not found'));
        final showPay = session.can(Perm.paymentsView);
        final tabs = <Tab>[
          const Tab(text: 'Attendance'),
          if (showPay) const Tab(text: 'Payments'),
          const Tab(text: 'Details'),
        ];
        return DefaultTabController(
          length: tabs.length,
          child: Scaffold(
            appBar: AppBar(
              title: Text(l.name),
              actions: [
                if (session.can(Perm.reports))
                  IconButton(
                    tooltip: 'Statement PDF',
                    icon: const Icon(Icons.picture_as_pdf_outlined),
                    onPressed: () => _statement(context, ref, l),
                  ),
                if (session.can(Perm.labourManage))
                  IconButton(
                    tooltip: 'Edit',
                    icon: const Icon(Icons.edit_outlined),
                    onPressed: () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => LabourFormScreen(labourId: l.id)),
                    ),
                  ),
              ],
            ),
            body: Column(children: [
              _Header(labour: l),
              TabBar(tabs: tabs),
              Expanded(
                child: TabBarView(children: [
                  _AttendanceTab(labour: l, showMoney: showPay),
                  if (showPay) _PaymentsTab(labour: l),
                  _DetailsTab(labour: l),
                ]),
              ),
            ]),
          ),
        );
      },
    );
  }

  Future<void> _statement(BuildContext context, WidgetRef ref, Labour l) async {
    final p = await pickPeriod(context, title: 'Statement period');
    if (p == null || !context.mounted) return;
    await openPdf(
      context,
      title: 'Statement',
      fileName: 'statement_${l.name.replaceAll(' ', '_')}_${p.from}_${p.to}',
      build: () async => StatementPdf.build(await ref.read(reportServiceProvider).statement(l.id, p.from, p.to)),
    );
  }
}

class _Header extends StatelessWidget {
  const _Header({required this.labour});
  final Labour labour;

  @override
  Widget build(BuildContext context) {
    final l = labour;
    final Color c = switch (l.status) {
      LabourStatus.active => Palette.present,
      LabourStatus.inactive => Palette.half,
      _ => Palette.absent,
    };
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 4, 16, 12),
      child: Row(children: [
        Avatar(name: l.name, image: fileImage(l.photoPath), size: 60),
        const SizedBox(width: 14),
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(l.name, style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
            const SizedBox(height: 2),
            Text([l.skill, if (l.mobile.isNotEmpty) l.mobile].join(' • '),
                style: const TextStyle(color: Palette.muted)),
            const SizedBox(height: 6),
            Wrap(spacing: 6, children: [
              Pill(LabourStatus.label(l.status), color: c),
              Pill('Since ${D.show(l.joinDate)}', color: Palette.muted),
            ]),
          ]),
        ),
      ]),
    );
  }
}

// ---- attendance tab -----------------------------------------------------------

typedef _MonthKey = ({String id, int y, int m});

final _monthProvider = FutureProvider.autoDispose.family<_MonthData, _MonthKey>((ref, k) async {
  ref.watch(dbTickProvider);
  final from = DateTime(k.y, k.m, 1);
  final to = D.lastOfMonth(from);
  final att = await ref.watch(attendanceServiceProvider).entries(
        labourId: k.id,
        from: D.ymd(from),
        to: D.ymd(to),
      );
  final ledger = await ref.watch(paymentServiceProvider).ledger(k.id, from: D.ymd(from), to: D.ymd(to));
  return _MonthData(att, ledger);
});

class _MonthData {
  _MonthData(this.entries, this.ledger);
  final List<AttendanceData> entries;
  final Ledger ledger;
}

class _AttendanceTab extends ConsumerStatefulWidget {
  const _AttendanceTab({required this.labour, required this.showMoney});
  final Labour labour;
  final bool showMoney;

  @override
  ConsumerState<_AttendanceTab> createState() => _AttendanceTabState();
}

class _AttendanceTabState extends ConsumerState<_AttendanceTab> {
  DateTime _month = D.firstOfMonth(DateTime.now());

  @override
  Widget build(BuildContext context) {
    final data = ref.watch(_monthProvider((id: widget.labour.id, y: _month.year, m: _month.month)));
    final canNext = _month.isBefore(D.firstOfMonth(DateTime.now()));
    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
      children: [
        Row(children: [
          IconButton(
            onPressed: () => setState(() => _month = DateTime(_month.year, _month.month - 1)),
            icon: const Icon(Icons.chevron_left),
          ),
          Expanded(
            child: Text(D.monthTitle(_month),
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
          ),
          IconButton(
            onPressed: canNext ? () => setState(() => _month = DateTime(_month.year, _month.month + 1)) : null,
            icon: const Icon(Icons.chevron_right),
          ),
        ]),
        data.when(
          loading: () => const Padding(padding: EdgeInsets.all(40), child: LoadingView()),
          error: (e, _) => ErrorView(e),
          data: (m) => Column(children: [
            _Calendar(
              month: _month,
              entries: m.entries,
              onDayTap: (d) => editAttendanceDay(context, ref, widget.labour, d),
            ),
            Text('Tap a day to add or correct attendance',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 12, color: Palette.muted)),
            const SizedBox(height: 14),
            Row(children: [
              _Tally('P', m.ledger.presentRows),
              const SizedBox(width: 8),
              _Tally('H', m.ledger.halfRows),
              const SizedBox(width: 8),
              _Tally('A', m.ledger.absentRows),
              const SizedBox(width: 8),
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(vertical: 10),
                  decoration: BoxDecoration(color: Palette.infoBg, borderRadius: BorderRadius.circular(12)),
                  child: Column(children: [
                    Text(num1(m.ledger.otHours),
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Palette.info)),
                    const Text('OT hours', style: TextStyle(fontSize: 11.5, color: Palette.info, fontWeight: FontWeight.w700)),
                  ]),
                ),
              ),
            ]),
            if (widget.showMoney) ...[
              const SizedBox(height: 14),
              AppCard(
                child: Column(children: [
                  Row(children: [
                    Expanded(child: Stat(label: 'Paid days', value: num1(m.ledger.paidDays))),
                    Expanded(child: Stat(label: 'Earned', value: Money.format(m.ledger.earned))),
                    Expanded(child: Stat(label: 'Paid', value: Money.format(m.ledger.cashPaid + m.ledger.deductions))),
                  ]),
                  const Divider(height: 24),
                  Row(children: [
                    const Text('Balance this month',
                        style: TextStyle(color: Palette.muted, fontWeight: FontWeight.w600)),
                    const Spacer(),
                    BalanceText(m.ledger.balance, size: 20),
                  ]),
                  if (m.ledger.opening != 0)
                    Padding(
                      padding: const EdgeInsets.only(top: 6),
                      child: Row(children: [
                        const Text('includes brought forward',
                            style: TextStyle(color: Palette.muted, fontSize: 12.5)),
                        const Spacer(),
                        Text(Money.format(m.ledger.opening),
                            style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                      ]),
                    ),
                ]),
              ),
            ],
          ]),
        ),
      ],
    );
  }
}

class _Tally extends StatelessWidget {
  const _Tally(this.letter, this.n);
  final String letter;
  final int n;

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(color: Palette.statusBg(letter), borderRadius: BorderRadius.circular(12)),
        child: Column(children: [
          Text('$n', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Palette.status(letter))),
          Text(switch (letter) { 'P' => 'Present', 'H' => 'Half', _ => 'Absent' },
              style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700, color: Palette.status(letter))),
        ]),
      ),
    );
  }
}

class _Calendar extends StatelessWidget {
  const _Calendar({required this.month, required this.entries, required this.onDayTap});
  final DateTime month;
  final ValueChanged<DateTime> onDayTap;
  final List<AttendanceData> entries;

  @override
  Widget build(BuildContext context) {
    final byDate = <String, List<AttendanceData>>{};
    for (final e in entries) {
      (byDate[e.date] ??= []).add(e);
    }
    final lead = month.weekday - 1;
    final days = D.daysInMonth(month);
    final cells = <Widget>[
      for (var i = 0; i < lead; i++) const SizedBox(),
      for (var d = 1; d <= days; d++) _cell(DateTime(month.year, month.month, d), byDate),
    ];
    return AppCard(
      padding: const EdgeInsets.all(10),
      child: Column(children: [
        Row(children: [
          for (final w in ['M', 'T', 'W', 'T', 'F', 'S', 'S'])
            Expanded(
              child: Center(
                child: Text(w, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Palette.muted)),
              ),
            ),
        ]),
        const SizedBox(height: 6),
        GridView.count(
          crossAxisCount: 7,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 4,
          crossAxisSpacing: 4,
          children: cells,
        ),
      ]),
    );
  }

  Widget _cell(DateTime date, Map<String, List<AttendanceData>> byDate) {
    final list = byDate[D.ymd(date)];
    String? letter;
    double ot = 0;
    if (list != null) {
      final v = list.fold<double>(0, (a, e) => a + dayValue(e.status));
      letter = v >= 1 ? 'P' : (v > 0 ? 'H' : 'A');
      ot = list.fold<double>(0, (a, e) => a + (e.status == 'A' ? 0 : e.otHours));
    }
    final future = date.isAfter(DateTime.now());
    return GestureDetector(
      onTap: future ? null : () => onDayTap(date),
      child: Container(
      decoration: BoxDecoration(
        color: letter == null ? (future ? Colors.transparent : const Color(0xFFF6F8F8)) : Palette.statusBg(letter),
        borderRadius: BorderRadius.circular(10),
        border: D.isToday(date) ? Border.all(color: Palette.brand, width: 1.6) : null,
      ),
      child: Stack(children: [
        Positioned(
          left: 5,
          top: 3,
          child: Text('${date.day}',
              style: TextStyle(fontSize: 10, color: future ? Palette.line : Palette.muted, fontWeight: FontWeight.w600)),
        ),
        if (letter != null)
          Center(
            child: Padding(
              padding: const EdgeInsets.only(top: 6),
              child: Text(letter,
                  style: TextStyle(fontSize: 17, fontWeight: FontWeight.w900, color: Palette.status(letter))),
            ),
          ),
        if (ot > 0)
          Positioned(
            right: 4,
            bottom: 2,
            child: Text('+${num1(ot)}', style: const TextStyle(fontSize: 9, color: Palette.info, fontWeight: FontWeight.w800)),
          ),
      ]),
    ));
  }
}

// ---- payments tab -------------------------------------------------------------

final _labourPaymentsProvider = FutureProvider.autoDispose.family<List<PaymentRow>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(paymentServiceProvider).list(labourId: id);
});

final _ledgerAllProvider = FutureProvider.autoDispose.family<Ledger, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(paymentServiceProvider).ledger(id);
});

class _PaymentsTab extends ConsumerWidget {
  const _PaymentsTab({required this.labour});
  final Labour labour;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final rows = ref.watch(_labourPaymentsProvider(labour.id));
    final ledger = ref.watch(_ledgerAllProvider(labour.id));
    final session = ref.watch(sessionProvider);
    final canAdd = session.can(Perm.paymentsManage);
    return Column(children: [
      Expanded(
        child: ListView(padding: const EdgeInsets.fromLTRB(16, 12, 16, 24), children: [
          ledger.maybeWhen(
            data: (l) => AccountSummary(l),
            orElse: () => const SizedBox(height: 90),
          ),
          const SectionTitle('PAYMENT HISTORY', padding: EdgeInsets.fromLTRB(4, 20, 4, 8)),
          rows.when(
            loading: () => const LoadingView(),
            error: (e, _) => ErrorView(e),
            data: (list) {
              if (list.isEmpty) {
                return const AppCard(
                  child: Text('No payments yet.', style: TextStyle(color: Palette.muted)),
                );
              }
              return AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < list.length; i++) ...[
                    PaymentTile(row: list[i], showName: false, canVoid: canAdd),
                    if (i < list.length - 1) const Divider(indent: 16, endIndent: 16),
                  ],
                ]),
              );
            },
          ),
        ]),
      ),
      if (canAdd)
        SafeArea(
          top: false,
          child: Container(
            padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
            decoration: const BoxDecoration(
              color: Palette.surface,
              border: Border(top: BorderSide(color: Palette.line)),
            ),
            child: FilledButton.icon(
              icon: const Icon(Icons.add),
              label: const Text('Add payment'),
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => PaymentFormScreen(labourId: labour.id)),
              ),
            ),
          ),
        ),
    ]);
  }
}

// ---- details tab --------------------------------------------------------------

final _ratesProvider = FutureProvider.autoDispose.family<List<LabourRate>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(labourServiceProvider).rates(id);
});
final _assignmentsProvider = FutureProvider.autoDispose.family<List<AssignmentInfo>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(labourServiceProvider).assignments(id);
});
final _docsProvider = FutureProvider.autoDispose.family<List<LabourDocument>, String>((ref, id) async {
  ref.watch(dbTickProvider);
  return ref.watch(labourServiceProvider).documents(id);
});

class _DetailsTab extends ConsumerWidget {
  const _DetailsTab({required this.labour});
  final Labour labour;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final session = ref.watch(sessionProvider);
    final manage = session.can(Perm.labourManage);
    final rates = ref.watch(_ratesProvider(labour.id));
    final asg = ref.watch(_assignmentsProvider(labour.id));
    final docs = ref.watch(_docsProvider(labour.id));
    final svc = ref.read(labourServiceProvider);
    final showRates = session.can(Perm.paymentsView) || session.can(Perm.labourManage);

    return ListView(padding: const EdgeInsets.fromLTRB(16, 4, 16, 32), children: [
      if (showRates) ...[
        SectionTitle('PAY RATE',
            padding: const EdgeInsets.fromLTRB(4, 12, 4, 8),
            trailing: manage
                ? TextButton(onPressed: () => _changeRate(context, ref), child: const Text('Change'))
                : null),
        rates.maybeWhen(
          data: (list) => AppCard(
            padding: EdgeInsets.zero,
            child: Column(children: [
              for (var i = 0; i < list.length; i++) ...[
                ListTile(
                  title: Text(
                    '${Money.format(list[i].amount)} ${list[i].payType == 'monthly' ? 'per month' : 'per day'}',
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                  subtitle: Text(
                    'From ${D.show(list[i].effectiveFrom)}'
                    '${list[i].otPerHour != null ? ' • OT ${Money.format(list[i].otPerHour!)}/h' : ''}',
                  ),
                  trailing: i == 0 ? const Pill('Current') : null,
                ),
                if (i < list.length - 1) const Divider(indent: 16),
              ],
            ]),
          ),
          orElse: () => const SizedBox(height: 60),
        ),
      ],
      SectionTitle('WORKS AT',
          padding: const EdgeInsets.fromLTRB(4, 20, 4, 8),
          trailing: manage
              ? TextButton(onPressed: () => _addSite(context, ref), child: const Text('Add site'))
              : null),
      asg.maybeWhen(
        data: (list) => list.isEmpty
            ? const AppCard(
                child: Text('Not assigned to any site (on bench).', style: TextStyle(color: Palette.muted)))
            : AppCard(
                padding: EdgeInsets.zero,
                child: Column(children: [
                  for (var i = 0; i < list.length; i++) ...[
                    ListTile(
                      leading: Icon(Icons.location_on_outlined, color: Palette.brand),
                      title: Text(list[i].site.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                      subtitle: Text('${list[i].company.name} • since ${D.show(list[i].assignment.fromDate)}'),
                      trailing: manage
                          ? IconButton(
                              tooltip: 'Remove from site',
                              icon: const Icon(Icons.remove_circle_outline, color: Palette.absent),
                              onPressed: () async {
                                final ok = await confirm(context,
                                    title: 'Remove from ${list[i].site.name}?',
                                    message: 'They will no longer appear on this site\'s attendance sheet from today. Past attendance stays.',
                                    confirmLabel: 'Remove',
                                    danger: true);
                                if (ok) await svc.unassign(list[i].assignment.id);
                              },
                            )
                          : null,
                    ),
                    if (i < list.length - 1) const Divider(indent: 16),
                  ],
                ]),
              ),
        orElse: () => const SizedBox(height: 60),
      ),
      if (session.can(Perm.labourDocs)) ...[
        SectionTitle('DOCUMENTS',
            padding: const EdgeInsets.fromLTRB(4, 20, 4, 8),
            trailing: TextButton(onPressed: () => _addDoc(context, ref), child: const Text('Add'))),
        docs.maybeWhen(
          data: (list) => list.isEmpty
              ? const AppCard(
                  child: Text('No documents. Add Aadhaar, bank passbook, police verification, etc.',
                      style: TextStyle(color: Palette.muted)))
              : AppCard(
                  padding: EdgeInsets.zero,
                  child: Column(children: [
                    for (var i = 0; i < list.length; i++) ...[
                      _DocTile(doc: list[i]),
                      if (i < list.length - 1) const Divider(indent: 16),
                    ],
                  ]),
                ),
          orElse: () => const SizedBox(height: 60),
        ),
      ],
      if (labour.address.isNotEmpty || labour.fatherName.isNotEmpty) ...[
        const SectionTitle('PERSONAL', padding: EdgeInsets.fromLTRB(4, 20, 4, 8)),
        AppCard(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            if (labour.fatherName.isNotEmpty) Text("Father's name: ${labour.fatherName}"),
            if (labour.address.isNotEmpty)
              Padding(padding: const EdgeInsets.only(top: 6), child: Text('Address: ${labour.address}')),
          ]),
        ),
      ],
      if (manage) ...[
        const SectionTitle('STATUS', padding: EdgeInsets.fromLTRB(4, 20, 4, 8)),
        if (labour.status == LabourStatus.active) ...[
          OutlinedButton.icon(
            icon: const Icon(Icons.pause_circle_outline),
            label: const Text('Mark inactive (on leave / not needed)'),
            onPressed: () => _setStatus(context, ref, LabourStatus.inactive),
          ),
          const SizedBox(height: 8),
          OutlinedButton.icon(
            style: OutlinedButton.styleFrom(foregroundColor: Palette.absent),
            icon: const Icon(Icons.exit_to_app),
            label: const Text('Mark as left'),
            onPressed: () => _setStatus(context, ref, LabourStatus.left),
          ),
        ] else
          FilledButton.icon(
            icon: const Icon(Icons.restart_alt),
            label: const Text('Reactivate'),
            onPressed: () async {
              await svc.setStatus(labour.id, LabourStatus.active, staffId: session.staff?.id);
            },
          ),
        FutureBuilder<bool>(
          future: svc.canHardDelete(labour.id),
          builder: (_, snap) => snap.data == true
              ? Padding(
                  padding: const EdgeInsets.only(top: 8),
                  child: TextButton.icon(
                    style: TextButton.styleFrom(foregroundColor: Palette.absent),
                    icon: const Icon(Icons.delete_outline),
                    label: const Text('Delete (added by mistake)'),
                    onPressed: () async {
                      final ok = await confirm(context,
                          title: 'Delete ${labour.name}?',
                          message: 'This person has no attendance or payments, so they can be removed completely.',
                          confirmLabel: 'Delete',
                          danger: true);
                      if (!ok) return;
                      await svc.hardDelete(labour.id, staffId: session.staff?.id);
                      if (context.mounted) Navigator.pop(context);
                    },
                  ),
                )
              : const SizedBox.shrink(),
        ),
      ],
    ]);
  }

  Future<void> _setStatus(BuildContext context, WidgetRef ref, String status) async {
    final ok = await confirm(
      context,
      title: status == LabourStatus.left ? 'Mark as left?' : 'Mark inactive?',
      message:
          'They will be removed from attendance sheets from today. All past attendance and payments stay, and you can reactivate later.',
      confirmLabel: 'Confirm',
    );
    if (!ok) return;
    await ref.read(labourServiceProvider).setStatus(labour.id, status, staffId: ref.read(sessionProvider).staff?.id);
  }

  Future<void> _addSite(BuildContext context, WidgetRef ref) async {
    final current = (await ref.read(labourServiceProvider).assignments(labour.id)).map((a) => a.site.id).toSet();
    if (!context.mounted) return;
    final picked = await showModalBottomSheet<Set<String>>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => _SitePickSheet(initial: current),
    );
    if (picked == null) return;
    final svc = ref.read(labourServiceProvider);
    for (final s in picked.difference(current)) {
      await svc.assign(labour.id, s);
    }
  }

  Future<void> _changeRate(BuildContext context, WidgetRef ref) async {
    final r = await showModalBottomSheet<_RateResult>(
      context: context,
      isScrollControlled: true,
      builder: (_) => const _RateSheet(),
    );
    if (r == null) return;
    try {
      await ref.read(labourServiceProvider).setRate(
            labourId: labour.id,
            from: r.from,
            payType: r.payType,
            amount: r.amount,
            otPerHour: r.ot,
            staffId: ref.read(sessionProvider).staff?.id,
          );
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }

  Future<void> _addDoc(BuildContext context, WidgetRef ref) async {
    final r = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (_) => _DocSheet(labourId: labour.id),
    );
    if (r == true && context.mounted) showInfo(context, 'Document saved');
  }
}

class _SitePickSheet extends StatefulWidget {
  const _SitePickSheet({required this.initial});
  final Set<String> initial;

  @override
  State<_SitePickSheet> createState() => _SitePickSheetState();
}

class _SitePickSheetState extends State<_SitePickSheet> {
  late Set<String> _sel = {...widget.initial};

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('Assign to sites', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
          const SizedBox(height: 4),
          const Text('Labour appears on the attendance sheet of every site selected.',
              style: TextStyle(color: Palette.muted, fontSize: 13)),
          const SizedBox(height: 10),
          Consumer(builder: (context, ref, _) => SiteChecklist(selected: _sel, onChanged: (v) => setState(() => _sel = v))),
          const SizedBox(height: 16),
          FilledButton(onPressed: () => Navigator.pop(context, _sel), child: const Text('Save')),
        ]),
      ),
    );
  }
}

class _RateResult {
  _RateResult(this.from, this.payType, this.amount, this.ot);
  final String from;
  final String payType;
  final int amount;
  final int? ot;
}

class _RateSheet extends StatefulWidget {
  const _RateSheet();

  @override
  State<_RateSheet> createState() => _RateSheetState();
}

class _RateSheetState extends State<_RateSheet> {
  String _type = 'daily';
  final _amount = TextEditingController();
  final _ot = TextEditingController();
  DateTime _from = DateTime.now();

  @override
  void dispose() {
    _amount.dispose();
    _ot.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(context).viewInsets.bottom + 20),
      child: SingleChildScrollView(
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('Change pay rate', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
          const SizedBox(height: 4),
          const Text('Days before the date below keep the old rate.',
              style: TextStyle(color: Palette.muted, fontSize: 13)),
          const SizedBox(height: 14),
          SegmentedButton<String>(
            segments: const [
              ButtonSegment(value: 'daily', label: Text('Per day')),
              ButtonSegment(value: 'monthly', label: Text('Per month')),
            ],
            selected: {_type},
            onSelectionChanged: (v) => setState(() => _type = v.first),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _amount,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: InputDecoration(labelText: 'New rate', prefixText: '${Money.symbol} '),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _ot,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: InputDecoration(labelText: 'Overtime per hour (optional)', prefixText: '${Money.symbol} '),
          ),
          const SizedBox(height: 12),
          InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: () async {
              final d = await showDatePicker(
                context: context,
                initialDate: _from,
                firstDate: DateTime(2020),
                lastDate: DateTime.now(),
              );
              if (d != null) setState(() => _from = d);
            },
            child: InputDecorator(
              decoration: const InputDecoration(labelText: 'Effective from', suffixIcon: Icon(Icons.calendar_today_outlined)),
              child: Text(D.showDt(_from)),
            ),
          ),
          const SizedBox(height: 18),
          FilledButton(
            onPressed: () {
              final a = Money.parse(_amount.text);
              if (a == null || a <= 0) return showError(context, 'Enter the new rate');
              Navigator.pop(
                context,
                _RateResult(D.ymd(_from), _type, a, _ot.text.trim().isEmpty ? null : Money.parse(_ot.text)),
              );
            },
            child: const Text('Save rate'),
          ),
        ]),
      ),
    );
  }
}

class _DocTile extends ConsumerWidget {
  const _DocTile({required this.doc});
  final LabourDocument doc;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final expiring = doc.expiryDate != null && doc.expiryDate!.compareTo(D.ymd(D.addDays(DateTime.now(), 30))) <= 0;
    final expired = doc.expiryDate != null && doc.expiryDate!.compareTo(D.today()) < 0;
    final img = fileImage(doc.filePath);
    return ListTile(
      onTap: img == null
          ? null
          : () => showDialog<void>(
                context: context,
                builder: (_) => Dialog(
                  clipBehavior: Clip.antiAlias,
                  child: InteractiveViewer(child: Image.file(File(doc.filePath!))),
                ),
              ),
      leading: ClipRRect(
        borderRadius: BorderRadius.circular(10),
        child: Container(
          width: 46,
          height: 46,
          color: Palette.brandTint,
          child: img != null
              ? Image(image: img, fit: BoxFit.cover)
              : Icon(Icons.description_outlined, color: Palette.brand),
        ),
      ),
      title: Text(doc.docType, style: const TextStyle(fontWeight: FontWeight.w700)),
      subtitle: Text([
        if (doc.docNumber.isNotEmpty) doc.docNumber,
        if (doc.expiryDate != null) 'expires ${D.show(doc.expiryDate!)}',
      ].join(' • ')),
      trailing: Row(mainAxisSize: MainAxisSize.min, children: [
        if (expired)
          const Pill('Expired', color: Palette.absent)
        else if (expiring)
          const Pill('Expiring', color: Palette.half),
        IconButton(
          icon: const Icon(Icons.delete_outline, color: Palette.muted),
          onPressed: () async {
            final ok = await confirm(context,
                title: 'Delete ${doc.docType}?', message: 'The document will be removed.', confirmLabel: 'Delete', danger: true);
            if (ok) await ref.read(labourServiceProvider).deleteDocument(doc.id);
          },
        ),
      ]),
    );
  }
}

class _DocSheet extends ConsumerStatefulWidget {
  const _DocSheet({required this.labourId});
  final String labourId;

  @override
  ConsumerState<_DocSheet> createState() => _DocSheetState();
}

class _DocSheetState extends ConsumerState<_DocSheet> {
  String _type = LabourService.docTypes.first;
  final _number = TextEditingController();
  DateTime? _expiry;
  String? _path;

  @override
  void dispose() {
    _number.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 0, 20, MediaQuery.of(context).viewInsets.bottom + 20),
      child: SingleChildScrollView(
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('Add document', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
          const SizedBox(height: 14),
          DropdownButtonFormField<String>(
            initialValue: _type,
            decoration: const InputDecoration(labelText: 'Document type'),
            items: [for (final t in LabourService.docTypes) DropdownMenuItem(value: t, child: Text(t))],
            onChanged: (v) => setState(() => _type = v!),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _number,
            decoration: const InputDecoration(labelText: 'Number (optional)'),
          ),
          const SizedBox(height: 12),
          InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: () async {
              final d = await showDatePicker(
                context: context,
                initialDate: _expiry ?? DateTime.now().add(const Duration(days: 365)),
                firstDate: DateTime(2020),
                lastDate: DateTime(2050),
              );
              if (d != null) setState(() => _expiry = d);
            },
            child: InputDecorator(
              decoration: InputDecoration(
                labelText: 'Expiry date (optional)',
                suffixIcon: _expiry == null
                    ? const Icon(Icons.calendar_today_outlined)
                    : IconButton(icon: const Icon(Icons.close), onPressed: () => setState(() => _expiry = null)),
              ),
              child: Text(_expiry == null ? '—' : D.showDt(_expiry!)),
            ),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: () async {
              final p = await pickAndStoreImage(context, folder: 'documents');
              if (p != null) setState(() => _path = p);
            },
            icon: Icon(_path == null ? Icons.add_a_photo_outlined : Icons.check_circle, color: _path == null ? null : Palette.present),
            label: Text(_path == null ? 'Take photo / choose image' : 'Image attached'),
          ),
          const SizedBox(height: 16),
          FilledButton(
            onPressed: () async {
              await ref.read(labourServiceProvider).addDocument(
                    labourId: widget.labourId,
                    docType: _type,
                    docNumber: _number.text,
                    filePath: _path,
                    expiryDate: _expiry == null ? null : D.ymd(_expiry!),
                  );
              if (context.mounted) Navigator.pop(context, true);
            },
            child: const Text('Save document'),
          ),
        ]),
      ),
    );
  }
}
