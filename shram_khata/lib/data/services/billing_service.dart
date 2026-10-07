import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../../domain/calc.dart';
import '../database.dart';
import 'base.dart';

/// One editable line of an invoice before it is saved.
class DraftLine {
  DraftLine({
    required this.labourId,
    required this.name,
    required this.skill,
    required this.days,
    required this.otHours,
    required this.rate,
    required this.otRate,
  });

  final String labourId;
  final String name;
  final String skill;
  final double days;
  final double otHours;
  int rate;
  int otRate;

  int get amount => (days * rate + otHours * otRate).round();
  bool get missingRate => rate <= 0;
}

class InvoiceDraft {
  InvoiceDraft({
    required this.companyId,
    required this.siteId,
    required this.from,
    required this.to,
    required this.lines,
    required this.overlapping,
  });

  final String companyId;
  final String? siteId;
  final String from;
  final String to;
  final List<DraftLine> lines;

  /// An existing invoice already covering part of this period, if any.
  final Invoice? overlapping;

  int get subtotal => lines.fold(0, (a, l) => a + l.amount);
  int get missingRates => lines.where((l) => l.missingRate).length;
  double get totalDays => lines.fold(0.0, (a, l) => a + l.days);
}

class InvoiceState {
  InvoiceState._();
  static const cancelled = 'cancelled';
  static const paid = 'paid';
  static const partial = 'partial';
  static const overdue = 'overdue';
  static const unpaid = 'unpaid';

  static String label(String s) => switch (s) {
        cancelled => 'Cancelled',
        paid => 'Paid',
        partial => 'Part paid',
        overdue => 'Overdue',
        _ => 'Unpaid',
      };
}

class InvoiceRow {
  InvoiceRow(this.invoice, this.companyName, this.paid);
  final Invoice invoice;
  final String companyName;
  final int paid;

  int get outstanding =>
      invoice.status == 'cancelled' ? 0 : invoice.total - paid;

  String get state {
    if (invoice.status == 'cancelled') return InvoiceState.cancelled;
    if (outstanding <= 0) return InvoiceState.paid;
    if (invoice.dueDate.compareTo(D.today()) < 0) return InvoiceState.overdue;
    if (paid > 0) return InvoiceState.partial;
    return InvoiceState.unpaid;
  }
}

class InvoiceDetail {
  InvoiceDetail({
    required this.invoice,
    required this.company,
    required this.site,
    required this.lines,
    required this.payments,
  });
  final Invoice invoice;
  final Company company;
  final Site? site;
  final List<InvoiceLine> lines;
  final List<InvoicePayment> payments;

  int get paid => payments.fold(0, (a, p) => a + p.amount);
  int get outstanding => invoice.status == 'cancelled' ? 0 : invoice.total - paid;
}

class BillingService extends Service {
  BillingService(super.db);

