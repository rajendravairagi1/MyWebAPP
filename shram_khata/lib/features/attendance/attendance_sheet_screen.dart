import '../../core/i18n.dart';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/services/attendance_service.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';
import '../labour/labour_pickers.dart';

final _sheetProvider =
    FutureProvider.autoDispose.family<Sheet, ({String siteId, String date})>((ref, a) async {
  ref.watch(dbTickProvider);
  return ref.watch(attendanceServiceProvider).loadSheet(a.siteId, a.date);
});

/// The supervisor's daily sheet: one tap per person for P / H / A.
class AttendanceSheetScreen extends ConsumerWidget {
  const AttendanceSheetScreen({super.key, required this.siteId, required this.date});
  final String siteId;
  final String date;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sheet = ref.watch(_sheetProvider((siteId: siteId, date: date)));
    final session = ref.watch(sessionProvider);
    final canMark = session.can(Perm.attendanceMark);
    return Scaffold(
      appBar: AppBar(
        title: sheet.maybeWhen(
          data: (s) => Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(s.site.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              Text('${s.company.name} • ${D.show(date)}',
                  style: const TextStyle(fontSize: 12.5, color: Palette.muted, fontWeight: FontWeight.w500)),
            ],
          ),
          orElse: () => Text(tr('Attendance')),
        ),
        actions: [
          if (canMark)
            IconButton(
              tooltip: tr('Add labour to this sheet'),
              icon: const Icon(Icons.person_add_alt_1_outlined),
              onPressed: () => _addLabour(context, ref),
            ),
        ],
      ),
      body: sheet.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (s) => _SheetBody(sheet: s, canMark: canMark, session: session),
      ),
    );
  }

  Future<void> _addLabour(BuildContext context, WidgetRef ref) async {
    final current = await ref.read(attendanceServiceProvider).loadSheet(siteId, date);
    if (!context.mounted) return;
    final l = await pickLabour(
      context,
      exclude: current.rows.map((r) => r.labour.id).toSet(),
      title: tr('Add to this sheet'),
    );
    if (l == null) return;
    await ref.read(attendanceServiceProvider).addToSheet(siteId: siteId, labourId: l.id, date: date);
    if (context.mounted) showInfo(context, trf('{name} added to {site}', {'name': l.name, 'site': current.site.name}));
  }
}

class _SheetBody extends ConsumerWidget {
  const _SheetBody({required this.sheet, required this.canMark, required this.session});
  final Sheet sheet;
  final bool canMark;
  final SessionState session;

