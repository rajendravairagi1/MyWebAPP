import 'dart:io';

import 'package:flutter_test/flutter_test.dart';
import 'package:shram_khata/core/plans.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/services/attendance_service.dart';
import 'package:shram_khata/data/services/company_service.dart';
import 'package:shram_khata/data/services/labour_service.dart';
import 'package:shram_khata/data/services/payment_service.dart';
import 'package:shram_khata/data/services/report_service.dart';
import 'package:shram_khata/data/services/workspace_service.dart';
import 'package:shram_khata/data/services/billing_service.dart';
import 'package:shram_khata/pdf/invoice_pdf.dart';
import 'package:shram_khata/pdf/register_pdf.dart';
import 'package:shram_khata/pdf/statement_pdf.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();
  invoiceMain();

  test('labour statement renders to a PDF', () async {
    final db = AppDatabase.inMemory();
    final ws = WorkspaceService(db);
    await ws.createWorkspace(
        plan: Plan.team, businessName: 'Shree Ganesh Manpower', ownerName: 'Rajendra', email: 'o@x.com', mobile: '9999999999', password: 'secret1');
    await ws.saveProfile((await ws.profile()).copyWith(
      tagline: 'Skilled & unskilled manpower supplier',
      address: '12, Industrial Area, Phase 2',
      city: 'Indore',
      state: 'Madhya Pradesh',
      pincode: '452001',
      website: 'www.shreeganesh.in',
      taxEnabled: true,
      taxId: '23ABCDE1234F1Z5',
      registrationId: 'UDYAM-MP-23-0012345',
      registrationLabel: 'Udyam',
      upiId: 'shreeganesh@okbank',
      footerNote: 'Thank you for your business.',
    ));
    final branch = (await ws.branches()).first.id;
    final cs = CompanyService(db);
    final c = await cs.addCompany(branchId: branch, name: 'Mahindra Plant');
    final s = await cs.addSite(companyId: c, name: 'Unit 3');
    final id = await LabourService(db).add(
        branchId: branch, name: 'रमेश कुमार (Ramesh)', skill: 'Mechanic', fatherName: 'Suresh', mobile: '9876543210', joinDate: '2026-09-01', payType: 'daily', amount: 70000, siteIds: [s]);
    final att = AttendanceService(db);
    for (var d = 1; d <= 6; d++) {
      final date = '2026-10-${d.toString().padLeft(2, '0')}';
      await att.mark(siteId: s, date: date, labourId: id, status: d == 3 ? 'A' : (d == 5 ? 'H' : 'P'), otHours: d == 2 ? 2 : 0);
    }
    final pay = PaymentService(db);
    await pay.add(branchId: branch, labourId: id, type: 'daily', amount: 50000, date: '2026-10-02', note: 'Lunch money');
    await pay.add(branchId: branch, labourId: id, type: 'advance', mode: 'upi', amount: 200000, date: '2026-10-04', reference: 'UTR123456');
    await pay.add(branchId: branch, labourId: id, type: 'deduction', amount: 10000, date: '2026-10-05', note: 'Helmet damage');
    final data = await ReportService(db).statement(id, '2026-10-01', '2026-10-31');
    final bytes = await StatementPdf.build(data);
    expect(String.fromCharCodes(bytes.take(4)), '%PDF');
    final out = File('$_scratch/statement.pdf');
    await out.parent.create(recursive: true);
    await out.writeAsBytes(bytes);
    await db.close();
  });
}

final _scratch = Directory.systemTemp.createTempSync('shram_pdf').path;

