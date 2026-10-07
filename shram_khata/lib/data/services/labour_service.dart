import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../database.dart';
import 'base.dart';

class LabourStatus {
  LabourStatus._();
  static const active = 'active';
  static const inactive = 'inactive';
  static const left = 'left';
  static const all = [active, inactive, left];

  static String label(String s) => switch (s) {
        active => 'Active',
        inactive => 'Inactive',
        left => 'Left',
        _ => s,
      };
}

/// A site a labourer is assigned to, with names for display.
class AssignmentInfo {
  AssignmentInfo(this.assignment, this.site, this.company);
  final Assignment assignment;
  final Site site;
  final Company company;
  String get label => '${company.name} • ${site.name}';
}

class LabourService extends Service {
  LabourService(super.db);

  static const defaultSkills = [
    'Helper',
    'Mason',
    'Carpenter',
    'Electrician',
    'Plumber',
    'Painter',
    'Welder',
    'Fitter',
    'Mechanic',
    'Engineer',
    'Driver',
    'Operator',
    'Supervisor',
    'Security Guard',
    'Housekeeping',
  ];

  Future<List<Labour>> list({
    String? branchId,
    Set<String>? statuses,
    String query = '',
    Set<String>? siteIds,
  }) async {
    final q = db.select(db.labours)..orderBy([(t) => OrderingTerm.asc(t.name)]);
    q.where((t) {
      Expression<bool> e = const Constant(true);
      if (branchId != null) e = e & t.branchId.equals(branchId);
      if (statuses != null && statuses.isNotEmpty) e = e & t.status.isIn(statuses);
      return e;
    });
    var rows = await q.get();
    final needle = query.trim().toLowerCase();
    if (needle.isNotEmpty) {
      rows = rows
          .where((l) =>
              l.name.toLowerCase().contains(needle) ||
              l.mobile.contains(needle) ||
              l.skill.toLowerCase().contains(needle))
          .toList();
    }
    if (siteIds != null && siteIds.isNotEmpty) {
      final asg = await (db.select(db.assignments)
            ..where((t) => t.siteId.isIn(siteIds) & t.toDate.isNull()))
          .get();
      final ids = asg.map((a) => a.labourId).toSet();
      rows = rows.where((l) => ids.contains(l.id)).toList();
    }
    return rows;
  }

  Future<Labour?> byId(String id) =>
      (db.select(db.labours)..where((t) => t.id.equals(id))).getSingleOrNull();

  /// Another active labourer with the same mobile number, if any.
  Future<Labour?> findDuplicate(String mobile, {String? exceptId}) async {
    final m = mobile.trim();
    if (m.isEmpty) return null;
    final q = db.select(db.labours)
      ..where((t) => t.mobile.equals(m) & t.status.equals(LabourStatus.active));
    final rows = await q.get();
    for (final r in rows) {
      if (r.id != exceptId) return r;
    }
    return null;
  }

  Future<String> add({
    required String branchId,
    required String name,
    String fatherName = '',
    String mobile = '',
    String address = '',
    required String skill,
    String? photoPath,
    required String joinDate,
    required String payType,
    required int amount,
    int? otPerHour,
    List<String> siteIds = const [],
    String? staffId,
  }) async {
    if (name.trim().isEmpty) throw AppException('Name is required.');
    if (amount <= 0) throw AppException('Enter the pay rate.');
    final id = newId();
    await db.transaction(() async {
      await db.into(db.labours).insert(LaboursCompanion.insert(
            id: id,
            branchId: branchId,
            name: name.trim(),
            fatherName: Value(fatherName.trim()),
            mobile: Value(mobile.trim()),
            address: Value(address.trim()),
            skill: Value(skill.trim().isEmpty ? 'Helper' : skill.trim()),
            photoPath: Value(photoPath),
            joinDate: joinDate,
          ));
      await db.into(db.labourRates).insert(LabourRatesCompanion.insert(
            id: newId(),
            labourId: id,
            effectiveFrom: joinDate,
            payType: Value(payType),
            amount: amount,
            otPerHour: Value(otPerHour),
          ));
      for (final s in siteIds) {
        await db.into(db.assignments).insert(AssignmentsCompanion.insert(
              id: newId(),
              labourId: id,
              siteId: s,
              fromDate: joinDate,
            ));
      }
      await audit(staffId, 'add', 'labour', id, name.trim());
    });
    return id;
  }

  Future<void> update(Labour l, {String? staffId}) async {
    if (l.name.trim().isEmpty) throw AppException('Name is required.');
    await db.update(db.labours).replace(l);
    await audit(staffId, 'edit', 'labour', l.id, l.name);
  }

  /// "Delete" for labour is deactivation so past attendance and payments stay
  /// intact. The labourer disappears from attendance sheets from today.
  Future<void> setStatus(String id, String status, {String? staffId}) async {
    final today = D.today();
    await db.transaction(() async {
      await (db.update(db.labours)..where((t) => t.id.equals(id))).write(
        LaboursCompanion(
          status: Value(status),
          leaveDate: Value(status == LabourStatus.active ? null : today),
        ),
      );
      if (status != LabourStatus.active) {
        await (db.update(db.assignments)
              ..where((t) => t.labourId.equals(id) & t.toDate.isNull()))
            .write(AssignmentsCompanion(toDate: Value(today)));
      }
      await audit(staffId, 'status:$status', 'labour', id);
    });
  }

