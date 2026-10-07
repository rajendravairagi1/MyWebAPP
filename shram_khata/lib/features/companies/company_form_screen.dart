import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/providers.dart';

class CompanyFormScreen extends ConsumerStatefulWidget {
  const CompanyFormScreen({super.key, this.company});
  final Company? company;

  @override
  ConsumerState<CompanyFormScreen> createState() => _CompanyFormScreenState();
}

class _CompanyFormScreenState extends ConsumerState<CompanyFormScreen> {
  final _form = GlobalKey<FormState>();
  late final _name = TextEditingController(text: widget.company?.name);
  late final _person = TextEditingController(text: widget.company?.contactPerson);
  late final _mobile = TextEditingController(text: widget.company?.mobile);
  late final _email = TextEditingController(text: widget.company?.email);
  late final _address = TextEditingController(text: widget.company?.address);
  late final _gstin = TextEditingController(text: widget.company?.gstin);
  String? _branchId;
  bool _busy = false;

  @override
  void dispose() {
    for (final c in [_name, _person, _mobile, _email, _address, _gstin]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _busy = true);
    try {
      final svc = ref.read(companyServiceProvider);
      if (widget.company != null) {
        await svc.updateCompany(widget.company!.copyWith(
          name: _name.text.trim(),
          contactPerson: _person.text.trim(),
          mobile: _mobile.text.trim(),
          email: _email.text.trim(),
          address: _address.text.trim(),
          gstin: _gstin.text.trim().toUpperCase(),
        ));
      } else {
        final branch = _branchId ?? ref.read(writeBranchProvider);
        await svc.addCompany(
          branchId: branch!,
          name: _name.text,
          contactPerson: _person.text,
          mobile: _mobile.text,
          email: _email.text,
          address: _address.text,
          gstin: _gstin.text,
        );
      }
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final plan = ref.watch(planProvider);
    final scope = ref.watch(scopeBranchProvider);
    final allowed = ref.watch(allowedBranchesProvider);
    final showBranch = widget.company == null && plan.multiBranch && scope == null && allowed.length > 1;
    final taxLabel = ref.watch(profileProvider).value?.taxLabel ?? 'GST';
    return Scaffold(
      appBar: AppBar(title: Text(widget.company == null ? 'Add company' : 'Edit company')),
      body: Form(
        key: _form,
        child: FormBody(
          bottom: FilledButton(onPressed: _busy ? null : _save, child: const Text('Save company')),
          children: [
            TextFormField(
              controller: _name,
              textCapitalization: TextCapitalization.words,
              decoration: const InputDecoration(labelText: 'Company name *'),
              validator: (v) => (v ?? '').trim().isEmpty ? 'Enter the company name' : null,
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _person,
              textCapitalization: TextCapitalization.words,
              decoration: const InputDecoration(labelText: 'Contact person'),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _mobile,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: 'Mobile'),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: 'Email'),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _address,
              maxLines: 3,
              textCapitalization: TextCapitalization.sentences,
              decoration: const InputDecoration(labelText: 'Billing address'),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _gstin,
              textCapitalization: TextCapitalization.characters,
              decoration: InputDecoration(labelText: '$taxLabel number (optional)'),
            ),
            if (showBranch) ...[
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: _branchId ?? allowed.first.id,
                decoration: const InputDecoration(labelText: 'Branch'),
                items: [for (final b in allowed) DropdownMenuItem(value: b.id, child: Text(b.name))],
                onChanged: (v) => _branchId = v,
              ),
            ],
          ],
        ),
      ),
    );
  }
}
