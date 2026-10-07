import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../../domain/calc.dart';
import '../database.dart';
import 'base.dart';

/// One labourer's line on a site's daily sheet.
class SheetRow {
  SheetRow({
    required this.labour,
    required this.entry,
    required this.otherValue,
    required this.otherSites,
  });

  final Labour labour;
  final AttendanceData? entry;

  /// Day value already recorded for this person at other sites that day.
  final double otherValue;
  final List<String> otherSites;

  String? get status => entry?.status;
  double get otHours => entry?.otHours ?? 0;

  /// Whether [status] can still be recorded here without exceeding one day.
  bool allows(String status) => fitsInDay(otherValue, status);
}

class Sheet {
  Sheet({
    required this.site,
    required this.company,
    required this.date,
    required this.rows,
    required this.submitted,
  });

  final Site site;
  final Company company;
  final String date;
  final List<SheetRow> rows;
  final AttendanceSheet? submitted;

  bool get isSubmitted => submitted != null;
  int get total => rows.length;
  int get present => rows.where((r) => r.status == AttStatus.present).length;
  int get half => rows.where((r) => r.status == AttStatus.half).length;
  int get absent => rows.where((r) => r.status == AttStatus.absent).length;
  int get unmarked => rows.where((r) => r.status == null).length;
  double get otTotal => rows.fold(0.0, (a, r) => a + r.otHours);
}

class AttendanceService extends Service {
  AttendanceService(super.db);

  /// Builds the sheet for a site and date: everyone assigned to the site on
  /// that day plus anyone who already has an entry there.
  ///
  /// An assignment's `toDate` is the first day it no longer applies.
  Future<Sheet> loadSheet(String siteId, String date) async {
    final site = await (db.select(db.sites)..where((t) => t.id.equals(siteId))).getSingle();
    final company =
        await (db.select(db.companies)..where((t) => t.id.equals(site.companyId))).getSingle();

    final asg = await (db.select(db.assignments)
          ..where((t) =>
              t.siteId.equals(siteId) &
              t.fromDate.isSmallerOrEqualValue(date) &
              (t.toDate.isNull() | t.toDate.isBiggerThanValue(date))))
        .get();
    final entries = await (db.select(db.attendance)
          ..where((t) => t.siteId.equals(siteId) & t.date.equals(date)))
        .get();
    final entryByLabour = {for (final e in entries) e.labourId: e};
    final ids = {...asg.map((a) => a.labourId), ...entryByLabour.keys};
    if (ids.isEmpty) {
      return Sheet(site: site, company: company, date: date, rows: [], submitted: await _submitted(siteId, date));
    }

    final labours = await (db.select(db.labours)..where((t) => t.id.isIn(ids))).get();
    final others = await (db.select(db.attendance).join([
      innerJoin(db.sites, db.sites.id.equalsExp(db.attendance.siteId)),
    ])
          ..where(db.attendance.labourId.isIn(ids) &
              db.attendance.date.equals(date) &
              db.attendance.siteId.equals(siteId).not()))
        .get();
    final otherValue = <String, double>{};
    final otherSites = <String, List<String>>{};
    for (final r in others) {
      final a = r.readTable(db.attendance);
      otherValue[a.labourId] = (otherValue[a.labourId] ?? 0) + dayValue(a.status);
      if (a.status != AttStatus.absent) {
        (otherSites[a.labourId] ??= []).add(r.readTable(db.sites).name);
      }
    }

    final rows = <SheetRow>[];
    for (final l in labours) {
      final entry = entryByLabour[l.id];
      // Skip people who joined later or left unless they already have an entry.
      if (entry == null && (l.status != 'active' || l.joinDate.compareTo(date) > 0)) continue;
      rows.add(SheetRow(
        labour: l,
        entry: entry,
        otherValue: otherValue[l.id] ?? 0,
        otherSites: otherSites[l.id] ?? const [],
      ));
    }
    rows.sort((a, b) => a.labour.name.toLowerCase().compareTo(b.labour.name.toLowerCase()));
    return Sheet(
      site: site,
      company: company,
      date: date,
      rows: rows,
      submitted: await _submitted(siteId, date),
    );
  }

  Future<AttendanceSheet?> _submitted(String siteId, String date) =>
      (db.select(db.attendanceSheets)
            ..where((t) => t.siteId.equals(siteId) & t.date.equals(date)))
          .getSingleOrNull();

  void _checkDate(String date) {
    if (date.compareTo(D.today()) > 0) {
      throw AppException('Attendance cannot be marked for a future date.');
    }
  }

  Future<void> _checkUnlocked(String siteId, String date, bool canEditLocked) async {
    if (canEditLocked) return;
    if (await _submitted(siteId, date) != null) {
      throw AppException('This sheet is already submitted. Ask the owner to unlock it.');
    }
  }

  Future<double> _otherValue(String labourId, String siteId, String date) async {
    final rows = await (db.select(db.attendance)
          ..where((t) =>
              t.labourId.equals(labourId) &
              t.date.equals(date) &
              t.siteId.equals(siteId).not()))
        .get();
    return rows.fold<double>(0, (a, r) => a + dayValue(r.status));
  }

