import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/dashboard_service.dart';
import '../../state/providers.dart';
import '../attendance/attendance_sheet_screen.dart';
import '../billing/invoices_screen.dart';
import '../labour/labour_list_screen.dart';
import 'dashboard_widgets.dart';

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final data = ref.watch(dashboardProvider);
    final profile = ref.watch(profileProvider).value;
    final session = ref.watch(sessionProvider);
    final date = ref.watch(selectedDateProvider);

    return Scaffold(
      body: SafeArea(
        bottom: false,
        child: RefreshIndicator(
          color: Palette.brand,
          onRefresh: () async => ref.invalidate(dashboardProvider),
          child: ListView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
            children: [
              _Header(
                name: session.staff?.name ?? '',
                business: profile?.name ?? '',
              ),
              const SizedBox(height: 12),
              const DateBar(),
              const SizedBox(height: 14),
              data.when(
                loading: () => const Padding(padding: EdgeInsets.only(top: 80), child: LoadingView()),
                error: (e, _) => ErrorView(e),
                data: (d) => _Body(d: d, date: date, session: session),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _Header extends ConsumerWidget {
  const _Header({required this.name, required this.business});
  final String name;
  final String business;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final plan = ref.watch(planProvider);
    final allowed = ref.watch(allowedBranchesProvider);
    final scope = ref.watch(scopeBranchProvider);
    final hour = DateTime.now().hour;
    final greet = hour < 12 ? 'Good morning' : (hour < 17 ? 'Good afternoon' : 'Good evening');
    return Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('$greet${name.isEmpty ? '' : ', ${name.split(' ').first}'}',
                  style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800)),
              if (business.isNotEmpty)
                Text(business, style: const TextStyle(color: Palette.muted, fontWeight: FontWeight.w500)),
            ],
          ),
        ),
        if (plan.multiBranch && allowed.length > 1)
          PopupMenuButton<String>(
            onSelected: (v) => ref.read(sessionProvider.notifier).switchBranch(v == '_all' ? null : v),
            itemBuilder: (_) => [
              if (ref.read(sessionProvider).branchLimit.isEmpty)
                const PopupMenuItem(value: '_all', child: Text('All branches')),
              for (final b in allowed) PopupMenuItem(value: b.id, child: Text(b.name)),
            ],
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Palette.brandTint,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(mainAxisSize: MainAxisSize.min, children: [
                Icon(Icons.apartment, size: 16, color: Palette.brandDark),
                const SizedBox(width: 6),
                Text(
                  scope == null ? 'All branches' : allowed.firstWhere((b) => b.id == scope).name,
                  style: TextStyle(fontWeight: FontWeight.w700, color: Palette.brandDark, fontSize: 13),
                ),
                Icon(Icons.arrow_drop_down, color: Palette.brandDark),
              ]),
            ),
          ),
      ],
    );
  }
}

/// ‹ Today › selector shared by Home and Attendance.
class DateBar extends ConsumerWidget {
  const DateBar({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final date = ref.watch(selectedDateProvider);
    final notifier = ref.read(selectedDateProvider.notifier);
    final today = D.isToday(date);
    final yesterday = D.ymd(date) == D.ymd(D.addDays(DateTime.now(), -1));
    final label = today ? 'Today' : (yesterday ? 'Yesterday' : D.weekday(date));
    return Container(
      decoration: BoxDecoration(
        color: Palette.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Palette.line),
      ),
      child: Row(
        children: [
          IconButton(
            onPressed: () => notifier.set(D.addDays(date, -1)),
            icon: const Icon(Icons.chevron_left),
          ),
          Expanded(
            child: InkWell(
              borderRadius: BorderRadius.circular(10),
              onTap: () async {
                final picked = await showDatePicker(
                  context: context,
                  initialDate: date,
                  firstDate: DateTime(2020),
                  lastDate: DateTime.now(),
                );
                if (picked != null) notifier.set(picked);
              },
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 8),
                child: Column(
                  children: [
                    Text(label,
                        style: const TextStyle(fontSize: 12, color: Palette.muted, fontWeight: FontWeight.w700)),
                    Text(D.showDt(date), style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                  ],
                ),
              ),
            ),
          ),
          IconButton(
            onPressed: today ? null : () => notifier.set(D.addDays(date, 1)),
            icon: const Icon(Icons.chevron_right),
          ),
        ],
      ),
    );
  }
}

