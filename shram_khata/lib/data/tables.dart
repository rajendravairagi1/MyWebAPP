import 'package:drift/drift.dart';

/// Every table uses a client-generated uuid so rows can be created offline
/// on any device and merged later without id clashes.
mixin PkId on Table {
  TextColumn get id => text()();
  @override
  Set<Column> get primaryKey => {id};
}

/// Money is always stored as integer paise. Dates without a time are stored
/// as `yyyy-MM-dd` text so they sort and compare correctly.

@DataClassName('Branch')
class Branches extends Table with PkId {
  TextColumn get name => text()();
  TextColumn get address => text().withDefault(const Constant(''))();
  TextColumn get city => text().withDefault(const Constant(''))();
  TextColumn get mobile => text().withDefault(const Constant(''))();
  BoolColumn get isActive => boolean().withDefault(const Constant(true))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class Roles extends Table with PkId {
  TextColumn get name => text()();

  /// Comma separated permission keys (see core/permissions.dart).
  TextColumn get permissions => text().withDefault(const Constant(''))();
  BoolColumn get isSystem => boolean().withDefault(const Constant(false))();
}

class StaffMembers extends Table with PkId {
  TextColumn get name => text()();
  TextColumn get mobile => text().withDefault(const Constant(''))();
  TextColumn get email => text().withDefault(const Constant(''))();
  TextColumn get roleId => text().references(Roles, #id)();

  /// Password (owner) or PIN (staff), salted + hashed.
  TextColumn get secretHash => text()();
  TextColumn get salt => text()();
  BoolColumn get isActive => boolean().withDefault(const Constant(true))();

  /// Comma separated branch ids this person may work in. Empty = all.
  TextColumn get branchIds => text().withDefault(const Constant(''))();

  /// Comma separated site ids a supervisor is limited to. Empty = all.
  TextColumn get siteIds => text().withDefault(const Constant(''))();
  BoolColumn get emailVerified => boolean().withDefault(const Constant(false))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class Companies extends Table with PkId {
  TextColumn get branchId => text().references(Branches, #id)();
  TextColumn get name => text()();
  TextColumn get contactPerson => text().withDefault(const Constant(''))();
  TextColumn get mobile => text().withDefault(const Constant(''))();
  TextColumn get email => text().withDefault(const Constant(''))();
  TextColumn get address => text().withDefault(const Constant(''))();
  TextColumn get gstin => text().withDefault(const Constant(''))();
  BoolColumn get isActive => boolean().withDefault(const Constant(true))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class Sites extends Table with PkId {
  TextColumn get companyId => text().references(Companies, #id)();
  TextColumn get name => text()();
  TextColumn get address => text().withDefault(const Constant(''))();
  BoolColumn get isActive => boolean().withDefault(const Constant(true))();
}

class Contracts extends Table with PkId {
  TextColumn get companyId => text().references(Companies, #id)();
  TextColumn get title => text()();
  TextColumn get startDate => text()();
  TextColumn get endDate => text().nullable()();
  IntColumn get paymentTermsDays => integer().withDefault(const Constant(30))();
  TextColumn get notes => text().withDefault(const Constant(''))();
  TextColumn get docPath => text().nullable()();
  BoolColumn get isActive => boolean().withDefault(const Constant(true))();
}

/// What the agency bills the company per skill under a contract.
class ContractRates extends Table with PkId {
  TextColumn get contractId => text().references(Contracts, #id)();
  TextColumn get skill => text()();
  IntColumn get perDay => integer()();
  IntColumn get otPerHour => integer().withDefault(const Constant(0))();
}

class Labours extends Table with PkId {
  TextColumn get branchId => text().references(Branches, #id)();
  TextColumn get name => text()();
  TextColumn get fatherName => text().withDefault(const Constant(''))();
  TextColumn get mobile => text().withDefault(const Constant(''))();
  TextColumn get address => text().withDefault(const Constant(''))();
  TextColumn get skill => text().withDefault(const Constant('Helper'))();
  TextColumn get photoPath => text().nullable()();
  TextColumn get joinDate => text()();
  TextColumn get leaveDate => text().nullable()();

  /// active | inactive | left
  TextColumn get status => text().withDefault(const Constant('active'))();
  TextColumn get note => text().withDefault(const Constant(''))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

/// What the agency pays a labourer. A new row from a date onwards changes the
/// rate without rewriting earlier days.
class LabourRates extends Table with PkId {
  TextColumn get labourId => text().references(Labours, #id)();
  TextColumn get effectiveFrom => text()();

  /// daily | monthly
  TextColumn get payType => text().withDefault(const Constant('daily'))();
  IntColumn get amount => integer()();

  /// Overtime paise per hour. Null = derived from the daily rate.
  IntColumn get otPerHour => integer().nullable()();
}

class LabourDocuments extends Table with PkId {
  TextColumn get labourId => text().references(Labours, #id)();
  TextColumn get docType => text()();
  TextColumn get docNumber => text().withDefault(const Constant(''))();
  TextColumn get filePath => text().nullable()();
  TextColumn get expiryDate => text().nullable()();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

/// Which sites a labourer works on. One labourer may have several active
/// assignments (e.g. a mechanic visiting different companies).
class Assignments extends Table with PkId {
  TextColumn get labourId => text().references(Labours, #id)();
  TextColumn get siteId => text().references(Sites, #id)();
  TextColumn get fromDate => text()();
  TextColumn get toDate => text().nullable()();
}

class Attendance extends Table with PkId {
  TextColumn get labourId => text().references(Labours, #id)();
  TextColumn get siteId => text().references(Sites, #id)();
  TextColumn get date => text()();

  /// P | A | H
  TextColumn get status => text()();
  RealColumn get otHours => real().withDefault(const Constant(0))();
  TextColumn get note => text().withDefault(const Constant(''))();
  TextColumn get markedBy => text().nullable()();
  DateTimeColumn get markedAt => dateTime().withDefault(currentDateAndTime)();
  BoolColumn get isLate => boolean().withDefault(const Constant(false))();

  @override
  List<Set<Column>> get uniqueKeys => [
        {labourId, siteId, date},
      ];
}

/// A submitted (locked) attendance sheet for one site and day.
class AttendanceSheets extends Table with PkId {
  TextColumn get siteId => text().references(Sites, #id)();
  TextColumn get date => text()();
  TextColumn get submittedBy => text().nullable()();
  DateTimeColumn get submittedAt => dateTime().withDefault(currentDateAndTime)();

  @override
  List<Set<Column>> get uniqueKeys => [
        {siteId, date},
      ];
}

class Payments extends Table with PkId {
  TextColumn get branchId => text().references(Branches, #id)();
  TextColumn get labourId => text().references(Labours, #id)();

  /// daily | advance | settlement | deduction
  TextColumn get type => text()();

  /// cash | upi | bank (ignored for deductions)
  TextColumn get mode => text().withDefault(const Constant('cash'))();
  IntColumn get amount => integer()();
  TextColumn get date => text()();
  TextColumn get note => text().withDefault(const Constant(''))();
  TextColumn get reference => text().withDefault(const Constant(''))();
  TextColumn get createdBy => text().nullable()();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();

  /// Payments are never deleted, only voided, so history stays auditable.
  DateTimeColumn get voidedAt => dateTime().nullable()();
  TextColumn get voidReason => text().nullable()();
}

class Invoices extends Table with PkId {
  TextColumn get branchId => text().references(Branches, #id)();
  TextColumn get companyId => text().references(Companies, #id)();
  TextColumn get siteId => text().nullable().references(Sites, #id)();
  TextColumn get number => text()();
  TextColumn get issueDate => text()();
  TextColumn get dueDate => text()();
  TextColumn get periodFrom => text()();
  TextColumn get periodTo => text()();
  IntColumn get subtotal => integer()();
  RealColumn get taxRate => real().withDefault(const Constant(0))();
  TextColumn get taxLabel => text().withDefault(const Constant('GST'))();
  IntColumn get taxAmount => integer().withDefault(const Constant(0))();
  IntColumn get total => integer()();

  /// issued | cancelled
  TextColumn get status => text().withDefault(const Constant('issued'))();
  TextColumn get notes => text().withDefault(const Constant(''))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class InvoiceLines extends Table with PkId {
  TextColumn get invoiceId => text().references(Invoices, #id)();
  TextColumn get labourId => text().nullable().references(Labours, #id)();
  TextColumn get description => text()();
  TextColumn get skill => text().withDefault(const Constant(''))();
  RealColumn get days => real()();
  IntColumn get rate => integer()();
  RealColumn get otHours => real().withDefault(const Constant(0))();
  IntColumn get otRate => integer().withDefault(const Constant(0))();
  IntColumn get amount => integer()();
}

class InvoicePayments extends Table with PkId {
  TextColumn get invoiceId => text().references(Invoices, #id)();
  IntColumn get amount => integer()();
  TextColumn get date => text()();
  TextColumn get mode => text().withDefault(const Constant('bank'))();
  TextColumn get reference => text().withDefault(const Constant(''))();
  TextColumn get note => text().withDefault(const Constant(''))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class AuditLogs extends Table with PkId {
  DateTimeColumn get at => dateTime().withDefault(currentDateAndTime)();
  TextColumn get staffId => text().nullable()();
  TextColumn get action => text()();
  TextColumn get entity => text()();
  TextColumn get entityId => text().withDefault(const Constant(''))();
  TextColumn get detail => text().withDefault(const Constant(''))();
}

class KeyValues extends Table {
  TextColumn get key => text()();
  TextColumn get value => text()();
  @override
  Set<Column> get primaryKey => {key};
}