  /// Records or updates one person's attendance for the day.
  Future<void> mark({
    required String siteId,
    required String date,
    required String labourId,
    required String status,
    double otHours = 0,
    String note = '',
    String? staffId,
    bool canEditLocked = false,
  }) async {
    _checkDate(date);
    if (!AttStatus.all.contains(status)) throw AppException('Invalid status.');
    await _checkUnlocked(siteId, date, canEditLocked);
    if (otHours < 0 || otHours > 16) throw AppException('Overtime must be between 0 and 16 hours.');
    final other = await _otherValue(labourId, siteId, date);
    if (!fitsInDay(other, status)) {
      throw AppException(
          'This person is already marked at another site on this day. One person cannot be more than one full day.');
    }
    final ot = status == AttStatus.absent ? 0.0 : otHours;
    final existing = await (db.select(db.attendance)
          ..where((t) =>
              t.labourId.equals(labourId) & t.siteId.equals(siteId) & t.date.equals(date)))
        .getSingleOrNull();
    final late = date.compareTo(D.today()) < 0;
    if (existing == null) {
      await db.into(db.attendance).insert(AttendanceCompanion.insert(
            id: newId(),
            labourId: labourId,
            siteId: siteId,
            date: date,
            status: status,
            otHours: Value(ot),
            note: Value(note),
            markedBy: Value(staffId),
            isLate: Value(late),
          ));
    } else {
      await (db.update(db.attendance)..where((t) => t.id.equals(existing.id))).write(
        AttendanceCompanion(
          status: Value(status),
          otHours: Value(ot),
          note: Value(note),
          markedBy: Value(staffId),
          markedAt: Value(DateTime.now()),
        ),
      );
      if (existing.status != status || existing.otHours != ot) {
        await audit(staffId, 'edit', 'attendance', existing.id,
            '$date ${existing.status}/${existing.otHours} -> $status/$ot');
      }
    }
  }

  Future<void> clear({
    required String siteId,
    required String date,
    required String labourId,
    String? staffId,
    bool canEditLocked = false,
  }) async {
    await _checkUnlocked(siteId, date, canEditLocked);
    await (db.delete(db.attendance)
          ..where((t) =>
              t.labourId.equals(labourId) & t.siteId.equals(siteId) & t.date.equals(date)))
        .go();
  }

  /// Marks everyone who has no entry yet as present (skipping anyone already
  /// at another site that day). Returns how many were marked.
  Future<int> markAllPresent({
    required String siteId,
    required String date,
    String? staffId,
    bool canEditLocked = false,
  }) async {
    _checkDate(date);
    await _checkUnlocked(siteId, date, canEditLocked);
    final sheet = await loadSheet(siteId, date);
    var n = 0;
    for (final r in sheet.rows.where((r) => r.status == null)) {
      if (!r.allows(AttStatus.present)) continue;
      await mark(
        siteId: siteId,
        date: date,
        labourId: r.labour.id,
        status: AttStatus.present,
        staffId: staffId,
        canEditLocked: canEditLocked,
      );
      n++;
    }
    return n;
  }

  /// Locks the sheet. Everyone must be marked; pass [markRestAbsent] to
  /// record the unmarked people as absent.
  Future<void> submit({
    required String siteId,
    required String date,
    String? staffId,
    bool markRestAbsent = false,
  }) async {
    _checkDate(date);
    final sheet = await loadSheet(siteId, date);
    if (sheet.isSubmitted) return;
    final unmarked = sheet.rows.where((r) => r.status == null).toList();
    if (unmarked.isNotEmpty && !markRestAbsent) {
      throw AppException('${unmarked.length} people are not marked yet.');
    }
    await db.transaction(() async {
      for (final r in unmarked) {
        await mark(
          siteId: siteId,
          date: date,
          labourId: r.labour.id,
          status: AttStatus.absent,
          staffId: staffId,
        );
      }
      await db.into(db.attendanceSheets).insert(AttendanceSheetsCompanion.insert(
            id: newId(),
            siteId: siteId,
            date: date,
            submittedBy: Value(staffId),
          ));
      await audit(staffId, 'submit', 'attendance_sheet', siteId, date);
    });
  }

  Future<void> unlock({required String siteId, required String date, String? staffId}) async {
    await (db.delete(db.attendanceSheets)
          ..where((t) => t.siteId.equals(siteId) & t.date.equals(date)))
        .go();
    await audit(staffId, 'unlock', 'attendance_sheet', siteId, date);
  }

  /// Puts a labourer on a site's sheet (creates an open assignment).
  Future<void> addToSheet({required String siteId, required String labourId, required String date}) async {
    final existing = await (db.select(db.assignments)
          ..where((t) =>
              t.siteId.equals(siteId) & t.labourId.equals(labourId) & t.toDate.isNull()))
        .get();
    if (existing.isNotEmpty) return;
    await db.into(db.assignments).insert(AssignmentsCompanion.insert(
          id: newId(),
          labourId: labourId,
          siteId: siteId,
          fromDate: date,
        ));
  }

  // ---- queries for ledgers and reports ---------------------------------------

  Future<List<AttendanceData>> entries({
    String? labourId,
    Iterable<String>? siteIds,
    Iterable<String>? labourIds,
    String? from,
    String? to,
  }) {
    final q = db.select(db.attendance)
      ..orderBy([(t) => OrderingTerm.asc(t.date), (t) => OrderingTerm.asc(t.siteId)]);
    q.where((t) {
      Expression<bool> e = const Constant(true);
      if (labourId != null) e = e & t.labourId.equals(labourId);
      if (siteIds != null) e = e & t.siteId.isIn(siteIds);
      if (labourIds != null) e = e & t.labourId.isIn(labourIds);
      if (from != null) e = e & t.date.isBiggerOrEqualValue(from);
      if (to != null) e = e & t.date.isSmallerOrEqualValue(to);
      return e;
    });
    return q.get();
  }
}
