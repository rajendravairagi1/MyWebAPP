import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../domain/calc.dart';
import '../../state/providers.dart';

/// Lets an owner / supervisor fix one worker's attendance for any past day
/// straight from the worker's calendar.
Future<void> editAttendanceDay(BuildContext context, WidgetRef ref, Labour labour, DateTime date) async {
  final session = ref.read(sessionProvider);
  if (!session.can(Perm.attendanceMark)) {
    showInfo(context, 'You do not have permission to change attendance.');
    return;
  }
  if (date.isAfter(DateTime.now())) {
    showInfo(context, 'Attendance cannot be marked for a future date.');
    return;
  }
  final ymd = D.ymd(date);
  final sites = await ref.read(labourServiceProvider).assignments(labour.id, activeOnly: false);
  final entries = await ref.read(attendanceServiceProvider).entries(labourId: labour.id, from: ymd, to: ymd);
  final choices = <Site, String>{};
  for (final a in sites) {
    final a0 = a.assignment;
    final covers = a0.fromDate.compareTo(ymd) <= 0 && (a0.toDate == null || a0.toDate!.compareTo(ymd) > 0);
    if (covers || entries.any((e) => e.siteId == a.site.id)) choices[a.site] = a.company.name;
  }
  if (!context.mounted) return;
  if (choices.isEmpty) {
    showInfo(context, 'This worker was not assigned to any site on ${D.show(ymd)}.');
    return;
  }
  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    builder: (_) => _DayEditor(labour: labour, ymd: ymd, choices: choices, entries: entries),
  );
}

class _DayEditor extends ConsumerStatefulWidget {
  const _DayEditor({required this.labour, required this.ymd, required this.choices, required this.entries});
  final Labour labour;
  final String ymd;
  final Map<Site, String> choices;
  final List<AttendanceData> entries;

  @override
  ConsumerState<_DayEditor> createState() => _DayEditorState();
}

class _DayEditorState extends ConsumerState<_DayEditor> {
  late Site _site = widget.entries.isEmpty
      ? widget.choices.keys.first
      : widget.choices.keys.firstWhere((s) => s.id == widget.entries.first.siteId,
          orElse: () => widget.choices.keys.first);
  String? _status;
  final _ot = TextEditingController();
  bool _busy = false;

  AttendanceData? get _existing {
    for (final e in widget.entries) {
      if (e.siteId == _site.id) return e;
    }
    return null;
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  void _load() {
    final e = _existing;
    _status = e?.status;
    _ot.text = e == null || e.otHours == 0 ? '' : e.otHours.toString();
  }

  @override
  void dispose() {
    _ot.dispose();
    super.dispose();
  }

  Future<void> _run(Future<void> Function() job) async {
    setState(() => _busy = true);
    try {
      await job();
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final session = ref.read(sessionProvider);
    final svc = ref.read(attendanceServiceProvider);
    final canEditLocked = session.can(Perm.attendanceEditLocked);
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 4, 20, 16 + MediaQuery.of(context).viewInsets.bottom),
      child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text('${widget.labour.name} • ${D.show(widget.ymd)}',
            style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
        const SizedBox(height: 12),
        if (widget.choices.length > 1)
          DropdownButtonFormField<String>(
            initialValue: _site.id,
            isExpanded: true,
            decoration: const InputDecoration(labelText: 'Site'),
            items: [
              for (final e in widget.choices.entries)
                DropdownMenuItem(value: e.key.id, child: Text('${e.value} • ${e.key.name}')),
            ],
            onChanged: (v) => setState(() {
              _site = widget.choices.keys.firstWhere((s) => s.id == v);
              _load();
            }),
          )
        else
          Text('${widget.choices.values.first} • ${_site.name}', style: const TextStyle(color: Palette.muted)),
        const SizedBox(height: 14),
        Wrap(spacing: 8, children: [
          for (final s in AttStatus.all)
            ChoiceChip(
              label: Text(AttStatus.label(s)),
              selected: _status == s,
              selectedColor: Palette.statusBg(s),
              onSelected: (_) => setState(() => _status = s),
            ),
        ]),
        if (_status != null && _status != AttStatus.absent) ...[
          const SizedBox(height: 12),
          TextField(
            controller: _ot,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: const InputDecoration(labelText: 'Overtime hours (optional)'),
          ),
        ],
        const SizedBox(height: 16),
        Row(children: [
          if (_existing != null)
            TextButton(
              onPressed: _busy
                  ? null
                  : () => _run(() => svc.clear(
                        siteId: _site.id,
                        date: widget.ymd,
                        labourId: widget.labour.id,
                        staffId: session.staff?.id,
                        canEditLocked: canEditLocked,
                      )),
              child: const Text('Clear'),
            ),
          const Spacer(),
          FilledButton(
            onPressed: _busy || _status == null
                ? null
                : () => _run(() => svc.mark(
                      siteId: _site.id,
                      date: widget.ymd,
                      labourId: widget.labour.id,
                      status: _status!,
                      otHours: double.tryParse(_ot.text.trim()) ?? 0,
                      staffId: session.staff?.id,
                      canEditLocked: canEditLocked,
                    )),
            child: const Text('Save'),
          ),
        ]),
      ]),
    );
  }
}
