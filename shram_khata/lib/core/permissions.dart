/// Everything a role may be allowed to do. Roles store a set of these keys.
class Perm {
  Perm._();

  static const dashboard = 'dashboard.view';
  static const labourView = 'labour.view';
  static const labourManage = 'labour.manage';
  static const labourDocs = 'labour.documents';
  static const attendanceView = 'attendance.view';
  static const attendanceMark = 'attendance.mark';
  static const attendanceEditLocked = 'attendance.edit_locked';
  static const paymentsView = 'payments.view';
  static const paymentsManage = 'payments.manage';
  static const companiesView = 'companies.view';
  static const companiesManage = 'companies.manage';
  static const billingView = 'billing.view';
  static const billingManage = 'billing.manage';
  static const reports = 'reports.view';
  static const staffManage = 'staff.manage';
  static const settingsManage = 'settings.manage';
  static const branchesManage = 'branches.manage';

  static const all = <String>[
    dashboard,
    labourView,
    labourManage,
    labourDocs,
    attendanceView,
    attendanceMark,
    attendanceEditLocked,
    paymentsView,
    paymentsManage,
    companiesView,
    companiesManage,
    billingView,
    billingManage,
    reports,
    staffManage,
    settingsManage,
    branchesManage,
  ];

  /// Permission groups as shown in the role editor.
  static const groups = <String, List<(String, String)>>{
    'Dashboard & reports': [
      (dashboard, 'See dashboard'),
      (reports, 'See and export reports'),
    ],
    'Labour': [
      (labourView, 'View labour list'),
      (labourManage, 'Add, edit, deactivate labour'),
      (labourDocs, 'View and upload documents'),
    ],
    'Attendance': [
      (attendanceView, 'View attendance'),
      (attendanceMark, 'Mark attendance'),
      (attendanceEditLocked, 'Edit after submission'),
    ],
    'Payments': [
      (paymentsView, 'View payments and balances'),
      (paymentsManage, 'Add and void payments'),
    ],
    'Companies & billing': [
      (companiesView, 'View companies and contracts'),
      (companiesManage, 'Add and edit companies, sites, contracts'),
      (billingView, 'View invoices'),
      (billingManage, 'Create invoices and record receipts'),
    ],
    'Administration': [
      (staffManage, 'Manage team and roles'),
      (settingsManage, 'Business settings'),
      (branchesManage, 'Manage branches'),
    ],
  };

  static const supervisor = <String>[
    labourView,
    attendanceView,
    attendanceMark,
  ];

  static const accountant = <String>[
    dashboard,
    labourView,
    attendanceView,
    paymentsView,
    paymentsManage,
    companiesView,
    billingView,
    billingManage,
    reports,
  ];
}

/// Ids of the built-in roles created for every workspace.
class RoleIds {
  RoleIds._();
  static const owner = 'role_owner';
  static const supervisor = 'role_supervisor';
  static const accountant = 'role_accountant';
}
