import '../../core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:drift/drift.dart' show Value;
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/countries.dart';
import '../../core/dates.dart';
import '../../core/files.dart';
import '../../core/money.dart';
import '../../core/phone.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../data/services/base.dart' show AppException;
import '../../data/services/labour_service.dart';
import '../../state/providers.dart';
import 'duty_hours.dart';
import 'labour_pickers.dart';
import 'shift_picker.dart';
import '../../domain/calc.dart' show Shift;

/// Add a labourer, or edit their basic details when [labourId] is given.
class LabourFormScreen extends ConsumerStatefulWidget {
  const LabourFormScreen({super.key, this.labourId});
  final String? labourId;

  @override
  ConsumerState<LabourFormScreen> createState() => _LabourFormScreenState();
}

class _LabourFormScreenState extends ConsumerState<LabourFormScreen> {
  final _form = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _father = TextEditingController();
  final _mobile = TextEditingController();
  final _skill = TextEditingController(text: 'Helper');
  final _address = TextEditingController();
  final _amount = TextEditingController();
  int _duty = 8;
  String _shift = Shift.day;
  String _payType = 'daily';
  DateTime _join = DateTime.now();
  String? _photo;
  Set<String> _sites = {};
  String? _branchId;
  Labour? _existing;
  bool _loading = true;
  bool _busy = false;

  /// The day rate typed so far (a monthly salary is converted to a day rate).
  int? _dailyPaise() {
    final a = Money.parse(_amount.text);
    if (a == null || a <= 0) return null;
    if (_payType == 'daily') return a;
    final divisor = ref.read(profileProvider).value?.monthDivisor ?? 26;
    return (a / divisor).round();
  }