class _Body extends ConsumerWidget {
  const _Body({required this.d, required this.date, required this.session});
  final DashboardData d;
  final DateTime date;
  final SessionState session;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final canMoney = session.can(Perm.paymentsView) || session.can(Perm.billingView);
    final deployed = d.present + d.half + d.absent + d.notMarked;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _Hero(d: d, deployed: deployed),
        const SizedBox(height: 12),
        GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 1.5,
          children: [
            KpiTile(
              label: 'Earned',
              value: Money.compact(d.billing),
              hint: 'billed to companies',
              icon: Icons.trending_up,
              color: ChartColors.billing,
            ),
            KpiTile(
              label: 'Labour cost',
              value: Money.compact(d.labourCost),
              hint: 'wages for the day',
              icon: Icons.engineering_outlined,
              color: ChartColors.cost,
            ),
            KpiTile(
              label: 'Paid out',
              value: Money.compact(d.paidOut),
              hint: d.advanceToday > 0
                  ? 'incl. ${Money.compact(d.advanceToday)} advance'
                  : 'cash / UPI / bank',
              icon: Icons.payments_outlined,
              color: Palette.info,
            ),
            KpiTile(
              label: 'Margin',
              value: (d.margin < 0 ? '−' : '') + Money.compact(d.margin.abs()),
              hint: 'earned − labour cost',
              icon: d.margin < 0 ? Icons.trending_down : Icons.savings_outlined,
              color: d.margin < 0 ? Palette.absent : Palette.present,
            ),
          ],
        ),
        if (_alerts(context, ref).isNotEmpty) ...[
          const SectionTitle('NEEDS ATTENTION', padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
          ..._alerts(context, ref),
        ],
        if (canMoney) ...[
          const SectionTitle('MONEY POSITION', padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
          Row(children: [
            Expanded(
              child: AppCard(
                onTap: session.can(Perm.billingView)
                    ? () => Navigator.push(
                        context, MaterialPageRoute(builder: (_) => const InvoicesScreen()))
                    : null,
                child: Stat(
                  label: 'To collect from companies',
                  value: Money.compact(d.companyOutstanding),
                  color: d.companyOutstanding > 0 ? Palette.info : Palette.ink,
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: AppCard(
                child: Stat(
                  label: 'To pay labour',
                  value: Money.compact(d.labourPayable),
                  color: d.labourPayable > 0 ? Palette.absent : Palette.ink,
                ),
              ),
            ),
          ]),
          const SizedBox(height: 10),
          AppCard(
            child: Stat(
              label: 'Advance to recover from workers',
              value: Money.compact(d.advanceToRecover),
              color: d.advanceToRecover > 0 ? Palette.half : Palette.ink,
            ),
          ),
        ],
        const SectionTitle('LAST 7 DAYS', padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
        AppCard(child: WeekChart(points: d.week)),
        const SectionTitle('COMPANIES TODAY', padding: EdgeInsets.fromLTRB(4, 22, 4, 8)),
        if (d.companies.isEmpty)
          const AppCard(
            child: Text('No attendance recorded for this day yet.',
                style: TextStyle(color: Palette.muted)),
          )
        else
          AppCard(
            padding: EdgeInsets.zero,
            child: Column(children: [
              for (var i = 0; i < d.companies.length; i++) ...[
                _CompanyRow(row: d.companies[i], max: d.companies.first.working),
                if (i < d.companies.length - 1) const Divider(indent: 16, endIndent: 16),
              ],
            ]),
          ),
        const SizedBox(height: 8),
        Center(
          child: Text(
            '${d.activeCompanies} companies • ${d.activeSites} sites • ${d.totalLabour} active labour',
            style: const TextStyle(fontSize: 12, color: Palette.muted),
          ),
        ),
      ],
    );
  }

  List<Widget> _alerts(BuildContext context, WidgetRef ref) {
    final out = <Widget>[];
    Widget alert(IconData icon, Color color, String title, String sub, {VoidCallback? onTap}) =>
        Padding(
          padding: const EdgeInsets.only(bottom: 8),
          child: AppCard(
            onTap: onTap,
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            child: Row(children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, size: 19, color: color),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w700)),
                  Text(sub, style: const TextStyle(fontSize: 12.5, color: Palette.muted)),
                ]),
              ),
              if (onTap != null) const Icon(Icons.chevron_right, color: Palette.muted),
            ]),
          ),
        );

    final isToday = D.isToday(date);
    for (final p in d.pendingSites.take(4)) {
      out.add(alert(
        Icons.pending_actions,
        Palette.half,
        '${p.site.name} not submitted',
        '${p.company.name} • ${p.headcount} labour',
        onTap: session.can(Perm.attendanceMark)
            ? () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => AttendanceSheetScreen(siteId: p.site.id, date: D.ymd(date)),
                  ),
                )
            : null,
      ));
    }
    if (d.pendingSites.length > 4) {
      out.add(alert(Icons.more_horiz, Palette.muted,
          '${d.pendingSites.length - 4} more sites pending', isToday ? 'Open Attendance tab' : ''));
    }
    if (d.overdueInvoices > 0) {
      out.add(alert(Icons.warning_amber_rounded, Palette.absent,
          '${d.overdueInvoices} overdue invoice${d.overdueInvoices == 1 ? '' : 's'}', 'Follow up with the company',
          onTap: session.can(Perm.billingView)
              ? () => Navigator.push(context, MaterialPageRoute(builder: (_) => const InvoicesScreen()))
              : null));
    }
    if (d.missingRateEntries > 0) {
      out.add(alert(Icons.currency_rupee, Palette.half,
          '${d.missingRateEntries} entries have no billing rate',
          'Add the skill to the company contract so it is billed'));
    }
    if (d.expiringDocs > 0) {
      out.add(alert(Icons.badge_outlined, Palette.info,
          '${d.expiringDocs} document${d.expiringDocs == 1 ? '' : 's'} expiring soon', 'Check labour documents',
          onTap: session.can(Perm.labourView)
              ? () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LabourListScreen(standalone: true)))
              : null));
    }
    if (d.expiringContracts > 0) {
      out.add(alert(Icons.event_busy_outlined, Palette.info,
          '${d.expiringContracts} contract${d.expiringContracts == 1 ? '' : 's'} ending within 30 days', 'Renew with the company'));
    }
    return out;
  }
}

