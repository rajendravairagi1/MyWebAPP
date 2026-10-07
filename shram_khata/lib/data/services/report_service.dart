import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../../domain/business_profile.dart';
import '../../domain/calc.dart';
import '../database.dart';
import 'attendance_service.dart';
import 'base.dart';
import 'billing_service.dart';
import 'payment_service.dart';

/// Everything the labour statement PDF needs.
class StatementData {
  StatementData({
    required this.profile,
    required this.labour,
    required this.from,
    required this.to,
    required this.entries,
    required this.siteNames,
    required this.payments,
    required this.ledger,
    required this.rate,
    required this.payType,
    required this.ratePaise,
  });

  final BusinessProfile profile;
  final Labour labour;
  final String from;
  final String to;
  final List<AttendanceData> entries;

  /// siteId -> "Company • Site"
  final Map<String, String> siteNames;
  final List<Payment> payments;
  final Ledger ledger;

  /// Pay rate in force at the end of the period.
  final PayRate? rate;
  final String payType;

  /// The rate as entered (monthly salary for monthly staff).
  final int ratePaise;

  Iterable<String> get sitesWorked =>
      entries.map((e) => siteNames[e.siteId] ?? '').where((s) => s.isNotEmpty).toSet();
}

class MatrixCell {
  MatrixCell(this.value, this.ot);
  final double value;
  final double ot;

  String get letter {
    if (value >= 1) return 'P';
    if (value > 0) return 'H';
    return 'A';
  }
}

class MatrixRow {
  MatrixRow(this.labour, this.cells);
  final Labour labour;
  final Map<String, MatrixCell> cells;

  double get days => cells.values.fold(0.0, (a, c) => a + c.value);
  double get ot => cells.values.fold(0.0, (a, c) => a + c.ot);
  int get absent => cells.values.where((c) => c.value == 0).length;
}

/// Labour × day grid for a set of sites.
class AttendanceMatrix {
  AttendanceMatrix(this.from, this.to, this.rows);
  final String from;
  final String to;
  final List<MatrixRow> rows;

  List<DateTime> get days => D.range(D.parse(from), D.parse(to));
  double get totalDays => rows.fold(0.0, (a, r) => a + r.days);
}

class InvoiceBundle {
  InvoiceBundle(this.profile, this.detail, this.matrix, this.branch);
  final BusinessProfile profile;
  final InvoiceDetail detail;
  final AttendanceMatrix matrix;
  final Branch? branch;
}

class BalanceRow {
  BalanceRow(this.labour, this.ledger);
  final Labour labour;
  final Ledger ledger;
}

class ReportService extends Service {
  ReportService(super.db);

  Future<StatementData> statement(String labourId, String from, String to) async {
    final profile = await loadProfile();
    final labour = await (db.select(db.labours)..where((t) => t.id.equals(labourId))).getSingle();
    final book = await rateBook();
    final att = await (db.select(db.attendance)
          ..where((t) => t.labourId.equals(labourId))
          ..orderBy([(t) => OrderingTerm.asc(t.date)]))
        .get();
    final pays = await (db.select(db.payments)
          ..where((t) => t.labourId.equals(labourId))
          ..orderBy([(t) => OrderingTerm.asc(t.date), (t) => OrderingTerm.asc(t.createdAt)]))
        .get();
    final ledger = buildLedger(attendance: att, payments: pays, rates: book, from: from, to: to);

    final siteRows = await db.select(db.sites).join([
      innerJoin(db.companies, db.companies.id.equalsExp(db.sites.companyId)),
    ]).get();
    final names = {
      for (final r in siteRows)
        r.readTable(db.sites).id:
            '${r.readTable(db.companies).name} • ${r.readTable(db.sites).name}'
    };

    final rates = await (db.select(db.labourRates)
          ..where((t) => t.labourId.equals(labourId) & t.effectiveFrom.isSmallerOrEqualValue(to))
          ..orderBy([(t) => OrderingTerm.desc(t.effectiveFrom)]))
        .get();
    final rateRow = rates.isEmpty ? null : rates.first;

    return StatementData(
      profile: profile,
      labour: labour,
      from: from,
      to: to,
      entries: att.where((a) => a.date.compareTo(from) >= 0 && a.date.compareTo(to) <= 0).toList(),
      siteNames: names,
      payments: pays
          .where((p) => p.voidedAt == null && p.date.compareTo(from) >= 0 && p.date.compareTo(to) <= 0)
          .toList(),
      ledger: ledger,
      rate: book.payRate(labourId, to),
      payType: rateRow?.payType ?? 'daily',
      ratePaise: rateRow?.amount ?? 0,
    );
  }