  /// Collects attendance for a company (optionally one site) over a period and
  /// prices it at the contract rates.
  Future<InvoiceDraft> preview({
    required String companyId,
    String? siteId,
    required String from,
    required String to,
  }) async {
    if (to.compareTo(from) < 0) throw AppException('End date is before the start date.');
    final sitesQ = db.select(db.sites)..where((t) => t.companyId.equals(companyId));
    final siteIds = (await sitesQ.get()).map((s) => s.id).toSet();
    final scope = siteId == null ? siteIds : {siteId};
    final att = scope.isEmpty
        ? <AttendanceData>[]
        : await (db.select(db.attendance)
              ..where((t) =>
                  t.siteId.isIn(scope) &
                  t.date.isBiggerOrEqualValue(from) &
                  t.date.isSmallerOrEqualValue(to) &
                  t.status.equals(AttStatus.absent).not()))
            .get();
    final book = await rateBook();
    final labourIds = att.map((a) => a.labourId).toSet();
    final labours = labourIds.isEmpty
        ? <Labour>[]
        : await (db.select(db.labours)..where((t) => t.id.isIn(labourIds))).get();
    final byId = {for (final l in labours) l.id: l};

    final groups = <String, DraftLine>{};
    for (final a in att) {
      final rate = book.billRate(a.labourId, a.siteId, a.date);
      final key = '${a.labourId}|${rate?.perDay ?? 0}|${rate?.otPerHour ?? 0}';
      final l = byId[a.labourId]!;
      final prev = groups[key];
      final days = (prev?.days ?? 0) + dayValue(a.status);
      final ot = (prev?.otHours ?? 0) + a.otHours;
      groups[key] = DraftLine(
        labourId: a.labourId,
        name: l.name,
        skill: l.skill,
        days: days,
        otHours: ot,
        rate: rate?.perDay ?? 0,
        otRate: rate?.otPerHour ?? 0,
      );
    }
    final lines = groups.values.toList()
      ..sort((a, b) => a.name.toLowerCase().compareTo(b.name.toLowerCase()));

    // Existing invoice for the same company covering part of this period.
    final existing = await (db.select(db.invoices)
          ..where((t) =>
              t.companyId.equals(companyId) &
              t.status.equals('cancelled').not() &
              t.periodFrom.isSmallerOrEqualValue(to) &
              t.periodTo.isBiggerOrEqualValue(from)))
        .get();
    Invoice? overlap;
    for (final i in existing) {
      if (siteId == null || i.siteId == null || i.siteId == siteId) {
        overlap = i;
        break;
      }
    }
    return InvoiceDraft(
      companyId: companyId,
      siteId: siteId,
      from: from,
      to: to,
      lines: lines,
      overlapping: overlap,
    );
  }

  /// Next number like `INV/2026-27/0007`, counted per financial year.
  Future<String> _nextNumber(String prefix, DateTime issue) async {
    final fy = D.financialYear(issue);
    final key = 'invseq_$fy';
    final row = await (db.select(db.keyValues)..where((t) => t.key.equals(key))).getSingleOrNull();
    final next = (int.tryParse(row?.value ?? '') ?? 0) + 1;
    await db.into(db.keyValues).insertOnConflictUpdate(
        KeyValuesCompanion.insert(key: key, value: next.toString()));
    return '${prefix.trim().isEmpty ? 'INV' : prefix.trim()}/$fy/${next.toString().padLeft(4, '0')}';
  }

  Future<String> create({
    required InvoiceDraft draft,
    required String branchId,
    required String issueDate,
    required int paymentTermsDays,
    String notes = '',
    String? staffId,
  }) async {
    if (draft.lines.isEmpty) throw AppException('There is no attendance to bill in this period.');
    if (draft.lines.any((l) => l.missingRate)) {
      throw AppException('Set a billing rate for every line before saving.');
    }
    final profile = await loadProfile();
    final issue = D.parse(issueDate);
    final due = D.ymd(D.addDays(issue, paymentTermsDays));
    final taxRate = profile.taxEnabled ? profile.taxRate : 0.0;
    final subtotal = draft.subtotal;
    final tax = (subtotal * taxRate / 100).round();
    final id = newId();
    await db.transaction(() async {
      final number = await _nextNumber(profile.invoicePrefix, issue);
      await db.into(db.invoices).insert(InvoicesCompanion.insert(
            id: id,
            branchId: branchId,
            companyId: draft.companyId,
            siteId: Value(draft.siteId),
            number: number,
            issueDate: issueDate,
            dueDate: due,
            periodFrom: draft.from,
            periodTo: draft.to,
            subtotal: subtotal,
            taxRate: Value(taxRate),
            taxLabel: Value(profile.taxLabel),
            taxAmount: Value(tax),
            total: subtotal + tax,
            notes: Value(notes.trim()),
          ));
      for (final l in draft.lines) {
        await db.into(db.invoiceLines).insert(InvoiceLinesCompanion.insert(
              id: newId(),
              invoiceId: id,
              labourId: Value(l.labourId),
              description: l.name,
              skill: Value(l.skill),
              days: l.days,
              rate: l.rate,
              otHours: Value(l.otHours),
              otRate: Value(l.otRate),
              amount: l.amount,
            ));
      }
      await audit(staffId, 'create', 'invoice', id, '$subtotal + $tax');
    });
    return id;
  }