  bool get _editing => widget.labourId != null;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    if (_editing) {
      final l = await ref.read(labourServiceProvider).byId(widget.labourId!);
      if (l != null) {
        _existing = l;
        _name.text = l.name;
        _father.text = l.fatherName;
        _mobile.text = l.mobile;
        _skill.text = l.skill;
        _address.text = l.address;
        _photo = l.photoPath;
        _join = D.parse(l.joinDate);
        _branchId = l.branchId;
        _shift = l.shift;
      }
    }
    if (mounted) setState(() => _loading = false);
  }

  @override
  void dispose() {
    for (final c in [_name, _father, _mobile, _skill, _address, _amount]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    final svc = ref.read(labourServiceProvider);
    final staffId = ref.read(sessionProvider).staff?.id;
    setState(() => _busy = true);
    try {
      final dup = await svc.findDuplicate(_mobile.text, exceptId: widget.labourId);
      if (dup != null && mounted) {
        final go = await confirm(
          context,
          title: tr('Mobile already used'),
          message: trf('{name} already has this mobile number. Save anyway?', {'name': dup.name}),
          confirmLabel: tr('Save anyway'),
        );
        if (!go) return;
      }
      if (_editing) {
        await svc.update(
          _existing!.copyWith(
            name: _name.text.trim(),
            fatherName: _father.text.trim(),
            mobile: _mobile.text.trim(),
            skill: _skill.text.trim().isEmpty ? 'Helper' : _skill.text.trim(),
            address: _address.text.trim(),
            photoPath: Value.absentIfNull(_photo),
            shift: _shift,
          ),
          staffId: staffId,
        );
      } else {
        final branch = _branchId ?? ref.read(writeBranchProvider);
        if (branch == null) throw AppException(tr('No branch found.'));
        if (_sites.isEmpty) {
          throw AppException(tr('Choose the company and site this worker works at.'));
        }
        await svc.add(
          branchId: branch,
          name: _name.text,
          fatherName: _father.text,
          mobile: _mobile.text,
          address: _address.text,
          skill: _skill.text,
          photoPath: _photo,
          joinDate: D.ymd(_join),
          payType: _payType,
          amount: Money.parse(_amount.text) ?? 0,
          dutyHours: _duty,
          shift: _shift,
          siteIds: _sites.toList(),
          staffId: staffId,
        );
      }
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) return const Scaffold(body: LoadingView());
    final country = ref.watch(profileProvider).value?.country ?? Countries.india;
    final plan = ref.watch(planProvider);
    final scope = ref.watch(scopeBranchProvider);
    final allowed = ref.watch(allowedBranchesProvider);
    final showBranch = !_editing && plan.multiBranch && scope == null && allowed.length > 1;
    return Scaffold(
      appBar: AppBar(title: Text(_editing ? tr('Edit labour') : tr('Add labour'))),
      body: Form(
        key: _form,
        child: FormBody(
          bottom: FilledButton(
            onPressed: _busy ? null : _save,
            child: Text(_editing ? tr('Save changes') : tr('Add labour')),
          ),
          children: [
            Center(
              child: GestureDetector(
                onTap: () async {
                  final p = await pickAndStoreImage(context, folder: 'labour');
                  if (p != null) setState(() => _photo = p);
                },
                child: Stack(
                  children: [
                    Avatar(name: _name.text, image: fileImage(_photo), size: 92),
                    Positioned(
                      right: 0,
                      bottom: 0,
                      child: Container(
                        padding: const EdgeInsets.all(7),
                        decoration: BoxDecoration(color: Palette.brand, shape: BoxShape.circle),
                        child: const Icon(Icons.photo_camera, size: 16, color: Colors.white),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 18),
            TextFormField(
              controller: _name,
              textCapitalization: TextCapitalization.words,
              onChanged: (_) => setState(() {}),
              decoration: InputDecoration(labelText: tr('Full name *')),
              validator: (v) => (v ?? '').trim().isEmpty ? tr('Enter the name') : null,
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _father,
              textCapitalization: TextCapitalization.words,
              decoration: InputDecoration(labelText: tr("Father's / husband's name")),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _mobile,
              keyboardType: TextInputType.phone,
              inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(Phone.maxLength(country))],
              decoration: InputDecoration(labelText: tr('Mobile number'), prefixText: Phone.prefix(country)),
              validator: (v) => Phone.validate(v, country),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _skill,
              textCapitalization: TextCapitalization.words,
              decoration: InputDecoration(labelText: tr('Skill / trade')),
            ),
            const SizedBox(height: 8),
            Wrap(spacing: 6, runSpacing: 6, children: [
              for (final s in LabourService.defaultSkills)
                ActionChip(
                  label: Text(tr(s), style: const TextStyle(fontSize: 12)),
                  visualDensity: VisualDensity.compact,
                  backgroundColor: _skill.text == s ? Palette.brandTint : null,
                  onPressed: () => setState(() => _skill.text = s),
                ),
            ]),
            const SizedBox(height: 12),
            TextFormField(
              controller: _address,
              maxLines: 2,
              textCapitalization: TextCapitalization.sentences,
              decoration: InputDecoration(labelText: tr('Address / village')),
            ),
            SectionTitle(tr('SHIFT'), padding: EdgeInsets.fromLTRB(2, 18, 2, 8)),
            ShiftPicker(shift: _shift, onChanged: (v) => setState(() => _shift = v)),
            if (showBranch) ...[
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: _branchId ?? allowed.first.id,
                decoration: InputDecoration(labelText: tr('Branch')),
                items: [for (final b in allowed) DropdownMenuItem(value: b.id, child: Text(b.name))],
                onChanged: (v) => _branchId = v,
              ),
            ],
            if (!_editing) ...[
              SectionTitle(tr('PAY RATE'), padding: EdgeInsets.fromLTRB(2, 22, 2, 8)),
              SegmentedButton<String>(
                segments: [
                  ButtonSegment(value: 'daily', label: Text(tr('Per day'))),
                  ButtonSegment(value: 'monthly', label: Text(tr('Per month'))),
                ],
                selected: {_payType},
                onSelectionChanged: (v) => setState(() => _payType = v.first),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _amount,
                onChanged: (_) => setState(() {}),
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: InputDecoration(
                  labelText: _payType == 'daily' ? tr('Salary per day *') : tr('Salary per month *'),
                  prefixText: '${Money.symbol} ',
                  helperText: _payType == 'monthly'
                      ? tr('Converted to a daily rate for attendance and overtime.')
                      : null,
                ),
                validator: (v) => (Money.parse(v ?? '') ?? 0) <= 0 ? tr('Enter the rate') : null,
              ),
              const SizedBox(height: 12),
              DutyHoursPicker(
                hours: _duty,
                onChanged: (v) => setState(() => _duty = v),
                dailyPaise: _dailyPaise(),
              ),
              const SizedBox(height: 12),
              InkWell(
                borderRadius: BorderRadius.circular(14),
                onTap: () async {
                  final d = await showDatePicker(
                    context: context,
                    initialDate: _join,
                    firstDate: DateTime(2015),
                    lastDate: DateTime.now(),
                  );
                  if (d != null) setState(() => _join = d);
                },
                child: InputDecorator(
                  decoration: InputDecoration(
                    labelText: tr('Joining date'),
                    suffixIcon: Icon(Icons.calendar_today_outlined),
                  ),
                  child: Text(D.showDt(_join)),
                ),
              ),
              SectionTitle(tr('COMPANY & SITE *'), padding: EdgeInsets.fromLTRB(2, 22, 2, 4)),
              SiteChecklist(single: true, selected: _sites, onChanged: (v) => setState(() => _sites = v)),
            ],
          ],
        ),
      ),
    );
  }
}