  /// Attendance grid for the given sites (and optionally only some labour).
  Future<AttendanceMatrix> matrix({
    required Iterable<String> siteIds,
    Iterable<String>? labourIds,
    required String from,
    required String to,
  }) async {
    final att = await AttendanceService(db).entries(
      siteIds: siteIds.toSet(),
      labourIds: labourIds?.toSet(),
      from: from,
      to: to,
    );
    final ids = att.map((a) => a.labourId).toSet();
    final labours = ids.isEmpty
        ? <Labour>[]
        : await (db.select(db.labours)..where((t) => t.id.isIn(ids))).get();
    final cells = <String, Map<String, MatrixCell>>{};
    for (final a in att) {
      final m = cells[a.labourId] ??= {};
      final prev = m[a.date];
      m[a.date] = MatrixCell(
        (prev?.value ?? 0) + dayValue(a.status),
        (prev?.ot ?? 0) + (a.status == AttStatus.absent ? 0 : a.otHours),
      );
    }
    final rows = [for (final l in labours) MatrixRow(l, cells[l.id] ?? {})]
      ..sort((a, b) => a.labour.name.toLowerCase().compareTo(b.labour.name.toLowerCase()));
    return AttendanceMatrix(from, to, rows);
  }

  Future<InvoiceBundle> invoice(String invoiceId) async {
    final profile = await loadProfile();
    final detail = await BillingService(db).detail(invoiceId);
    final siteIds = detail.invoice.siteId != null
        ? [detail.invoice.siteId!]
        : (await (db.select(db.sites)..where((t) => t.companyId.equals(detail.company.id))).get())
            .map((s) => s.id)
            .toList();
    final matrix = await this.matrix(
      siteIds: siteIds,
      labourIds: detail.lines.map((l) => l.labourId).whereType<String>(),
      from: detail.invoice.periodFrom,
      to: detail.invoice.periodTo,
    );
    final branch = await (db.select(db.branches)..where((t) => t.id.equals(detail.invoice.branchId)))
        .getSingleOrNull();
    return InvoiceBundle(profile, detail, matrix, branch);
  }

  /// Account position of every labourer for a period (or all time).
  Future<List<BalanceRow>> balances({String? branchId, String? from, String? to, bool activeOnly = false}) async {
    final book = await rateBook();
    final labours = await (db.select(db.labours)
          ..where((t) {
            Expression<bool> e = const Constant(true);
            if (branchId != null) e = e & t.branchId.equals(branchId);
            if (activeOnly) e = e & t.status.equals('active');
            return e;
          })
          ..orderBy([(t) => OrderingTerm.asc(t.name)]))
        .get();
    final att = await db.select(db.attendance).get();
    final pays = await db.select(db.payments).get();
    final attBy = <String, List<AttendanceData>>{};
    for (final a in att) {
      (attBy[a.labourId] ??= []).add(a);
    }
    final payBy = <String, List<Payment>>{};
    for (final p in pays) {
      (payBy[p.labourId] ??= []).add(p);
    }
    return [
      for (final l in labours)
        BalanceRow(
          l,
          buildLedger(
            attendance: attBy[l.id] ?? const [],
            payments: payBy[l.id] ?? const [],
            rates: book,
            from: from,
            to: to,
          ),
        )
    ];
  }

  Future<List<PaymentRow>> paymentRegister({String? branchId, required String from, required String to}) =>
      PaymentService(db).list(branchId: branchId, from: from, to: to, includeVoided: false);
}