void invoiceMain() {
  test('invoice, registers render to PDFs', () async {
    final db = AppDatabase.inMemory();
    final ws = WorkspaceService(db);
    await ws.createWorkspace(
        plan: Plan.team, businessName: 'Shree Ganesh Manpower', ownerName: 'Rajendra', email: 'o@x.com', mobile: '9999999999', password: 'secret1');
    await ws.saveProfile((await ws.profile()).copyWith(
      tagline: 'Skilled & unskilled manpower supplier',
      address: '12, Industrial Area, Phase 2',
      city: 'Indore',
      state: 'Madhya Pradesh',
      pincode: '452001',
      website: 'www.shreeganesh.in',
      taxEnabled: true,
      taxId: '23ABCDE1234F1Z5',
      registrationId: 'UDYAM-MP-23-0012345',
      registrationLabel: 'Udyam',
      upiId: 'shreeganesh@okbank',
      bankName: 'State Bank of India',
      accountName: 'Shree Ganesh Manpower',
      accountNumber: '39012345678',
      ifsc: 'SBIN0001234',
      invoiceTerms: '1. Payment within 30 days.\n2. Interest @18% p.a. on overdue amount.\n3. Subject to Indore jurisdiction.',
    ));
    final branch = (await ws.branches()).first.id;
    final cs = CompanyService(db);
    final c = await cs.addCompany(
        branchId: branch, name: 'Mahindra & Mahindra Ltd.', address: 'Pithampur Industrial Area, Dhar, MP', gstin: '23AAACM1234A1Z9', contactPerson: 'Mr. Verma', mobile: '9811111111');
    final s = await cs.addSite(companyId: c, name: 'Unit 3');
    await cs.saveContract(companyId: c, title: 'FY26-27', startDate: '2026-04-01', rates: [
      (skill: 'Helper', perDay: 70000, otPerHour: 9000),
      (skill: 'Mechanic', perDay: 120000, otPerHour: 15000),
    ]);
    final ls = LabourService(db);
    final att = AttendanceService(db);
    final names = ['Ramesh Kumar', 'Suresh Patil', 'रमेश यादव', 'Mohan Lal', 'Geeta Bai', 'Kiran Sharma', 'Dinesh Rao', 'Anil Gupta', 'Vijay Singh', 'Ajay Verma', 'Sunil Joshi', 'Pawan Kumar'];
    for (var i = 0; i < names.length; i++) {
      final id = await ls.add(
          branchId: branch, name: names[i], skill: i % 4 == 0 ? 'Mechanic' : 'Helper', joinDate: '2026-04-01', payType: 'daily', amount: 60000, siteIds: [s]);
      for (var d = 1; d <= 30; d++) {
        final date = '2026-09-${d.toString().padLeft(2, '0')}';
        final wd = DateTime(2026, 9, d).weekday;
        if (wd == DateTime.sunday) continue;
        final st = (d + i) % 11 == 0 ? 'A' : ((d + i) % 7 == 0 ? 'H' : 'P');
        await att.mark(siteId: s, date: date, labourId: id, status: st, otHours: (d + i) % 5 == 0 && st == 'P' ? 2 : 0);
      }
    }
    final bill = BillingService(db);
    final draft = await bill.preview(companyId: c, siteId: s, from: '2026-09-01', to: '2026-09-30');
    final invId = await bill.create(draft: draft, branchId: branch, issueDate: '2026-09-30', paymentTermsDays: 30, notes: 'Manpower supplied for September 2026.');
    await bill.addReceipt(invoiceId: invId, amount: 5000000, date: '2026-10-05', mode: 'bank', reference: 'NEFT123');
    final reports = ReportService(db);
    final bundle = await reports.invoice(invId);
    await File('$_scratch/invoice.pdf').writeAsBytes(await InvoicePdf.build(bundle));
    await File('$_scratch/register.pdf').writeAsBytes(await RegisterPdf.attendance(
        profile: bundle.profile, heading: 'Mahindra & Mahindra Ltd. • Unit 3', matrix: bundle.matrix));
    final one = (await ls.list()).first;
    final pay = PaymentService(db);
    for (var i = 0; i < 40; i++) {
      await pay.add(branchId: branch, labourId: one.id, type: 'daily', amount: 30000 + i * 100, date: '2026-09-${(i % 28 + 1).toString().padLeft(2, '0')}');
    }
    await File('$_scratch/payments.pdf').writeAsBytes(await RegisterPdf.payments(
        profile: bundle.profile, rows: await reports.paymentRegister(from: '2026-09-01', to: '2026-09-30'), from: '2026-09-01', to: '2026-09-30'));
    await File('$_scratch/balances.pdf').writeAsBytes(await RegisterPdf.balances(
        profile: bundle.profile, rows: await reports.balances(from: '2026-09-01', to: '2026-09-30'), from: '2026-09-01', to: '2026-09-30'));
    await db.close();
  });
}
