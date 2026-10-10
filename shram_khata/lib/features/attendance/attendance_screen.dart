import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/attendance_service.dart';
import '../../state/providers.dart';
import '../home/dashboard_screen.dart' show DateBar;
import 'attendance_sheet_screen.dart';

/// Pick a site for the selected day; each site shows its progress.
class AttendanceScreen extends ConsumerWidget {
  const AttendanceScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sheets = ref.watch(siteSheetsProvider);
    final date = ref.watch(selectedDateProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Attendance')),
      body: Column(
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(16, 4, 16, 8),
            child: DateBar(),
          ),
          const _PendingDays(),
          Expanded(
            child: sheets.when(
              loading: () => const LoadingView(),
              error: (e, _) => ErrorView(e),
              data: (list) {
                if (list.isEmpty) {
                  return const EmptyState(
                    icon: Icons.location_city_outlined,
                    title: 'No sites yet',
                    message:
                        'Add a company and a site first (More → Companies), then assign labour to it.',
                  );
                }
                final byCompany = <String, List<Sheet>>{};
                for (final s in list) {
                  (byCompany[s.company.name] ??= []).add(s);
                }
                return RefreshIndicator(
                  color: Palette.brand,
                  onRefresh: () async => ref.invalidate(siteSheetsProvider),
                  child: ListView(
                    padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                    children: [
                      for (final e in byCompany.entries) ...[
                        SectionTitle(e.key.toUpperCase(),
                            padding: const EdgeInsets.fromLTRB(4, 14, 4, 8)),
                        for (final s in e.value)
                          Padding(
                            padding: const EdgeInsets.only(bottom: 10),
                            child: _SiteCard(sheet: s, date: D.ymd(date)),
                          ),
                      ],
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _SiteCard extends StatelessWidget {
  const _SiteCard({required this.sheet, required this.date});
  final Sheet sheet;
  final String date;

  @override
  Widget build(BuildContext context) {
    final marked = sheet.total - sheet.unmarked;
    final progress = sheet.total == 0 ? 0.0 : marked / sheet.total;
    final Widget badge;
    if (sheet.isSubmitted) {
      badge = const Pill('Submitted', color: Palette.present, icon: Icons.lock_outline);
    } else if (sheet.total == 0) {
      badge = const Pill('No labour', color: Palette.muted);
    } else if (marked == 0) {
      badge = const Pill('Not started', color: Palette.absent);
    } else {
      badge = const Pill('In progress', color: Palette.half);
    }
    return AppCard(
      onTap: () => Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => AttendanceSheetScreen(siteId: sheet.site.id, date: date)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Expanded(
              child: Text(sheet.site.name,
                  style: const TextStyle(fontSize: 16.5, fontWeight: FontWeight.w800)),
            ),
            badge,
          ]),
          const SizedBox(height: 12),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: progress,
              minHeight: 7,
              backgroundColor: Palette.line,
              color: sheet.isSubmitted ? Palette.present : Palette.brand,
            ),
          ),
          const SizedBox(height: 10),
          Row(children: [
            Expanded(
              child: Text('$marked of ${sheet.total} marked',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(color: Palette.muted, fontSize: 13, fontWeight: FontWeight.w500)),
            ),
            _Count('P', sheet.present),
            const SizedBox(width: 8),
            _Count('H', sheet.half),
            const SizedBox(width: 8),
            _Count('A', sheet.absent),
          ]),
        ],
      ),
    );
  }
}

class _Count extends StatelessWidget {
  const _Count(this.letter, this.n);
  final String letter;
  final int n;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: Palette.statusBg(letter),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text('$letter $n',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12.5, color: Palette.status(letter))),
    );
  }
}

/// "Not submitted" past days, one tap to jump there and fill them in.
class _PendingDays extends ConsumerWidget {
  const _PendingDays();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final days = ref.watch(pendingDaysProvider).value ?? const <DateTime>[];
    if (days.isEmpty) return const SizedBox.shrink();
    final selected = ref.watch(selectedDateProvider);
    return SizedBox(
      height: 44,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        children: [
          const Center(
            child: Padding(
              padding: EdgeInsets.only(right: 8),
              child: Text('Pending:', style: TextStyle(fontSize: 12.5, color: Palette.muted, fontWeight: FontWeight.w700)),
            ),
          ),
          for (final d in days)
            Padding(
              padding: const EdgeInsets.only(right: 8),
              child: ActionChip(
                avatar: const Icon(Icons.history, size: 16),
                label: Text(D.showDt(d)),
                backgroundColor: D.ymd(d) == D.ymd(selected) ? Palette.halfBg : null,
                onPressed: () => ref.read(selectedDateProvider.notifier).set(d),
              ),
            ),
        ],
      ),
    );
  }
}
