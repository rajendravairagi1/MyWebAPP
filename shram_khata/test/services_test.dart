import 'package:flutter_test/flutter_test.dart';
import 'package:shram_khata/core/money.dart';
import 'package:shram_khata/core/plans.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/services/attendance_service.dart';
import 'package:shram_khata/data/services/base.dart';
import 'package:shram_khata/data/services/billing_service.dart';
import 'package:shram_khata/data/services/company_service.dart';
import 'package:shram_khata/data/services/dashboard_service.dart';
import 'package:shram_khata/data/services/labour_service.dart';
import 'package:shram_khata/data/services/payment_service.dart';
import 'package:shram_khata/data/services/workspace_service.dart';
import 'package:shram_khata/domain/business_profile.dart';
import 'package:shram_khata/domain/calc.dart';

void main() {
  late AppDatabase db;
  late String branchId;
  late String companyA, companyB, siteA, siteB;

  setUp(() async {
    db = AppDatabase.inMemory();
    final ws = WorkspaceService(db);
    await ws.createWorkspace(
      plan: Plan.team,
      businessName: 'Test Agency',
      ownerName: 'Owner',
      email: 'o@x.com',
      mobile: '9999999999',
      password: 'secret1',
    );
    branchId = (await ws.branches()).first.id;
    final cs = CompanyService(db);
    companyA = await cs.addCompany(branchId: branchId, name: 'ABC Ltd');
    companyB = await cs.addCompany(branchId: branchId, name: 'XYZ Corp');
    siteA = await cs.addSite(companyId: companyA, name: 'Plant 1');
    siteB = await cs.addSite(companyId: companyB, name: 'Unit 2');
    await cs.saveContract(
      companyId: companyA,
      title: 'Annual',
      startDate: '2026-01-01',
      rates: [
        (skill: 'Helper', perDay: 70000, otPerHour: 10000),
        (skill: 'Mechanic', perDay: 150000, otPerHour: 20000),
      ],
    );
    await cs.saveContract(
      companyId: companyB,
      title: 'Annual',
      startDate: '2026-01-01',
      rates: [(skill: 'Mechanic', perDay: 160000, otPerHour: 0)],
    );
  });

  tearDown(() => db.close());

  Future<String> helper({List<String>? sites, int rate = 60000}) =>
      LabourService(db).add(
        branchId: branchId,
        name: 'Ramesh',
        skill: 'Helper',
        joinDate: '2026-10-01',
        payType: 'daily',
        amount: rate,
        siteIds: sites ?? [siteA],
      );

  group('Money', () {
    test('formats with Indian grouping', () {
      expect(Money.format(123456700), '₹12,34,567');
      expect(Money.format(150), '₹1.50');
      expect(Money.compact(15000000), '₹1.5L');
    });
    test('parses user input', () {
      expect(Money.parse('₹ 1,250.50'), 125050);
      expect(Money.parse(''), isNull);
      expect(Money.parse('abc'), isNull);
    });
    test('amount in words', () {
      expect(Money.inWords(12345000), 'Rupees One Lakh Twenty Three Thousand Four Hundred Fifty Only');
      expect(Money.inWords(100), 'Rupees One Only');
      expect(Money.inWords(150), 'Rupees One and Fifty Paise Only');
    });
  });

  group('wages', () {
    test('half day and overtime', () {
      const rate = PayRate(60000, 7500);
      expect(wageFor(rate, 'P', 0), 60000);
      expect(wageFor(rate, 'H', 0), 30000);
      expect(wageFor(rate, 'A', 3), 0);
      expect(wageFor(rate, 'P', 2), 75000);
    });
    test('monthly salary converts to a daily rate', () {
      final r = LabourRate(
          id: '1', labourId: 'l', effectiveFrom: '2026-01-01', payType: 'monthly', amount: 1300000, otPerHour: null);
      final p = resolveRate(r);
      expect(p.dailyPaise, 50000);
      expect(p.otPerHourPaise, 6250);
    });
  });

  group('attendance', () {
    test('marks, updates and submits a sheet', () async {
      final id = await helper();
      final svc = AttendanceService(db);
      var sheet = await svc.loadSheet(siteA, '2026-10-05');
      expect(sheet.rows.length, 1);
      expect(sheet.unmarked, 1);
      await svc.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'P', otHours: 2);
      await svc.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'H', otHours: 2);
      sheet = await svc.loadSheet(siteA, '2026-10-05');
      expect(sheet.half, 1);
      expect(sheet.rows.single.otHours, 2);
      await svc.submit(siteId: siteA, date: '2026-10-05');
      await expectLater(
        svc.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'P'),
        throwsA(isA<AppException>()),
      );
      await svc.mark(
          siteId: siteA, date: '2026-10-05', labourId: id, status: 'P', canEditLocked: true);
    });

    test('submit needs everyone marked', () async {
      await helper();
      final svc = AttendanceService(db);
      await expectLater(svc.submit(siteId: siteA, date: '2026-10-05'), throwsA(isA<AppException>()));
      await svc.submit(siteId: siteA, date: '2026-10-05', markRestAbsent: true);
      final s = await svc.loadSheet(siteA, '2026-10-05');
      expect(s.absent, 1);
      expect(s.isSubmitted, isTrue);
    });

    test('a person cannot exceed one day across sites', () async {
      final id = await helper(sites: [siteA, siteB]);
      final svc = AttendanceService(db);
      await svc.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'H');
      await svc.mark(siteId: siteB, date: '2026-10-05', labourId: id, status: 'H');
      await expectLater(
        svc.mark(siteId: siteB, date: '2026-10-05', labourId: id, status: 'P'),
        throwsA(isA<AppException>()),
      );
      final other = await svc.loadSheet(siteB, '2026-10-05');
      expect(other.rows.single.otherValue, 0.5);
    });

    test('future dates are rejected', () async {
      final id = await helper();
      await expectLater(
        AttendanceService(db).mark(siteId: siteA, date: '2099-01-01', labourId: id, status: 'P'),
        throwsA(isA<AppException>()),
      );
    });

    test('left labour disappears from later sheets but keeps history', () async {
      final id = await helper();
      final svc = AttendanceService(db);
      await svc.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'P');
      await LabourService(db).setStatus(id, 'left');
      final past = await svc.loadSheet(siteA, '2026-10-05');
      expect(past.rows.length, 1);
      final later = await svc.loadSheet(siteA, '2026-10-30');
      expect(later.rows, isEmpty);
    });
  });

  group('ledger', () {
    test('earned, paid and balance with rate change', () async {
      final id = await helper(rate: 60000);
      final att = AttendanceService(db);
      for (final d in ['2026-10-01', '2026-10-02', '2026-10-03']) {
        await att.mark(siteId: siteA, date: d, labourId: id, status: 'P');
      }
      await att.mark(siteId: siteA, date: '2026-10-04', labourId: id, status: 'H', otHours: 2);
      await att.mark(siteId: siteA, date: '2026-10-05', labourId: id, status: 'A');
      // Raise the rate from 5th; earlier days must keep the old rate.
      await LabourService(db).setRate(labourId: id, from: '2026-10-05', payType: 'daily', amount: 80000);
      await att.mark(siteId: siteA, date: '2026-10-06', labourId: id, status: 'P');

      final pay = PaymentService(db);
      await pay.add(branchId: branchId, labourId: id, type: 'daily', amount: 50000, date: '2026-10-02');
      await pay.add(branchId: branchId, labourId: id, type: 'advance', mode: 'upi', amount: 100000, date: '2026-10-03');
      await pay.add(branchId: branchId, labourId: id, type: 'deduction', amount: 5000, date: '2026-10-04');

      final l = await pay.ledger(id);
      // 3 full days at 600 + half day at 600 + 2h OT at 75 + 1 day at 800
      expect(l.presentRows, 4);
      expect(l.halfRows, 1);
      expect(l.absentRows, 1);
      expect(l.paidDays, 4.5);
      expect(l.otHours, 2);
      expect(l.basePaise, 3 * 60000 + 30000 + 80000);
      expect(l.otPaise, 2 * 7500);
      expect(l.cashPaid, 150000);
      expect(l.deductions, 5000);
      expect(l.balance, (3 * 60000 + 30000 + 80000 + 15000) - 150000 - 5000);
    });

    test('opening balance folds in earlier periods', () async {
      final id = await helper(rate: 60000);
      final att = AttendanceService(db);
      await att.mark(siteId: siteA, date: '2026-10-01', labourId: id, status: 'P');
      await att.mark(siteId: siteA, date: '2026-10-06', labourId: id, status: 'P');
      await PaymentService(db).add(
          branchId: branchId, labourId: id, type: 'daily', amount: 20000, date: '2026-10-01');
      final l = await PaymentService(db).ledger(id, from: '2026-10-05', to: '2026-10-31');
      expect(l.opening, 40000);
      expect(l.earned, 60000);
      expect(l.balance, 100000);
    });

    test('voided payments are ignored but kept', () async {
      final id = await helper();
      final pay = PaymentService(db);
      final pid = await pay.add(
          branchId: branchId, labourId: id, type: 'advance', amount: 50000, date: '2026-10-02');
      await pay.voidPayment(pid, 'wrong entry');
      expect((await pay.ledger(id)).cashPaid, 0);
      expect((await pay.list(labourId: id)).single.payment.voidedAt, isNotNull);
      await expectLater(pay.voidPayment(pid, ''), throwsA(isA<AppException>()));
    });
  });

  group('billing', () {
    Future<void> work() async {
      final h = await helper(); // Helper at ABC (siteA)
      final m = await LabourService(db).add(
        branchId: branchId,
        name: 'Suresh',
        skill: 'Mechanic',
        joinDate: '2026-10-01',
        payType: 'daily',
        amount: 100000,
        siteIds: [siteA, siteB],
      );
      final att = AttendanceService(db);
      await att.mark(siteId: siteA, date: '2026-10-01', labourId: h, status: 'P', otHours: 1);
      await att.mark(siteId: siteA, date: '2026-10-02', labourId: h, status: 'H');
      await att.mark(siteId: siteA, date: '2026-10-03', labourId: h, status: 'A');
      await att.mark(siteId: siteA, date: '2026-10-01', labourId: m, status: 'H');
      await att.mark(siteId: siteB, date: '2026-10-01', labourId: m, status: 'H');
    }

    test('prices attendance at the contract rate per company', () async {
      await work();
      final bill = BillingService(db);
      final a = await bill.preview(companyId: companyA, from: '2026-10-01', to: '2026-10-31');
      expect(a.lines.length, 2);
      final ramesh = a.lines.firstWhere((l) => l.name == 'Ramesh');
      expect(ramesh.days, 1.5);
      expect(ramesh.amount, (1.5 * 70000 + 10000).round());
      final suresh = a.lines.firstWhere((l) => l.name == 'Suresh');
      expect(suresh.days, 0.5);
      expect(suresh.rate, 150000);
      final b = await bill.preview(companyId: companyB, from: '2026-10-01', to: '2026-10-31');
      expect(b.lines.single.rate, 160000);
      expect(b.subtotal, 80000);
    });

    test('flags lines with no contract rate', () async {
      final id = await LabourService(db).add(
        branchId: branchId,
        name: 'Gopal',
        skill: 'Welder',
        joinDate: '2026-10-01',
        payType: 'daily',
        amount: 90000,
        siteIds: [siteA],
      );
      await AttendanceService(db).mark(siteId: siteA, date: '2026-10-01', labourId: id, status: 'P');
      final d = await BillingService(db).preview(companyId: companyA, from: '2026-10-01', to: '2026-10-31');
      expect(d.missingRates, 1);
      await expectLater(
        BillingService(db).create(draft: d, branchId: branchId, issueDate: '2026-10-31', paymentTermsDays: 30),
        throwsA(isA<AppException>()),
      );
      d.lines.single.rate = 100000;
      await BillingService(db).create(draft: d, branchId: branchId, issueDate: '2026-10-31', paymentTermsDays: 30);
    });

    test('creates numbered invoices with tax, receipts and overlap warning', () async {
      await work();
      final ws = WorkspaceService(db);
      await ws.saveProfile((await ws.profile()).copyWith(taxEnabled: true, taxRate: 18, invoicePrefix: 'AG'));
      final bill = BillingService(db);
      final draft = await bill.preview(companyId: companyA, from: '2026-10-01', to: '2026-10-31');
      final id = await bill.create(draft: draft, branchId: branchId, issueDate: '2026-10-31', paymentTermsDays: 15);
      final d = await bill.detail(id);
      expect(d.invoice.number, 'AG/2026-27/0001');
      expect(d.invoice.dueDate, '2026-11-15');
      expect(d.invoice.taxAmount, (draft.subtotal * 0.18).round());
      expect(d.invoice.total, draft.subtotal + d.invoice.taxAmount);

      final again = await bill.preview(companyId: companyA, from: '2026-10-15', to: '2026-11-15');
      expect(again.overlapping?.id, id);
      final second = await bill.create(
          draft: await bill.preview(companyId: companyB, from: '2026-10-01', to: '2026-10-31'),
          branchId: branchId,
          issueDate: '2026-11-02',
          paymentTermsDays: 30);
      expect((await bill.detail(second)).invoice.number, 'AG/2026-27/0002');

      await bill.addReceipt(invoiceId: id, amount: 50000, date: '2026-11-01');
      expect((await bill.detail(id)).outstanding, d.invoice.total - 50000);
      await expectLater(
        bill.addReceipt(invoiceId: id, amount: d.invoice.total, date: '2026-11-01'),
        throwsA(isA<AppException>()),
      );
      await expectLater(bill.cancel(id), throwsA(isA<AppException>()));
      final rows = await bill.list();
      expect(rows.first.state, isNot(InvoiceState.paid));
    });
  });

  group('workspace', () {
    test('login by email or mobile, wrong secret rejected', () async {
      final ws = WorkspaceService(db);
      expect(await ws.login('o@x.com', 'secret1'), isNotNull);
      expect(await ws.login('9999999999', 'secret1'), isNotNull);
      expect(await ws.login('o@x.com', 'nope'), isNull);
    });

    test('solo plan allows no team; team plan does', () async {
      final ws = WorkspaceService(db); // created as team
      await ws.addStaff(name: 'Sup', mobile: '9876543210', roleId: 'role_supervisor', pin: '1234');
      expect((await ws.login('9876543210', '1234'))?.name, 'Sup');
      await ws.changePlan(Plan.company);
      await ws.addBranch(name: 'Pune');
      expect((await ws.branches()).length, 2);
      await expectLater(ws.changePlan(Plan.solo), throwsA(isA<AppException>()));
    });

    test('custom roles and protected owner', () async {
      final ws = WorkspaceService(db);
      final rid = await ws.saveRole(name: 'Cashier', permissions: ['payments.view']);
      expect((await ws.roles()).any((r) => r.id == rid), isTrue);
      await expectLater(ws.deleteRole('role_owner'), throwsA(isA<AppException>()));
      final owner = (await ws.staff()).first;
      await expectLater(ws.updateStaff(owner.id, isActive: false), throwsA(isA<AppException>()));
    });

    test('profile round-trips', () async {
      final ws = WorkspaceService(db);
      await ws.saveProfile(const BusinessProfile(name: 'A', taxEnabled: true, taxId: '27AAAAA0000A1Z5', upiId: 'a@upi'));
      final p = await ws.profile();
      expect(p.taxEnabled, isTrue);
      expect(p.taxId, '27AAAAA0000A1Z5');
      expect(p.upiId, 'a@upi');
    });
  });

  group('labour', () {
    test('duplicate mobile detected; hard delete only when unused', () async {
      final ls = LabourService(db);
      final id = await ls.add(
          branchId: branchId, name: 'A', mobile: '9000000001', skill: 'Helper', joinDate: '2026-10-01', payType: 'daily', amount: 50000);
      expect(await ls.findDuplicate('9000000001'), isNotNull);
      expect(await ls.findDuplicate('9000000001', exceptId: id), isNull);
      expect(await ls.canHardDelete(id), isTrue);
      await AttendanceService(db).mark(siteId: siteA, date: '2026-10-01', labourId: id, status: 'P');
      expect(await ls.canHardDelete(id), isFalse);
    });
  });

  group('dashboard', () {
    test('summarises the day', () async {
      final h = await helper();
      final h2 = await LabourService(db).add(
          branchId: branchId, name: 'Kiran', skill: 'Helper', joinDate: '2026-10-01', payType: 'daily', amount: 60000, siteIds: [siteA]);
      await helper(); // third, left unmarked
      final att = AttendanceService(db);
      final today = DateTime.now();
      final d = '${today.year}-${today.month.toString().padLeft(2, '0')}-${today.day.toString().padLeft(2, '0')}';
      await att.mark(siteId: siteA, date: d, labourId: h, status: 'P', otHours: 1);
      await att.mark(siteId: siteA, date: d, labourId: h2, status: 'A');
      await PaymentService(db).add(branchId: branchId, labourId: h, type: 'daily', amount: 30000, date: d);
      final data = await DashboardService(db).load(today);
      expect(data.totalLabour, 3);
      expect(data.present, 1);
      expect(data.absent, 1);
      expect(data.notMarked, 1);
      expect(data.billing, 70000 + 10000);
      expect(data.labourCost, 60000 + 7500);
      expect(data.paidOut, 30000);
      expect(data.pendingSites.length, 1);
      expect(data.companies.single.company.name, 'ABC Ltd');
      expect(data.week.length, 7);
    });
  });
}