  /// True only if nothing refers to this labourer (an entry made by mistake).
  Future<bool> canHardDelete(String id) async {
    final a = await (db.select(db.attendance)..where((t) => t.labourId.equals(id))).get();
    if (a.isNotEmpty) return false;
    final p = await (db.select(db.payments)..where((t) => t.labourId.equals(id))).get();
    if (p.isNotEmpty) return false;
    final il = await (db.select(db.invoiceLines)..where((t) => t.labourId.equals(id))).get();
    return il.isEmpty;
  }

  Future<void> hardDelete(String id, {String? staffId}) async {
    if (!await canHardDelete(id)) {
      throw AppException('This labourer has attendance or payments. Mark inactive instead.');
    }
    await db.transaction(() async {
      await (db.delete(db.assignments)..where((t) => t.labourId.equals(id))).go();
      await (db.delete(db.labourRates)..where((t) => t.labourId.equals(id))).go();
      await (db.delete(db.labourDocuments)..where((t) => t.labourId.equals(id))).go();
      await (db.delete(db.labours)..where((t) => t.id.equals(id))).go();
      await audit(staffId, 'delete', 'labour', id);
    });
  }

  // ---- rates -----------------------------------------------------------------

  Future<List<LabourRate>> rates(String labourId) => (db.select(db.labourRates)
        ..where((t) => t.labourId.equals(labourId))
        ..orderBy([(t) => OrderingTerm.desc(t.effectiveFrom)]))
      .get();

  /// Sets the pay rate from [from] onwards. Earlier days keep their old rate.
  Future<void> setRate({
    required String labourId,
    required String from,
    required String payType,
    required int amount,
    int? otPerHour,
    String? staffId,
  }) async {
    if (amount <= 0) throw AppException('Enter the pay rate.');
    await db.transaction(() async {
      await (db.delete(db.labourRates)
            ..where((t) => t.labourId.equals(labourId) & t.effectiveFrom.equals(from)))
          .go();
      await db.into(db.labourRates).insert(LabourRatesCompanion.insert(
            id: newId(),
            labourId: labourId,
            effectiveFrom: from,
            payType: Value(payType),
            amount: amount,
            otPerHour: Value(otPerHour),
          ));
      await audit(staffId, 'rate', 'labour', labourId, '$payType $amount from $from');
    });
  }

  // ---- assignments ---------------------------------------------------------

  Future<List<AssignmentInfo>> assignments(String labourId, {bool activeOnly = true}) async {
    final q = db.select(db.assignments).join([
      innerJoin(db.sites, db.sites.id.equalsExp(db.assignments.siteId)),
      innerJoin(db.companies, db.companies.id.equalsExp(db.sites.companyId)),
    ])
      ..where(db.assignments.labourId.equals(labourId))
      ..orderBy([OrderingTerm.desc(db.assignments.fromDate)]);
    if (activeOnly) q.where(db.assignments.toDate.isNull());
    final rows = await q.get();
    return rows
        .map((r) => AssignmentInfo(
              r.readTable(db.assignments),
              r.readTable(db.sites),
              r.readTable(db.companies),
            ))
        .toList();
  }

  Future<void> assign(String labourId, String siteId, {String? from}) async {
    final existing = await (db.select(db.assignments)
          ..where((t) =>
              t.labourId.equals(labourId) & t.siteId.equals(siteId) & t.toDate.isNull()))
        .get();
    if (existing.isNotEmpty) return;
    await db.into(db.assignments).insert(AssignmentsCompanion.insert(
          id: newId(),
          labourId: labourId,
          siteId: siteId,
          fromDate: from ?? D.today(),
        ));
  }

  Future<void> unassign(String assignmentId, {String? to}) =>
      (db.update(db.assignments)..where((t) => t.id.equals(assignmentId)))
          .write(AssignmentsCompanion(toDate: Value(to ?? D.today())));

  // ---- documents --------------------------------------------------------------

  static const docTypes = [
    'Aadhaar',
    'PAN',
    'Bank passbook',
    'Photo ID',
    'Police verification',
    'Medical certificate',
    'ESI / PF card',
    'Skill certificate',
    'Other',
  ];

  Future<List<LabourDocument>> documents(String labourId) =>
      (db.select(db.labourDocuments)
            ..where((t) => t.labourId.equals(labourId))
            ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
          .get();

  Future<void> addDocument({
    required String labourId,
    required String docType,
    String docNumber = '',
    String? filePath,
    String? expiryDate,
  }) =>
      db.into(db.labourDocuments).insert(LabourDocumentsCompanion.insert(
            id: newId(),
            labourId: labourId,
            docType: docType,
            docNumber: Value(docNumber.trim()),
            filePath: Value(filePath),
            expiryDate: Value(expiryDate),
          ));

  Future<void> deleteDocument(String id) =>
      (db.delete(db.labourDocuments)..where((t) => t.id.equals(id))).go();

  /// Documents that expire within [days] (or already have).
  Future<List<(Labour, LabourDocument)>> expiringDocuments({int days = 30}) async {
    final limit = D.ymd(D.addDays(DateTime.now(), days));
    final q = db.select(db.labourDocuments).join([
      innerJoin(db.labours, db.labours.id.equalsExp(db.labourDocuments.labourId)),
    ])
      ..where(db.labourDocuments.expiryDate.isNotNull() &
          db.labourDocuments.expiryDate.isSmallerOrEqualValue(limit) &
          db.labours.status.equals(LabourStatus.active));
    final rows = await q.get();
    return rows.map((r) => (r.readTable(db.labours), r.readTable(db.labourDocuments))).toList();
  }
}
