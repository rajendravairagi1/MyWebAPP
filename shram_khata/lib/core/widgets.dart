import 'package:flutter/material.dart';

import 'money.dart';
import 'theme.dart';

/// Shows an error from a service call as a snackbar.
void showError(BuildContext context, Object e) {
  final msg = e.toString().replaceFirst('Exception: ', '');
  ScaffoldMessenger.of(context)
    ..hideCurrentSnackBar()
    ..showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: Palette.absent,
    ));
}

void showInfo(BuildContext context, String msg) {
  ScaffoldMessenger.of(context)
    ..hideCurrentSnackBar()
    ..showSnackBar(SnackBar(content: Text(msg)));
}

Future<bool> confirm(
  BuildContext context, {
  required String title,
  required String message,
  String confirmLabel = 'Yes',
  bool danger = false,
}) async {
  final r = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      title: Text(title),
      content: Text(message),
      actions: [
        TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
        TextButton(
          onPressed: () => Navigator.pop(ctx, true),
          style: danger ? TextButton.styleFrom(foregroundColor: Palette.absent) : null,
          child: Text(confirmLabel),
        ),
      ],
    ),
  );
  return r ?? false;
}

/// A white rounded container, optionally tappable.
class AppCard extends StatelessWidget {
  const AppCard({
    super.key,
    required this.child,
    this.onTap,
    this.padding = const EdgeInsets.all(16),
    this.color,
    this.margin,
  });

  final Widget child;
  final VoidCallback? onTap;
  final EdgeInsets padding;
  final Color? color;
  final EdgeInsets? margin;

  @override
  Widget build(BuildContext context) {
    final card = Material(
      color: color ?? Palette.surface,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: Palette.line),
      ),
      clipBehavior: Clip.antiAlias,
      child: onTap == null
          ? Padding(padding: padding, child: child)
          : InkWell(onTap: onTap, child: Padding(padding: padding, child: child)),
    );
    return margin == null ? card : Padding(padding: margin!, child: card);
  }
}

class SectionTitle extends StatelessWidget {
  const SectionTitle(this.text, {super.key, this.trailing, this.padding});
  final String text;
  final Widget? trailing;
  final EdgeInsets? padding;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: padding ?? const EdgeInsets.fromLTRB(20, 20, 20, 8),
      child: Row(
        children: [
          Expanded(
            child: Text(
              text,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: Palette.muted,
                letterSpacing: 0.6,
              ),
            ),
          ),
          ?trailing,
        ],
      ),
    );
  }
}

class EmptyState extends StatelessWidget {
  const EmptyState({
    super.key,
    required this.icon,
    required this.title,
    this.message,
    this.action,
  });

  final IconData icon;
  final String title;
  final String? message;
  final Widget? action;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 76,
              height: 76,
              decoration: const BoxDecoration(color: Palette.brandTint, shape: BoxShape.circle),
              child: Icon(icon, size: 36, color: Palette.brand),
            ),
            const SizedBox(height: 18),
            Text(title,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w700)),
            if (message != null) ...[
              const SizedBox(height: 6),
              Text(message!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Palette.muted, height: 1.4)),
            ],
            if (action != null) ...[const SizedBox(height: 20), action!],
          ],
        ),
      ),
    );
  }
}

/// Small coloured pill, e.g. status badges.
class Pill extends StatelessWidget {
  const Pill(this.text, {super.key, this.color = Palette.brand, this.bg, this.icon});
  final String text;
  final Color color;
  final Color? bg;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 4),
      decoration: BoxDecoration(
        color: bg ?? color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[Icon(icon, size: 13, color: color), const SizedBox(width: 4)],
          Text(text,
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: color)),
        ],
      ),
    );
  }
}

/// Initial-letter avatar, or the photo when there is one.
class Avatar extends StatelessWidget {
  const Avatar({super.key, required this.name, this.image, this.size = 44});
  final String name;
  final ImageProvider? image;
  final double size;

  @override
  Widget build(BuildContext context) {
    final initial = name.trim().isEmpty ? '?' : name.trim().characters.first.toUpperCase();
    return CircleAvatar(
      radius: size / 2,
      backgroundColor: Palette.brandTint,
      backgroundImage: image,
      child: image == null
          ? Text(initial,
              style: TextStyle(
                fontSize: size * 0.42,
                fontWeight: FontWeight.w700,
                color: Palette.brandDark,
              ))
          : null,
    );
  }
}

/// Label above a value, used in stat rows.
class Stat extends StatelessWidget {
  const Stat({super.key, required this.label, required this.value, this.color, this.align});
  final String label;
  final String value;
  final Color? color;
  final CrossAxisAlignment? align;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: align ?? CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, color: Palette.muted)),
        const SizedBox(height: 2),
        Text(value,
            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: color ?? Palette.ink)),
      ],
    );
  }
}

/// Coloured amount: green when positive, red when the agency owes money.
class BalanceText extends StatelessWidget {
  const BalanceText(this.paise, {super.key, this.size = 15});
  final int paise;
  final double size;

  @override
  Widget build(BuildContext context) {
    final color = paise > 0 ? Palette.absent : (paise < 0 ? Palette.half : Palette.present);
    return Text(
      Money.format(paise.abs()),
      style: TextStyle(fontSize: size, fontWeight: FontWeight.w800, color: color),
    );
  }
}

class LoadingView extends StatelessWidget {
  const LoadingView({super.key});
  @override
  Widget build(BuildContext context) =>
      const Center(child: CircularProgressIndicator(color: Palette.brand));
}

class ErrorView extends StatelessWidget {
  const ErrorView(this.error, {super.key});
  final Object error;
  @override
  Widget build(BuildContext context) => EmptyState(
        icon: Icons.error_outline,
        title: 'Something went wrong',
        message: error.toString(),
      );
}

/// Standard padded scroll body for forms.
class FormBody extends StatelessWidget {
  const FormBody({super.key, required this.children, this.bottom});
  final List<Widget> children;
  final Widget? bottom;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Expanded(
          child: ListView(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
            keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
            children: children,
          ),
        ),
        if (bottom != null)
          SafeArea(
            top: false,
            child: Container(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
              decoration: const BoxDecoration(
                color: Palette.surface,
                border: Border(top: BorderSide(color: Palette.line)),
              ),
              child: bottom,
            ),
          ),
      ],
    );
  }
}

/// A grouped "settings" style list container.
class GroupCard extends StatelessWidget {
  const GroupCard({super.key, required this.children});
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    final items = <Widget>[];
    for (var i = 0; i < children.length; i++) {
      items.add(children[i]);
      if (i < children.length - 1) {
        items.add(const Divider(indent: 56));
      }
    }
    return Card(
      clipBehavior: Clip.antiAlias,
      child: Column(children: items),
    );
  }
}

class MenuTile extends StatelessWidget {
  const MenuTile({
    super.key,
    required this.icon,
    required this.title,
    this.subtitle,
    this.onTap,
    this.trailing,
    this.color,
  });

  final IconData icon;
  final String title;
  final String? subtitle;
  final VoidCallback? onTap;
  final Widget? trailing;
  final Color? color;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      minVerticalPadding: 12,
      leading: Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          color: (color ?? Palette.brand).withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(11),
        ),
        child: Icon(icon, size: 21, color: color ?? Palette.brand),
      ),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600)),
      subtitle: subtitle == null ? null : Text(subtitle!),
      trailing: trailing ?? (onTap != null ? const Icon(Icons.chevron_right, color: Palette.muted) : null),
    );
  }
}
