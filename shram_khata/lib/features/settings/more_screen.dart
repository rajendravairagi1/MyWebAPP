import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/files.dart';
import '../../core/permissions.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';
import '../billing/invoices_screen.dart';
import '../companies/companies_screen.dart';
import '../reports/reports_screen.dart';
import '../team/team_screen.dart';
import 'account_screen.dart';
import 'backup_screen.dart';
import 'branches_screen.dart';
import 'business_profile_screen.dart';
import 'plan_screen.dart';

class MoreScreen extends ConsumerWidget {
  const MoreScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final s = ref.watch(sessionProvider);
    final profile = ref.watch(profileProvider).value;
    final plan = ref.watch(planProvider);
    void go(Widget w) => Navigator.push(context, MaterialPageRoute(builder: (_) => w));

    return Scaffold(
      appBar: AppBar(title: const Text('More')),
      body: ListView(padding: const EdgeInsets.fromLTRB(16, 0, 16, 32), children: [
        AppCard(
          onTap: s.can(Perm.settingsManage) ? () => go(const BusinessProfileScreen()) : null,
          child: Row(children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: Palette.brandTint,
                borderRadius: BorderRadius.circular(16),
                image: fileImage(profile?.logoPath) == null
                    ? null
                    : DecorationImage(image: fileImage(profile!.logoPath)!, fit: BoxFit.cover),
              ),
              child: fileImage(profile?.logoPath) == null ? Icon(Icons.storefront, color: Palette.brand, size: 28) : null,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(profile?.name.isNotEmpty == true ? profile!.name : 'Set up your business',
                    style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                const SizedBox(height: 3),
                Row(children: [
                  Pill(plan.title),
                  const SizedBox(width: 6),
                  Flexible(
                    child: Text('${s.staff?.name ?? ''} • ${s.role?.name ?? ''}',
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                  ),
                ]),
              ]),
            ),
            if (s.can(Perm.settingsManage)) const Icon(Icons.chevron_right, color: Palette.muted),
          ]),
        ),
        if (profile != null && profile.address.isEmpty && s.can(Perm.settingsManage))
          Padding(
            padding: const EdgeInsets.only(top: 10),
            child: AppCard(
              color: Palette.halfBg,
              onTap: () => go(const BusinessProfileScreen()),
              padding: const EdgeInsets.all(12),
              child: const Row(children: [
                Icon(Icons.edit_note, color: Palette.half),
                SizedBox(width: 10),
                Expanded(
                  child: Text('Add your logo, address, tax and payment details so statements and bills look complete.',
                      style: TextStyle(color: Palette.half, fontWeight: FontWeight.w600, fontSize: 13)),
                ),
              ]),
            ),
          ),
        const SectionTitle('BUSINESS'),
        GroupCard(children: [
          if (s.can(Perm.companiesView))
            MenuTile(icon: Icons.apartment_outlined, title: 'Companies & contracts', subtitle: 'Sites, billing rates', onTap: () => go(const CompaniesScreen())),
          if (s.can(Perm.billingView))
            MenuTile(icon: Icons.receipt_long_outlined, title: 'Invoices', subtitle: 'Bills to companies, receipts', color: Palette.info, onTap: () => go(const InvoicesScreen())),
          if (s.can(Perm.reports))
            MenuTile(icon: Icons.bar_chart_rounded, title: 'Reports', subtitle: 'Statements, registers, balances', color: Palette.present, onTap: () => go(const ReportsScreen())),
        ]),
        if (s.can(Perm.staffManage) || s.can(Perm.settingsManage) || s.can(Perm.branchesManage)) ...[
          const SectionTitle('ADMINISTRATION'),
          GroupCard(children: [
            if (s.can(Perm.staffManage))
              MenuTile(icon: Icons.groups_2_outlined, title: 'Team & roles', subtitle: plan.hasTeam ? 'Supervisors, accountants, custom roles' : 'Available on Owner + Team', onTap: () => go(const TeamScreen())),
            if (s.can(Perm.branchesManage) && plan.multiBranch)
              MenuTile(icon: Icons.account_tree_outlined, title: 'Branches', onTap: () => go(const BranchesScreen())),
            if (s.can(Perm.settingsManage))
              MenuTile(icon: Icons.business_center_outlined, title: 'Business profile', subtitle: 'Logo, tax, bank, UPI, invoice details', onTap: () => go(const BusinessProfileScreen())),
            if (s.can(Perm.settingsManage))
              MenuTile(icon: Icons.workspace_premium_outlined, title: 'Plan', subtitle: plan.title, color: Palette.half, onTap: () => go(const PlanSettingsScreen())),
          ]),
        ],
        const SectionTitle('ACCOUNT'),
        GroupCard(children: [
          MenuTile(icon: Icons.lock_outline, title: 'Account & security', onTap: () => go(const AccountScreen())),
          if (s.isOwner)
            MenuTile(icon: Icons.cloud_download_outlined, title: 'Backup & export', subtitle: 'Save a copy of all your data', onTap: () => go(const BackupScreen())),
          MenuTile(
            icon: Icons.logout,
            title: 'Sign out',
            color: Palette.absent,
            onTap: () async {
              final ok = await confirm(context, title: 'Sign out?', message: 'Your data stays on this phone.', confirmLabel: 'Sign out');
              if (ok) await ref.read(sessionProvider.notifier).logout();
            },
          ),
        ]),
        const SizedBox(height: 20),
        const Center(child: Text('HazriBook  •  v1.0', style: TextStyle(color: Palette.muted, fontSize: 12))),
      ]),
    );
  }
}
