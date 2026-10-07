import '../data/database.dart';

/// Attendance status codes stored in the database.
class AttStatus {
  AttStatus._();
  static const present = 'P';
  static const absent = 'A';
  static const half = 'H';
  static const all = [present, half, absent];

  static String label(String s) => switch (s) {
        present => 'Full day',
        half => 'Half day',
        absent => 'Absent',
        _ => s,
      };
}

/// Payment entry types.
class PayType {
  PayType._();
  static const daily = 'daily';
  static const advance = 'advance';
  static const settlement = 'settlement';
  static const deduction = 'deduction';
  static const all = [daily, advance, settlement, deduction];

  static String label(String t) => switch (t) {
        daily => 'Daily payment',
        advance => 'Advance',
        settlement => 'Settlement',
        deduction => 'Deduction / fine',
        _ => t,
      };

  /// Money physically handed over (as opposed to a deduction).
  static bool isCashOut(String t) => t != deduction;
}

class PayMode {
  PayMode._();
  static const cash = 'cash';
  static const upi = 'upi';
  static const bank = 'bank';
  static const all = [cash, upi, bank];

  static String label(String m) => switch (m) {
        cash => 'Cash',
        upi => 'UPI',
        bank => 'Bank',
        _ => m,
      };
}

double dayValue(String status) => switch (status) {
      AttStatus.present => 1.0,
      AttStatus.half => 0.5,
      _ => 0.0,
    };

const hoursPerDay = 8;
const defaultMonthDivisor = 26;

/// What one labourer earns per day / per overtime hour, in paise.
class PayRate {
  const PayRate(this.dailyPaise, this.otPerHourPaise);
  final int dailyPaise;
  final int otPerHourPaise;
}

/// What the company is billed per day / per overtime hour, in paise.
class BillRate {
  const BillRate(this.perDay, this.otPerHour);
  final int perDay;
  final int otPerHour;
}

PayRate resolveRate(LabourRate r, {int monthDivisor = defaultMonthDivisor}) {
  final daily =
      r.payType == 'monthly' ? (r.amount / monthDivisor).round() : r.amount;
  final ot = r.otPerHour ?? (daily / hoursPerDay).round();
  return PayRate(daily, ot);
}

int wageFor(PayRate rate, String status, double otHours) {
  final base = (rate.dailyPaise * dayValue(status)).round();
  final ot = status == AttStatus.absent ? 0 : (rate.otPerHourPaise * otHours).round();
  return base + ot;
}

/// Looks up pay and billing rates as they were on a given date, so changing a
/// rate today never rewrites earlier days.
class RateBook {
  RateBook({
    required Iterable<LabourRate> labourRates,
    required Iterable<Labour> labours,
    required Iterable<Site> sites,
    required Iterable<Contract> contracts,
    required Iterable<ContractRate> contractRates,
    this.monthDivisor = defaultMonthDivisor,
  }) {
    for (final r in labourRates) {
      (_pay[r.labourId] ??= []).add(r);
    }
    for (final list in _pay.values) {
      list.sort((a, b) => a.effectiveFrom.compareTo(b.effectiveFrom));
    }
    for (final l in labours) {
      _skill[l.id] = l.skill.trim().toLowerCase();
    }
    for (final s in sites) {
      _siteCompany[s.id] = s.companyId;
    }
    for (final c in contracts) {
      if (!c.isActive) continue;
      (_contracts[c.companyId] ??= []).add(c);
    }
    for (final list in _contracts.values) {
      list.sort((a, b) => b.startDate.compareTo(a.startDate)); // newest first
    }
    for (final r in contractRates) {
      (_rates[r.contractId] ??= []).add(r);
    }
  }

  final int monthDivisor;
  final _pay = <String, List<LabourRate>>{};
  final _skill = <String, String>{};
  final _siteCompany = <String, String>{};
  final _contracts = <String, List<Contract>>{};
  final _rates = <String, List<ContractRate>>{};

  PayRate? payRate(String labourId, String date) {
    final list = _pay[labourId];
    if (list == null || list.isEmpty) return null;
    LabourRate pick = list.first;
    for (final r in list) {
      if (r.effectiveFrom.compareTo(date) <= 0) pick = r;
    }
    return resolveRate(pick, monthDivisor: monthDivisor);
  }

  /// The contract rate for this labourer's skill at this site on [date].
  BillRate? billRate(String labourId, String siteId, String date) {
    final companyId = _siteCompany[siteId];
    final skill = _skill[labourId];
    if (companyId == null || skill == null) return null;
    for (final c in _contracts[companyId] ?? const <Contract>[]) {
      if (c.startDate.compareTo(date) > 0) continue;
      if (c.endDate != null && c.endDate!.compareTo(date) < 0) continue;
      for (final r in _rates[c.id] ?? const <ContractRate>[]) {
        if (r.skill.trim().toLowerCase() == skill) {
          return BillRate(r.perDay, r.otPerHour);
        }
      }
    }
    return null;
  }