class _Hero extends StatelessWidget {
  const _Hero({required this.d, required this.deployed});
  final DashboardData d;
  final int deployed;

  @override
  Widget build(BuildContext context) {
    Widget legend(Color c, String label, int n) => Padding(
          padding: const EdgeInsets.symmetric(vertical: 3.5),
          child: Row(children: [
            Container(width: 10, height: 10, decoration: BoxDecoration(color: c, shape: BoxShape.circle)),
            const SizedBox(width: 8),
            Expanded(child: Text(label, style: TextStyle(color: Colors.white.withValues(alpha: 0.85), fontSize: 13))),
            Text('$n', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 15)),
          ]),
        );
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Palette.brand, Palette.brandDark],
        ),
      ),
      padding: const EdgeInsets.all(18),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            const Text('ATTENDANCE',
                style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w700, letterSpacing: 1)),
            const Spacer(),
            if (d.otHours > 0) Pill('OT ${num1(d.otHours)}h', color: Colors.white, bg: Colors.white24),
          ]),
          const SizedBox(height: 12),
          Row(
            children: [
              AttendanceRing(
                present: d.present,
                half: d.half,
                absent: d.absent,
                unmarked: d.notMarked,
                size: 124,
                center: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('${d.working}',
                        style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.w800, height: 1)),
                    Text('working', style: TextStyle(color: Colors.white.withValues(alpha: 0.8), fontSize: 12)),
                  ],
                ),
              ),
              const SizedBox(width: 20),
              Expanded(
                child: Column(children: [
                  legend(const Color(0xFF4ADE80), 'Present', d.present),
                  legend(const Color(0xFFFBBF24), 'Half day', d.half),
                  legend(const Color(0xFFF87171), 'Absent', d.absent),
                  legend(const Color(0x66FFFFFF), 'Not marked', d.notMarked),
                ]),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(children: [
              Expanded(child: _HeroStat('Total labour', '${d.totalLabour}')),
              Expanded(child: _HeroStat('Deployed', '${d.deployed}')),
              Expanded(child: _HeroStat('On bench', '${(d.totalLabour - d.deployed).clamp(0, 1 << 30)}')),
            ]),
          ),
        ],
      ),
    );
  }
}

class _HeroStat extends StatelessWidget {
  const _HeroStat(this.label, this.value);
  final String label;
  final String value;
  @override
  Widget build(BuildContext context) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(color: Colors.white70, fontSize: 11.5)),
          Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
        ],
      );
}

class _CompanyRow extends StatelessWidget {
  const _CompanyRow({required this.row, required this.max});
  final CompanyDay row;
  final int max;

  @override
  Widget build(BuildContext context) {
    final total = row.present + row.half + row.absent;
    return Padding(
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Expanded(
              child: Text(row.company.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
            ),
            Text('${row.working}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
            Text(' / $total', style: const TextStyle(color: Palette.muted)),
          ]),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: SizedBox(
              height: 8,
              child: Row(children: [
                if (row.present > 0)
                  Expanded(flex: row.present, child: Container(color: const Color(0xFF22C55E))),
                if (row.half > 0) ...[
                  const SizedBox(width: 2),
                  Expanded(flex: row.half, child: Container(color: const Color(0xFFF59E0B))),
                ],
                if (row.absent > 0) ...[
                  const SizedBox(width: 2),
                  Expanded(flex: row.absent, child: Container(color: const Color(0xFFEF4444))),
                ],
              ]),
            ),
          ),
          const SizedBox(height: 8),
          Row(children: [
            Text('${row.present}P  ${row.half}H  ${row.absent}A',
                style: const TextStyle(fontSize: 12, color: Palette.muted, fontWeight: FontWeight.w600)),
            const Spacer(),
            Text('${Money.compact(row.billing)} earned',
                style: const TextStyle(fontSize: 12, color: Palette.muted)),
          ]),
        ],
      ),
    );
  }
}