  Future<void> _mark(BuildContext context, WidgetRef ref, SheetRow r, String status, {double? ot}) async {
    HapticFeedback.selectionClick();
    try {
      if (r.status == status && ot == null) {
        // Tapping the selected status again clears it.
        await ref.read(attendanceServiceProvider).clear(
              siteId: sheet.site.id,
              date: sheet.date,
              labourId: r.labour.id,
              staffId: session.staff?.id,
              canEditLocked: session.can(Perm.attendanceEditLocked),
            );
        return;
      }
      await ref.read(attendanceServiceProvider).mark(
            siteId: sheet.site.id,
            date: sheet.date,
            labourId: r.labour.id,
            status: status,
            otHours: ot ?? (status == AttStatus.absent ? 0 : r.otHours),
            staffId: session.staff?.id,
            canEditLocked: session.can(Perm.attendanceEditLocked),
          );
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }

  Future<void> _setOt(BuildContext context, WidgetRef ref, SheetRow r, double hours) =>
      _mark(context, ref, r, r.status ?? AttStatus.present, ot: hours.clamp(0, 16).toDouble());

  Future<void> _markAll(BuildContext context, WidgetRef ref) async {
    try {
      final n = await ref.read(attendanceServiceProvider).markAllPresent(
            siteId: sheet.site.id,
            date: sheet.date,
            staffId: session.staff?.id,
            canEditLocked: session.can(Perm.attendanceEditLocked),
          );
      if (context.mounted) showInfo(context, n == 0 ? tr('Everyone is already marked') : trf('{n} marked present', {'n': n}));
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }

  Future<void> _submit(BuildContext context, WidgetRef ref) async {
    var restAbsent = false;
    if (sheet.unmarked > 0) {
      final ok = await confirm(
        context,
        title: trf('{n} not marked', {'n': sheet.unmarked}),
        message: trf('Mark the remaining {n} as absent and submit? The sheet will be locked.', {'n': sheet.unmarked}),
        confirmLabel: tr('Mark absent & submit'),
      );
      if (!ok) return;
      restAbsent = true;
    } else {
      final ok = await confirm(
        context,
        title: tr('Submit attendance?'),
        message:
            trf('Present {present} • Half {half} • Absent {absent}.\nThe sheet will be locked; only the owner can change it afterwards.',
                {'present': sheet.present, 'half': sheet.half, 'absent': sheet.absent}),
        confirmLabel: tr('Submit'),
      );
      if (!ok) return;
    }
    try {
      await ref.read(attendanceServiceProvider).submit(
            siteId: sheet.site.id,
            date: sheet.date,
            staffId: session.staff?.id,
            markRestAbsent: restAbsent,
          );
      if (context.mounted) showInfo(context, tr('Attendance submitted'));
    } catch (e) {
      if (context.mounted) showError(context, e);
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final locked = sheet.isSubmitted && !session.can(Perm.attendanceEditLocked);
    final editable = canMark && !locked;
    return Column(
      children: [
        _Summary(sheet: sheet),
        if (sheet.isSubmitted)
          Container(
            margin: const EdgeInsets.fromLTRB(16, 0, 16, 8),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              color: Palette.presentBg,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(children: [
              const Icon(Icons.lock_outline, size: 18, color: Palette.present),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  trf('Submitted at {time}. {note}', {
                    'time': TimeOfDay.fromDateTime(sheet.submitted!.submittedAt).format(context),
                    'note': locked ? tr('Locked.') : tr('You can still edit or unlock.'),
                  }),
                  style: const TextStyle(color: Palette.present, fontWeight: FontWeight.w600, fontSize: 13),
                ),
              ),
              if (session.can(Perm.attendanceEditLocked))
                TextButton(
                  onPressed: () => ref.read(attendanceServiceProvider).unlock(
                        siteId: sheet.site.id,
                        date: sheet.date,
                        staffId: session.staff?.id,
                      ),
                  child: Text(tr('Unlock')),
                ),
            ]),
          ),
        Expanded(
          child: sheet.rows.isEmpty
              ? EmptyState(
                  icon: Icons.groups_outlined,
                  title: tr('No labour on this site'),
                  message: tr('Tap the + person icon to add labour, or assign labour to this site from their profile.'),
                )
              : ListView.separated(
                  padding: const EdgeInsets.fromLTRB(16, 4, 16, 16),
                  itemCount: sheet.rows.length,
                  separatorBuilder: (_, _) => const SizedBox(height: 8),
                  itemBuilder: (context, i) => _Row(
                    row: sheet.rows[i],
                    editable: editable,
                    onMark: (st) => _mark(context, ref, sheet.rows[i], st),
                    onOt: (h) => _setOt(context, ref, sheet.rows[i], h),
                  ),
                ),
        ),
        if (editable && sheet.rows.isNotEmpty && !sheet.isSubmitted)
          SafeArea(
            top: false,
            child: Container(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
              decoration: const BoxDecoration(
                color: Palette.surface,
                border: Border(top: BorderSide(color: Palette.line)),
              ),
              child: Row(children: [
                Expanded(
                  flex: 3,
                  child: OutlinedButton.icon(
                    onPressed: sheet.unmarked == 0 ? null : () => _markAll(context, ref),
                    icon: const Icon(Icons.done_all),
                    label: Text(tr('All present')),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  flex: 4,
                  child: FilledButton.icon(
                    onPressed: () => _submit(context, ref),
                    icon: const Icon(Icons.lock_outline),
                    label: Text(tr('Submit sheet')),
                  ),
                ),
              ]),
            ),
          ),
      ],
    );
  }
}

class _Summary extends StatelessWidget {
  const _Summary({required this.sheet});
  final Sheet sheet;

  @override
  Widget build(BuildContext context) {
    Widget cell(String label, int n, Color color, Color bg) => Expanded(
          child: Container(
            margin: const EdgeInsets.symmetric(horizontal: 3),
            padding: const EdgeInsets.symmetric(vertical: 8),
            decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)),
            child: Column(children: [
              Text('$n', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: color)),
              Text(tr(label), style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700, color: color)),
            ]),
          ),
        );
    return Padding(
      padding: const EdgeInsets.fromLTRB(13, 4, 13, 10),
      child: Row(children: [
        cell('Present', sheet.present, Palette.present, Palette.presentBg),
        cell('Half', sheet.half, Palette.half, Palette.halfBg),
        cell('Absent', sheet.absent, Palette.absent, Palette.absentBg),
        cell('Pending', sheet.unmarked, Palette.muted, const Color(0xFFE8ECEE)),
      ]),
    );
  }
}

class _Row extends StatelessWidget {
  const _Row({required this.row, required this.editable, required this.onMark, required this.onOt});
  final SheetRow row;
  final bool editable;
  final void Function(String status) onMark;
  final void Function(double hours) onOt;