  String? companyOfSite(String siteId) => _siteCompany[siteId];

  /// Wage owed to the labourer for one attendance row.
  int wage(AttendanceData a) {
    final rate = payRate(a.labourId, a.date);
    if (rate == null) return 0;
    return wageFor(rate, a.status, a.otHours);
  }

  /// Amount billable to the company for one attendance row.
  int bill(AttendanceData a) {
    final rate = billRate(a.labourId, a.siteId, a.date);
    if (rate == null) return 0;
    final base = (rate.perDay * dayValue(a.status)).round();
    final ot = a.status == AttStatus.absent ? 0 : (rate.otPerHour * a.otHours).round();
    return base + ot;
  }

  bool hasBillRate(AttendanceData a) =>
      a.status == AttStatus.absent || billRate(a.labourId, a.siteId, a.date) != null;
}

/// A labourer's account for a period: what was earned, paid and what is left.
class Ledger {
  Ledger({
    required this.presentRows,
    required this.halfRows,
    required this.absentRows,
    required this.paidDays,
    required this.otHours,
    required this.basePaise,
    required this.otPaise,
    required this.paidDaily,
    required this.paidAdvance,
    required this.paidSettlement,
    required this.deductions,
    required this.opening,
  });

  final int presentRows;
  final int halfRows;
  final int absentRows;

  /// Total paid days (P = 1, H = 0.5).
  final double paidDays;
  final double otHours;
  final int basePaise;
  final int otPaise;
  final int paidDaily;
  final int paidAdvance;
  final int paidSettlement;
  final int deductions;

  /// Balance carried in from before the period started.
  final int opening;

  int get earned => basePaise + otPaise;
  int get cashPaid => paidDaily + paidAdvance + paidSettlement;

  /// Positive: agency still owes the labourer. Negative: labourer has taken
  /// more than earned (recoverable advance).
  int get balance => opening + earned - cashPaid - deductions;
}

/// Builds the ledger from raw rows. [from]/[to] are inclusive `yyyy-MM-dd`
/// bounds; anything before [from] is folded into the opening balance.
Ledger buildLedger({
  required Iterable<AttendanceData> attendance,
  required Iterable<Payment> payments,
  required RateBook rates,
  String? from,
  String? to,
}) {
  var present = 0, half = 0, absent = 0;
  var days = 0.0, ot = 0.0;
  var base = 0, otPay = 0;
  var opening = 0;

  for (final a in attendance) {
    if (to != null && a.date.compareTo(to) > 0) continue;
    final rate = rates.payRate(a.labourId, a.date);
    final w = rate == null ? 0 : wageFor(rate, a.status, a.otHours);
    if (from != null && a.date.compareTo(from) < 0) {
      opening += w;
      continue;
    }
    switch (a.status) {
      case AttStatus.present:
        present++;
      case AttStatus.half:
        half++;
      default:
        absent++;
    }
    days += dayValue(a.status);
    if (a.status != AttStatus.absent) ot += a.otHours;
    final otPart = rate == null || a.status == AttStatus.absent
        ? 0
        : (rate.otPerHourPaise * a.otHours).round();
    base += w - otPart;
    otPay += otPart;
  }

  var daily = 0, adv = 0, settle = 0, ded = 0;
  for (final p in payments) {
    if (p.voidedAt != null) continue;
    if (to != null && p.date.compareTo(to) > 0) continue;
    if (from != null && p.date.compareTo(from) < 0) {
      opening -= p.amount;
      continue;
    }
    switch (p.type) {
      case PayType.daily:
        daily += p.amount;
      case PayType.advance:
        adv += p.amount;
      case PayType.settlement:
        settle += p.amount;
      default:
        ded += p.amount;
    }
  }

  return Ledger(
    presentRows: present,
    halfRows: half,
    absentRows: absent,
    paidDays: days,
    otHours: ot,
    basePaise: base,
    otPaise: otPay,
    paidDaily: daily,
    paidAdvance: adv,
    paidSettlement: settle,
    deductions: ded,
    opening: opening,
  );
}

/// Whether [status] can be recorded for a labourer on a day where they
/// already have [otherDayValue] worth of attendance at other sites.
/// One person cannot work more than one full day in total.
bool fitsInDay(double otherDayValue, String status) =>
    otherDayValue + dayValue(status) <= 1.0 + 1e-9;
