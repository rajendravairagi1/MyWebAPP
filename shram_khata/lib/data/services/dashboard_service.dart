import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../../domain/calc.dart';
import '../database.dart';
import 'base.dart';
import 'billing_service.dart';
import 'labour_service.dart';
import 'company_service.dart';
import 'payment_service.dart';

class CompanyDay {
  CompanyDay({
    required this.company,
    required this.present,
    required this.half,
    required this.absent,
    required this.billing,
    required this.cost,
  });
  final Company company;
  final int present;
  final int half;
  final int absent;
  final int billing;
  final int cost;
  int get working => present + half;
}

class DayPoint {
  DayPoint(this.date, this.billing, this.cost);
  final DateTime date;
  final int billing;
  final int cost;
}

class PendingSite {
  PendingSite(this.site, this.company, this.headcount);
  final Site site;
  final Company company;
  final int headcount;
}

class DashboardData {
  DashboardData({
    required this.totalLabour,
    required this.deployed,
    required this.present,
    required this.half,
    required this.absent,
    required this.notMarked,
    required this.otHours,
    required this.billing,
    required this.labourCost,
    required this.paidOut,
    required this.advanceToday,
    required this.advanceToRecover,
    required this.companies,
    required this.pendingSites,
    required this.activeCompanies,
    required this.activeSites,
    required this.companyOutstanding,
    required this.overdueInvoices,
    required this.labourPayable,
    required this.missingRateEntries,
    required this.week,
    required this.expiringDocs,
    required this.expiringContracts,
  });

  final int totalLabour;

  /// Active labour assigned to at least one site on the day.
  final int deployed;
  final int present;
  final int half;
  final int absent;
  final int notMarked;
  final double otHours;

  /// What the day is worth when billed to companies ("earned").
  final int billing;

  /// Wages accrued to labour for the day.
  final int labourCost;

  /// Money actually paid to labour on the day.
  final int paidOut;

  /// Advances handed out on the day (part of [paidOut]).
  final int advanceToday;

  /// Total workers currently owe the agency (negative balances).
  final int advanceToRecover;
  final List<CompanyDay> companies;
  final List<PendingSite> pendingSites;
  final int activeCompanies;
  final int activeSites;
  final int companyOutstanding;
  final int overdueInvoices;
  final int labourPayable;
  final int missingRateEntries;
  final List<DayPoint> week;
  final int expiringDocs;
  final int expiringContracts;

  int get margin => billing - labourCost;
  int get working => present + half;
}

class DashboardService extends Service {
  DashboardService(super.db);