  Future<void> cancel(String invoiceId, {String? staffId}) async {
    final paid = await (db.select(db.invoicePayments)..where((t) => t.invoiceId.equals(invoiceId))).get();
    if (paid.isNotEmpty) {
      throw AppException('Receipts are recorded against this invoice. Remove them before cancelling.');
    }
    await (db.update(db.invoices)..where((t) => t.id.equals(invoiceId)))
        .write(const InvoicesCompanion(status: Value('cancelled')));
    await audit(staffId, 'cancel', 'invoice', invoiceId);
  }

  Future<void> addReceipt({
    required String invoiceId,
    required int amount,
    required String date,
    String mode = PayMode.bank,
    String reference = '',
    String note = '',
    String? staffId,
  }) async {
    if (amount <= 0) throw AppException('Enter an amount greater than zero.');
    final d = await detail(invoiceId);
    if (d.invoice.status == 'cancelled') throw AppException('This invoice is cancelled.');
    if (amount > d.outstanding) {
      throw AppException('Amount is more than the outstanding balance.');
    }
    await db.into(db.invoicePayments).insert(InvoicePaymentsCompanion.insert(
          id: newId(),
          invoiceId: invoiceId,
          amount: amount,
          date: date,
          mode: Value(mode),
          reference: Value(reference.trim()),
          note: Value(note.trim()),
        ));
    await audit(staffId, 'receipt', 'invoice', invoiceId, '$amount');
  }

  Future<void> removeReceipt(String receiptId, {String? staffId}) async {
    await (db.delete(db.invoicePayments)..where((t) => t.id.equals(receiptId))).go();
    await audit(staffId, 'remove_receipt', 'invoice', receiptId);
  }

  Future<List<InvoiceRow>> list({String? branchId, String? companyId}) async {
    final q = db.select(db.invoices).join([
      innerJoin(db.companies, db.companies.id.equalsExp(db.invoices.companyId)),
    ]);
    Expression<bool> e = const Constant(true);
    if (branchId != null) e = e & db.invoices.branchId.equals(branchId);
    if (companyId != null) e = e & db.invoices.companyId.equals(companyId);
    q.where(e);
    q.orderBy([OrderingTerm.desc(db.invoices.issueDate), OrderingTerm.desc(db.invoices.createdAt)]);
    final rows = await q.get();
    final pays = await db.select(db.invoicePayments).get();
    final paidBy = <String, int>{};
    for (final p in pays) {
      paidBy[p.invoiceId] = (paidBy[p.invoiceId] ?? 0) + p.amount;
    }
    return rows.map((r) {
      final inv = r.readTable(db.invoices);
      return InvoiceRow(inv, r.readTable(db.companies).name, paidBy[inv.id] ?? 0);
    }).toList();
  }

  Future<InvoiceDetail> detail(String id) async {
    final inv = await (db.select(db.invoices)..where((t) => t.id.equals(id))).getSingle();
    final company = await (db.select(db.companies)..where((t) => t.id.equals(inv.companyId))).getSingle();
    final site = inv.siteId == null
        ? null
        : await (db.select(db.sites)..where((t) => t.id.equals(inv.siteId!))).getSingleOrNull();
    final lines = await (db.select(db.invoiceLines)
          ..where((t) => t.invoiceId.equals(id))
          ..orderBy([(t) => OrderingTerm.asc(t.description)]))
        .get();
    final pays = await (db.select(db.invoicePayments)
          ..where((t) => t.invoiceId.equals(id))
          ..orderBy([(t) => OrderingTerm.asc(t.date)]))
        .get();
    return InvoiceDetail(invoice: inv, company: company, site: site, lines: lines, payments: pays);
  }

  /// Total still to collect from companies.
  Future<int> totalOutstanding({String? branchId}) async {
    final rows = await list(branchId: branchId);
    return rows.fold<int>(0, (a, r) => a + r.outstanding);
  }
}
