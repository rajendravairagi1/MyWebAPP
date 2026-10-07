import 'package:shram_khata/core/dates.dart';
import 'package:shram_khata/core/plans.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/services/attendance_service.dart';
import 'package:shram_khata/data/services/billing_service.dart';
import 'package:shram_khata/data/services/company_service.dart';
import 'package:shram_khata/data/services/labour_service.dart';
import 'package:shram_khata/data/services/payment_service.dart';
import 'package:shram_khata/data/services/workspace_service.dart';

class Seeded {
  Seeded(this.ownerId, this.branchId, this.siteA, this.siteB, this.companyA, this.labourIds, this.invoiceId);
  final String ownerId, branchId, siteA, siteB, companyA, invoiceId;
  final List<String> labourIds;
}

/// A realistic small agency: 2 companies, 3 sites, 8 labour, a week of
/// attendance, payments and one invoice.
Future<Seeded> seed(AppDatabase db, {Plan plan = Plan.company}) async {
  final ws = WorkspaceService(db);
  final owner = await ws.createWorkspace(
    plan: plan,
    businessName: 'Shree Ganesh Manpower',
    ownerName: 'Rajendra Vairagi',
    email: 'owner@example.com',
    mobile: '9876543210',
    password: 'secret1',
  );
  await ws.saveProfile((await ws.profile()).copyWith(
    tagline: 'Skilled & unskilled manpower supplier',
    address: '12, Industrial Area, Phase 2',
    city: 'Indore',
    state: 'Madhya Pradesh',
    pincode: '452001',
    website: 'www.shreeganesh.in',
    taxEnabled: true,
    taxId: '23ABCDE1234F1Z5',
    upiId: 'shreeganesh@okbank',
    mobile: '9876543210',
    email: 'owner@example.com',
  ));
  final branchId = (await ws.branches()).first.id;
  final cs = CompanyService(db);
  final ca = await cs.addCompany(branchId: branchId, name: 'Mahindra & Mahindra Ltd.', contactPerson: 'Mr. Verma', mobile: '9811111111', gstin: '23AAACM1234A1Z9', address: 'Pithampur Industrial Area, Dhar');
  final cb = await cs.addCompany(branchId: branchId, name: 'Tata Projects', contactPerson: 'Ms. Iyer', mobile: '9822222222');
  final s1 = await cs.addSite(companyId: ca, name: 'Plant 3');
  final s2 = await cs.addSite(companyId: ca, name: 'Warehouse');
  final s3 = await cs.addSite(companyId: cb, name: 'Metro Depot');
  await cs.saveContract(companyId: ca, title: 'FY 2026-27', startDate: '2026-04-01', rates: [
    (skill: 'Helper', perDay: 70000, otPerHour: 9000),
    (skill: 'Mechanic', perDay: 120000, otPerHour: 15000),
    (skill: 'Welder', perDay: 110000, otPerHour: 14000),
  ]);
  await cs.saveContract(companyId: cb, title: 'Depot manpower', startDate: '2026-06-01', endDate: D.ymd(D.addDays(DateTime.now(), 20)), rates: [
    (skill: 'Helper', perDay: 68000, otPerHour: 8500),
    (skill: 'Electrician', perDay: 130000, otPerHour: 16000),
  ]);
  final ls = LabourService(db);
  final people = [
    ('Ramesh Kumar', 'Helper', 60000, s1),
    ('Suresh Patil', 'Mechanic', 95000, s1),
    ('रमेश यादव', 'Helper', 60000, s1),
    ('Mohan Lal', 'Welder', 90000, s2),
    ('Geeta Bai', 'Helper', 55000, s2),
    ('Kiran Sharma', 'Electrician', 100000, s3),
    ('Dinesh Rao', 'Helper', 60000, s3),
    ('Anil Gupta', 'Helper', 60000, s3),
  ];
  final ids = <String>[];
  for (final p in people) {
    ids.add(await ls.add(
      branchId: branchId,
      name: p.$1,
      skill: p.$2,
      mobile: '98${(70000000 + ids.length * 1111111).toString()}',
      joinDate: '2026-04-01',
      payType: 'daily',
      amount: p.$3,
      siteIds: [p.$4],
    ));
  }
  // Suresh (mechanic) also visits the warehouse.
  await ls.assign(ids[1], s2, from: '2026-04-01');

  final att = AttendanceService(db);
  final now = DateTime.now();
  for (var back = 6; back >= 0; back--) {
    final d = D.ymd(D.addDays(now, -back));
    for (var i = 0; i < ids.length; i++) {
      if (back == 0 && i >= 5) continue; // today: some not marked yet
      final st = (i + back) % 7 == 3 ? 'A' : ((i + back) % 5 == 4 ? 'H' : 'P');
      await att.mark(siteId: people[i].$4, date: d, labourId: ids[i], status: st, otHours: st == 'P' && (i + back) % 4 == 0 ? 2 : 0);
    }
    if (back > 0) {
      for (final s in [s1, s2, s3]) {
        await att.submit(siteId: s, date: d, markRestAbsent: true);
      }
    }
  }
  final pay = PaymentService(db);
  await pay.add(branchId: branchId, labourId: ids[0], type: 'daily', amount: 50000, date: D.ymd(now));
  await pay.add(branchId: branchId, labourId: ids[1], type: 'advance', mode: 'upi', amount: 200000, date: D.ymd(now));
  await pay.add(branchId: branchId, labourId: ids[3], type: 'settlement', mode: 'bank', amount: 150000, date: D.ymd(D.addDays(now, -1)));
  await pay.add(branchId: branchId, labourId: ids[2], type: 'deduction', amount: 10000, date: D.ymd(D.addDays(now, -2)), note: 'Helmet damage');

  final bill = BillingService(db);
  final draft = await bill.preview(companyId: ca, from: D.ymd(D.addDays(now, -6)), to: D.ymd(D.addDays(now, -1)));
  final inv = await bill.create(draft: draft, branchId: branchId, issueDate: D.ymd(D.addDays(now, -1)), paymentTermsDays: 30, notes: 'Manpower supplied for the week.');
  await bill.addReceipt(invoiceId: inv, amount: 100000, date: D.ymd(now), reference: 'NEFT 88231');
  return Seeded(owner.id, branchId, s1, s2, ca, ids, inv);
}