  @override
  Widget build(BuildContext context) {
    final l = row.labour;
    final photo = l.photoPath != null && File(l.photoPath!).existsSync() ? FileImage(File(l.photoPath!)) : null;
    final st = row.status;
    return AppCard(
      padding: const EdgeInsets.fromLTRB(12, 10, 12, 10),
      child: Column(
        children: [
          Row(children: [
            Avatar(name: l.name, image: photo, size: 42),
            const SizedBox(width: 12),
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(l.name,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15.5)),
                Text(
                  row.otherSites.isEmpty
                      ? l.skill
                      : trf('{skill} • also at {sites}', {'skill': l.skill, 'sites': row.otherSites.join(', ')}),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(color: Palette.muted, fontSize: 12.5),
                ),
              ]),
            ),
          ]),
          const SizedBox(height: 10),
          Row(children: [
            for (final s in const ['P', 'H', 'A']) ...[
              Expanded(
                child: _StatusButton(
                  letter: s,
                  selected: st == s,
                  enabled: editable && (row.allows(s) || st == s),
                  onTap: () => onMark(s),
                ),
              ),
              if (s != 'A') const SizedBox(width: 8),
            ],
          ]),
          if (st != null && st != AttStatus.absent) _OtRow(hours: row.otHours, editable: editable, onChanged: onOt),
        ],
      ),
    );
  }
}

/// Overtime hours for someone marked present / half, right inside the card.
class _OtRow extends StatelessWidget {
  const _OtRow({required this.hours, required this.editable, required this.onChanged});
  final double hours;
  final bool editable;
  final void Function(double hours) onChanged;

  @override
  Widget build(BuildContext context) {
    final has = hours > 0;
    return Container(
      margin: const EdgeInsets.only(top: 10),
      padding: const EdgeInsets.fromLTRB(10, 6, 6, 6),
      decoration: BoxDecoration(
        color: has ? Palette.infoBg : const Color(0xFFF4F6F7),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Icon(Icons.schedule, size: 16, color: has ? Palette.info : Palette.muted),
          const SizedBox(width: 6),
          Expanded(
            child: Text(tr('Overtime hours'),
                style: TextStyle(
                    fontSize: 13, fontWeight: FontWeight.w700, color: has ? Palette.info : Palette.muted)),
          ),
          IconButton(
            visualDensity: VisualDensity.compact,
            onPressed: editable && hours > 0 ? () => onChanged(hours - 0.5) : null,
            icon: const Icon(Icons.remove_circle_outline),
          ),
          SizedBox(
            width: 52,
            child: Text(trf('{h} h', {'h': num1(hours)}),
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: has ? Palette.info : Palette.ink)),
          ),
          IconButton(
            visualDensity: VisualDensity.compact,
            onPressed: editable && hours < 16 ? () => onChanged(hours + 0.5) : null,
            icon: const Icon(Icons.add_circle_outline),
          ),
        ]),
        Padding(
          padding: const EdgeInsets.only(bottom: 4),
          child: Wrap(spacing: 8, children: [
            for (final h in const [1.0, 2.0, 3.0, 4.0])
              ChoiceChip(
                visualDensity: VisualDensity.compact,
                label: Text(trf('{h}h', {'h': num1(h)})),
                selected: hours == h,
                onSelected: editable ? (_) => onChanged(hours == h ? 0 : h) : null,
              ),
          ]),
        ),
      ]),
    );
  }
}

class _StatusButton extends StatelessWidget {
  const _StatusButton({
    required this.letter,
    required this.selected,
    required this.enabled,
    required this.onTap,
  });

  final String letter;
  final bool selected;
  final bool enabled;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final color = Palette.status(letter);
    final label = switch (letter) { 'P' => tr('Present'), 'H' => tr('Half'), _ => tr('Absent') };
    return Opacity(
      opacity: enabled || selected ? 1 : 0.35,
      child: Material(
        color: selected ? color : Palette.statusBg(letter).withValues(alpha: 0.55),
        borderRadius: BorderRadius.circular(12),
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: enabled ? onTap : null,
          child: Container(
            height: 46,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: selected ? color : color.withValues(alpha: 0.25)),
            ),
            child: FittedBox(
              fit: BoxFit.scaleDown,
              child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                Text(letter,
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: selected ? Colors.white : color,
                    )),
                const SizedBox(width: 6),
                Text(label,
                    style: TextStyle(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w700,
                      color: selected ? Colors.white : color,
                    )),
              ]),
            ),
          ),
        ),
      ),
    );
  }
}
