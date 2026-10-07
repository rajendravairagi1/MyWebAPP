/// Subscription tiers. Limits are enforced in the UI and services so the
/// owner of the product decides what each tier unlocks.
enum Plan {
  solo(
    id: 'solo',
    title: 'Solo',
    tagline: 'You run everything yourself',
    features: [
      'One user: you do it all',
      'Unlimited labour, companies and sites',
      'Attendance, payments, PDF statements and bills',
    ],
    maxStaff: 1,
    customRoles: false,
    multiBranch: false,
  ),
  team(
    id: 'team',
    title: 'Owner + Team',
    tagline: 'Supervisors and accountants work with you',
    features: [
      'Everything in Solo',
      'Add supervisors, accountants and more',
      'Create custom roles with your own permissions',
      'Supervisors see only their own sites',
    ],
    maxStaff: 1000,
    customRoles: true,
    multiBranch: false,
  ),
  company(
    id: 'company',
    title: 'Company',
    tagline: 'Several branches under one owner',
    features: [
      'Everything in Owner + Team',
      'Multiple branches, each with its own team',
      'Switch branches or see all together',
      'Branch-wise dashboard and reports',
    ],
    maxStaff: 1000,
    customRoles: true,
    multiBranch: true,
  );

  const Plan({
    required this.id,
    required this.title,
    required this.tagline,
    required this.features,
    required this.maxStaff,
    required this.customRoles,
    required this.multiBranch,
  });

  final String id;
  final String title;
  final String tagline;
  final List<String> features;
  final int maxStaff;
  final bool customRoles;
  final bool multiBranch;

  bool get hasTeam => maxStaff > 1;

  static Plan fromId(String? id) =>
      Plan.values.firstWhere((p) => p.id == id, orElse: () => Plan.solo);
}