  Future<DashboardData> load(DateTime date, {String? branchId}) async {
    final day = D.ymd(date);
    final weekFrom = D.ymd(D.addDays(date, -6));

    final companies = await CompanyService(db).companies(branchId: branchId);
    final companyIds = companies.map((c) => c.id).toSet();
    final sites = (await CompanyService(db).sites(branchId: branchId))
        .where((s) => companyIds.contains(s.companyId))
        .toList();
    final siteIds = sites.map((s) => s.id).toSet();
    final siteCompany = {for (final s in sites) s.id: s.companyId};

    final allLabours = await LabourService(db).list(
      branchId: branchId,
      statuses: {LabourStatus.active},
    );
    final labourIds = allLabours.map((l) => l.id).toSet();

    final book = await rateBook();

    final att = siteIds.isEmpty
        ? <AttendanceData>[]
        : await (db.select(db.attendance)
              ..where((t) =>
                  t.siteId.isIn(siteIds) &
                  t.date.isBiggerOrEqualValue(weekFrom) &
                  t.date.isSmallerOrEqualValue(day)))
            .get();

    // Week series and today's money.
    final billBy = <String, int>{};
    final costBy = <String, int>{};
    for (final a in att) {
      billBy[a.date] = (billBy[a.date] ?? 0) + book.bill(a);
      costBy[a.date] = (costBy[a.date] ?? 0) + book.wage(a);
    }
    final week = [
      for (var i = 6; i >= 0; i--)
        () {
          final d = D.addDays(date, -i);
          final k = D.ymd(d);
          return DayPoint(d, billBy[k] ?? 0, costBy[k] ?? 0);
        }()
    ];

    final today = att.where((a) => a.date == day).toList();
    var otHours = 0.0;
    var missing = 0;
    final perLabour = <String, double>{};
    final perCompany = <String, Map<String, double>>{};
    final compBill = <String, int>{};
    final compCost = <String, int>{};
    for (final a in today) {
      perLabour[a.labourId] = (perLabour[a.labourId] ?? 0) + dayValue(a.status);
      final cid = siteCompany[a.siteId];
      if (cid != null) {
        final m = perCompany[cid] ??= {};
        m[a.labourId] = (m[a.labourId] ?? 0) + dayValue(a.status);
        compBill[cid] = (compBill[cid] ?? 0) + book.bill(a);
        compCost[cid] = (compCost[cid] ?? 0) + book.wage(a);
      }
      if (a.status != AttStatus.absent) otHours += a.otHours;
      if (!book.hasBillRate(a)) missing++;
    }
    int countWhere(Map<String, double> m, bool Function(double) f) =>
        m.values.where(f).length;
    final present = countWhere(perLabour, (v) => v >= 1);
    final half = countWhere(perLabour, (v) => v > 0 && v < 1);
    final absent = countWhere(perLabour, (v) => v == 0);

    final companyRows = [
      for (final c in companies)
        CompanyDay(
          company: c,
          present: countWhere(perCompany[c.id] ?? {}, (v) => v >= 1),
          half: countWhere(perCompany[c.id] ?? {}, (v) => v > 0 && v < 1),
          absent: countWhere(perCompany[c.id] ?? {}, (v) => v == 0),
          billing: compBill[c.id] ?? 0,
          cost: compCost[c.id] ?? 0,
        )
    ]..removeWhere((r) => r.present + r.half + r.absent == 0);
    companyRows.sort((a, b) => b.working.compareTo(a.working));

    // Who should have been marked today, and which sites haven't submitted.
    final asg = siteIds.isEmpty
        ? <Assignment>[]
        : await (db.select(db.assignments)
              ..where((t) =>
                  t.siteId.isIn(siteIds) &
                  t.fromDate.isSmallerOrEqualValue(day) &
                  (t.toDate.isNull() | t.toDate.isBiggerThanValue(day))))
            .get();
    final expected = <String>{};
    final headBySite = <String, int>{};
    for (final a in asg) {
      if (!labourIds.contains(a.labourId)) continue;
      expected.add(a.labourId);
      headBySite[a.siteId] = (headBySite[a.siteId] ?? 0) + 1;
    }
    final notMarked = expected.where((id) => !perLabour.containsKey(id)).length;

    final submitted = siteIds.isEmpty
        ? <AttendanceSheet>[]
        : await (db.select(db.attendanceSheets)
              ..where((t) => t.siteId.isIn(siteIds) & t.date.equals(day)))
            .get();
    final done = submitted.map((s) => s.siteId).toSet();
    final compById = {for (final c in companies) c.id: c};
    final pending = [
      for (final s in sites)
        if ((headBySite[s.id] ?? 0) > 0 && !done.contains(s.id))
          PendingSite(s, compById[s.companyId]!, headBySite[s.id]!)
    ];

    final paidRows = await PaymentService(db).list(
      branchId: branchId,
      from: day,
      to: day,
      includeVoided: false,
    );
    final paidOut = paidRows
        .where((r) => PayType.isCashOut(r.payment.type))
        .fold<int>(0, (a, r) => a + r.payment.amount);

    final advanceToday = paidRows
        .where((r) => r.payment.type == PayType.advance)
        .fold<int>(0, (a, r) => a + r.payment.amount);

    final invoices = await BillingService(db).list(branchId: branchId);
    final outstanding = invoices.fold<int>(0, (a, r) => a + r.outstanding);
    final overdue = invoices.where((r) => r.state == InvoiceState.overdue).length;

    final balances = await PaymentService(db).balances(branchId: branchId);
    final payable = balances.entries
        .where((e) => labourIds.contains(e.key) && e.value > 0)
        .fold<int>(0, (a, e) => a + e.value);

    final recover = balances.entries
        .where((e) => labourIds.contains(e.key) && e.value < 0)
        .fold<int>(0, (a, e) => a - e.value);

    final docs = await LabourService(db).expiringDocuments();
    final contracts = await CompanyService(db).expiringContracts(branchId: branchId);

    return DashboardData(
      totalLabour: allLabours.length,
      deployed: expected.length,
      present: present,
      half: half,
      absent: absent,
      notMarked: notMarked,
      otHours: otHours,
      billing: billBy[day] ?? 0,
      labourCost: costBy[day] ?? 0,
      paidOut: paidOut,
      advanceToday: advanceToday,
      advanceToRecover: recover,
      companies: companyRows,
      pendingSites: pending,
      activeCompanies: companies.length,
      activeSites: sites.length,
      companyOutstanding: outstanding,
      overdueInvoices: overdue,
      labourPayable: payable,
      missingRateEntries: missing,
      week: week,
      expiringDocs: docs.where((d) => labourIds.contains(d.$1.id)).length,
      expiringContracts: contracts.length,
    );
  }
}
