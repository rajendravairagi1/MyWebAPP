import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../../domain/calc.dart';
import '../database.dart';
import 'base.dart';

class PaymentRow {
  PaymentRow(this.payment, this.labourName);
  final Payment payment;
  final String labourName;
}

class PaymentService extends Service {
  PaymentService(super.db);

  Future<String> add({
    required String branchId,
    required String labourId,
    required String type,
    String mode = PayMode.cash,
    required int amount,
    required String date,
    String note = '',
    String reference = '',
    String? staffId,
  }) async {
    if (amount <= 0) throw AppException('Enter an amount greater than zero.');
    if (!PayType.all.contains(type)) throw AppException('Invalid payment type.');
    if (date.compareTo(D.today()) > 0) {
      throw AppException('Payment date cannot be in the future.');
    }
    final id = newId();
    await db.into(db.payments).insert(PaymentsCompanion.insert(
          id: id,
          branchId: branchId,
          labourId: labourId,
          type: type,
          mode: Value(type == PayType.deduction ? PayMode.cash : mode),
          amount: amount,
          date: date,
          note: Value(note.trim()),
          reference: Value(reference.trim()),
          createdBy: Value(staffId),
        ));
    await audit(staffId, 'add', 'payment', id, '$type $amount');
    return id;
  }

  /// Payments are never removed: voiding keeps the record and the reason.
  Future<void> voidPayment(String id, String reason, {String? staffId}) async {
    if (reason.trim().isEmpty) throw AppException('Give a reason for voiding.');
    await (db.update(db.payments)..where((t) => t.id.equals(id))).write(
      PaymentsCompanion(
        voidedAt: Value(DateTime.now()),
        voidReason: Value(reason.trim()),
      ),
    );
    await audit(staffId, 'void', 'payment', id, reason.trim());
  }

  Future<List<PaymentRow>> list({
    String? branchId,
    String? labourId,
    String? from,
    String? to,
    String? type,
    String? mode,
    bool includeVoided = true,
  }) async {
    final q = db.select(db.payments).join([
      innerJoin(db.labours, db.labours.id.equalsExp(db.payments.labourId)),
    ]);
    Expression<bool> e = const Constant(true);
    if (branchId != null) e = e & db.payments.branchId.equals(branchId);
    if (labourId != null) e = e & db.payments.labourId.equals(labourId);
    if (from != null) e = e & db.payments.date.isBiggerOrEqualValue(from);
    if (to != null) e = e & db.payments.date.isSmallerOrEqualValue(to);
    if (type != null) e = e & db.payments.type.equals(type);
    if (mode != null) e = e & db.payments.mode.equals(mode);
    if (!includeVoided) e = e & db.payments.voidedAt.isNull();
    q.where(e);
    q.orderBy([
      OrderingTerm.desc(db.payments.date),
      OrderingTerm.desc(db.payments.createdAt),
    ]);
    return (await q.get())
        .map((r) => PaymentRow(r.readTable(db.payments), r.readTable(db.labours).name))
        .toList();
  }

  Future<Ledger> ledger(String labourId, {String? from, String? to, RateBook? book}) async {
    final rates = book ?? await rateBook();
    final att = await (db.select(db.attendance)..where((t) => t.labourId.equals(labourId))).get();
    final pays = await (db.select(db.payments)..where((t) => t.labourId.equals(labourId))).get();
    return buildLedger(attendance: att, payments: pays, rates: rates, from: from, to: to);
  }

  /// Balance for every labourer (all time). Positive = agency owes them.
  Future<Map<String, int>> balances({String? branchId, String? upTo}) async {
    final rates = await rateBook();
    final labours = await (db.select(db.labours)
          ..where((t) => branchId == null ? const Constant(true) : t.branchId.equals(branchId)))
        .get();
    final ids = labours.map((l) => l.id).toSet();
    final att = await db.select(db.attendance).get();
    final pays = await db.select(db.payments).get();
    final attBy = <String, List<AttendanceData>>{};
    for (final a in att) {
      if (ids.contains(a.labourId)) (attBy[a.labourId] ??= []).add(a);
    }
    final payBy = <String, List<Payment>>{};
    for (final p in pays) {
      if (ids.contains(p.labourId)) (payBy[p.labourId] ??= []).add(p);
    }
    final out = <String, int>{};
    for (final l in labours) {
      out[l.id] = buildLedger(
        attendance: attBy[l.id] ?? const [],
        payments: payBy[l.id] ?? const [],
        rates: rates,
        to: upTo,
      ).balance;
    }
    return out;
  }
}
